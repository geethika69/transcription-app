@extends('layout')
@section('content')
<div class="d-flex justify-content-between align-items-center">
    <h1>File: <span class="text-primary">{{ $file->name }}</span></h1>
    <a href="{{ route('files.index') }}" class="btn btn-secondary">← Back to Files</a>
</div>

@if (session('success'))
    <div class="alert alert-success mt-3">{{ session('success') }}</div>
@endif
@if ($errors->any())
    <div class="alert alert-danger"><ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
@endif

<h3 class="mt-4">Add New Segment</h3>
<form method="POST" action="{{ route('segments.store', $file) }}" class="mb-5 border p-4 bg-white rounded">
    @csrf
    <div class="row g-3">
        <div class="col-md-3"><label class="form-label">Speaker</label><input type="text" name="speaker" class="form-control" value="Speaker 1" required></div>
        <div class="col-md-3"><label class="form-label">Start Time</label><input type="text" name="start_time" class="form-control" placeholder="00:00:05" required></div>
        <div class="col-md-3"><label class="form-label">End Time</label><input type="text" name="end_time" class="form-control" placeholder="00:00:09" required></div>
        <div class="col-md-6"><label class="form-label">Source Text</label><textarea name="source_text" class="form-control" rows="2" required></textarea></div>
        <div class="col-md-6"><label class="form-label">Translated Text</label><textarea name="translated_text" class="form-control" rows="2"></textarea></div>
        <div class="col-12"><label class="form-label">Status</label>
            <select name="status" class="form-select" required>
                <option value="new">New</option>
                <option value="translated">Translated</option>
                <option value="reviewed">Reviewed</option>
            </select>
        </div>
    </div>
    <button type="submit" class="btn btn-success mt-3">Add Segment</button>
</form>

<h3>Segments</h3>
<form method="GET" action="{{ route('files.show', $file) }}" class="mb-3">
    <div class="input-group w-50">
        <span class="input-group-text">Filter by status:</span>
        <select name="status" class="form-select" onchange="this.form.submit()">
            <option value="">All</option>
            <option value="new" {{ request('status')==='new'?'selected':'' }}>New</option>
            <option value="translated" {{ request('status')==='translated'?'selected':'' }}>Translated</option>
            <option value="reviewed" {{ request('status')==='reviewed'?'selected':'' }}>Reviewed</option>
        </select>
    </div>
</form>

<table class="table table-striped table-bordered">
    <thead><tr><th>Speaker</th><th>Start</th><th>End</th><th>Source Text</th><th>Translated Text</th><th>Status</th><th>Actions</th></tr></thead>
    <tbody>
        @forelse ($segments as $segment)
        <tr>
            <td>{{ $segment->speaker }}</td>
            <td>{{ $segment->start_time }}</td>
            <td>{{ $segment->end_time }}</td>
            <td style="max-width:300px;">{{ $segment->source_text }}</td>
            <td style="max-width:300px;">{{ $segment->translated_text ?? '-' }}</td>
            <td><span class="badge @if($segment->status==='reviewed')bg-success @elseif($segment->status==='translated')bg-info @else bg-secondary @endif">{{ ucfirst($segment->status) }}</span></td>
            <td>
                <form method="POST" action="{{ route('segments.destroy', [$file, $segment]) }}" onsubmit="return confirm('Delete?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                </form>
            </td>
        </tr>
        @empty
        <tr><td colspan="7" class="text-center">No segments yet. Add one above!</td></tr>
        @endforelse
    </tbody>
</table>
@endsection