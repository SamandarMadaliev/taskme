<?php

namespace App\Enums;

enum TaskPriorityEnum: string
{
    case TASK_PRIORITY_LOW = 'low';
    case TASK_PRIORITY_MEDIUM = 'medium';
    case TASK_PRIORITY_HIGH = 'high';
}
