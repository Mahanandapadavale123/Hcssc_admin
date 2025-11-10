@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h3 class="mb-3">✏️ Edit Holiday</h3>

    <form action="{{ route('holidays.update', $holiday->id) }}" method="POST">
        @csrf @method('PUT')

        <div class="mb-3">
            <label class="form-label">Holiday Title</label>
            <input type="text" name="title" class="form-control" value="{{ old('title', $holiday->title) }}" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Holiday Date</label>
            <input type="date" name="holiday_date" class="form-control" value="{{ old('holiday_date', $holiday->holiday_date) }}" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Type</label>
            <select name="type" class="form-select">
                <option value="public" {{ $holiday->type == 'public' ? 'selected' : '' }}>Public</option>
                <option value="optional" {{ $holiday->type == 'optional' ? 'selected' : '' }}>Optional</option>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Description</label>
            <textarea name="description" class="form-control" rows="3">{{ old('description', $holiday->description) }}</textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">Status</label>
            <select name="status" class="form-select">
                <option value="active" {{ $holiday->status == 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ $holiday->status == 'inactive' ? 'selected' : '' }}>Inactive</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Update Holiday</button>
        <a href="{{ route('holidays.index') }}" class="btn btn-secondary">Back</a>
    </form>
</div>
@endsection
