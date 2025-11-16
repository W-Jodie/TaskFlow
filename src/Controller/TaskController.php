<?php

namespace App\Controller;

use App\Entity\Task;
use App\Form\TaskType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/tasks')]
class TaskController extends AbstractController
{
    #[Route('/', name: 'task_index')]
    public function index(EntityManagerInterface $em): Response
    {
        // Tri par la bonne propriété : deadline
        $tasks = $em->getRepository(Task::class)->findBy([], ['deadline' => 'ASC']);

        return $this->render('task/index.html.twig', [
            'tasks' => $tasks
        ]);
    }

    #[Route('/create', name: 'task_create')]
    public function create(Request $request, EntityManagerInterface $em): Response
    {
        $task = new Task();
        $task->setIsDone(false); // valeur par défaut

        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($task);
            $em->flush();

            $this->addFlash('success', 'Tâche ajoutée avec succès !');
            return $this->redirectToRoute('task_index');
        }

        return $this->render('task/form.html.twig', [
            'form' => $form,
            'title' => 'Créer une tâche'
        ]);
    }

    #[Route('/edit/{id}', name: 'task_edit')]
    public function edit(Task $task, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            $this->addFlash('success', 'Tâche modifiée avec succès !');
            return $this->redirectToRoute('task_index');
        }

        return $this->render('task/form.html.twig', [
            'form' => $form,
            'title' => 'Modifier une tâche'
        ]);
    }

    #[Route('/delete/{id}', name: 'task_delete')]
    public function delete(Task $task, EntityManagerInterface $em): Response
    {
        $em->remove($task);
        $em->flush();

        $this->addFlash('success', 'Tâche supprimée !');
        return $this->redirectToRoute('task_index');
    }

    #[Route('/toggle/{id}', name: 'task_toggle')]
    public function toggle(Task $task, EntityManagerInterface $em): Response
    {
        $task->setIsDone(!$task->isDone());
        $em->flush();

        return $this->redirectToRoute('task_index');
    }
}
