<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        if ($request->user()->role !== 'admin') {
            return response()->json([
                'message' => 'Only administrators can manage users.'
            ], 403);
        }

        return response()->json([
            'users' => User::latest()->get()
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        if ($request->user()->role !== 'admin') {
            return response()->json([
                'message' => 'Only administrators can create users.'
            ], 403);
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'role' => ['required', Rule::in(['admin', 'project-manager', 'team-lead', 'developer', 'team-member', 'member'])],
            'job_title' => ['nullable', 'string', 'max:255'],
        ]);

        $temporaryPassword = Str::random(12);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'role' => $data['role'],
            'job_title' => $data['job_title'] ?? null,
            'password' => Hash::make($temporaryPassword),
            'must_change_password' => true,
            'temporary_password_expires_at' => now()->addHours(24),
        ]);

        return response()->json([
            'message' => 'User created successfully.',
            'user' => $user,
            'temporary_password' => $temporaryPassword,
        ], 201);
    }
}