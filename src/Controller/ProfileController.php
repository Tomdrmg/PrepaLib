<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\EditPasswordType;
use App\Form\EditProfileType;
use App\Form\Model\EditPassword;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\PasswordHasher\PasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

final class ProfileController extends AbstractController
{
    #[Route('/profile', name: 'app_profile')]
    public function profile(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $profileForm = $this->createForm(EditProfileType::class, $user);
        $profileForm->handleRequest($request);

        if ($profileForm->isSubmitted() && $profileForm->isValid()) {
            $entityManager->flush();
            $this->addFlash("success", "Vos informations ont bien été mises à jour.");
            return $this->redirectToRoute("app_profile");
        }

        return $this->render('user/profile/profile.html.twig', [
            'profileForm' => $profileForm->createView(),
        ]);
    }

    #[Route('/profile/security', name: 'app_security')]
    public function security(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {
        $user = $this->getUser();
        $editPasswordModel = new EditPassword();
        $passwordForm = $this->createForm(EditPasswordType::class, $editPasswordModel);
        $passwordForm->handleRequest($request);

        if ($passwordForm->isSubmitted() && $passwordForm->isValid()) {
            if (!$passwordHasher->isPasswordValid($user, $editPasswordModel->oldPassword)) {
                $passwordForm->get('oldPassword')->addError(new FormError('Mot de passe actuel incorrect.'));
            } else if (strlen(trim($editPasswordModel->newPassword, " ")) == 0) {
                $passwordForm->get('newPassword')->addError(new FormError('Votre mot de passe ne peux être vide.'));
            } else {
                $user->setPassword(
                    $passwordHasher->hashPassword(
                        $user,
                        $editPasswordModel->newPassword
                    )
                );

                $entityManager->flush();
                $this->addFlash("success", "Votre mot de passe a bien été mis à jour.");
                return $this->redirectToRoute("app_password");
            }
        }

        return $this->render('user/profile/password.html.twig', [
            'passwordForm' => $passwordForm->createView(),
        ]);
    }

    #[Route('/profile/delete', name: 'app_delete_account')]
    public function delete(): Response
    {
        return $this->render('user/soon/soon.html.twig', [

        ]);
    }
}
