<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ClientLoginRequest;
use Illuminate\Http\RedirectResponse;
use App\Http\Responses\ApiResponse;
use App\Models\ClientUser;
use App\Support\ErrorCodes;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ClientLoginController extends Controller
{
    public function index()
    {
        return view('pages.auth.signin');
    }

    /**
     * POST /client/login
     *
     * Accepts email OR phone number in the 'login' field.
     * Authenticates exclusively against the 'client' guard.
     */
    public function __invoke(ClientLoginRequest $request): JsonResponse|RedirectResponse
    {
        $login    = $request->input('login');
        $password = $request->input('password');

        // Detect field and find user — never touches admin users table
        if ($request->isEmail()) {
            $user = ClientUser::where('email', $login)->first();
        } else {
            $user = ClientUser::where('phone_number', $login)->first();
        }

        // User not found OR wrong password
        if (! $user || ! Hash::check($password, $user->password)) {
            if ($request->expectsJson()) {
                return ApiResponse::error(
                    code: ErrorCodes::AUTH_INVALID_CREDENTIALS,
                    statusCode: 401,
                );
            }

            return redirect()
                ->back()
                ->withInput()
                ->with('error', __('errors.' . ErrorCodes::AUTH_INVALID_CREDENTIALS));
        }

        // Optionally block unverified accounts (toggle this depending on project policy)
        // if (!$user->is_verified) {
        //     return ApiResponse::error(ErrorCodes::AUTH_UNVERIFIED_ACCOUNT, statusCode: 403);
        // }

        // Authenticate against the client guard only — never logs into admin session
        Auth::guard('client')->login($user, remember: false);

        if ($request->expectsJson()) {
            return ApiResponse::success(
                data: [
                    'user_id'  => $user->user_id,
                    'nickname' => $user->nickname,
                    'email'    => $user->email,
                ],
                code: ErrorCodes::LOGIN_SUCCESS,
            );
        }

        return redirect()
            ->intended('/tradding')
            ->with('success', __('errors.' . ErrorCodes::LOGIN_SUCCESS));
    }
}
