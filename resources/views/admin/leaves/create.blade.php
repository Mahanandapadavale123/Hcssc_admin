@extends('layouts.app')

@section('content')
<div class="card">
    <div class="card-header">
        <h4>Apply for Leave</h4>
    </div>
    <div class="card-body">
        <form action="{{ route('leaves.store') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label>Leave Type</label>
                <select name="leave_type" class="form-select" required>
                    <option value="">Select Type</option>
                    <option value="Sick">Sick Leave</option>
                    <option value="Casual">Casual Leave</option>
                    <option value="Earned">Earned Leave</option>
                </select>
            </div>

            <div class="mb-3">
                <label>Start Date</label>
                <input type="date" name="start_date" class="form-control" required>
            </div>

            <div class="mb-3">
                <label>End Date</label>
                <input type="date" name="end_date" class="form-control" required>
            </div>

            <div class="mb-3">
                <label>Reason</label>
                <textarea name="reason" class="form-control" required></textarea>
            </div>

            <button type="submit" class="btn btn-primary">Submit</button>
        </form>
    </div>
</div>
@endsection
