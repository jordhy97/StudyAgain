<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JwtAuth\Exceptions\JWTException;
use App\User;

class AuthController extends Controller
{
    /**
     * AuthController constructor.
     */
    public function __construct()
    {
        $this->middleware('jwt.auth', ['only' => [
            'userInfo'
        ]]);
    }

    /**
     * Authenticate a user from the given credentials (email and password) and return JWT if the credentials are valid,
     * else return error message.
     * @param Request $request sent request.
     * @return Response
     */
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        try {
            if (! $token = JWTAuth::attempt($credentials)) {
                return response()->json(['error' => 'invalid_credentials'], 401);
            }
        } catch (JWTException $e) {
            return response()->json(['error' => 'could_not_create_token'], 500);
        }

        return response()->json(compact('token'));
    }

    /**
     * Register a user and return JWT. Return error message if user already exists.
     * @param Request $request sent request.
     * @return Response
     */
    public function register(Request $request)
    {
        $request['password'] = bcrypt($request['password']);
        $request['remember_token'] = str_random(10);
        $credentials = $request->only('name', 'email', 'password', 'remember_token');

        $user = null;
        try {
            $user = User::create($credentials);
        } catch (\Exception $e) {
            return response()->json(['error' => 'The email address you specified is already in use.'], 500);
        }
        return response("User successfully registered",200);
    }

    /**
     * Get the current authenticated user information.
     * @return Response
     */
    public function userInfo()
    {
        $user = JWTAuth::parseToken()->authenticate();

        // If the token is invalid
        if (! $user) {
            return response()->json(['invalid user'], 401);
        }

        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email
        ]);
    }
}
