<?php

namespace App\Controller;

use App\Entity\Exercise;
use App\Entity\ExerciseCategory;
use App\Entity\ExercisePref;
use App\Entity\RevisionSheet;
use App\Entity\Subject;
use App\Entity\User;
use App\Form\CommentType;
use App\Form\Model\TextModel;
use App\Form\OnlyTagsType;
use App\Repository\ExerciseRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
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
        return $this->render('user/ressource/subject.html.twig', [
            "subject" => $subject,
        ]);
    }

    #[Route('/ressources/{subject}/exercises', name: 'app_exercises')]
    public function subjectExercices(Subject $subject): Response
    {
        if ($subject->countExercises() == 0)
            return $this->redirectToRoute('app_subject', ['subject' => $subject->getId()]);

        return $this->render('user/ressource/exercises.html.twig', [
            "subject" => $subject,
            "form" => $this->createForm(OnlyTagsType::class)->createView(),
        ]);
    }

    #[Route('/ressources/exercice/{exercise}', name: 'app_exercise')]
    public function exercise(Exercise $exercise, Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();

        $pref = $exercise->getPrefFor($user);

        if (!$pref) {
            $pref = new ExercisePref();
            $pref->setExercise($exercise);
            $pref->setUser($user);
            $pref->setDone(false);
            $pref->setFavorite(false);
            $pref->setDifficulty(-1);
            $pref->setComment("");

            $entityManager->persist($pref);
        }

        $comment = new TextModel();
        $comment->text = $pref->getComment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $pref->setComment($comment->text?: '');
            $entityManager->flush();
            $this->addFlash('success', "Votre commentaire personnel a bien été modifié.");
            return $this->redirectToRoute('app_exercise', ['exercise' => $exercise->getId()]);
        }

        return $this->render('user/ressource/exercise.html.twig', [
            "exercise" => $exercise,
            "form" => $form->createView(),
        ]);
    }

    #[Route('/api/ressources/cat/{category}/exercises', name: 'api_category_exercises', methods: ['GET'])]
    #[IsGranted('IS_AUTHENTICATED_REMEMBERED')]
    public function categoryExercises(ExerciseCategory $category): JsonResponse
    {
        /**
         * @var User $user
         */
        $user = $this->getUser();

        /**
         * @var Exercise[] $exercises
         */
        $exercises = $category->getExercises()->toArray();
        usort($exercises, function ($a, $b) {
            return $a->getSortNumber() <=> $b->getSortNumber();
        });

        $data = [];
        foreach ($exercises as $exercise) {
            $data[] = $this->exerciseToArray($user, $exercise);
        }

        return new JsonResponse([
            'results' => $data,
        ]);
    }

    private function exerciseToArray(User $user, Exercise $exercise): array
    {
        $pref = $exercise->getPrefFor($user);
        if (!$pref) {
            $pref = new ExercisePref();
            $pref->setDifficulty(-1);
            $pref->setExercise($exercise);
            $pref->setDone(false);
            $pref->setFavorite(false);
            $pref->setComment("");
        }

        return [
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
            'pref' => $pref->toArray()
        ];
    }

    #[Route('/api/ressources/sub/{subject}/exercises', name: 'api_list_exercises', methods: ['GET'])]
    #[IsGranted('IS_AUTHENTICATED_REMEMBERED')]
    public function listExercises(Subject $subject, Request $request, ExerciseRepository $exerciseRepo): JsonResponse
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

        $result = $exerciseRepo->findFilteredExercisesWithCount($subject, $user, $difficulties, $done, $favorite, $tagIds, $search, $tagsMode, $page, $limit);

        $totalResults = $result['totalResults'];
        $exercises = $result['exercises'];
        $totalPages = $result['totalPages'];
        $page = $result['page'];

        $data = [];
        /**
         * @var Exercise $exercise
         */
        foreach ($exercises as $exercise) {
            $data[] = $this->exerciseToArray($user, $exercise);
        }

        return new JsonResponse([
            'page' => $page,
            'limit' => $limit,
            'totalResults' => $totalResults,
            'totalPages' => $totalPages,
            'results' => $data,
        ]);
    }

    #[Route('/ressources/{subject}/sheets', name: 'app_sheets')]
    public function subjectSheets(Subject $subject): Response
    {
        if ($subject->countRevisionSheets() == 0)
            return $this->redirectToRoute('app_subject', ['subject' => $subject->getId()]);

        return $this->render('user/ressource/sheets.html.twig', [
            "subject" => $subject,
            "sheets" => $subject->getRevisionSheets(),
        ]);
    }

    #[Route('/ressources/sheet/{sheet}', name: 'app_sheet')]
    public function sheet(RevisionSheet $sheet): Response
    {
        return $this->render('user/ressource/sheet.html.twig', [
            "subject" => $sheet->getSubject(),
            "sheet" => $sheet,
        ]);
    }
}
