<?php

namespace App\Controller;

use App\Entity\Tasks;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use App\Repository\TasksRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class TasksController extends AbstractController
{
    #[Route('/', name: 'tasks', methods:['GET'])]
    public function tasksList(TasksRepository $repository): Response
    {
        $tasks = $repository->findAll();
        return $this->render('tasks/home.html.twig', [
            'tasks' => $tasks,
        ]);
    }

    #[Route('/create_task', name: "create_task", methods:['POST'])]
    public function createTask(Request $request, EntityManagerInterface $entity)
    {
        //Get the data from the form
        $data = $request->request->all();

        //Check if the form is not empty
        if (empty($data['title']) || strlen($data['title']) < 3) 
        {
            $this->addFlash('error', 'The title must be at least 3 characters long!');
            return $this->redirectToRoute('create_task_display');
        }
    
        if (empty($data['description']) || empty($data['status']))
        {
            $this->addFlash('error', 'All fields are required!');
            return $this->redirectToRoute('create_task_display');
        }

        //Create a new task
        $task = new Tasks();
        $task->setTitle($data['title']);
        $task->setDescription($data['description']);
        $task->setStatus($data['status']);
        $task->setCreatedAt(new \DateTime('now'));
        $task->setUpdatedAt(new \DateTime('now'));
        $entity->persist($task);
        $entity->flush();

        //Send a success message
        $this->addFlash('success', 'Task created successfully!');
        return $this->redirectToRoute("tasks");
    }

    #[Route('/create_task', name: 'create_task_display', methods: ['GET'])]
    public function createTaskDisplay(): Response
    {
        return $this->render('tasks/createTask.html.twig');
    }
}
