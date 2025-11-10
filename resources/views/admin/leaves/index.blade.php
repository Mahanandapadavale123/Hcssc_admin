@extends('layouts.app')

@section('content')
    <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
        @include('components.breadcrumb', [
            'page' => 'Leaves',
            'base' => 'Employee',
            'page_name' => 'Leaves',
        ])

        <div class="d-flex my-xl-auto right-content align-items-center flex-wrap">
            <div class="mb-2">
                <a href="javascript:void(0)" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addLeaveModal">
                    <i class="fa fa-plus"></i> Add Leave
                </a>
            </div>
        </div>
    </div>

    <!-- Leaves Info -->
    <div class="row">
        @php
            $colors = ['bg-black-le', 'bg-blue-le', 'bg-purple-le', 'bg-pink-le'];
            $icons = ['ti-calendar-event', 'ti-vaccine', 'ti-hexagon-letter-c', 'ti-hexagonal-prism-plus'];
        @endphp

        @foreach ($leaveTypes as $index => $type)
            @php
                $color = $colors[$index % count($colors)];
                $icon = $icons[$index % count($icons)];
                $remaining = $remainingLeaves[$type->id] ?? $type->total_days;
            @endphp

            <div class="col-xl-3 col-md-6">
                <div class="card {{ $color }}">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="text-start">
                                <p class="mb-1">{{ $type->name }}</p>
                                <h4>{{ $type->total_days }}</h4>
                            </div>
                            <div class="d-flex">
                                <div class="flex-shrink-0 me-2">
                                    <span class="avatar avatar-md d-flex">
                                        <i class="ti {{ $icon }} fs-32"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <span class="badge bg-light text-dark">
                            Remaining Leaves: {{ $remainingLeaves[$type->id] ?? $type->total_days }}
                        </span>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    <!-- /Leaves Info -->

    <div class="card">
        <div class="card-body">
            <table class="table table-bordered text-center">
                <thead>
                    <tr>
                        <th>SL</th>
                        <th>Leave Type</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Status</th>
                        <th>Approved By</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($leaves as $index => $leave)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $leave->leavetype->name ?? '-' }}</td>
                            <td>{{ $leave->start_date }}</td>
                            <td>{{ $leave->end_date }}</td>
                            <td>
                                <span
                                    class="badge
                                    {{ $leave->status == 'approved'
                                        ? 'badge-success'
                                        : ($leave->status == 'pending'
                                            ? 'badge-warning'
                                            : 'badge-danger') }}">
                                    {{ ucfirst($leave->status) }}
                                </span>
                            </td>
                            <td>
                                {{ $leave->approver->name ?? '-' }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Add Leave Modal -->
    <div class="modal fade" id="addLeaveModal" tabindex="-1" aria-labelledby="addLeaveModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form id="leaveForm" action="{{ route('leaves.store') }}" method="POST">
                    @csrf
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="addLeaveModalLabel">Add Leave</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="leave_type_id" class="form-label">Leave Type</label>
                                <select name="leave_type" id="leave_type" class="form-select" required>
                                    <option value="">Select Leave Type</option>
                                    @foreach ($leaveTypes as $type)
                                        @php
                                            $remaining = $remainingLeaves[$type->id] ?? $type->total_days;
                                        @endphp
                                        <option value="{{ $type->id }}" data-remaining="{{ $remaining }}">
                                            {{ $type->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <small id="remainingText" class="text-muted d-block mt-1"></small>
                            </div>

                            <div class="mb-3">
                                <label for="reason" class="form-label">Reason</label>
                                <input type="text" name="reason" class="form-control" required>
                            </div>

                            <div class="mb-3">
                                <label for="start_date" class="form-label">Start Date</label>
                                <input type="date" name="start_date" class="form-control" required>
                            </div>

                            <div class="mb-3">
                                <label for="end_date" class="form-label">End Date</label>
                                <input type="date" name="end_date" class="form-control" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">Save Leave</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {


            $('#leave_type').on('change', function() {
                const remaining = $(this).find(':selected').data('remaining');
                if (remaining !== undefined) {
                    $('#remainingText').text('Remaining Leaves: ' + remaining);
                } else {
                    $('#remainingText').text('');
                }
            });


            $('#leaveForm').on('submit', function(e) {
                e.preventDefault();

                $.ajax({
                    url: $(this).attr('action'),
                    method: 'POST',
                    data: $(this).serialize(),
                    success: function(res) {
                        if (res.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: res.message,
                                timer: 2000,
                                showConfirmButton: false
                            });
                            location.reload();
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops!',
                                text: res.message,
                            });
                        }
                    },
                    error: function(err) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'Something went wrong.',
                        });
                        console.log(err);
                    }
                });
            });


            @if (session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: '{{ session('success') }}',
                    timer: 2000,
                    showConfirmButton: false
                });
            @endif

            @if (session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Oops!',
                    text: '{{ session('error') }}',
                    timer: 2000,
                    showConfirmButton: false
                });
            @endif
        });
    </script>
@endpush
