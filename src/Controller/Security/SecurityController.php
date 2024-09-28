<?php

namespace App\Controller\Security;

use Stripe\Stripe;
use App\Entity\User;
use App\Form\UserType;
use App\Entity\Payment;
use App\Service\JWTService;
use Stripe\Checkout\Session;
use App\Form\ChangePasswordType;
use App\Form\ForgotPasswordType;
use App\Service\SendEmailService;
use App\Repository\UserRepository;
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

            // hash le mot de passe de l'utilisateur
            $hashedPassword = $passwordHasher->hashPassword($user, $form->get('password')->getData());
            $user->setPassword($hashedPassword);

            // stocke temporairement les données de l'utilisateur en session
            $session->set('pending_user', [
                'firstName' => $user->getFirstName(),
                'lastName' => $user->getlastName(),
                'email' => $user->getEmail(),
                'password' => $user->getPassword(),
                'roles' => $user->getRoles(),
                'siren' => $user->getSiren(),
            ]);

            $session->set('pending_user', $user);

            // stocke le montant du paiement dans la session
            $session->set('pending_payment_amount', $amount);

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
        $user->setFirstName($pendingUserData->getFirstName());
        $user->setlastName($pendingUserData->getlastName());
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

    #[Route('/forgot-password', name: 'app.forgot.password')]
    public function forgottenPasswod(Request $request, UserRepository $userRepo, JWTService $jwt, SendEmailService $mail): Response
    {
        //dd($_ENV);
        $form = $this->createForm(ForgotPasswordType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // On récupère l'utilisateur par son email
            $user = $userRepo->findOneByEmail($form->get('email')->getData());

            // Si l'utilisateur existe
            if ($user) {
                // On génère un token
                $header = [
                    'typ' => 'JWT',
                    'alg' => 'HS256',
                ];

                $payload = [
                    'user_id' => $user->getId(),
                ];

                // On génère le token
                $token = $jwt->generate($header, $payload, $this->getParameter('app.jwtsecret'));

                // On génère l'URL vers app.reset.password
                $url = $this->generateUrl('app.reset.password', ['token' => $token], UrlGeneratorInterface::ABSOLUTE_URL);

                $mail->send(
                    'no-reply@att.fr',
                    $user->getEmail(),
                    'Récupération de mot de passe',
                    'email',
                    compact('user', 'url') // ['user' => $user, 'url'=>$url]
                );

                $this->addFlash('success', 'Email envoyé avec succès.');
                return $this->redirectToRoute('app.login');
            }

            // S'il n'existe pas
            $this->addFlash('error', 'Un problème est survenu.');
            return $this->redirectToRoute('app.login');
        }

        return $this->render('Security/Password/forgot_password.html.twig', [
            'passwordForm' => $form,
        ]);
    }

    #[Route('/reset-password/{token}', name: 'app.reset.password')]
    public function resetPassword(JWTService $jwt, Request $request, UserRepository $userRepo, $token, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $em): Response
    {
        // On vérifie si le token est valide (cohérent, pas expiré et signature correcte)
        if($jwt->isValid($token) && !$jwt->isExpired($token) && $jwt->check($token, $this->getParameter('app.jwtsecret'))){
            $payload = $jwt->getPayload($token); // On récupère les données (payload)
            $user = $userRepo->find($payload['user_id']); // On récupère le user

            if($user){
                $form = $this->createForm(ChangePasswordType::class);
                $form->handleRequest($request);

                if($form->isSubmitted() && $form->isValid()){
                    // $user->setPassword($passwordHasher->hashPassword($user, $form->get('password')->getData()));
                    $plainPassword = $form->get('plainPassword')->getData();
                    $hashedPassword = $passwordHasher->hashPassword($user, $plainPassword);
                    $user->setPassword($hashedPassword);
                    $em->flush();

                    $this->addFlash('success', 'Mot de passe changé avec succès');
                    return $this->redirectToRoute('app.login');
                }
                return $this->render('Security/Password/reset.html.twig', [
                    'newPassword' => $form,
                ]);
            }
        }

        $this->addFlash('danger', 'Le token est invalide ou a expiré');
        return $this->redirectToRoute('app.login');
    }
}


