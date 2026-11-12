<?php

namespace App\Http\Controllers;

use App\Models\Todo;
use App\Http\Requests\StoreTodoRequest;
use App\Http\Requests\UpdateTodoRequest;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TodoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTodoRequest $request)
    {
        $validateData = $request->validated();
        $user = $request->user();
        try {
            $todo = Todo::create(
                [
                    'user_id' => $user['id'],
                    'title' => $validateData['title']
                ]
            );
            return response()->json([
                'message' => 'Task added successful',
                'data' => $todo
            ], 200);
        } catch (\Exception $e) {
            Log::info('error while adding task' . $e);
            return response()->json([
                'message' => 'error while adding task',
                'error' => $e,
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request)
    {
        $user = $request->user();
        try {
            $user = User::find($user['id']);
            if (!$user) {
                return response()->json([
                    'message' => 'No user found',
                    'data' => null
                ], 403);
            }
            $todo = Todo::where('user_id', $user['id'])->paginate(3);

            return response()->json([
                'message' => 'User Task',
                'data' => $todo
            ], 200);
        } catch (\Exception $e) {
            Log::info('error while fetching task ' . $e);
            return response()->json([
                'message' => 'error while fetching task',
                'error' => $e,
            ], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function update(Request $request, string $id)
    {
        $validateData = $request->validate([
            'title' => 'required|string',
        ]);
        $user = $request->user();
        try {
            $todo = User::where('id', $user['id'])->get();
            if (!$todo) {
                return response()->json([
                    'message' => 'No user found',
                    'data' => null,
                ],403);
            };
            $existTitle = Todo::where('id', $id)->where('user_id', $user['id'])->first();

            if (!$existTitle) {
                return response()->json([
                    'message' => 'No task found for this user',
                    'data' => null
                ], 403);
            };
            $existTitle['title'] = $validateData['title'];
            $existTitle->save();
            $existTitle->refresh();

            return response()->json([
                'message' => 'Updating task successful',
                'data' => [
                    $existTitle
                ],
            ], 200);
        } catch (\Exception $e) {
            Log::info('error while updating todo' . $e);
            return response()->json([
                'message' => 'error while updating todo',
                'error' => $e,
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function is_complete(Request $request, string $id)
    {
        $user = $request->user();
        try {
            $todo = User::where('id', $user['id'])->get();
            if (!$todo) {
                return response()->json([
                    'message' => 'No user found',
                    'data' => null,
                ], 404);
            };
            $existTitle = Todo::where('id', $id)->where('user_id', $user['id'])->first();
            if (!$existTitle) {
                return response()->json([
                    'message' => 'No task found for this user',
                    'data' => null
                ], 404);
            };
            $existTitle['is_complete'] = !$existTitle['is_complete'];
            $existTitle->save();
            $existTitle->refresh();

            return response()->json([
                'message' => 'Updating task successful',
                'data' => [
                    $existTitle
                ],
            ], 200);
        } catch (\Exception $e) {
            Log::info('error while updating todo' . $e);
            return response()->json([
                'message' => 'error while updating todo',
                'error' => $e,
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $id)
    {
        $user = $request->user();
        try {
            $todo = User::where('id', $user['id'])->get();
            if (!$todo) {
                return response()->json([
                    'message' => 'No user found',
                    'data' => null,
                ]);
            };
            $existTitle = Todo::where('id', $id)->where('user_id', $user['id'])->first();
            if (!$existTitle) {
                return response()->json([
                    'message' => 'No task found for this user',
                    'data' => null
                ]);
            };
            $existTitle->delete();
            $existTitle->refresh();
            return response()->json([
                'message' => 'Task deleted successful',
                'data' => [
                    null
                ],
            ]);
        } catch (\Exception $e) {
            Log::info('error while deleting todo' . $e);
            return response()->json([
                'message' => 'error while deleting todo',
                'error' => $e,
            ], 500);
        }
    }
}
