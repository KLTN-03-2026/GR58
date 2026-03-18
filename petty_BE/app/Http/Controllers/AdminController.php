<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class AdminController extends Controller
{
    public function dangNhap(Request $request): JsonResponse
    {
        $rules = [
            'email' => 'required|email',
            'password' => 'required|string',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => Lang::get('messages.validation_failed'),
                'errors' => $validator->errors(),
            ], 422);
        }

        $credentials = $validator->validated();

        $admin = Admin::where('email', $credentials['email'])->first();
        if (! $admin || ! Hash::check($credentials['password'], $admin->mat_khau)) {
            return response()->json([
                'status' => false,
                'message' => Lang::get('messages.login_failed'),
            ], 401);
        }

        try {
            $token = $admin->createToken('api-token')->plainTextToken;
        } catch (\Exception $e) {
            Log::error('Admin token creation failed: ' . $e->getMessage());
            $token = null;
        }

        if (method_exists($admin, 'makeHidden')) {
            $admin->makeHidden(['mat_khau']);
        }

        // Load thông tin vai trò và quyền
        $admin->load('phanQuyen');

        return response()->json([
            'status' => true,
            'message' => Lang::get('messages.login_success'),
            'data' => $admin,
            'token' => $token,
            'redirect_url' => '/admin/dashboard',
        ], 200);
    }

    /**
     * Logout the authenticated admin by revoking current access token.
     */
    public function dangXuat(Request $request): JsonResponse
    {
        $admin = $request->user();
        if (! $admin) {
            return response()->json([
                'status' => false,
                'message' => Lang::get('messages.unauthorized'),
            ], 401);
        }

        try {
            // If using Sanctum personal access tokens
            $token = $request->user()->currentAccessToken();
            if ($token) {
                $token->delete();
            } else {
                // fallback: delete all tokens for this admin
                $admin->tokens()->delete();
            }
        } catch (\Exception $e) {
            Log::error('Admin logout failed: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => Lang::get('messages.server_error'),
            ], 500);
        }

        return response()->json([
            'status' => true,
            'message' => Lang::get('messages.logout_success') ?? 'Đã đăng xuất.',
        ], 200);
    }
}
