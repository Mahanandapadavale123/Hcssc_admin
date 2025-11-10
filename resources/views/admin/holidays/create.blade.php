@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h3 class="mb-3">➕ Add Holiday</h3>

    <form action="{{ route('holidays.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label class="form-label">Holiday Title</label>
            <input type="text" name="title" class="form-control" value="{{ old('title') }}" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Holiday Date</label>
            <input type="date" name="date" class="form-control" value="{{ old('date') }}" required>
        </div>

        {{-- <div class="mb-3">
            <label class="form-label">Type</label>
            <select name="type" class="form-select">
                <option value="public">Public</option>
                <option value="optional">Optional</option>
            </select>
        </div> --}}

        <div class="mb-3">
            <label class="form-label">Description</label>
            <textarea name="description" class="form-control" rows="3">{{ old('description') }}</textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">Status</label>
            <select name="status" class="form-select">
                <option value="active" selected>Active</option>
                <option value="inactive">Inactive</option>
            </select>
        </div>

        <button type="submit" class="btn btn-success">Save Holiday</button>
        <a href="{{ route('holidays.index') }}" class="btn btn-secondary">Back</a>
    </form>
</div>
@endsection
