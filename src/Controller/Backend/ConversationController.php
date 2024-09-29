<?php

namespace App\Controller\Backend;

use App\Entity\Annonce;
use App\Entity\Message;
use App\Entity\Conversation;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ConversationController extends AbstractController
{
    #[Route('/conversation', name: 'app.conversation')]
    public function index(EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        $conversations = $em->getRepository(Conversation::class)->findBy(['creator' => $user]); // Récupération des conversations créées par l'utilisateur connecté

        return $this->render('Backend/Conversation/index.html.twig', [
            'conversations' => $conversations, // Récupération des conversations liées à l'utilisateur connecté
        ]);
    }

    #[Route('/conversation/{id}', name: 'app.conversation.new')]
    public function newConversation($id, EntityManagerInterface $em): Response
    {
        //dd($id);

        $annonce = $em->getRepository(Annonce::class)->find($id); // Récupération de l'annonce à partir de son id  
        $user = $this->getUser(); // Récupération de l'utilisateur connecté  

        // dd($annonce);
        if (!$annonce) {
            throw $this->createNotFoundException('Annonce introuvable');
        }

        // Vérification si la conversation existe déjà
        $conversation = $em->getRepository(Conversation::class)->findOneBy(['annonce' => $annonce, 'creator' => $user]); 

        if (!$conversation) {
            // Si la conversation n'existe pas, on en crée une nouvelle
            $conversation = new Conversation();
            $conversation->setAnnonce($annonce); 
            $conversation->setCreator($user); 
            //$annonce->addConversation($conversation);  // Ajout de la conversation à l'annonce

            $em->persist($conversation);
            $em->persist($annonce);
            $em->flush();
        }

        return $this->redirectToRoute('app.conversation.show', [
            'id' => $conversation->getId()
        ]);
    }

    #[Route('/conversation/{id}', name: 'app.conversation.show')]
    public function showConversation($id, EntityManagerInterface $em): Response
    {
        $conversation = $em->getRepository(Conversation::class)->find($id); // Récupération de la conversation à partir de son id 

        if (!$conversation) {
            $this->addFlash('error', 'La conversation n\'existe pas.');
        }

        return $this->render('Backend/Conversation/show.html.twig', [
            'conversation' => $conversation,
        ]);
    }

    #[Route('/conversation/{id}/message', name: 'app.conversation.send', methods: ['POST'])]
    public function sendMessage($id, Request $request, EntityManagerInterface $em): Response
    {
        $conversation = $em->getRepository(Conversation::class)->find($id); // Récupération de la conversation à partir de son id
        $user = $this->getUser(); // Récupération de l'utilisateur connecté

        $message = new Message(); // Création d'un nouveau message
        $message->setConversation($conversation); // Liaison du message à la conversation
        $message->setSender($user); // Liaison du message à l'utilisateur qui envoie le message
        $message->setContent($request->request->get('content')); // Récupération du contenu du message depuis la requête
        $message->setCreatedAtValue(new \DateTime()); // Définition de la date de création du message

        $em->persist($message); 
        $em->flush(); 

        return $this->redirectToRoute('app.conversation.show', [
            'id' => $conversation->getId()
        ]);
    }
}
