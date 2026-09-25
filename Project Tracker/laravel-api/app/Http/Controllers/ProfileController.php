<?php

namespace App\Http\Controllers;

use App\AvatarStorage;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    /**
     * Show the logged-in person's profile page.
     */
    public function edit(Request $request)
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update name, job title and profile picture.
     */
    public function update(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'job_title' => ['nullable', 'string', 'max:255'],
            'job_title_other' => ['nullable', 'string', 'max:255', 'required_if:job_title,other'],
            'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'remove_avatar' => ['nullable', 'boolean'],
        ], [
            'job_title_other.required_if' => 'Please type your job title.',
            'avatar.uploaded' => 'The photo could not be uploaded. Try a smaller image (under 2 MB).',
            'avatar.max' => 'The photo must be smaller than 2 MB.',
        ]);

        $user->name = $data['name'];
        $jobTitle = ($data['job_title'] ?? null) === 'other'
            ? trim($data['job_title_other'] ?? '')
            : ($data['job_title'] ?? null);

        $user->job_title = $jobTitle !== '' ? $jobTitle : null;

        if ($request->hasFile('avatar')) {
            $user->avatar = AvatarStorage::store($request->file('avatar'), $user->avatar);
        } elseif ($request->boolean('remove_avatar')) {
            AvatarStorage::delete($user->avatar);
            $user->avatar = null;
        }

        $user->save();

        return redirect()
            ->route('profile.edit')
            ->with('success', 'Your profile was updated.');
    }
}