<?php

namespace App\Controller\Api;

use App\Entity\Task;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/tasks')]
class ApiTaskController extends AbstractController
{
    #[Route('/', name: 'api_tasks_list', methods: ['GET'])]
    public function list(EntityManagerInterface $em): JsonResponse
    {
        $tasks = $em->getRepository(Task::class)->findAll();

        $data = [];

        foreach ($tasks as $task) {
            $data[] = [
                'id'        => $task->getId(),
                'title'     => $task->getTitle(),
                'description' => $task->getDescription(),
                'deadline'  => $task->getDeadline()?->format('Y-m-d'),
                'isDone'    => $task->isDone(),
            ];
        }

        return new JsonResponse($data);
    }
}
