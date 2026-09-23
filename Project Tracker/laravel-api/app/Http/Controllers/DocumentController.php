<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\Project;
use App\ProjectAccess;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DocumentController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $documents = Document::query()
            ->whereHas('project', function ($query) use ($user) {
                $query->when($user->role !== 'admin', function ($query) use ($user) {
                    $query->where(function ($query) use ($user) {
                        $query->where('owner_id', $user->id)
                            ->orWhereHas('members', function ($query) use ($user) {
                                $query->where('users.id', $user->id);
                            });
                    });
                });
            })
            ->when($request->filled('project_id'), fn ($query) =>
                $query->where('project_id', $request->project_id)
            )
            ->latest()
            ->paginate(15);

        return response()->json($documents);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'project_id' => ['required', 'exists:projects,id'],
            'name' => ['required', 'string', 'max:255'],
            'file_path' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:pdf,doc,excel,design,other'],
            'size' => ['required', 'string', 'max:50'],
        ]);

        $project = Project::findOrFail($validated['project_id']);

        abort_unless(
            ProjectAccess::canView($request->user(), $project),
            403
        );

        $validated['uploaded_by'] = $request->user()->id;

        $document = Document::create($validated);

        return response()->json($document, 201);
    }

    public function show(Request $request, Document $document): JsonResponse
    {
        abort_unless(
            ProjectAccess::canView($request->user(), $document->project),
            403
        );

        return response()->json($document);
    }

    public function update(Request $request, Document $document): JsonResponse
    {
        abort_unless(
            ProjectAccess::canManage($request->user(), $document->project),
            403
        );

        $validated = $request->validate([
            'project_id' => ['sometimes', 'exists:projects,id'],
            'name' => ['sometimes', 'string', 'max:255'],
            'file_path' => ['sometimes', 'string', 'max:255'],
            'type' => ['sometimes', 'in:pdf,doc,excel,design,other'],
            'size' => ['sometimes', 'string', 'max:50'],
        ]);

        $document->update($validated);

        return response()->json($document);
    }

    public function destroy(Request $request, Document $document): JsonResponse
    {
        abort_unless(
            ProjectAccess::canManage($request->user(), $document->project),
            403
        );

        $document->delete();

        return response()->json([
            'message' => 'Document deleted successfully.',
        ]);
    }
}