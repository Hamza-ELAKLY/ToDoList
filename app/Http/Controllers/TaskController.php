<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Events\TaskCreated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class TaskController extends Controller
{
    public function index()
    {
        return response()->json(Auth::user()->tasks);
    }

    public function show(Task $task)
    {
        $this->authorizeUser($task);
        return response()->json($task);
    }

    public function store(Request $request)
    {
        Log::info('[1/5] Received request to create task.');
        $request->validate(['title' => 'required|string|max:255']);
        
        $task = Auth::user()->tasks()->create($request->only('title'));
        Log::info('[2/5] Task saved to database.', ['task_id' => $task->id, 'title' => $task->title]);

        Log::info('[3/5] Instantiating TaskCreated event.');
        $event = new TaskCreated($task);

        Log::info('[4/5] Broadcasting event now...');
        try {
            broadcast($event);
            Log::info('[5/5] Broadcast command finished without errors.');
        } catch (\Exception $e) {
            Log::error('[X] BROADCAST FAILED!', ['error' => $e->getMessage()]);
        }
        
        return response()->json($task, 201);
    }

    public function update(Request $request, Task $task)
    {
        $this->authorizeUser($task);
        $request->validate([
            'title' => 'sometimes|string|max:255',
            'completed' => 'sometimes|boolean'
        ]);
        
        $task->update($request->only('title', 'completed'));
        return response()->json($task);
    }

    public function destroy(Task $task)
    {
        $this->authorizeUser($task);
        $task->delete();
        return response()->json(['message' => 'deleted']);
    }

    private function authorizeUser(Task $task)
    {
        if ($task->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }
    }
}