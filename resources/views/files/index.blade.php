@extends('layout')
@section('content')
<h1 class="mb-4">Transcription Files</h1>

@if (session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif
@if ($errors->any())
    <div class="alert alert-danger"><ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
@endif

<h3>Create New File Manually</h3>
<form method="POST" action="{{ route('files.store') }}" class="mb-5">
    @csrf
    <div class="input-group">
        <input type="text" name="name" class="form-control" placeholder="Enter file name" required>
        <button type="submit" class="btn btn-primary">Create Manually</button>
    </div>
</form>

<h3>Or Upload JSON File with Segments</h3>
<form method="POST" action="{{ route('files.store') }}" enctype="multipart/form-data" class="mb-5">
    @csrf
    <div class="row">
        <div class="col-md-6"><input type="text" name="name" class="form-control" placeholder="File name for upload" required></div>
        <div class="col-md-6"><input type="file" name="json_file" accept=".json" class="form-control"></div>
    </div>
    <button type="submit" class="btn btn-primary mt-3">Upload JSON & Create File</button>
</form>

<h2 class="mt-5">Your Files</h2>
@if ($files->isEmpty())
    <p>No files yet. Create one above!</p>
@else
    <table class="table table-hover">
        <thead class="table-dark"><tr><th>Name</th><th>Created At</th><th>Segments</th><th>Action</th></tr></thead>
        <tbody>
            @foreach ($files as $file)
            <tr>
                <td>{{ $file->name }}</td>
                <td>{{ $file->created_at->format('Y-m-d H:i') }}</td>
                <td>{{ $file->segments_count }}</td>
                <td><a href="{{ route('files.show', $file) }}" class="btn btn-info btn-sm">Open & Manage</a></td>
            </tr>
            @endforeach
        </tbody>
    </table>
@endif
@endsection