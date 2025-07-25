<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Http\Requests\ListTaskRequest;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Http\Resources\TaskResource;

class TaskController extends Controller
{
    protected $tasksPerPage = 10;

    public function index(ListTaskRequest $request)
    {
        $query = Task::query()
            ->filterByStatus($request->status)
            ->filterByDueDate($request->due_date, $request->due_date_from, $request->due_date_to);

        if ($request->has('sort_by')) {
            $query->orderBy($request->sort_by, $request->get('sort_order', 'asc'));
        }

        $perPage = $request->get('per_page', $this->tasksPerPage);
        
        return TaskResource::collection($query->paginate($perPage));
    }

    public function store(StoreTaskRequest $request)
    {
        $task = Task::create($request->validated());

        return (new TaskResource($task))->response()->setStatusCode(201);
    }

    public function show($id)
    {
        $task = Task::find($id);

        if (!$task) {
            return response()->json(['message' => 'Task not found'], 404);
        }
        
        return new TaskResource($task);
    }

    public function update(UpdateTaskRequest $request, $id)
    {
        $task = Task::find($id);

        if (!$task) {
            return response()->json(['message' => 'Task not found'], 404);
        }

        $task->update($request->validated());
        return (new TaskResource($task))->response()->setStatusCode(200);
    }

    public function destroy($id)
    {
        $task = Task::find($id);

        if (!$task) {
            return response()->json(['message' => 'Task not found'], 404);
        }

        $task->delete();

        return response()->json(['message' => 'Task deleted successfully'], 204);
    }
}
