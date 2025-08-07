<?php

namespace App\Controller;

use App\Entity\Chapter;
use App\Entity\Exercise;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $exerciseRepo = $entityManager->getRepository(Exercise::class);

        return $this->render('user/home/home.html.twig', [
            "stats" => [
                "chapters" => $entityManager->getRepository(Chapter::class)->count(),
                "users" => $entityManager->getRepository(User::class)->count(),
                "exercises" => $exerciseRepo->count(),
                "completed" => -1,
                "corrected" => $exerciseRepo->countCorrected() / max(1, $exerciseRepo->count())
            ]
        ]);
    }
}
