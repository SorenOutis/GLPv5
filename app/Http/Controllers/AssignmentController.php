<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AssignmentController extends Controller
{
    public function index()
    {
        $assignments = Assignment::orderBy('due_date', 'asc')
            ->get()
            ->map(fn ($assignment) => [
                'id' => $assignment->id,
                'title' => $assignment->title,
                'description' => $assignment->description,
                'file_path' => $assignment->file_path,
                'due_date' => $assignment->due_date,
                'category' => $assignment->category,
                'is_active' => $assignment->is_active,
                'created_at' => $assignment->created_at,
                'submission' => $assignment->submissions()
                    ->where('user_id', Auth::id())
                    ->first()
                    ?->only(['id', 'status', 'grade', 'feedback', 'file_path', 'created_at', 'notes', 'xp', 'updated_at']),
            ]);

        return Inertia::render('Assignment', [
            'assignments' => $assignments,
        ]);
    }

    public function upload(Request $request, Assignment $assignment)
    {
        // Debug logging
        \Log::info('Upload method called', [
            'assignment_id' => $assignment->id,
            'method' => $request->method(),
            'has_file' => $request->hasFile('file'),
            'user_id' => auth()->id(),
        ]);

        $request->validate([
            'file' => 'required|file|max:102400', // 100MB
            'notes' => 'nullable|string|max:1000',
        ]);

        $user = Auth::user();

        // Check if user already has a submission
        $submission = AssignmentSubmission::where('user_id', $user->id)
            ->where('assignment_id', $assignment->id)
            ->first();

        // Store the file
        $filePath = $request->file('file')->store('submissions', 'public');

        if ($submission) {
            // Update existing submission
            if ($submission->file_path) {
                Storage::disk('public')->delete($submission->file_path);
            }
            $submission->update([
                'file_path' => $filePath,
                'notes' => $request->input('notes'),
                'status' => 'submitted',
            ]);
        } else {
            // Create new submission
            $submission = AssignmentSubmission::create([
                'user_id' => $user->id,
                'assignment_id' => $assignment->id,
                'file_path' => $filePath,
                'notes' => $request->input('notes'),
                'status' => 'submitted',
            ]);
        }

        return redirect()->route('assignments')->with('success', 'Assignment submitted successfully!');
    }

    public function show($id)
    {
        $assignment = Assignment::findOrFail($id);
        $submission = AssignmentSubmission::where('user_id', Auth::id())
            ->where('assignment_id', $id)
            ->first();

        return Inertia::render('AssignmentDetail', [
            'assignment' => $assignment,
            'submission' => $submission,
        ]);
    }
}
