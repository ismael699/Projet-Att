<?php

namespace App\Controller\Backend;
 
use DateTimeImmutable;
use App\Entity\Annonce;
use App\Entity\Message;
use App\Form\MessageType;
use App\Entity\Conversation;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ConversationController extends AbstractController
{
    #[Route('/conversation', name: 'app.conversation')]
    public function index(EntityManagerInterface $em): Response
    {
        $user = $this->getUser();

        // Récupération des conversations créées par l'utilisateur connecté
        $createdConversations = $em->getRepository(Conversation::class)->findBy(['creator' => $user]);

        // Récupération des conversations associées aux annonces dont l'utilisateur est le propriétaire
        $ownedConversations = $em->getRepository(Conversation::class)
            ->createQueryBuilder('c')
            ->innerJoin('c.annonce', 'a') // Liaison à l'entité Annonce
            ->where('a.user = :user') // Utilisez 'user' pour correspondre à la propriété de l'entité Annonce
            ->setParameter('user', $user)
            // ->orderBy('c.createdAt', 'DESC') // Trier par la date de création du message le plus récent
            ->getQuery()
            ->getResult();

        // Fusionner les deux ensembles de conversations
        $conversations = array_merge($createdConversations, $ownedConversations);

        // Trier les conversations par la date du dernier message
        usort($conversations, function (Conversation $a, Conversation $b) {
            $lastMessageA = $a->getLastMessage();
            $lastMessageB = $b->getLastMessage();

            if ($lastMessageA === null && $lastMessageB === null) {
                return 0;
            }

            if ($lastMessageA === null) {
                return 1;
            }

            if ($lastMessageB === null) {
                return -1;
            }

            return $lastMessageB->getCreatedAt() <=> $lastMessageA->getCreatedAt();
        });

        return $this->render('Backend/Conversation/index.html.twig', [
            'conversations' => $conversations, // Récupération des conversations liées à l'utilisateur connecté
        ]);
    }

    #[Route('/annonce/{id}/conversation/new', name: 'app.conversation.new')]
    public function newConversation(#[MapEntity(id: 'id')] ?Annonce $annonce, EntityManagerInterface $em): Response 
    // "?Annonce Annonce" c'est le param converter, ça permet d'aller chercher dans la base de donnée un objet Annonce ayant l'ID des paramètres de l'url, 
    // et si il n'existe pas le code va continuer car il y a un point d'interrogation
    {
        $user = $this->getUser(); // Récupération de l'utilisateur connecté  

        if (!$annonce) {
            dd("L'annonce n'existe pas");
            throw $this->createNotFoundException('Annonce introuvable');
        }

        // Vérification si la conversation existe déjà
        $conversation = $em->getRepository(Conversation::class)->findOneBy(['annonce' => $annonce, 'creator' => $user]); 

        if (!$conversation) {
            // Si la conversation n'existe pas, on en crée une nouvelle
            $conversation = new Conversation();
            $conversation->setAnnonce($annonce)
                         ->setCreator($user); 
            
            // Et on crée un message par défaut
            $message = new Message();
            $message->setCreatedAt(new DateTimeImmutable())
                    ->setSender($user)
                    ->setContent('Bonjour, êtes-vous disponible ?')
                    ->setConversation($conversation);
            
            $em->persist($message);
            $em->persist($conversation);
            $em->flush();
        };

        $this->addFlash('success', 'Message envoyé avec succès.');
        return $this->redirectToRoute('app.annonce.index');
    }

    #[Route('/conversation/{id}', name: 'app.conversation.show')]
    public function messageConversation($id, EntityManagerInterface $em, Request $request): Response
    {
        $conversation = $em->getRepository(Conversation::class)->find($id); // Récupération de la conversation à partir de son id 

        if (!$conversation) {
            $this->addFlash('error', 'La conversation n\'existe pas.');
            return $this->redirectToRoute('app.conversation');
        }

        // Créer un nouveau formulaire de message
        $user = $this->getUser(); 
        $form = $this->createForm(MessageType::class);
        $form->handleRequest($request); 

        if ($form->isSubmitted() && $form->isValid()) {
            $message = $form->getData();
            $message->setConversation($conversation); 
            $message->setSender($user); 
            $message->setCreatedAtValue(new \DateTime()); 

            $em->persist($message); 
            $em->flush(); 
        }

        // Créer le paiement
        

        return $this->render('Backend/Conversation/show.html.twig', [
            'form' => $form->createView(),
            'conversation' => $conversation,
            'messages' => $conversation->getMessages(), // Récupération des messages de la conversation
        ]);
    }
}
