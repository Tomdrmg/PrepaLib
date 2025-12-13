<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ForgotPasswordType;
use App\Form\Model\ForgotPassword;
use App\Form\Model\ResetPassword;
use App\Form\RegistrationFormType;
use App\Form\ResetPasswordType;
use App\Service\MailService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Uid\Uuid;

class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_home');
        }

        $error = $authenticationUtils->getLastAuthenticationError();

        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('user/security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager, Security $security): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_home');
        }

        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($entityManager->getRepository(User::class)->findOneBy(["email" => $form->get('email')->getData()])) {
                $this->addFlash("error", "Cette adresse email est déjà utilisé.");
                return $this->redirectToRoute("app_register");
            }

            $user->setPassword(
                $passwordHasher->hashPassword(
                    $user,
                    $form->get('password')->getData()
                )
            );
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash("success", "Votre compte a bien été crée, vous avez automatiquement été connecté.");
            return $security->login($user);
        }

        return $this->render('user/security/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    /**
     * @throws TransportExceptionInterface
     */
    #[Route(path: '/password/forgot', name: 'app_forgot_password')]
    public function forgotPassword(Request $request, EntityManagerInterface $entityManager, AuthenticationUtils $authenticationUtils, MailService $mailService): Response
    {
        $user = $this->getUser();

        $model = new ForgotPassword();
        $model->email = $authenticationUtils->getLastUsername();
        if ($user) $model->email = $user->getEmail();

        $form = $this->createForm(ForgotPasswordType::class, $model);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $entityManager->getRepository(User::class)->findOneBy(['email' => $form->get('email')->getData()]);
            if ($user) {
                $mailService->sendReset($user);
            }

            $this->addFlash("info", "Si cette adresse email est bien associée à un compte, vous allez recevoir un mail pour réinitialiser votre mot de passe.");
        }

        return $this->render('user/security/forgot.html.twig', [
            "forgotPasswordForm" => $form->createView(),
        ]);
    }

    #[Route(path: '/password/reset/{token}', name: 'app_reset_password')]
    public function resetPassword(string $token, Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {
        $user = $entityManager->getRepository(User::class)->findOneBy(['resetToken' => $token]);
        if (!$user || !$user->getResetTokenRequestedAt() || time() - $user->getResetTokenRequestedAt()->getTimestamp() > 600) {
            $this->addFlash("error", "Cette réinitialisation n'existe pas ou a déjà expirée");
            return $this->redirectToRoute('app_forgot_password');
        }

        $model = new ResetPassword();
        $form = $this->createForm(ResetPasswordType::class, $model);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
           $user->setResetToken(null);
           $user->setResetTokenRequestedAt(null);
           $user->setPassword($passwordHasher->hashPassword($user, $model->password));
           $entityManager->flush();

            $this->addFlash("success", "Votre mot de passe a bien été modifié.");
            return $this->redirectToRoute('app_login');
        }

        return $this->render('user/security/reset.html.twig', [
            "resetPasswordForm" => $form->createView(),
        ]);
    }
}
