<?php

namespace App\Api\V1\Services;

use App\Api\V1\Data\TaskFiltersData;
use App\Models\Task;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class TaskService
{
    public function getTasks(TaskFiltersData $filters, ?int $perPage = null): LengthAwarePaginator|Collection
    {
        $query = Task::query()
            ->when(!auth()->user()->is_admin, function ($query) {
                $query->where('user_id', auth()->id());
            })
            ->when(
                $filters->sort_field,
                function ($query) use ($filters) {
                    $query->orderBy($filters->sort_field, $filters->sort_order);
                },
                function ($query) {
                    $query->latest('id');
                },
            )
            ->when($filters->title, function ($query) use ($filters) {
                $query->whereLike('title', '%'.$filters->title.'%');
            })
            ->when($filters->user_name, function ($query) use ($filters) {
                $query->whereHas('user', function ($query) use ($filters) {
                    $query->whereLike('name', '%'.$filters->user_name.'%');
                });
            })
            ->when($filters->due_date, function ($query) use ($filters) {
                $query->whereDate('due_date', $filters->due_date);
            })
            ->when($filters->status, function ($query) use ($filters) {
                $query->where('status', $filters->status->value);
            });

        if ($perPage) {
            return $query->paginate($perPage);
        }

        return $query->get();
    }
}
