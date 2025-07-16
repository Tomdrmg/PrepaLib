<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ResourceController extends AbstractController
{
    #[Route('/ressource/mathematics', name: 'app_mathematics')]
    public function mathematics(): Response
    {
        return $this->render('user/soon/soon.html.twig', [

        ]);
    }

    #[Route('/ressource/physics', name: 'app_physics')]
    public function physics(): Response
    {
        return $this->render('user/soon/soon.html.twig', [

        ]);
    }

    #[Route('/ressource/informatics', name: 'app_informatics')]
    public function informatics(): Response
    {
        return $this->render('user/soon/soon.html.twig', [

        ]);
    }
}
