<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;

class TaskController extends Controller
{
    // 1. नया टास्क क्रिएट करना
    public function createTask(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string'
        ]);

        $task = Task::create([
            'user_id' => $request->user()->id,
            'title' => $request->title,
            'description' => $request->description,
            'status' => 'Pending'
        ]);

        return response()->json(['message' => 'Task created successfully', 'task' => $task], 201);
    }

    // 2. डेस्कटॉप टाइमर सिंक करना (Seconds track करना)
    public function syncTimer(Request $request, $id)
    {
        $request->validate([
            'seconds' => 'required|integer|min:0'
        ]);

        $task = Task::where('id', $id)->where('user_id', $request->user()->id)->first();

        if (!$task) {
            return response()->json(['message' => 'Task not found.'], 404);
        }

        $task->increment('tracked_seconds', $request->seconds);
        $task->update(['status' => 'Ongoing']);

        return response()->json([
            'message' => 'Timer synced successfully',
            'total_tracked_seconds' => $task->fresh()->tracked_seconds,
            'status' => $task->status
        ], 200);
    }

    // 3. टास्क कम्प्लीट करना
    public function completeTask(Request $request, $id)
    {
        $task = Task::where('id', $id)->where('user_id', $request->user()->id)->first();

        if (!$task) {
            return response()->json(['message' => 'Task not found.'], 404);
        }

        $task->update(['status' => 'Completed']);

        return response()->json([
            'message' => 'Task marked as Completed.',
            'task' => $task
        ], 200);
    }
}
