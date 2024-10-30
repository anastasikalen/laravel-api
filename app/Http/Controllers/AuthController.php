<?php

namespace App\Http\Controllers;

use App\Jobs\SendWelcomeEmail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use OpenApi\Annotations as OA;
/**
 * @OA\Info(
 *     title="Your API Title",
 *     version="1.0.0",
 *     description="Description of your API"
 * )
 */
/**
 * @OA\Tag(name="Auth", description="Authentication and user management")
 */

/**
 * @OA\Schema(
 *     schema="RegisterRequest",
 *     type="object",
 *     required={"name", "email", "password"},
 *     @OA\Property(property="name", type="string", example="John Doe"),
 *     @OA\Property(property="email", type="string", format="email", example="john@example.com"),
 *     @OA\Property(property="password", type="string", minLength=6, example="password123"),
 * )
 */

/**
 * @OA\Schema(
 *     schema="LoginRequest",
 *     type="object",
 *     required={"email", "password"},
 *     @OA\Property(property="email", type="string", format="email", example="john@example.com"),
 *     @OA\Property(property="password", type="string", example="password123"),
 * )
 */

/**
 * @OA\PathItem(path="/api/register")
 */

/**
 * @OA\PathItem(path="/api/login")
 */

/**
 * @OA\PathItem(path="/api/logout")
 */

/**
 * @OA\PathItem(path="/api/tokens")
 */

 class AuthController extends Controller
 {
     /**
      * @OA\Post(
      *     path="/api/register",
      *     tags={"Auth"},
      *     summary="Register a new user",
      *     @OA\RequestBody(
      *         required=true,
      *         @OA\JsonContent(ref="/schemas/RegisterRequest")
      *     ),
      *     @OA\Response(
      *         response=201,
      *         description="User registered successfully."
      *     ),
      *     @OA\Response(
      *         response=400,
      *         description="Validation error."
      *     )
      * )
      */
     public function register(Request $request)
     {
         $request->validate([
             'name' => 'required|string',
             'email' => 'required|string|email|unique:users',
             'password' => 'required|string|min:8',
         ]);
 
         $user = User::create([
             'name' => $request->name,
             'email' => $request->email,
             'password' => Hash::make($request->password),
         ]);
 
         SendWelcomeEmail::dispatch($user);
 
         return response()->json(['message' => 'User registered successfully.'], 201);
     }
 
     /**
      * @OA\Post(
      *     path="/api/login",
      *     tags={"Auth"},
      *     summary="Log in a user",
      *     @OA\RequestBody(
      *         required=true,
      *         @OA\JsonContent(ref="/schemas/LoginRequest")
      *     ),
      *     @OA\Response(
      *         response=200,
      *         description="Login successful.",
      *         @OA\JsonContent(type="object", @OA\Property(property="token", type="string"))
      *     ),
      *     @OA\Response(
      *         response=401,
      *         description="Invalid credentials."
      *     )
      * )
      */
     public function login(Request $request)
     {
         $request->validate([
             'email' => 'required|string|email',
             'password' => 'required|string',
         ]);
 
         if (!Auth::attempt($request->only('email', 'password'))) {
             return response()->json(['message' => 'Invalid credentials.'], 401);
         }
 
         $user = Auth::user();
         $token = $user->createToken('API Token')->plainTextToken;
 
         return response()->json(['token' => $token], 200);
     }
 
     /**
      * @OA\Post(
      *     path="/api/logout",
      *     tags={"Auth"},
      *     summary="Log out the authenticated user",
      *     @OA\Response(
      *         response=200,
      *         description="Logged out successfully."
      *     ),
      *     @OA\Response(
      *         response=401,
      *         description="Unauthenticated."
      *     )
      * )
      */
     public function logout(Request $request)
     {
         $request->user()->currentAccessToken()->delete();
         return response()->json(['message' => 'Logged out successfully.'], 200);
     }

    // public function tokens(Request $request)
    // {
    //     $user = Auth::user(); // Получаем аутентифицированного пользователя
    //     $latestToken = $user->tokens()->latest()->first(); // Получаем последний созданный токен
    
    //     if (!$latestToken) {
    //         return response()->json(['message' => 'No tokens found'], 404);
    //     }
    
    //     return response()->json($latestToken, 200); // Возвращаем последний токен
    // }

}
