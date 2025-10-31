<?php

namespace App\Controller;

use App\Entity\QuizData;
use App\Entity\RevisionElement;
use App\Entity\RevisionPref;
use App\Entity\RevisionSheet;
use App\Entity\Subject;
use App\Entity\User;
use App\Form\StartQuizType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use function Symfony\Component\String\u;

final class QuizController extends AbstractController
{
    #[Route('/quiz/start/{subject}/{ids}', name: 'app_start_quiz', requirements: ['ids' => '\d+(,\d+)*'])]
    public function startQuiz(Subject $subject, string $ids, Request $request, EntityManagerInterface $entityManager): Response
    {
            /** @var User $user */
            $user = $this->getUser();

            // Convertir la chaîne d'IDs en tableau
            $idArray = explode(',', $ids);

            // Nettoyer les IDs (supprimer les espaces, etc.)
            $idArray = array_map('intval', $idArray);
            $idArray = array_filter($idArray); // Supprimer les valeurs vides

            if (empty($idArray)) {
                return $this->json(['error' => 'Aucun ID valide fourni'], 400);
            }

            // Récupérer les entités

            /** @var array<int, RevisionSheet> $entities */
            $entities = $entityManager->getRepository(RevisionSheet::class)->findBy(['id' => $idArray, 'parent' => null]);

            if (empty($entities)) {
                return $this->json(['error' => 'Aucune entité trouvée'], 404);
            }

            $quizData = new QuizData();
            $form = $this->createForm(StartQuizType::class, $quizData);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $quizData->setSubject($subject);

                foreach ($entities as $entity) {
                    $quizData->addSheet($entity);
                }

                if ($quizData->getNeverSeenWeight() < 0) $quizData->setNeverSeenWeight(0);
                if ($quizData->getUnknownWeight() < 0) $quizData->setUnknownWeight(0);
                if ($quizData->getFamiliarWeight() < 0) $quizData->setFamiliarWeight(0);
                if ($quizData->getKnownWeight() < 0) $quizData->setKnownWeight(0);
                if ($quizData->getMasteredWeight() < 0) $quizData->setMasteredWeight(0);

                if ($user->getQuizData()) $entityManager->remove($user->getQuizData());
                $entityManager->flush();

                $quizData->setUser($user);
                $user->setQuizData($quizData);
                $entityManager->persist($quizData);
                $entityManager->flush();

                return $this->redirectToRoute('app_play_quiz');
            }

            $totalQuestions = 0;
            foreach ($entities as $entity) {
                $totalQuestions += $entity->countQuestionsRec();
            }

            return $this->render('user/quiz/start.html.twig', [
                "subject" => $subject,
                "sheets" => $entities,
                "form" => $form->createView(),
                "totalQuestions" => $totalQuestions
            ]);
    }

    #[Route('/quiz/play/', name: 'app_play_quiz')]
    public function playQuiz(Request $request, EntityManagerInterface $entityManager): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $quizData = $user->getQuizData();
        if (!$quizData) {
            return $this->redirectToRoute('app_home');
        }

        // Récupérer les questions disponibles basées sur les préférences
        $availableQuestions = $this->getAvailableQuestions($quizData, $entityManager);

        // Mélanger les questions selon les poids et s'assurer d'avoir le nombre demandé
        $shuffledQuestions = $this->shuffleQuestionsByWeight($availableQuestions, $quizData, $user, $entityManager);

        // S'assurer d'avoir exactement le nombre de questions demandé, même si ça implique des répétitions
        $questionCount = $quizData->getQuestionCount();
        $finalQuestions = [];

        if (count($shuffledQuestions) > 0) {
            for ($i = 0; $i < $questionCount; $i++) {
                $finalQuestions[] = $shuffledQuestions[$i % count($shuffledQuestions)];
            }
        }

        // Stocker les questions dans la session
        $session = $request->getSession();
        $session->set('quiz_questions', $finalQuestions);
        $session->set('current_question_index', 0);
        $session->set('quiz_stats', [
            'success' => 0,
            'failures' => 0,
            'total' => $questionCount
        ]);

        return $this->render('user/quiz/play.html.twig', [
            'quizData' => $quizData,
            'currentQuestion' => $finalQuestions[0] ?? null,
            'questionNumber' => 1,
            'totalQuestions' => $questionCount,
            'subject' => $quizData->getSubject(),
        ]);
    }

    #[Route('/quiz/next', name: 'app_quiz_next', methods: ['POST'])]
    public function nextQuestion(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();
        $session = $request->getSession();

        $questions = $session->get('quiz_questions', []);
        $currentIndex = $session->get('current_question_index', 0);
        $stats = $session->get('quiz_stats', []);

        // Mettre à jour les stats si une réponse a été donnée
        $data = json_decode($request->getContent(), true);
        if (isset($data['is_correct'])) {
            if ($data['is_correct']) {
                $stats['success']++;
            } else {
                $stats['failures']++;
            }
            $session->set('quiz_stats', $stats);
        }

        // Passer à la question suivante
        $currentIndex++;
        $session->set('current_question_index', $currentIndex);

        if ($currentIndex >= count($questions) || $currentIndex >= $stats['total']) {
            // Quiz terminé
            return $this->json([
                'finished' => true,
                'stats' => $stats
            ]);
        }

        $nextQuestion = $questions[$currentIndex];

        $element = $nextQuestion['element'];
        $revisionPref = $entityManager->getRepository(RevisionPref::class)->findOneBy([
            'user' => $this->getUser(),
            'revisionElement' => $element
        ]);
        $nextQuestion['difficulty'] = $revisionPref ? $revisionPref->getDifficulty() : -1;
        $questions[$currentIndex] = $nextQuestion;
        $session->set('quiz_questions', $questions);


        return $this->json([
            'finished' => false,
            'stats' => $stats,
            'question' => [
                'id' => $nextQuestion['id'],
                'question' => $nextQuestion['question_content'],
                'answer' => $nextQuestion['answer_content'],
                'questionNumber' => $currentIndex + 1,
                'totalQuestions' => $stats['total'],
                'difficulty' => $nextQuestion['difficulty'],
                'revision_element_id' => $nextQuestion['revision_element_id']
            ]
        ]);
    }

    private function getAvailableQuestions(QuizData $quizData, EntityManagerInterface $entityManager): array
    {
        $questions = [];

        foreach ($quizData->getSheets() as $sheet) {
            $elements = $sheet->getRevisionElementsRec();
            foreach ($elements as $element) {
                $revisionPref = $entityManager->getRepository(RevisionPref::class)->findOneBy([
                    'user' => $this->getUser(),
                    'revisionElement' => $element
                ]);

                foreach ($element->getQuestions() as $question) {
                    $questions[] = [
                        'id' => $question->getId(),
                        'revision_element_id' => $element->getId(),
                        'question_content' => $question->getQuestion()->getContent(), // Adaptez selon votre structure Element
                        'answer_content' => $question->getAnswer()->getContent(), // Adaptez selon votre structure Element
                        'element' => $element,
                        'difficulty' => $revisionPref ? $revisionPref->getDifficulty() : -1,
                    ];
                }
            }
        }

        return $questions;
    }

    private function shuffleQuestionsByWeight(array $questions, QuizData $quizData, User $user, EntityManagerInterface $entityManager): array
    {
        $weightedQuestions = [];

        foreach ($questions as $question) {
            $revisionElement = $question['element'];
            $revisionPref = $entityManager->getRepository(RevisionPref::class)->findOneBy([
                'user' => $user,
                'revisionElement' => $revisionElement
            ]);

            $difficulty = $revisionPref ? $revisionPref->getDifficulty() : 4; // 4 = never seen

            switch ($difficulty) {
                case 0: $weight = $quizData->getMasteredWeight(); break;
                case 1: $weight = $quizData->getKnownWeight(); break;
                case 2: $weight = $quizData->getFamiliarWeight(); break;
                case 3: $weight = $quizData->getUnknownWeight(); break;
                case -1: $weight = $quizData->getNeverSeenWeight(); break;
                default: $weight = 1;
            }

            // Ajouter la question autant de fois que son poids
            for ($i = 0; $i < max(0, $weight); $i++) {
                $weightedQuestions[] = $question;
            }
        }

        shuffle($weightedQuestions);
        return $weightedQuestions;
    }
}
