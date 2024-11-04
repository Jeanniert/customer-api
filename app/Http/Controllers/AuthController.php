<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\ActivityLog;
use App\Models\Tokens;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/auth/register",
     *     tags={"Auth"},
     *     summary="Register User",
     *     description="Register a new user by providing name, email, and password.",
     *     @OA\Parameter(
     *         name="name",
     *         in="query",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         ),
     *         description="Name of the user"
     *     ),
     *     @OA\Parameter(
     *         name="email",
     *         in="query",
     *         required=true,
     *         @OA\Schema(
     *             type="string",
     *             format="email"
     *         ),
     *         description="Email of the user"
     *     ),
     *     @OA\Parameter(
     *         name="password",
     *         in="query",
     *         required=true,
     *         @OA\Schema(
     *             type="string",
     *             format="password"
     *         ),
     *         description="Password of the user"
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "email", "password"},
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="email", type="string", format="email", example="john.doe@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="password123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User successfully created",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="User successfully created"),
     *             @OA\Property(property="token", type="string", example="Bearer your_token_here")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation errors",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="errors", type="array", @OA\Items(type="string"))
     *         )
     *     )
     * )
     */
    public function create(Request $request)
    {

        $rules = [
            'name' => 'required|string|max:100',
            'email' => 'required|string|email|max:100|unique:users',
            'password' => 'required|string|min:8'
        ];

        $validator = Validator::make($request->input(), $rules);
        if ($validator->fails()) {
            ActivityLog::create([
                'user_id' => null,
                'action' => 'register_failed',
                'details' => 'Intento de registro fallido - Validación fallida: ' . $request->email,
                'ip_address' => $request->ip()
            ]);
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()->all()
            ], 400);
        }

        $new_user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ]);
        ActivityLog::create([
            'user_id' => $new_user->id,
            'action' => 'register_successful',
            'details' => 'Registro de usuario exitoso: ' . $new_user->email,
            'ip_address' => $request->ip()
        ]);
        return response()->json([
            'status' => true,
            'message' => 'User successfully created',
            'token' => $new_user->createToken('API TOKEN')->plainTextToken
        ], 200);
    }


    /**
     * @OA\Post(
     *     path="/api/auth/login",
     *     tags={"Auth"},
     *     summary="Sign in",
     *     description="Authenticate user by providing email and password.",
     *     @OA\Parameter(
     *         name="email",
     *         in="query",
     *         required=true,
     *         @OA\Schema(
     *             type="string",
     *             format="email"
     *         ),
     *         description="Email of the user"
     *     ),
     *     @OA\Parameter(
     *         name="password",
     *         in="query",
     *         required=true,
     *         @OA\Schema(
     *             type="string",
     *             format="password"
     *         ),
     *         description="Password of the user"
     *     ),
     *      @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email", "password"},
     *             @OA\Property(property="email", type="string", format="email", example="john.doe@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="password123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User successfully authenticated",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="User successfully authenticated"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="John Doe"),
     *                 @OA\Property(property="email", type="string", example="john.doe@example.com")
     *             ),
     *             @OA\Property(property="token", type="string", example="Bearer your_token_here")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="errors", type="array", @OA\Items(type="string", example="Unauthorized"))
     *         )
     *     )
     * )
     */
    public function login(Request $request)
    {

        $rules = [
            'email' => 'required|string|email|max:100',
            'password' => 'required|string'
        ];

        $validator = Validator::make($request->input(), $rules);
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()->all()
            ], 400);
        }

        if (!Auth::attempt($request->only('email', 'password'))) {
            $rules = [
                'name' => 'required|string|max:100',
                'email' => 'required|string|email|max:100|unique:users',
                'password' => 'required|string|min:8'
            ];

            $validator = Validator::make($request->input(), $rules);
            ActivityLog::create([
                'user_id' => null, // Sin usuario
                'action' => 'login_failed',
                'details' => 'Intento de inicio de sesión fallido - Credenciales incorrectas: ' . $request->email,
                'ip_address' => $request->ip()
            ]);
            return response()->json([
                'status' => false,
                'errors' => ['Unauthorized']
            ], 401);
        }

        $user = User::where('email', $request->email)->first();
        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'login_successful',
            'details' => 'Inicio de sesión exitoso:' . $user->email,
            'ip_address' => $request->ip()
        ]);
        $token = $this->generateToken($user);
        return response()->json([
            'status' => true,
            'message' => 'User successfully authenticated',
            'data' => $user,
            'token' => $token
        ], 200);
    }

    public function logout(Request $request)
    {
        auth()->user()->tokens()->delete();
        ActivityLog::create([
            'user_id' => $request->user()->id,
            'action' => 'logout_successful',
            'details' => 'Cierre de sesión exitoso',
            'ip_address' => $request->ip()
        ]);
        return response()->json([
            'status' => true,
            'message' => 'User successfully logged out'
        ], 200);
    }

    private function generateToken($user)
    {
        $randomNumber = rand(200, 500);
        $tokenString = $user->email . now() . $randomNumber;
        $token = sha1($tokenString);
        $expiresAt = Carbon::now()->addHours(12);

        Tokens::create([
            'user_id' => $user->id,
            'token' => $token,
            'expires_at' => $expiresAt
        ]);

        return $token;
    }
}
