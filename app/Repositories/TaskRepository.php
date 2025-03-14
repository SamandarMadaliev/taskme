<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Enums\TaskStatusEnum;
use App\Models\Task;
use Illuminate\Pagination\LengthAwarePaginator;

class TaskRepository implements TaskRepositoryInterface
{
    private Task $taskModel;

    public function __construct()
    {
        $this->taskModel = new Task();
    }

    public function getTasksByUser(
        int    $userId,
        ?array $sort = null,
        ?int   $limit = null,
        ?array $status = null,
    ): ?LengthAwarePaginator
    {
        $task = $this->taskModel::query()->where('user_id', '=', $userId);

        if ($status) {
            $task->whereIn('status', $status);
        }

        if ($sort) {
            foreach ($sort as $field => $direction) {
                $task->orderBy($field, $direction);
            }
        }

        return $task->paginate($limit);
    }

    public function getTaskById(int $id): ?Task
    {
        return $this->taskModel::query()
            ->where('id', '=', $id)
            ->first();
    }

    public function createTask(array $data): Task
    {
        return $this->taskModel::create($data);
    }

    public function updateTask(int $id, int $userId, array $data): ?Task
    {
        // Getting the task
        $task = $this->taskModel::where('id', '=', $id)
            ->where('user_id', '=', $userId)
            ->firstOrFail();
        // Updating the task by given data
        $task->update($data);
        // Returning the task back
        return $task;
    }

    public function deleteTask(int $id, int $userId): void
    {
        $this->taskModel::query()
            ->where('id', '=', $id)
            ->where('user_id', '=', $userId)
            ->delete();
    }

    public function completeTask(int $id, int $userId): int
    {
        return $this->taskModel::query()
            ->where('id', '=', $id)
            ->where('user_id', '=', $userId)
            ->where('status', '!=', TaskStatusEnum::TASK_STATUS_COMPLETED->value)
            ->update(['status' => TaskStatusEnum::TASK_STATUS_COMPLETED->value]);
    }
}
