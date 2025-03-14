<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\TaskPriorityEnum;
use App\Enums\TaskStatusEnum;
use App\Repositories\TaskRepositoryInterface;
use App\Rules\QuerySortValidator;
use App\Rules\QueryStatusValidator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class TaskController extends Controller
{
    private const PAGINATION_DEFAULT_LIMIT = 25;
    private TaskRepositoryInterface $taskRepository;

    public function __construct(TaskRepositoryInterface $taskRepository)
    {
        $this->taskRepository = $taskRepository;
    }

    /**
     * Display a listing of the resource.
     * @throws ValidationException
     */
    public function index(Request $request): JsonResponse
    {
        $limit = (int)$request->query('limit', self::PAGINATION_DEFAULT_LIMIT);

        if ($limit < config('app.pagination_min_limit')) {
            $limit = self::PAGINATION_DEFAULT_LIMIT;
        } elseif ($limit > config('app.pagination_max_limit')) {
            $limit = config('app.pagination_max_limit');
        }

        // Validate status and sort
        Validator::make($request->query(), [
            'status' => [new QueryStatusValidator()],
        ])->validate();
        Validator::make($request->query(), [
            'sort' => [new QuerySortValidator()],
        ])->validate();

        $sort = $request->query('sort');
        $status = $request->query('status');

        $userId = Auth::id();
        $tasks = $this->taskRepository->getTasksByUser($userId, $sort, $limit, $status);

        if ($tasks->isEmpty()) {
            return response()->json(
                [
                    'message' => 'Tasks not found',
                ],
                Response::HTTP_BAD_REQUEST
            );
        }

        return response()->json([
            'tasks' => $tasks,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        try {

            $allowedTaskStatuses = [
                TaskStatusEnum::TASK_STATUS_PENDING,
                TaskStatusEnum::TASK_STATUS_IN_PROGRESS,
            ];
            $validatedData = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'status' => [
                    'string',
                    Rule::in(array_column(
                        $allowedTaskStatuses,
                        'value'
                    ))
                ],
                'priority' => ['string', new Enum(TaskPriorityEnum::class)],
                'due_date' => 'required|date|date_format:Y-m-d H:i:s|after:now',
            ]);

        } catch (\Exception $e) {
            return response()->json(
                [
                    'errors' => 'Invalid credentials',
                ],
                Response::HTTP_BAD_REQUEST
            );
        }

        $validatedData['user_id'] = auth()->id();
        $task = $this->taskRepository->createTask($validatedData);

        return response()->json(
            [
                'message' => 'Task created',
                'task' => $task,
            ],
            Response::HTTP_CREATED,
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id): JsonResponse
    {
        $task = $this->taskRepository->getTaskById($id);

        if (!$task || $task->user_id !== Auth::id()) {
            return response()->json(
                [
                    'message' => 'Task not found',
                ],
                Response::HTTP_BAD_REQUEST
            );
        }

        return response()->json([
            'task' => $task,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        try {

            $validatedData = $request->validate([
                'title' => 'string|max:255',
                'description' => 'string',
                'status' => ['string', new Enum(TaskStatusEnum::class)],
                'priority' => ['string', new Enum(TaskPriorityEnum::class)],
                'due_date' => 'date|date_format:Y-m-d H:i:s|after:now',
            ]);

        } catch (\Exception $e) {
            return response()->json(
                [
                    'errors' => 'Invalid credentials',
                ],
                Response::HTTP_BAD_REQUEST
            );
        }
        $userId = auth()->id();
        $task = $this->taskRepository->updateTask((int)$id, $userId, $validatedData);

        return response()->json(
            [
                'message' => 'Task updated',
                'task' => $task,
            ],
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): JsonResponse
    {
        $task = $this->taskRepository->getTaskById($id);
        if (!$task || $task->user_id !== auth()->id()) {
            return response()->json(
                [
                    'message' => 'Task not found',
                ],
                Response::HTTP_BAD_REQUEST
            );
        }
        $userId = auth()->id();
        $this->taskRepository->deleteTask($id, $userId);
        return response()->json(
            ['message' => 'Task deleted'],
        );
    }

    public function complete(int $id): JsonResponse
    {
        $task = $this->taskRepository->getTaskById($id);
        if (!$task || $task->user_id !== auth()->id()) {
            return response()->json(
                [
                    'message' => 'Task not found',
                ],
                Response::HTTP_BAD_REQUEST
            );
        }

        $this->taskRepository->completeTask($id, auth()->id());
        return response()->json(
            ['message' => 'Task completed'],
        );
    }
}
