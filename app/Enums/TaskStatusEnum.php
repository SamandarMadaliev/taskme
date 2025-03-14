<?php

namespace App\Enums;

enum TaskStatusEnum: string
{
    case TASK_STATUS_PENDING = 'pending';
    case TASK_STATUS_IN_PROGRESS = 'in_progress';
    case TASK_STATUS_COMPLETED = 'completed';
    case TASK_STATUS_ARCHIVED = 'archived';
}
