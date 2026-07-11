<?php

namespace App\Api\V1\Data;

use App\Enums\TaskStatus;
use Spatie\LaravelData\Data;

class TaskFiltersData extends Data
{
    public function __construct(
        public ?string     $sort_field = null,
        public ?string     $sort_order = 'asc',
        public ?string     $title = null,
        public ?string     $user_name = null,
        public ?string     $due_date = null,
        public ?TaskStatus $status = null,
    ) {
    }
}
