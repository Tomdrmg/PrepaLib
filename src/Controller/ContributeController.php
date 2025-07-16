<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ContributeController extends AbstractController
{
    #[Route('/contribute/suggestion', name: 'app_contribute_suggestion')]
    public function suggestion(): Response
    {
        return $this->render('user/soon/soon.html.twig', [

        ]);
    }

    #[Route('/contribute/exercise', name: 'app_contribute_exercise')]
    public function exercise(): Response
    {
        return $this->render('user/soon/soon.html.twig', [

        ]);
    }

    #[Route('/contribute/chapter', name: 'app_contribute_chapter')]
    public function chapter(): Response
    {
        return $this->render('user/soon/soon.html.twig', [

        ]);
    }
}
