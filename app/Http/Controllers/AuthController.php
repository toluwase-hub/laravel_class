<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Pest\Plugins\Tia\Storage;
use Psy\ManualUpdater\Checker;

class AuthController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function Register(Request $request)
    {
        $validateUserData = $request->validate([
            'surname' => 'required|string',
            'firstname' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string|unique:users,phone',
            'password' => 'required|min:8|max:12|confirmed',
            'image' => 'required|mimes:jpg,png'
        ]);
        try {
             if($request->hasFile('image')){
            $imagepath = $request->file('image')->store('/users', 'public');
        }
        $user = user::create([
           'surname' => $validateUserData['surname'],
           'firstname' => $validateUserData['firstname'],
           'email' => $validateUserData['email'],
           'phone' => $validateUserData['phone'],
           'password' => $validateUserData['password'],
           'image' => $imagepath
        ]);
        return response()->json([
            $user,
        ]);
        } catch (\Exception $e) {
         Log:;info('error while creating account' . $e);
         return response()->json([
            'message' => 'error while creating account',
            'error' => $e
         ]);
        }
       
    }

    /**
     * Store a newly created resource in storage.
     */
    public function Login(Request $request)
    {
        $validateLoginData = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $user = user::where('email', $validateLoginData['email'])->first();
        if (!$user || !Hash::check($validateLoginData['password'], $user->password)) {
            return response()->json(
                [
                    'message' => 'invalid email or password'
                ]
            );
        };
        // $user->tokens()->delete();
        $token = $user->createToken('user-token')->plainTextToken;
        return response()->json(
            [
                'message' => 'login successful',
                'data' => [
                    'user' => $user,
                    'token' => $token
                ]
            ]
        );
    }

    /**.
     * Display the specified resource.
     */
    public function Logout(Request $request)
    {
        $user = $request->user();

        $user->tokens()->delete();

        return response()->json([
            'messsage' => 'Logout successful'
        ]);
    }

   public function destroy (Request $request, string $id)
   {
    $user = User::find($id);
    if(!$user){
        return response()->json([
            'message' => 'No user found',
            'data' => null
        ]);
    }
    $user->delete();
    return response()->json([
        'message' => 'User ' . $id . ' deleted successful'
    ]);
   }
}
