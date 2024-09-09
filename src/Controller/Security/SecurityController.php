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
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
       // Garde le dernier email entré par l'utilisateur
       $lastEmail = $authenticationUtils->getLastUsername();
       // Récupère l'erreur de connexion s'il y en a une
       $error = $authenticationUtils->getLastAuthenticationError();

       // Ajoute un message flash en cas d'erreur
        if ($error) {
            $this->addFlash('error', 'Email ou mot de passe incorrect.');
        }

       return $this->render('Security/login.html.twig', [
           'last_email' => $lastEmail,
           'error' => $error,
       ]); 
    }

    #[Route('/logout', name: 'app.logout')]
    public function logout(): void
    {}

    #[Route('/register', name: 'app.register', methods: ['GET', 'POST'])]
    public function register(Request $request, UserPasswordHasherInterface $passwordHasher, SessionInterface $session, EntityManagerInterface $em): Response
    {
        $user = new User(); // créer un nouvel objet utilisateur

        $form = $this->createForm(UserType::class, $user); // créer du form
        $form->handleRequest($request); // traite les informations soumises

        if ($form->isSubmitted() && $form->isValid()) {
            // vérifie si l'email existe 
            $email = $em->getRepository(User::class)->findOneBy(['email' => $user->getEmail()]);
            if ($email) {
                $this->addFlash('error', 'L\'email est déjà utilisé.');
                return $this->redirectToRoute('app.register');
            }

            // vérifie si le numéro de Siren existe
            $siren = $em->getRepository(User::class)->findOneBy(['siren' => $user->getSiren()]);
            if ($siren) {
                $this->addFlash('error', 'Le K-bis est déjà utilisé.');
                return $this->redirectToRoute('app.register');
            }

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

            // hash le mot de passe de l'utilisateur
            $hashedPassword = $passwordHasher->hashPassword($user, $form->get('password')->getData());
            $user->setPassword($hashedPassword);

            // stocke temporairement les données de l'utilisateur en session
            $session->set('pending_user', [
                'email' => $user->getEmail(),
                'password' => $user->getPassword(),
                'roles' => $user->getRoles(),
                'siren' => $user->getSiren(),
            ]);

            $session->set('pending_user', $user);

            // stocke le montant du paiement dans la session
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
            // throw $this->createNotFoundException('Données utilisateur ou montant du paiement introuvables.');

            $this->addFlash('error', 'Données utilisateur ou montant du paiement introuvables.');
            return $this->redirectToRoute('app.login');
        }

        // Création de l'objet utilisateur en lui assignant les données
        $user = new User();
        $user->setEmail($pendingUserData->getEmail());
        $user->setPassword($pendingUserData->getPassword());
        $user->setRoles($pendingUserData->getRoles());
        $user->setSiren($pendingUserData->getSiren());

        // Création de l'objet payment 
        $payment = new Payment();
        $payment->setStatus('succeeded');
        $payment->setPaymentDate(new \DateTime());
        $payment->setAmount($amount);

        // Relie l'utilisateur au paiement
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
