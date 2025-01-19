<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Copypasta;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Repository\CopypastaRepository;

final class HomeController extends AbstractController
{
    #[Route('/copypasta', name: 'app_home')]
    public function index(CopypastaRepository $cpRepo): Response
    {
        $messages = $cpRepo->findTopThreeByCount();

        return $this->render('home/index.html.twig', [
            'messages' => $messages,
        ]);
    }

    #[Route('/', name: 'app_copypasta', methods: ['POST'])]
    public function copypasta(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        // Vérification des données
        if (!isset($data['messages']) || !is_array($data['messages'])) {
            return new JsonResponse(['error' => 'Invalid data format'], 400);
        }

        $savedMessages = [];
        foreach ($data['messages'] as $messageData) {
            if (
                !isset($messageData['text']) || 
                !isset($messageData['users']) || 
                !isset($messageData['count']) || 
                !is_array($messageData['users'])
            ) {
                continue; // Ignorer les messages avec un format incorrect
            }

            // Créer une nouvelle entité Copypasta
            $copypasta = new Copypasta();
            $copypasta->setAuthor($messageData['users'][0]); // Premier utilisateur ayant envoyé le message
            $copypasta->setMessage($messageData['text']);
            $copypasta->setCount($messageData['count']);
            $copypasta->setRepostBy($messageData['users']);
            $copypasta->setDate(new \DateTime()); // Date actuelle

            // Sauvegarder en base de données
            $entityManager->persist($copypasta);
            $savedMessages[] = $messageData['text'];
        }

        // Exécuter les requêtes en base
        $entityManager->flush();

        // Retourner une réponse JSON avec les messages sauvegardés
        return new JsonResponse([
            'success' => true,
            'savedMessages' => $savedMessages,
        ]);
    }
}
