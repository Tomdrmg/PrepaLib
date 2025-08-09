<?php

namespace App\Controller;

use App\Entity\Chapter;
use App\Entity\Element;
use App\Entity\ElementList;
use App\Entity\Exercise;
use App\Entity\ExerciseCategory;
use App\Entity\Hint;
use App\Entity\Subject;
use App\Entity\Tag;
use App\Entity\TagList;
use App\Entity\User;
use App\Form\ExerciseCategoryType;
use App\Form\ExerciseType;
use App\Form\Model\ExerciseModel;
use App\Form\Model\HintModel;
use App\Form\SubjectType;
use App\Form\TagType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AdminController extends AbstractController
{
    #[Route('/admin', name: 'app_admin_dashboard')]
    public function dashboard(EntityManagerInterface $entityManager): Response
    {
        $exerciseRepo = $entityManager->getRepository(Exercise::class);

        return $this->render('admin/dashboard/dashboard.html.twig', [
            "stats" => [
                "chapters" => $entityManager->getRepository(Chapter::class)->count(),
                "users" => $entityManager->getRepository(User::class)->count(),
                "exercises" => $exerciseRepo->count(),
                "completed" => -1,
                "corrected" => $exerciseRepo->countCorrected() * 100 / max(1, $exerciseRepo->count())
            ]
        ]);
    }

    #[Route('/admin/users', name: 'app_admin_users')]
    public function users(EntityManagerInterface $entityManager): Response
    {
        return $this->render('admin/data/users.html.twig', [
            'users' => $entityManager->getRepository(User::class)->findAll()
        ]);
    }

    #[Route('/admin/promote/{user}', name: 'app_admin_promote')]
    public function promote(User $user, EntityManagerInterface $entityManager): Response
    {
        if ($user->getId() === $this->getUser()->getId()) {
            $this->addFlash('info', 'Vous ne pouvez pas vous promouvoir vous même.');
        } else if (!in_array('ROLE_ADMIN', $user->getRoles())) {
            $roles = $user->getRoles();
            $roles[] = 'ROLE_ADMIN';
            $user->setRoles($roles);

            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Utilisateur promu administrateur.');
        } else {
            $this->addFlash('info', 'Cet utilisateur est déjà administrateur.');
        }

        return $this->redirectToRoute('app_admin_users');
    }

    #[Route('/admin/demote/{user}', name: 'app_admin_demote')]
    public function demote(User $user, EntityManagerInterface $entityManager): Response
    {

        $roles = $user->getRoles();
        if ($user->getId() === $this->getUser()->getId()) {
            $this->addFlash('info', 'Vous ne pouvez pas vous rétrograder vous même.');
        } else if (in_array('ROLE_ADMIN', $roles)) {
            $roles = array_filter($roles, fn ($role) => $role !== 'ROLE_ADMIN');
            $user->setRoles(array_values($roles));

            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('warning', 'Utilisateur rétrogradé avec succès.');
        } else {
            $this->addFlash('info', 'Cet utilisateur n’est pas administrateur.');
        }

        return $this->redirectToRoute('app_admin_users');
    }

    #[Route('/admin/subjects', name: 'app_admin_subjects')]
    public function subjects(Request $request, EntityManagerInterface $entityManager): Response
    {
        $subject = new Subject();
        $form = $this->createForm(SubjectType::class, $subject);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($subject);
            $entityManager->flush();
            $this->addFlash("success", "La matière a bien été créée.");
            return $this->redirectToRoute('app_admin_subjects');
        }

        return $this->render('admin/data/subjects.html.twig', [
            "subjects" => $entityManager->getRepository(Subject::class)->findAll(),
            "form" => $form->createView()
        ]);
    }

    #[Route('/admin/subject/{subject}', name: 'app_admin_subject')]
    public function subject(Subject $subject, Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(SubjectType::class, $subject);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash("success", "La matière a bien été modifiée.");
            return $this->redirectToRoute('app_admin_subject', ['subject' => $subject->getId()]);
        }

        return $this->render('admin/data/subject.html.twig', [
            "subject" => $subject,
            "form" => $form->createView()
        ]);
    }

    #[Route('/admin/exercises/{subject}', name: 'app_admin_exercises')]
    public function exercises(Subject $subject, Request $request, EntityManagerInterface $entityManager): Response
    {
        $category = new ExerciseCategory();
        $form = $this->createForm(ExerciseCategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->get('parent')->getData() !== null) {
                $category->setParent($entityManager->getRepository(ExerciseCategory::class)->find($form->get('parent')->getData()));
            }
            $editId = $form->get('id')->getData();

            if ($editId !== null) {
                $storedCategory = $entityManager->getRepository(ExerciseCategory::class)->find($editId);
                if ($storedCategory === null) {
                    $this->addFlash('warning', 'Cette catégorie n\'existe pas.');
                    return $this->redirectToRoute('app_admin_exercises', ['subject' => $subject->getId()]);
                }

                $storedCategory->setName($category->getName());
                $storedCategory->setColor($category->getColor());
                $storedCategory->setSortNumber($category->getSortNumber());
                $storedCategory->setParent($category->getParent());

                foreach ($storedCategory->getTags() as $tag) {
                    $storedCategory->removeTag($tag);
                }

                foreach ($category->getTags() as $tag) {
                    $storedCategory->addTag($tag);
                }

                $this->addFlash('success', 'La catégorie à bien été modifié');
            } else {
                $category->setSubject($subject);
                $entityManager->persist($category);
                $this->addFlash("success", "La catégorie a bien été créée.");
            }

            $entityManager->flush();
            return $this->redirectToRoute('app_admin_exercises', ['subject' => $subject->getId()]);
        }

        return $this->render('admin/data/exercises.html.twig', [
            "subject" => $subject,
            "form" => $form->createView()
        ]);
    }

    #[Route('/admin/exercise/add/{category}', name: 'app_admin_add_exercise')]
    public function addExercise(ExerciseCategory $category, Request $request, EntityManagerInterface $entityManager): Response
    {
        $exerciseModel = new ExerciseModel();
        $form = $this->createForm(ExerciseType::class, $exerciseModel);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $exercise = new Exercise();

            $exercise->setSortNumber($exerciseModel->sortNumber);

            $exercise->setCategory($category);

            $exercise->setTitle($exerciseModel->title);

            $solution = new Element();
            $solution->setContent($exerciseModel->solution ? $exerciseModel->solution : '');
            $entityManager->persist($solution);
            $exercise->setSolution($solution);

            $statement = new Element();
            $statement->setContent($exerciseModel->statement);
            $entityManager->persist($statement);
            $exercise->setStatement($statement);

            foreach ($exerciseModel->tags as $tag) {
                $exercise->addTag($tag);
            }

            foreach ($exerciseModel->hints as $hintModel) {
                $newHintElem = new Element();
                $newHintElem->setContent($hintModel->content);
                $entityManager->persist($newHintElem);

                $newHint = new Hint();
                $newHint->setExercise($exercise);
                $newHint->setElement($newHintElem);
                $newHint->setLore($hintModel->lore ?: '');
                $entityManager->persist($newHint);

                $exercise->addHint($newHint);
            }

            $entityManager->persist($exercise);
            $entityManager->flush();
            $this->addFlash("success", "L'exercice a bien été ajouté.");
            return $this->redirectToRoute('app_admin_exercises', ['subject' => $category->getSubject()->getId()]);
        }

        return $this->render('admin/data/exercise.html.twig', [
            "category" => $category,
            "form" => $form->createView()
        ]);
    }

    #[Route('/admin/exercise/edit/{exercise}', name: 'app_admin_edit_exercise')]
    public function editExercise(Exercise $exercise, Request $request, EntityManagerInterface $entityManager): Response
    {
        $exerciseModel = new ExerciseModel();
        $exerciseModel->sortNumber = $exercise->getSortNumber();
        $exerciseModel->title = $exercise->getTitle();
        $exerciseModel->statement = $exercise->getStatement()->getContent();
        if ($exercise->getSolution() !== null)
            $exerciseModel->solution = $exercise->getSolution()->getContent();

        foreach ($exercise->getTags() as $tag) {
            $exerciseModel->tags->add($tag);
        }

        foreach ($exercise->getHints() as $hint) {
            $hintModel = new HintModel();
            $hintModel->content = $hint->getElement()->getContent();
            $hintModel->lore = $hint->getLore();
            $exerciseModel->hints[] = $hintModel;
        }

        $form = $this->createForm(ExerciseType::class, $exerciseModel);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $exercise->setSortNumber($exerciseModel->sortNumber);
            $exercise->setTitle($exerciseModel->title);
            $exercise->getStatement()->setContent($exerciseModel->statement);
            $exercise->getSolution()->setContent($exerciseModel->solution ? $exerciseModel->solution : '');

            $exercise->getTags()->clear();
            foreach ($exerciseModel->tags as $tag) {
                $exercise->addTag($tag);
            }

            foreach ($exercise->getHints() as $oldHint) {
                $exercise->removeHint($oldHint);
                $entityManager->remove($oldHint);
            }

            foreach ($exerciseModel->hints as $hintModel) {
                $newHintElem = new Element();
                $newHintElem->setContent($hintModel->content);
                $entityManager->persist($newHintElem);

                $newHint = new Hint();
                $newHint->setExercise($exercise);
                $newHint->setElement($newHintElem);
                $newHint->setLore($hintModel->lore ?: '');
                $entityManager->persist($newHint);

                $exercise->addHint($newHint);
            }

            $entityManager->flush();
            $this->addFlash("success", "L'exercice a bien été modifié.");
            return $this->redirectToRoute('app_admin_exercises', ['subject' => $exercise->getCategory()->getSubject()->getId()]);
        }

        return $this->render('admin/data/exercise.html.twig', [
            "category" => $exercise->getCategory(),
            "form" => $form->createView()
        ]);
    }

    #[Route('/admin/category/delete/{category}', name: 'app_admin_delete_category')]
    public function deleteCategory(ExerciseCategory $category, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($category);
        $entityManager->flush();
        $this->addFlash("success", "La catégorie a bien été supprimée.");

        return $this->redirectToRoute('app_admin_exercises', ['subject' => $category->getSubject()->getId()]);
    }

    #[Route('/admin/exercise/delete/{exercise}', name: 'app_admin_delete_exercise')]
    public function deleteExercise(Exercise $exercise, EntityManagerInterface $entityManager): Response
    {
        $subjectId = $exercise->getCategory()->getSubject()->getId();

        $entityManager->remove($exercise->getStatement());
        $entityManager->remove($exercise->getSolution());
        foreach ($exercise->getHints() as $hint) {
            $entityManager->remove($hint);
        }
        foreach ($exercise->getExercisePrefs() as $pref) {
            $entityManager->remove($pref);
        }
        $entityManager->remove($exercise);
        $entityManager->flush();
        $this->addFlash("success", "L'exercice a bien été supprimée.");

        return $this->redirectToRoute('app_admin_exercises', ['subject' => $subjectId]);
    }

    #[Route('/admin/tags', name: 'app_admin_tags')]
    public function tags(Request $request, EntityManagerInterface $entityManager): Response
    {
        $tag = new Tag();
        $form = $this->createForm(TagType::class, $tag);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $editId = $form->get('id')->getData();

            if ($editId !== null) {
                $storedTag = $entityManager->getRepository(Tag::class)->find($editId);
                if ($storedTag === null) {
                    $this->addFlash('warning', 'Ce tag n\'existe pas.');
                    return $this->redirectToRoute('app_admin_tags');
                }

                $storedTag->setName($tag->getName());
                $storedTag->setColor($tag->getColor());
                $this->addFlash('success', 'Le tag à bien été modifié');
            } else {
                $entityManager->persist($tag);
                $this->addFlash('success', 'Le tag à bien été crée');
            }

            $entityManager->flush();
            return $this->redirectToRoute('app_admin_tags');
        }

        return $this->render('admin/data/tags.html.twig', [
            'tags' => $entityManager->getRepository(Tag::class)->findAll(),
            'form' => $form->createView()
        ]);
    }

    #[Route('/admin/tag/delete/{tag}', name: 'app_admin_delete_tag')]
    public function deleteTag(Tag $tag, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($tag);
        $entityManager->flush();
        $this->addFlash("success", "Le tag a bien été supprimer.");

        return $this->redirectToRoute('app_admin_tags');
    }
}
