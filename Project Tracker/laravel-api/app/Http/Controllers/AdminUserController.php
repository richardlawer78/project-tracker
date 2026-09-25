<?php

namespace App\Http\Controllers;

use App\AvatarStorage;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AdminUserController extends Controller
{
    /**
     * Display all users.
     */
    public function index()
    {
        $users = User::query()
            ->latest()
            ->get();

        return view('admin.users.index', compact('users'));
    }

    /**
     * Show the create-user form.
     */
    public function create()
    {
        return view('admin.users.create');
    }

    /**
     * Store a new user.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],

            'email' => [
                'required',
                'email',
                'max:255',
                'unique:users,email',
            ],

            'role' => [
                'required',
                Rule::in([
                    'admin',
                    'project-manager',
                    'team-lead',
                    'developer',
                    'team-member',
                    'member',
                ]),
            ],

            'job_title' => [
                'nullable',
                'string',
                'max:255',
            ],

            'job_title_other' => [
                'nullable',
                'string',
                'max:255',
                'required_if:job_title,other',
            ],

            'availability_percent' => [
                'nullable',
                'integer',
                'min:0',
                'max:100',
            ],

            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
            ],

            'avatar' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
            ],
        ], $this->validationMessages());

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'job_title' => $this->resolveJobTitle($validated),
            'availability_percent' => $validated['availability_percent'] ?? null,
            'password' => $validated['password'],
            'avatar' => $request->hasFile('avatar')
                ? AvatarStorage::store($request->file('avatar'))
                : null,
        ]);

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'User created successfully.');
    }

    /**
     * Show the edit-user form.
     */
    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Update an existing user.
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],

            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],

            'role' => [
                'required',
                Rule::in([
                    'admin',
                    'project-manager',
                    'team-lead',
                    'developer',
                    'team-member',
                    'member',
                ]),
            ],

            'job_title' => [
                'nullable',
                'string',
                'max:255',
            ],

            'job_title_other' => [
                'nullable',
                'string',
                'max:255',
                'required_if:job_title,other',
            ],

            'availability_percent' => [
                'nullable',
                'integer',
                'min:0',
                'max:100',
            ],

            'avatar' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
            ],

            'remove_avatar' => ['nullable', 'boolean'],
        ], $this->validationMessages());

        if ($request->hasFile('avatar')) {
            $validated['avatar_path'] = AvatarStorage::store($request->file('avatar'), $user->avatar);
        } elseif ($request->boolean('remove_avatar')) {
            AvatarStorage::delete($user->avatar);
            $validated['avatar_path'] = null;
        }

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'job_title' => $this->resolveJobTitle($validated),
            'availability_percent' => $validated['availability_percent'] ?? null,
        ] + (array_key_exists('avatar_path', $validated) ? ['avatar' => $validated['avatar_path']] : []));

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'User updated successfully.');
    }

    /**
     * Delete a user.
     */
    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        AvatarStorage::delete($user->avatar);

        $user->delete();

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'User deleted successfully.');
    }

    /**
     * Friendly error messages for the profile picture upload.
     */
    private function validationMessages(): array
    {
        return [
            'job_title_other.required_if' => 'Please type the job title.',
            'avatar.uploaded' => 'The photo could not be uploaded. Try a smaller image (under 2 MB).',
            'avatar.max' => 'The photo must be smaller than 2 MB.',
        ];
    }

    /**
     * Use the typed job title when "Other" was chosen.
     */
    private function resolveJobTitle(array $validated): ?string
    {
        $title = ($validated['job_title'] ?? null) === 'other'
            ? trim($validated['job_title_other'] ?? '')
            : ($validated['job_title'] ?? null);

        return $title !== '' ? $title : null;
    }
}