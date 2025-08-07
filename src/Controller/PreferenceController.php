<?php

namespace App\Controller;

use App\Entity\Exercise;
use App\Entity\ExercisePref;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class PreferenceController extends AbstractController
{
    #[Route('/api/exercise/{exercise}/preference/set', name: 'api_set_preference', methods: ['PUT'])]
    #[IsGranted('IS_AUTHENTICATED_REMEMBERED')]
    public function setPreference(Exercise $exercise, Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $user = $this->getUser();

        $pref = $exercise->getPrefFor($user);
        if (!$pref) {
            $pref = new ExercisePref();
            $pref->setUser($user);
            $pref->setExercise($exercise);
            $pref->setComment('');
            $pref->setDone(false);
            $pref->setTodo(false);
            $pref->setFavorite(false);
            $pref->setDifficulty(-1);
            $entityManager->persist($pref);
        }

        $data = json_decode($request->getContent(), true);

        $updated = false;

        if (array_key_exists('favorite', $data)) {
            if (is_bool($data['favorite'])) {
                $pref->setFavorite($data['favorite']);
                $updated = true;
            } else {
                return new JsonResponse(['success' => false, 'error' => 'favorite doit être un booléen'], 400);
            }
        }

        if (array_key_exists('todo', $data)) {
            if (is_bool($data['todo'])) {
                $pref->setTodo($data['todo']);
                $updated = true;
            } else {
                return new JsonResponse(['success' => false, 'error' => 'todo doit être un booléen'], 400);
            }
        }

        if (array_key_exists('done', $data)) {
            if (is_bool($data['done'])) {
                $pref->setDone($data['done']);
                $updated = true;
            } else {
                return new JsonResponse(['success' => false, 'error' => 'done doit être un booléen'], 400);
            }
        }

        if (array_key_exists('comment', $data)) {
            if (is_string($data['comment'])) {
                $pref->setComment(trim($data['comment']));
                $updated = true;
            } else {
                return new JsonResponse(['success' => false, 'error' => 'comment doit être une chaîne de caractères'], 400);
            }
        }

        if (array_key_exists('difficulty', $data)) {
            if (is_int($data['difficulty'])) {
                $pref->setDifficulty($data['difficulty']);
                $updated = true;
            } else {
                return new JsonResponse(['success' => false, 'error' => 'difficulty doit être un entier'], 400);
            }
        }

        if (!$updated) {
            return new JsonResponse(['success' => false, 'error' => 'Aucune donnée valide à mettre à jour'], 400);
        }

        $entityManager->flush();
        return new JsonResponse(['success' => true, 'pref' => $pref->toArray()]);
    }
}
