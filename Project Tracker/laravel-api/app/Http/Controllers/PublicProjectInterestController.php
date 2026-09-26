<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectInterest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PublicProjectInterestController extends Controller
{
    public function store(Request $request, Project $project): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'company' => ['nullable', 'string', 'max:150'],
            'message' => ['nullable', 'string', 'max:2000'],
        ]);

        ProjectInterest::create([
            'project_id' => $project->id,
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'company' => $data['company'] ?? null,
            'message' => $data['message'] ?? null,
            'status' => 'new',
        ]);

        return redirect()
            ->back()
            ->with('success', 'Your interest has been submitted successfully.');
    }
}
