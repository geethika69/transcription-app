<?php

namespace App\Http\Controllers;

use App\Models\TranscriptionFile;
use App\Models\Segment;
use Illuminate\Http\Request;

class TranscriptionFileController extends Controller
{
    public function index()
    {
        $files = TranscriptionFile::withCount('segments')->orderByDesc('created_at')->get();
        return view('files.index', compact('files'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'json_file' => 'nullable|file|mimes:json|max:10240',
        ]);

        $file = TranscriptionFile::create(['name' => $request->name]);

        if ($request->hasFile('json_file')) {
            $content = file_get_contents($request->file('json_file')->getRealPath());
            $json = json_decode($content, true);

            if (json_last_error() !== JSON_ERROR_NONE || !is_array($json)) {
                return redirect()->back()->withErrors(['json_file' => 'Invalid JSON format.']);
            }

            foreach ($json as $data) {
                $data['transcription_file_id'] = $file->id;
                if (empty($data['speaker'])) $data['speaker'] = 'Speaker 1';
                if (!isset($data['status']) || !in_array($data['status'], ['new', 'translated', 'reviewed'])) {
                    $data['status'] = 'new';
                }
                if (!isset($data['translated_text'])) $data['translated_text'] = null;
                Segment::create($data);
            }
        }

        return redirect()->route('files.index')->with('success', 'Transcription file created successfully!');
    }

    public function show(Request $request, TranscriptionFile $file)
    {
        $query = $file->segments()->orderBy('start_time');
        if ($request->filled('status') && in_array($request->status, ['new', 'translated', 'reviewed'])) {
            $query->where('status', $request->status);
        }
        $segments = $query->get();

        return view('files.show', compact('file', 'segments'));
    }
}