<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DocumentController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $documents = Document::query()
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
            'uploaded_by' => ['required', 'exists:users,id'],
        ]);

        $document = Document::create($validated);

        return response()->json($document, 201);
    }

    public function show(Document $document): JsonResponse
    {
        return response()->json($document);
    }

    public function update(Request $request, Document $document): JsonResponse
    {
        $validated = $request->validate([
            'project_id' => ['sometimes', 'exists:projects,id'],
            'name' => ['sometimes', 'string', 'max:255'],
            'file_path' => ['sometimes', 'string', 'max:255'],
            'type' => ['sometimes', 'in:pdf,doc,excel,design,other'],
            'size' => ['sometimes', 'string', 'max:50'],
            'uploaded_by' => ['sometimes', 'exists:users,id'],
        ]);

        $document->update($validated);

        return response()->json($document);
    }

    public function destroy(Document $document): JsonResponse
    {
        $document->delete();

        return response()->json([
            'message' => 'Document deleted successfully.',
        ]);
    }
}