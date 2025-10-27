<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\CommentType;
use App\Form\Model\TextModel;
use App\Form\NewsType;
use App\Service\MailService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Uid\Uuid;

class MailController extends AbstractController
{
    #[Route('/admin/mail/news', name: 'app_mail_news')]
    public function index(Request $request, MailService $mailService): Response
    {
        $content = new TextModel();
        $form = $this->createForm(NewsType::class, $content);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $received = $mailService->sendNews($content->text);

            $this->addFlash('success', "La notification a bien été envoyée à {$received} utilisateur(s).");
            return $this->redirectToRoute('app_mail_news');
        }

        return $this->render('admin/mail/news.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
