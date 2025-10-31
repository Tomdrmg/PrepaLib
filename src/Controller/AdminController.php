<?php

namespace App\Controller;

use App\Entity\Element;
use App\Entity\Exercise;
use App\Entity\ExerciseCategory;
use App\Entity\RevisionElement;
use App\Entity\RevisionQuestion;
use App\Entity\RevisionSheet;
use App\Entity\Subject;
use App\Entity\Tag;
use App\Entity\User;
use App\Form\ExerciseCategoryType;
use App\Form\ExerciseType;
use App\Form\FullRevisionSheetType;
use App\Form\RevisionElementType;
use App\Form\RevisionSheetType;
use App\Form\SubjectType;
use App\Form\TagType;
use App\Repository\ExerciseRepository;
use App\Repository\RevisionSheetRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use function Symfony\Component\Translation\t;

final class AdminController extends AbstractController
{
    #[Route('/admin', name: 'app_admin_dashboard')]
    public function dashboard(EntityManagerInterface $entityManager): Response
    {
        /**
         * @var ExerciseRepository $exerciseRepo
         */
        $exerciseRepo = $entityManager->getRepository(Exercise::class);
        /**
         * @var RevisionSheetRepository $sheetsRepo
         */
        $sheetsRepo = $entityManager->getRepository(RevisionSheet::class);

        return $this->render('admin/dashboard/dashboard.html.twig', [
            "stats" => [
                "questions" => $entityManager->getRepository(RevisionQuestion::class)->count(),
                "users" => $entityManager->getRepository(User::class)->count(),
                "exercises" => $exerciseRepo->count(),
                "cards" => $sheetsRepo->countWithoutParent(),
                "corrected" => round($exerciseRepo->countCorrected() * 100 / max(1, $exerciseRepo->count()), 2)
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

    #[Route('/admin/{subject}/exercises', name: 'app_admin_exercises')]
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
        $exercise = new Exercise();
        $form = $this->createForm(ExerciseType::class, $exercise);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $exercise->setCategory($category);

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
        $form = $this->createForm(ExerciseType::class, $exercise);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
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

    #[Route('/admin/{subject}/sheets', name: 'app_admin_sheets')]
    public function sheets(Subject $subject): Response
    {
        return $this->render('admin/data/sheets.html.twig', [
            'subject' => $subject
        ]);
    }

    #[Route('/admin/{subject}/sheets/add', name: 'app_admin_add_sheet')]
    public function addSheet(Subject $subject, Request $request, EntityManagerInterface $entityManager): Response
    {
        $sheet = new RevisionSheet();
        $form = $this->createForm(RevisionSheetType::class, $sheet);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $sheet->setSubject($subject);

            $entityManager->persist($sheet);
            $entityManager->flush();

            $this->addFlash("success", "La fiche a bien été créée.");
            return $this->redirectToRoute('app_admin_sheets', ['subject' => $subject->getId()]);
        }

        return $this->render('admin/data/add_sheet.html.twig', [
            'subject' => $subject,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/admin/sheet/{sheet}', name: 'app_admin_sheet')]
    public function sheet(RevisionSheet $sheet, Request $request, EntityManagerInterface $entityManager): Response
    {
        if ($sheet->getParent() !== null) {
            return $this->redirectToRoute('app_admin_sheet', ['sheet' => $sheet->getParent()->getId()]);
        }

        $subject = $sheet->getSubject();

        $editForm = $this->createForm(RevisionSheetType::class, $sheet);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $entityManager->flush();

            $this->addFlash("success", "La fiche a bien été modifiée.");
            return $this->redirectToRoute('app_admin_sheet', ['sheet' => $sheet->getId()]);
        }

        $formSheet = new RevisionSheet();
        $fullForm = $this->createForm(FullRevisionSheetType::class, $formSheet);
        $fullForm->handleRequest($request);

        if ($fullForm->isSubmitted() && $fullForm->isValid() && $fullForm->get('parent')->getData() !== null) {
            $formSheet->setParent($entityManager->getRepository(RevisionSheet::class)->find($fullForm->get('parent')->getData()));
            $editId = $fullForm->get('id')->getData();

            if ($editId !== null) {
                $storedSheet = $entityManager->getRepository(RevisionSheet::class)->find($editId);
                if ($storedSheet === null) {
                    $this->addFlash('warning', 'Cette fiche n\'existe pas.');
                    return $this->redirectToRoute('app_admin_sheet', ['sheet' => $sheet->getId()]);
                }

                $storedSheet->setTitle($formSheet->getTitle());
                $storedSheet->setParent($formSheet->getParent());
                $storedSheet->setSortNumber($formSheet->getSortNumber());

                $this->addFlash('success', 'La fiche à bien été modifié');
            } else {
                $formSheet->setSubject($subject);
                $entityManager->persist($formSheet);
                $this->addFlash("success", "La fiche a bien été créée.");
            }

            $entityManager->flush();
            return $this->redirectToRoute('app_admin_sheet', ['sheet' => $sheet->getId()]);
        }

        return $this->render('admin/data/sheet.html.twig', [
            'sheet' => $sheet,
            'subject' => $subject,
            'editForm' => $editForm->createView(),
            'fullForm' => $fullForm->createView(),
        ]);
    }

    #[Route('/admin/sheet/delete/{sheet}', name: 'app_admin_delete_sheet')]
    public function deleteSheet(RevisionSheet $sheet, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($sheet);
        $entityManager->flush();
        $this->addFlash("success", "La fiche a bien été supprimée.");

        if ($sheet->getParent() === null) {
            return $this->redirectToRoute('app_admin_sheets', ['subject' => $sheet->getSubject()->getId()]);
        } else {
            return $this->redirectToRoute('app_admin_sheet', ['sheet' => $sheet->getHighestParent()->getId()]);
        }
    }

    #[Route('/admin/sheet/{sheet}/new/element', name:'app_admin_add_sheet_element')]
    public function addSheetElement(RevisionSheet $sheet, Request $request, EntityManagerInterface $entityManager): Response
    {
        $subject = $sheet->getSubject();

        $element = new RevisionElement();
        $form = $this->createForm(RevisionElementType::class, $element);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $element->setRevisionSheet($sheet);

            $entityManager->persist($element);
            $entityManager->flush();

            $this->addFlash("success", "Cet élément a bien été crée.");
            return $this->redirectToRoute('app_admin_sheet', ['sheet' => $sheet->getId()]);
        }

        return $this->render('admin/data/revision_element.html.twig', [
            'sheet' => $sheet,
            'subject' => $subject,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/admin/sheet/edit/element/{element}', name:'app_admin_edit_sheet_element')]
    public function editSheetElement(RevisionElement $element, Request $request, EntityManagerInterface $entityManager): Response
    {
        $sheet = $element->getRevisionSheet();
        $subject = $sheet->getSubject();

        $form = $this->createForm(RevisionElementType::class, $element);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($element);
            $entityManager->flush();

            $this->addFlash("success", "Cet élément a bien été modifié.");
            return $this->redirectToRoute('app_admin_sheet', ['sheet' => $sheet->getId()]);
        }

        return $this->render('admin/data/revision_element.html.twig', [
            'sheet' => $sheet,
            'subject' => $subject,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/admin/sheet/delete/element/{element}', name: 'app_admin_delete_sheet_element')]
    public function deleteSheetElement(RevisionElement $element, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($element);
        $entityManager->flush();
        $this->addFlash("success", "L'élément a bien été supprimée.");

        return $this->redirectToRoute('app_admin_sheet', ['sheet' => $element->getRevisionSheet()->getId()]);
    }
}
