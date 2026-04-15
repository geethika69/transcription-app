<?php

namespace App\Http\Controllers;

use App\Models\Segment;
use App\Models\TranscriptionFile;
use Illuminate\Http\Request;

class SegmentController extends Controller
{
    public function store(Request $request, TranscriptionFile $file)
    {
        $validated = $request->validate([
            'speaker' => 'required|string|max:255',
            'start_time' => ['required', 'regex:/^([01]?[0-9]|2[0-3]):[0-5][0-9]:[0-5][0-9]$/'],
            'end_time' => ['required', 'regex:/^([01]?[0-9]|2[0-3]):[0-5][0-9]:[0-5][0-9]$/'],
            'source_text' => 'required|string',
            'translated_text' => 'nullable|string',
            'status' => 'required|in:new,translated,reviewed',
        ]);

        $startSec = Segment::timeToSeconds($validated['start_time']);
        $endSec = Segment::timeToSeconds($validated['end_time']);

        if ($endSec <= $startSec) {
            return back()->withErrors(['end_time' => 'End time must be greater than start time.'])->withInput();
        }

        if ($validated['status'] === 'reviewed' && empty(trim($validated['translated_text'] ?? ''))) {
            return back()->withErrors(['translated_text' => 'Translated text is required for reviewed status.'])->withInput();
        }

        $existingSegments = $file->segments()->get();
        $overlapped = [];
        foreach ($existingSegments as $existing) {
            if (Segment::overlaps($existing->start_time, $existing->end_time, $validated['start_time'], $validated['end_time'])) {
                $overlapped[] = $existing;
            }
        }

        if (count($overlapped) > 1) {
            return back()->withErrors(['general' => 'The new segment overlaps with more than one existing segment. Please adjust the times.'])->withInput();
        }

        if (count($overlapped) === 1) {
            $existing = $overlapped[0];
            $existing->end_time = $validated['end_time'];
            $existing->source_text = trim($existing->source_text . ' ' . $validated['source_text']);
            if (!empty($validated['translated_text'])) {
                $existing->translated_text = trim(($existing->translated_text ?? '') . ' ' . $validated['translated_text']);
            }
            $existing->save();
            return redirect()->route('files.show', $file)->with('success', 'Segment merged with existing one (overlap handled).');
        }

        $validated['transcription_file_id'] = $file->id;
        Segment::create($validated);

        return redirect()->route('files.show', $file)->with('success', 'Segment added successfully.');
    }

    public function destroy(TranscriptionFile $file, Segment $segment)
    {
        if ($segment->transcription_file_id !== $file->id) {
            abort(404);
        }
        $segment->delete();
        return redirect()->route('files.show', $file)->with('success', 'Segment deleted successfully.');
    }
}
