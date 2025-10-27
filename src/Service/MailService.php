<?php

namespace App\Service;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\Entity;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\HtmlToTextConverter\DefaultHtmlToTextConverter;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Uid\Uuid;
use TijsVerkoyen\CssToInlineStyles\CssToInlineStyles;
use Twig\Environment;

class MailService
{
    private MailerInterface $mailer;
    private EntityManagerInterface $entityManager;
    private Environment $twig;
    private UrlGeneratorInterface $router;

    public function __construct(MailerInterface $mailer, EntityManagerInterface $entityManager, Environment $twig, UrlGeneratorInterface $router)
    {
        $this->mailer = $mailer;
        $this->entityManager = $entityManager;
        $this->twig = $twig;
        $this->router = $router;
    }

    public function sendNews(string $content): int
    {
        $counter = 0;
        foreach ($this->entityManager->getRepository(User::class)->findAll() as $user) {
            if ($user->isWantsNews()) {
                $counter++;

                $html = $this->twig->render('mails/news.html.twig', [
                    'content' => $content,
                    'firstname' => $user->getFirstname(),
                    'lastname' => $user->getLastname(),
                ]);

                $textConverter = new DefaultHtmlToTextConverter();
                $text = $textConverter->convert($html, 'utf-8');

                $email = (new Email())
                    ->from(new Address('news@prepalib.arkean.fr', 'News - PrepaLib'))
                    ->to(new Address($user->getEmail()))
                    ->subject('Nouvelles ressources disponibles')
                    ->text($text)
                    ->html($html);

                $this->mailer->send($email);
            }
        }

        return $counter;
    }

    public function sendReset(User $user): void
    {
        $resetToken = Uuid::v4()->toRfc4122();
        $user->setResetToken($resetToken);
        $user->setResetTokenRequestedAt(new \DateTimeImmutable());
        $this->entityManager->flush();

        $resetUrl = $this->router->generate('app_reset_password', ['token' => $resetToken], UrlGeneratorInterface::ABSOLUTE_URL);

        $html = $this->twig->render('mails/reset.html.twig', [
            'firstname' => $user->getFirstname(),
            'lastname' => $user->getLastname(),
            'reset_url' => $resetUrl,
            'expires' => 10
        ]);

        $textConverter = new DefaultHtmlToTextConverter();
        $text = $textConverter->convert($html, 'utf-8');

        $email = (new Email())
            ->from(new Address('noreply@prepalib.arkean.fr', 'Noreply - PrepaLib'))
            ->to(new Address($user->getEmail()))
            ->subject('Réinitialisation de votre mot de passe')
            ->text($text)
            ->html($html);

        $this->mailer->send($email);
    }
}
