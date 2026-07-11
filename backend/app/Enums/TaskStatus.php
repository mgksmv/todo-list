<?php

namespace App\Enums;

use App\Enums\Traits\HasLabels;

enum TaskStatus: string
{
    use HasLabels;

    case PENDING = 'pending';
    case IN_PROGRESS = 'in_progress';
    case COMPLETED = 'completed';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => __('task-statuses.pending'),
            self::IN_PROGRESS => __('task-statuses.in_progress'),
            self::COMPLETED => __('task-statuses.completed'),
        };
    }
}
