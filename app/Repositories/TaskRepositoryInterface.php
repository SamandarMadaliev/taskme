<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Task;

interface TaskRepositoryInterface
{
    public function getTasksByUser(
        int    $userId,
        ?array $sort = null,
        ?int   $limit = null,
        ?array $status = null,
    );

    public function getTaskById(int $id): ?Task;

    public function createTask(array $data): bool;

    public function updateTask(int $id, int $userId, array $data): int;

    public function deleteTask(int $id, int $userId): void;

    public function completeTask(int $id, int $userId): int;
}
