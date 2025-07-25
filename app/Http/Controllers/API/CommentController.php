<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Http\Requests\StoreCommentRequest;
use App\Http\Resources\CommentResource;

class CommentController extends Controller
{
    public function index($taskId)
    {
        $task = Task::find($taskId);
        
        if (!$task) {
            return response()->json(['message' => 'Task not found'], 404);
        }
        
        return CommentResource::collection($task->comments);
    }

    public function store(StoreCommentRequest $request, $taskId)
    {
        $task = Task::find($taskId);
        
        if (!$task) {
            return response()->json(['message' => 'Task not found'], 404);
        }

        $comment = $task->comments()->create($request->validated());

        return (new CommentResource($comment))->response()->setStatusCode(201);
    }
}
