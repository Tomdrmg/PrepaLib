<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\EditPasswordType;
use App\Form\EditProfileType;
use App\Form\Model\EditPassword;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\PasswordHasher\PasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

final class ProfileController extends AbstractController
{
    #[Route('/user/profile', name: 'app_profile')]
    public function index(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {
        $user = $this->getUser();
        $profileForm = $this->createForm(EditProfileType::class, $user);
        $profileForm->handleRequest($request);

        if ($profileForm->isSubmitted() && $profileForm->isValid()) {
            $entityManager->flush();
        }

        $editPasswordModel = new EditPassword();
        $passwordForm = $this->createForm(EditPasswordType::class, $editPasswordModel);
        $passwordForm->handleRequest($request);

        if ($passwordForm->isSubmitted() && $passwordForm->isValid()) {
            // Vérifie le mot de passe actuel (déjà fait par la contrainte UserPassword)
            // Hash le nouveau mot de passe et mets à jour l'utilisateur
            $user->setPassword(
                $passwordHasher->hash(
                    $user,
                    $editPasswordModel->newPassword
                )
            );

            $entityManager->flush();
        }

        return $this->render('user/profile/profile.html.twig', [
            'profileForm' => $profileForm->createView(),
            'passwordForm' => $passwordForm->createView(),
        ]);
    }
}
