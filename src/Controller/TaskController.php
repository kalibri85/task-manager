<?php

namespace App\Controller;

use App\Entity\Task;
use App\Form\TaskType;
use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Attribute\Route;
use Knp\Component\Pager\PaginatorInterface;

final class TaskController extends AbstractController
{
    // List of tasks, optional filter: status, hide done. 
    // Use KnpPaginatorBundle to paginate result, 5 tasks per page. 
    // Routes: GET / and GET /task
    #[Route('/', name: 'home', methods: ['GET'])]
    #[Route('/task', name: 'app_task_index', methods: ['GET'])]
    public function index(Request $request, TaskRepository $taskRepository, PaginatorInterface $paginator): Response
    {   
        $task = new Task();
        $form = $this->createForm(TaskType::class, $task);
        $status = $request->query->get('status');
        $hideDone = $request->query->get('hideDone');

        $queryBuilder = $taskRepository->createQueryBuilder('t')
            ->orderBy('t.dueDate', 'ASC');

        if($status) {
            $queryBuilder->andWhere('t.status = :status')
                ->setParameter('status', $status);
        }

        if($hideDone) {
            $queryBuilder->andWhere('t.status != :done')
                ->setParameter('done', 'done');
        }

        $tasks = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1),
            5
        );

        return $this->render('task/index.html.twig', [
            'tasks' => $tasks,
            'currentStatus'  => $status,
            'hideDone' => $hideDone,
            'form' => $form->createView(),
        ]);
    }
    // Create new task. 
    // Route: GET /task/new - show the form and POST /task/new - submit the form
    #[Route('/task/new', name: 'app_task_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $task = new Task();
        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($task);
            $entityManager->flush();

            return $this->redirectToRoute('app_task_index');
        }

        return $this->render('task/new.html.twig', [
            'task' => $task,
            'form' => $form->createView(),
        ]);
    }
    // Show details of the single task 
    // Routes: GET /{id}
    #[Route('/{id}', name: 'app_task_show', methods: ['GET'])]
    public function show(Task $task): Response
    {
        return $this->render('task/show.html.twig', [
            'task' => $task,
        ]);
    }
    // Edit an existing task by ID. 
    // Routes: GET /{id}/edit - show edit form and POST /{id}/edit - submit the form
    #[Route('/{id}/edit', name: 'app_task_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Task $task, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_task_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('task/_edit_form.html.twig', [
            'task' => $task,
            'form' => $form->createView(),
        ]);
    }
    // Delete a task by ID. 
    // Routes: POST /{id} - delete action
    #[Route('/{id}', name: 'app_task_delete', methods: ['POST'])]
    public function delete(Request $request, Task $task, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$task->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($task);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_task_index', [], Response::HTTP_SEE_OTHER);
    }
}
