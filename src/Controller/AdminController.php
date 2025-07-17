<?php

namespace App\Controller;

use App\Entity\Chapter;
use App\Entity\Exercise;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AdminController extends AbstractController
{
    #[Route('/admin', name: 'app_admin_dashboard')]
    public function dashboard(EntityManagerInterface $entityManager): Response
    {
        $exerciseRepo = $entityManager->getRepository(Exercise::class);
        
        return $this->render('admin/dashboard/dashboard.html.twig', [
            "stats" => [
                "chapters" => $entityManager->getRepository(Chapter::class)->count(),
                "users" => $entityManager->getRepository(User::class)->count(),
                "exercises" => $exerciseRepo->count(),
                "completed" => 1023,
                "corrected" => $exerciseRepo->countCorrected() / max(1, $exerciseRepo->count())
            ]
        ]);
    }

    #[Route('/admin/users', name: 'app_admin_users')]
    public function users(): Response
    {
        return $this->render('admin/dashboard/dashboard.html.twig', [

        ]);
    }

    #[Route('/admin/subjects', name: 'app_admin_subjects')]
    public function subjects(): Response
    {
        return $this->render('admin/dashboard/dashboard.html.twig', [

        ]);
    }

    #[Route('/admin/tags', name: 'app_admin_tags')]
    public function tags(): Response
    {
        return $this->render('admin/dashboard/dashboard.html.twig', [

        ]);
    }
}
