<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Task;
use App\Form\Type\TaskType;
use App\Repository\TaskRepository;
use Carbon\Carbon;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class TodoController.
 */
class TodoController extends AbstractController
{
    /**
     *
     *
     * @var TaskRepository
     */
    private TaskRepository $taskRepository;

    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;

    /**
     * TodoController constructor.
     *
     * @param TaskRepository         $taskRepository
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(TaskRepository $taskRepository, EntityManagerInterface $entityManager)
    {
        $this->taskRepository = $taskRepository;
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/", name="app.tasks.list", methods={"GET"})
     *
     * @return Response
     */
    public function index(): Response
    {
        $tasks = $this->taskRepository->findActiveTasks();

        return $this->render('tasklist.html.twig', [
            'tasks' => $tasks,
        ]);
    }

    /**
     * @Route("/create", name="app.tasks.create", methods={"GET", "POST"})
     *
     * @param Request $request
     *
     * @return Response
     */
    public function create(Request $request): Response
    {
        $task = new Task();

        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $task = $form->getData();
            $this->entityManager->persist($task);
            $this->entityManager->flush();

            $this->addFlash('success', sprintf('Task "%s" is created 💪', $task->getDescription()));

            return $this->redirectToRoute('app.tasks.list');
        }

        return $this->render('create.html.twig', [
            'taskform' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{task}/complete", name="app.tasks.complete", methods={"GET"})
     *
     * @param Task $task
     *
     * @return Response
     */
    public function complete(Task $task): Response
    {
        $task->setCompletedAt(Carbon::now());

        $this->entityManager->flush();

        $this->addFlash('success', sprintf('Task "%s" is marked as completed 👍', $task->getDescription()));

        return $this->redirectToRoute('app.tasks.list');
    }
}
