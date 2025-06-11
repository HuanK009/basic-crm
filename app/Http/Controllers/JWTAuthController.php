<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;


class JWTAuthController extends Controller
{
    /**
     * Create a new JWTAuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login()
    {
        $credentials = request(['email', 'password']);

        if (! $token = Auth::attempt($credentials)) {
            return response()->json([
                'error' => 'Unauthorized',
                'message' => 'Your user ID and/or password does not match.'
            ], 401);
        }

        return $this->respondWithToken($token, request()->only('remember')['remember']);
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me(Request $request)
    {
        $user = User::find($request->user()->id)->with(['additionalInfo', 'spouseInfo', 'personalPref'])->first();
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        return response()->json($user);
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        Auth::logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(Auth::refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token, $remember = false)
    {
        $expires_in = Auth::factory()->getTTL() * 60;
        if ($remember) {
            $expires_in = $expires_in * 24 * 365; // 365 days
        }
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => $expires_in
        ]);
    }


    /**
     * Get the token array structure.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function register()
    {
        $data = request()->only(['first_name', 'last_name', 'email', 'password', 'confirm_password']);
        $data['password'] = bcrypt($data['password']);

        $user = \App\Models\User::create($data);

        return response()->json([
            'message' => 'User registered successfully',
            'user' => $data
        ], 200);
    }
}
