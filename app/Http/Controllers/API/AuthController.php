<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password as RulesPassword;
use Illuminate\Validation\ValidationException;
use Validator;

/**
 * @group Authentication
 *
 * APIs for user authentication
 */
class AuthController extends Controller
{
    /**
     * @unauthenticated
     * 
     * Register a new user.
     * @bodyParam name string required The name of the user.
     * @bodyParam email string required The email of the user.
     * @bodyParam password string required The password of the user.
     * @bodyParam password_confirmation string required The password confirmation of the user.
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     * @response scenario=success {
     *  "success": true,
     *  "message": "User registered successfully.",
     *  "token": "7|PxhOAOaHYFmEV2Pa86ooc1gbsqg4RIJkjESs5zk69432cc0e",
     *  "user": {
     *      "name": "a",
     *      "email": "a@a.com",
     *      "updated_at": "2025-06-29T13:18:49.000000Z",
     *      "created_at": "2025-06-29T13:18:49.000000Z",
     *      "id": 1
     * }
     */
    public function register(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:6|confirmed',
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 422);
            }

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            $token = $user->createToken('MyAppToken')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'User registered successfully.',
                'token' => $token,
                'user' => $user,
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Registration failed. Please try again later.'], 500);
        }
        
    }

    /**
     * @unauthenticated
     * 
     * Login a user.
     * @bodyParam email string required The email of the user.
     * @bodyParam password string required The password of the user.
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     * @response scenario=success {
     *  "success": true,
     *       "message": "Login successful.",
     *       "token": "6|S5dxhPUCUUCCCE6NOBHANjpEOi4leD3aATQhk3eY669d3ae0",
     *       "user": {
     *           "id": 1,
     *           "name": "admin",
     *           "email": "admin@admin.com",
     *           "email_verified_at": null,
     *           "created_at": "2025-06-28T10:09:51.000000Z",
     *           "updated_at": "2025-06-28T10:09:51.000000Z"
     *       }
     * } 
     */
    public function login(Request $request)
    {
        try {
            if (!Auth::attempt($request->only('email', 'password'))) {
                return response()->json(['message' => 'Invalid credentials'], 401);
            }

            $user = Auth::user();
            $token = $user->createToken('MyAppToken')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Login successful.',
                'token' => $token,
                'user' => $user,
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Login failed. Please try again later.'], 500);
        }
    }

    /**
     * @authenticated
     * 
     * Logout the authenticated user.
     * @return \Illuminate\Http\JsonResponse
     * @response scenario=success {
     *  "success": true,
     *  "message": "Successfully logged out"
     * }
     */
    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Successfully logged out',
        ]);
    }

    /**
     * @unauthenticated
     * 
     * Send password reset token.
     * @bodyParam email string required The email of the user.
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     * @response scenario=success {
     *  "message": "Password reset token generated",
     *  "token": "7|PxhOAOaHYFmEV2Pa86ooc1gbsqg4RIJkjESs5zk69432cc0e",
     *  "email": "a@a.com",
     *  "success": true
     * }
     */
    public function sendResetToken(Request $request)
    {
        try {
            $request->validate(['email' => 'required|email']);

            $user = User::where('email', $request->email)->first();

            if (!$user) {
                throw ValidationException::withMessages([
                    'email' => [__('passwords.user')]
                ]);
            }

            $token = Password::createToken($user);

            if (app()->environment('local', 'development')) {
                return response()->json([
                    'message' => 'Password reset token generated',
                    'token' => $token,
                    'email' => $user->email,
                    'success' => true
                ]);
            }

            //If not in local or development environment, send email
        } catch (ValidationException $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to send reset token. Please try again later.'], 500);
        }
    }

    /**
     * @unauthenticated
     * 
     * Reset password.
     * @bodyParam token string required The password reset token.
     * @bodyParam email string required The email of the user.
     * @bodyParam password string required The password of the user.
     * @bodyParam password_confirmation string required The password confirmation of the user.
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     * @response scenario=success {
     *  "message": "Password reset successfully",
     *  "success": true
     * }
     */
    public function resetPassword(Request $request)
    {
        try {
            $request->validate([
                'token' => 'required',
                'email' => 'required|email',
                'password' => ['required', 'confirmed', RulesPassword::defaults()],
            ]);

            $status = Password::reset(
                $request->only('email', 'password', 'password_confirmation', 'token'),
                function ($user, $password) {
                    $user->forceFill([
                        'password' => Hash::make($password),
                        'remember_token' => Str::random(60),
                    ])->save();
                }
            );

            if ($status === Password::PASSWORD_RESET) {
                return response()->json([
                    'message' => __($status),
                    'success' => true
                ]);
            }

            throw ValidationException::withMessages([
                'email' => __($status)
            ]);
        } catch (ValidationException $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }
}