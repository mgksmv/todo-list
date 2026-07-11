<?php

namespace App\Api\V1\Controllers;

use App\Api\V1\Data\TaskFiltersData;
use App\Api\V1\Requests\TaskIndexRequest;
use App\Api\V1\Requests\TaskSaveRequest;
use App\Api\V1\Resources\TaskResource;
use App\Api\V1\Services\TaskService;
use App\Models\Task;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Attributes\Controllers\Authorize;

#[Group('Задачи')]
class TaskController extends ApiController
{
    public function __construct(
        protected TaskService $taskService,
    ) {
    }

    /**
     * Получить список с сортировкой, фильтрами, поиском и пагинацией
     */
    public function index(TaskIndexRequest $request): JsonResponse
    {
        $tasks = $this->taskService->getTasks(
            TaskFiltersData::from($request->validated()),
            self::DEFAULT_PAGINATION_LIMIT,
        );

        return $this->success(TaskResource::collection($tasks), meta: $this->paginationMeta($tasks));
    }

    /**
     * Создать задачу
     */
    public function store(TaskSaveRequest $request): JsonResponse
    {
        $task = Task::query()->create($request->validated());

        return $this->success(TaskResource::make($task));
    }

    /**
     * Получить одну задачу
     */
    public function show(Task $task): JsonResponse
    {
        return $this->success(TaskResource::make($task));
    }

    /**
     * Редактировать задачу
     */
    #[Authorize('update', 'task')]
    public function update(TaskSaveRequest $request, Task $task): JsonResponse
    {
        $task->update($request->validated());

        return $this->success(TaskResource::make($task));
    }

    /**
     * Удалить задачу
     */
    #[Authorize('delete', 'task')]
    public function destroy(Task $task): JsonResponse
    {
        $task->delete();

        return $this->success();
    }
}
