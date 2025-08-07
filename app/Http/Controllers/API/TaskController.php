<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use App\Models\Task;
use App\Http\Requests\ListTaskRequest;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Http\Resources\TaskResource;

class TaskController extends Controller
{
    use ApiResponse;

    public function index(ListTaskRequest $request)
    {
        $query = Task::query()
            ->filterByStatus($request->status)
            ->filterByDueDate($request->due_date, $request->due_date_from, $request->due_date_to)
            ->applySort($request->sort_by, $request->get('sort_order', 'asc'));

        $perPage = $request->get('per_page', config('api.tasks_per_page'));
        $paginator = $query->paginate($perPage);
        
        return $this->success(
            $this->formatPaginatedResponse(TaskResource::collection($paginator), $paginator),
            'Tasks retrieved successfully'
        );
    }

    public function store(StoreTaskRequest $request)
    {
        $task = Task::create($request->validated());

        return $this->success(new TaskResource($task), 'Task created successfully', 201);
    }

    public function show(Task $task)
    {
        return $this->success(new TaskResource($task), 'Task retrieved successfully');
    }

    public function update(UpdateTaskRequest $request, Task $task)
    {
        $task->update($request->validated());

        return $this->success(new TaskResource($task), 'Task updated successfully');
    }

    public function destroy(Task $task)
    {
        $task->delete();

        return $this->success(null, 'Task deleted successfully');
    }
}
