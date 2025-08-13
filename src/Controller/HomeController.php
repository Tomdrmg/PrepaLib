<?php

namespace App\Controller;

use App\Entity\Chapter;
use App\Entity\Exercise;
use App\Entity\User;
use App\Repository\ExerciseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        /**
         * @var ExerciseRepository $exerciseRepo
         */
        $exerciseRepo = $entityManager->getRepository(Exercise::class);

        return $this->render('user/home/home.html.twig', [
            "stats" => [
                "chapters" => $entityManager->getRepository(Chapter::class)->count(),
                "users" => $entityManager->getRepository(User::class)->count(),
                "exercises" => $exerciseRepo->count(),
                "completed" => -1,
                "corrected" => round($exerciseRepo->countCorrected() * 100 / max(1, $exerciseRepo->count()), 2)
            ]
        ]);
    }
}
