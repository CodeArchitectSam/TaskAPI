<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use App\Models\Task;
use App\Http\Requests\StoreCommentRequest;
use App\Http\Resources\CommentResource;

class CommentController extends Controller
{
    use ApiResponse;

    public function index($id)
    {
        $task = Task::find($id);
        return $this->success(CommentResource::collection($task->comments), 'Comments retrieved successfully', 200);
    }

    public function store(StoreCommentRequest $request, $taskId)
    {
        $task = Task::find($taskId);
        
        if (!$task) {
            return $this->error('Task not found', 404);
        }

        $comment = $task->comments()->create($request->validated());

        return $this->success(new CommentResource($comment), 'Comment created successfully', 201);
    }
}
