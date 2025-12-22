<?php

namespace App\Controller;

use App\Entity\Chapter;
use App\Entity\Exercise;
use App\Entity\RevisionElement;
use App\Entity\RevisionQuestion;
use App\Entity\RevisionSheet;
use App\Entity\User;
use App\Repository\ExerciseRepository;
use App\Repository\RevisionElementRepository;
use App\Repository\RevisionSheetRepository;
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
        /**
         * @var RevisionSheetRepository $sheetsRepo
         */
        $sheetsRepo = $entityManager->getRepository(RevisionSheet::class);

        return $this->render('user/home/home.html.twig', [
            "stats" => [
                "questions" => $entityManager->getRepository(RevisionQuestion::class)->count(),
                "users" => $entityManager->getRepository(User::class)->count(),
                "exercises" => $exerciseRepo->count(),
                "cards" => $sheetsRepo->countWithoutParent(),
                "corrected" => round($exerciseRepo->countCorrected() * 100 / max(1, $exerciseRepo->count()), 2)
            ]
        ]);
    }
}
