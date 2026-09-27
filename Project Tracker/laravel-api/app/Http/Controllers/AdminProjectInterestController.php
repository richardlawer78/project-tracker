<?php

namespace App\Http\Controllers;

use App\Models\ProjectInterest;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

class AdminProjectInterestController extends Controller
{
    public function index()
    {
        $interests = ProjectInterest::query()
            ->with('project:id,name')
            ->latest()
            ->paginate(20);

        return view('admin.interests.index', compact('interests'));
    }

    public function show(ProjectInterest $interest)
    {
        $interest->load('project:id,name');

        return view('admin.interests.show', compact('interest'));
    }

    public function update(Request $request, ProjectInterest $interest): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', 'in:new,contacted,qualified,closed'],
        ]);

        $interest->update([
            'status' => $data['status'],
        ]);

        return back()->with('success', 'Interest status updated.');
    }

    public function destroy(ProjectInterest $interest): RedirectResponse
    {
        $interest->delete();

        return redirect()
            ->route('admin.interests.index')
            ->with('success', 'Interest deleted.');
    }
}
