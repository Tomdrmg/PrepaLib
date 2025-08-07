<?php

namespace App\Controller;

use App\Entity\Exercise;
use App\Entity\ExercisePref;
use App\Entity\Subject;
use App\Entity\User;
use App\Form\OnlyTagsType;
use App\Repository\ExerciseRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class ResourceController extends AbstractController
{
    #[Route('/ressources/{subject}', name: 'app_subject')]
    public function subjectHome(Subject $subject): Response
    {
        return $this->redirectToRoute('app_subject_exercises', ['subject' => $subject->getId()]);
    }

    #[Route('/ressources/{subject}/exercises', name: 'app_subject_exercises')]
    public function subjectExercices(Subject $subject): Response
    {
        return $this->render('user/ressource/exercises.html.twig', [
            "subject" => $subject,
            "form" => $this->createForm(OnlyTagsType::class)->createView(),
        ]);
    }

    #[Route('/ressources/exercice/{exercise}', name: 'app_exercise')]
    public function exercise(Exercise $exercise): Response
    {
        return $this->render('user/ressource/exercise.html.twig', [
            "exercise" => $exercise
        ]);
    }

    #[Route('/api/ressources/exercises', name: 'api_list_exercises', methods: ['GET'])]
    #[IsGranted('IS_AUTHENTICATED_REMEMBERED')]
    public function listExercises(Request $request, ExerciseRepository $exerciseRepo): JsonResponse
    {
        /**
         * @var User $user
         */
        $user = $this->getUser();

        $done = $request->query->get('done', null);
        if ($done !== null) $done = filter_var($done, FILTER_VALIDATE_BOOLEAN);

        $favorite = $request->query->get('favorite', null);
        if ($favorite !== null) $favorite = filter_var($favorite, FILTER_VALIDATE_BOOLEAN);

        $difficulties = $request->query->get('difficulties', null);
        if (is_string($difficulties)) {
            $difficulties = explode(',', $difficulties);
        } else if (!is_array($difficulties)) {
            $difficulties = [];
        }

        $tagIds = $request->query->get('tags', null);
        if (is_string($tagIds)) {
            $tagIds = explode(',', $tagIds);
        } else if (!is_array($tagIds)) {
            $tagIds = [];
        }

        $search = $request->query->get('search', null);

        $tagsMode = $request->query->get('tagsMode', 'any');
        if ($tagsMode !== 'any' && $tagsMode != 'all') $tagsMode = 'any';

        $page = max(1, $request->query->getInt('page', 1));
        $limit = min(50, $request->query->getInt('limit', 10));

        $result = $exerciseRepo->findFilteredExercisesWithCount($user, $difficulties, $done, $favorite, $tagIds, $search, $tagsMode, $page, $limit);

        $totalResults = $result['totalResults'];
        $exercises = $result['exercises'];
        $totalPages = $result['totalPages'];
        $page = $result['page'];

        $data = [];
        /**
         * @var Exercise $exercise
         */
        foreach ($exercises as $exercise) {
            $pref = $exercise->getPrefFor($user);
            if (!$pref) {
                $pref = new ExercisePref();
                $pref->setDifficulty(-1);
                $pref->setExercise($exercise);
                $pref->setDone(false);
                $pref->setFavorite(false);
                $pref->setComment("");
            }

            $data[] = [
                'id' => $exercise->getId(),
                'title' => $exercise->getTitle(),
                'statement' => $exercise->getStatement()->getContent(),
                'category' => [
                    'name' => $exercise->getCategory()->getName(),
                    'color' => $exercise->getCategory()->getColor(),
                ],
                'firstCategory' => [
                    'name' => $exercise->getFirstCategory()->getName(),
                    'color' => $exercise->getFirstCategory()->getColor(),
                ],
                'tags' => array_map(fn($tag) => [
                    'name' => $tag->getName(),
                    'color' => $tag->getColor()
                ], $exercise->getFullTags()),
                'pref' => $pref->toArray(),
                'path' => $exercise->getCategoryPath()
            ];
        }

        return new JsonResponse([
            'page' => $page,
            'limit' => $limit,
            'totalResults' => $totalResults,
            'totalPages' => $totalPages,
            'results' => $data,
        ]);
    }
}
