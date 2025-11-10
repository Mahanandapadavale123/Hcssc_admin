@extends('layouts.app')

@section('content')
    <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
        @include('components.breadcrumb', [
            'page' => 'Leaves',
            'base' => 'Admin',
            'page_name' => 'Leave Requests',
        ])
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">

                <div class="container mt-4">
                    <h4 class="mb-4">Leave Requests </h4>

                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>Sl</th>
                                <th>Employee</th>
                                <th>Type</th>
                                <th>Days</th>
                                <th>Status</th>
                                <th>Reason</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($leaves as $leave)
                                <tr>
                                    <td>{{ $leave->id }}</td>
                                    <td>{{ $leave->employee->name ?? 'N/A' }}</td>
                                    <td>{{ $leave->leave_type }}</td>
                                    <td>{{ $leave->days }}</td>
                                    <td>
                                        <span class="badge
                                                @if ($leave->status == 'pending') bg-warning
                                                @elseif($leave->status == 'approved') bg-success
                                                @else bg-danger @endif">
                                                {{ ucfirst($leave->status) }}
                                        </span>
                                    </td>
                                    <td>{{ $leave->reason }}</td>
                                    <td>
                                        @if ($leave->status == 'pending')
                                            <button class="btn btn-success btn-sm approve-btn" data-id="{{ $leave->id }}">
                                                Approve
                                            </button>
                                            <button class="btn btn-danger btn-sm reject-btn" data-id="{{ $leave->id }}">
                                                Reject
                                            </button>
                                        @else
                                            <em>No Action</em>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endsection

            @push('scripts')
                <script>
                    $(document).ready(function() {

                        //  Approve confirmation
                        $('.approve-btn').on('click', function() {
                            const leaveId = $(this).data('id');

                            Swal.fire({
                                title: 'Are you sure?',
                                text: 'You want to approve this leave?',
                                icon: 'question',
                                showCancelButton: true,
                                confirmButtonText: 'Yes, approve it!',
                                cancelButtonText: 'Cancel',
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    $.ajax({
                                        url: `/leaves/${leaveId}/approve`,
                                        method: 'POST',
                                        data: {
                                            _token: '{{ csrf_token() }}'
                                        },
                                        success: function(res) {
                                            Swal.fire({
                                                icon: 'success',
                                                title: 'Approved!',
                                                text: res.message ||
                                                    'Leave has been approved.',
                                                timer: 1500,
                                                showConfirmButton: false
                                            });
                                            setTimeout(() => location.reload(), 1500);
                                        },
                                        error: function(err) {
                                            Swal.fire({
                                                icon: 'error',
                                                title: 'Error!',
                                                text: 'Something went wrong.'
                                            });
                                        }
                                    });
                                }
                            });
                        });

                        // Reject with reason
                        $('.reject-btn').on('click', function() {
                            const leaveId = $(this).data('id');

                            Swal.fire({
                                title: 'Reject Reason',
                                input: 'textarea',
                                inputLabel: 'Please enter reason for rejection',
                                inputPlaceholder: 'Type your reason...',
                                showCancelButton: true,
                                confirmButtonText: 'Reject',
                                confirmButtonColor: '#d33',
                                inputValidator: (value) => {
                                    if (!value) return 'You need to write a reason!';
                                }
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    $.ajax({
                                        url: `/leaves/${leaveId}/reject`,
                                        method: 'POST',
                                        data: {
                                            _token: '{{ csrf_token() }}',
                                            reject_reason: result.value
                                        },
                                        success: function(res) {
                                            Swal.fire({
                                                icon: 'success',
                                                title: 'Rejected!',
                                                text: res.message ||
                                                    'Leave has been rejected.',
                                                timer: 1500,
                                                showConfirmButton: false
                                            });
                                            setTimeout(() => location.reload(), 1500);
                                        },
                                        error: function(err) {
                                            Swal.fire({
                                                icon: 'error',
                                                title: 'Error!',
                                                text: 'Something went wrong.'
                                            });
                                        }
                                    });
                                }
                            });
                        });

                    });
                </script>
            @endpush
