<?php

namespace App\Http\Controllers;

use App\Models\LessonLearned;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LessonLearnedController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $lessons = LessonLearned::query()
            ->when($request->filled('project_id'), fn ($query) =>
                $query->where('project_id', $request->project_id)
            )
            ->when($request->filled('category'), fn ($query) =>
                $query->where('category', $request->category)
            )
            ->latest('date')
            ->paginate(15);

        return response()->json($lessons);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'project_id' => ['required', 'exists:projects,id'],
            'title' => ['required', 'string', 'max:255'],
            'category' => ['required', 'in:process,technical,team,scope,quality'],
            'impact' => ['required', 'in:positive,negative'],
            'description' => ['nullable', 'string'],
            'date' => ['required', 'date'],
        ]);

        $lesson = LessonLearned::create($validated);

        return response()->json($lesson, 201);
    }

    public function show(LessonLearned $lessonLearned): JsonResponse
    {
        return response()->json($lessonLearned);
    }

    public function update(Request $request, LessonLearned $lessonLearned): JsonResponse
    {
        $validated = $request->validate([
            'project_id' => ['sometimes', 'exists:projects,id'],
            'title' => ['sometimes', 'string', 'max:255'],
            'category' => ['sometimes', 'in:process,technical,team,scope,quality'],
            'impact' => ['sometimes', 'in:positive,negative'],
            'description' => ['sometimes', 'nullable', 'string'],
            'date' => ['sometimes', 'date'],
        ]);

        $lessonLearned->update($validated);

        return response()->json($lessonLearned);
    }

    public function destroy(LessonLearned $lessonLearned): JsonResponse
    {
        $lessonLearned->delete();

        return response()->json([
            'message' => 'Lesson learned deleted successfully.',
        ]);
    }
}