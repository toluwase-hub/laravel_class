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
        try {
            $product = Product::find($validateData['user_id']);
            if (!$product) {
                return response()->json([
                    'message' => 'No user found',
                    'data' => null
                ]);
            }

            $todo = Todo::create(
                $validateData
            );
            $todo->refresh();
            return response()->json([
                'message' => 'Task added successful',
                'data' => $todo
            ]);
        } catch (\Exception $e) {
            Log::info('error while creating product' . $e);
            return response()->json([
                'message' => 'error while adding task',
                'error' => $e,
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Todo $todo, string $id)
    {
        $todo = Todo::where('user_id', $id)->paginate(3);
        try {
            if ($todo->isEmpty()) {
                return response()->json([
                    'message' => 'No user found',
                    'data' => null
                ]);
            }
            return response()->json([
                'message' => 'User ' . $id . ' Task',
                'data' => $todo
            ]);
        } catch (\Exception $e) {
            Log::info('error while creating product' . $e);
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
            'todo_id' => 'required|exists:todos,id',
            'title' => 'required|string',
        ], [
            'todo_id.exists' => 'No task found for this user'
        ]);

        try {
            $todo = Todo::where('user_id', $id)->get();
            if ($todo->isEmpty()) {
                return response()->json([
                    'message' => 'No user found',
                    'data' => null,
                ]);
            };
            $existTitle = Todo::where('id', $validateData['todo_id'])->where('user_id', $id)->first();

            if (!$existTitle) {
                return response()->json([
                    'message' => 'No task found for this user',
                    'data' => null
                ]);
            };
            $existTitle['title'] = $validateData['title'];
            $existTitle->save();
            $existTitle->refresh();

            return response()->json([
                'message' => 'Updating task successful',
                'data' => [
                    $existTitle
                ],
            ]);
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
    public function is_complete(UpdateTodoRequest $request, string $id)
    {
        $validateData = $request->validated();
        try {
            $todo = Todo::where('user_id', $id)->get();
            if ($todo->isEmpty()) {
                return response()->json([
                    'message' => 'No user found',
                    'data' => null,
                ]);
            };
            $existTitle = Todo::where('id', $validateData['todo_id'])->where('user_id', $id)->first();

            if (!$existTitle) {
                return response()->json([
                    'message' => 'No task found for this user',
                    'data' => null
                ]);
            };
            $existTitle['is_complete'] = $validateData['is_complete'];
            $existTitle->save();
            $existTitle->refresh();

            return response()->json([
                'message' => 'Updating task successful',
                'data' => [
                    $existTitle
                ],
            ]);
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
        $validateData = $request->validate([
            'todo_id' => 'required|exists:todos,id'
        ], [
            'todo_id.exists' => 'No task found for this user'
        ]);

        try {
            $todo = Todo::where('user_id', $id)->get();
            if ($todo->isEmpty()) {
                return response()->json([
                    'message' => 'No user found',
                    'data' => null,
                ]);
            };
            $existTitle = Todo::where('id', $validateData['todo_id'])->where('user_id', $id)->first();

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
