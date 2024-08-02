<?php

namespace App\Controller\Security;

use DateTime;
use Stripe\Stripe;
use App\Entity\User;
use App\Form\UserType;
use App\Entity\Payment;
use Stripe\Checkout\Session;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class SecurityController extends AbstractController
{
    #[Route('/login', name: 'app.login', methods: ['GET', 'POST'])]
    public function login(AuthenticationUtils $authenticationUtils, EntityManagerInterface $entityManager): Response
    {
        // garde le dernier email entré par l'utilisateur
        $lastEmail = $authenticationUtils->getLastUsername();

        // vérifie si l'utilisateur existe
        $user = $entityManager->getRepository(User::class)->findOneBy(['email' => $lastEmail]);

        if ($user) {
            $payment = $user->getPayment();

            if ($payment && $payment->getStatus() === 'succeeded') {
                // si le status est "succeeded"
                return $this->redirectToRoute('app.annonce.index');
            } else {
                // sinon
                $this->addFlash('error', 'Utilisateur non trouvé.');
                return $this->redirectToRoute('app.register');
            }
        }

        return $this->render('Security/login.html.twig', [
            'last_email' => $lastEmail
        ]);
    }

    #[Route('/logout', name: 'app.logout')]
    public function logout(): void
    {}

    #[Route('/register', name: 'app.register', methods: ['GET', 'POST'])]
    public function register(Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $passwordHasher, SessionInterface $session): Response
    {
        $user = new User(); // nouvel utilisateur

        $form = $this->createForm(UserType::class, $user); // création du form
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $roles = $user->getRoles(); // récupère le rôle choisi par l'utilisateur
            $amount = 0; // prix de base

            if (in_array('ROLE_CLIENT', $roles)) {
                $amount = 1000; // 10.00 euro
            } elseif (in_array('ROLE_CHAUFFEUR', $roles)) {
                $amount = 1500; // 15.00 euro
            }

            // création la session stripe avec le montant calculé
            Stripe::setApiKey($this->getParameter('stripe_secret_key')); 

            $sessionStripe = Session::create([
                'payment_method_types' => ['card'], // méthode de paiement, ici par carte
                'line_items' => [[
                    'price_data' => [
                        'currency'  => 'eur', // devise de la transaction
                        'product_data' => [
                            'name' => 'Inscription',
                        ],
                        'unit_amount' => $amount, // montant de la transaction
                    ],
                    'quantity' => 1, // quantité du service acheter
                ]],
                'mode' => 'payment', // mode de la session ('payement' pour un paiement unique)
                // url de redirection
                'success_url' => $this->generateUrl('app.payment_success', [], UrlGeneratorInterface::ABSOLUTE_URL),
                'cancel_url' => $this->generateUrl('app.payment_cancel', [], UrlGeneratorInterface::ABSOLUTE_URL),
            ]);

            // Hash le mot de passe sans encore persister l'utilisateur
            $hashedPassword = $passwordHasher->hashPassword($user, $user->getPassword());
            $user->setPassword($hashedPassword);

            // Stocke temporairement les données de l'utilisateur dans la session
            $session->set('pending_user', [
                'email' => $user->getEmail(),
                'password' => $user->getPassword(),
                'roles' => $user->getRoles(),
                'siren' => $user->getSiren(),
                'fileName' => $user->getFileName(),
                'fileSize' => $user->getFileSize(),
            ]);

            // Stocke le montant du paiement dans la session
            $session->set('pending_payment_amount', $amount);

            return $this->redirect($sessionStripe->url, 303);
        }

        return $this->render('Security/register.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/payment/success', name: 'app.payment_success')]
    public function paymentSuccess(EntityManagerInterface $em, SessionInterface $session): Response 
    {
        // Récupère les données de l'utilisateur depuis la session
        $pendingUserData = $session->get('pending_user');
        $amount = $session->get('pending_payment_amount');

        if (!$pendingUserData || !$amount) {
            throw $this->createNotFoundException('Données utilisateur ou montant du paiement introuvables.');
        }

        // Crée l'utilisateur et assigne les données
        $user = new User();
        $user->setEmail($pendingUserData['email']);
        $user->setPassword($pendingUserData['password']);
        $user->setRoles($pendingUserData['roles']);
        $user->setSiren($pendingUserData['siren']);
        $user->setFileName($pendingUserData['fileName']);
        $user->setFileSize($pendingUserData['fileSize']);

        // Crée un nouvel objet Payment et l'associe à l'utilisateur
        $payment = new Payment();
        $payment->setStatus('succeeded');
        $payment->setPaymentDate(new \DateTime());
        $payment->setAmount($amount);

        $user->setPayment($payment);

        // Persiste l'utilisateur et le paiement dans la base de données
        $em->persist($user);
        $em->persist($payment);
        $em->flush();

        // Supprime les données de la session
        $session->remove('pending_user');
        $session->remove('pending_payment_amount');

        $this->addFlash('success', 'Paiement effectué avec succès.');

        return $this->redirectToRoute('app.login');
    }

    #[Route('/payment/cancel', name: 'app.payment_cancel')]
    public function paymentCancel(SessionInterface $session): Response 
    {
       // Supprime les données de la session
        $session->remove('pending_user');
        $session->remove('pending_payment_amount');

        $this->addFlash('error', 'Paiement annulé.');

        return $this->redirectToRoute('app.register');
    }
}
