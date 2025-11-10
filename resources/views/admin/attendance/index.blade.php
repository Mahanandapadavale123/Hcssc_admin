@extends('layouts.app')
@section('content')

    <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
        @include('components.breadcrumb', [
            'page' => 'Attendance',
            'base' => 'Employee',
            'page_name' => 'Attendance',
        ])

    </div>


    <div class="d-flex justify-content-center align-items-center">
        <div class="col-xl-3 col-lg-4">
            <div class="card text-center shadow-lg">
                <div class="card-body">
                    <div class="mb-3">
                        Good {{ now()->format('A') == 'AM' ? 'Morning' : 'Evening' }}, {{ Auth::user()->name }}
                        <h4>{{ now()->format('h:i A, d M Y') }}</h4>
                    </div>

                    <div class="attendance-circle-progress mx-auto mb-3" data-value="65">
                        <span class="progress-left">
                            <span class="progress-bar border-success"></span>
                        </span>
                        <span class="progress-right">
                            <span class="progress-bar border-success"></span>
                        </span>
                        <div class="avatar avatar-xxl avatar-rounded">
                            <img src="{{ asset('admin/img/profiles/avatar-27.jpg') }}" alt="Img">
                        </div>
                    </div>

                    <div class="badge badge-md badge-primary mb-3">
                        Production: {{ $totalHoursToday ?? '0' }} hrs
                    </div>

                    @if (!$todayAttendance)
                        <!-- No check-in yet -->
                        <form action="{{ route('admin.attendance.checkIn') }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-success">Check In</button>
                        </form>
                    @elseif($todayAttendance && !$todayAttendance->check_out)
                        <!-- Checked in but not checked out -->
                        <form action="{{ route('admin.attendance.checkOut') }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-danger">Check Out</button>
                        </form>
                        {{-- <form action="{{ route('admin.attendance.startBreak') }}" method="POST" class="mt-2">
                            @csrf
                            <button type="submit" class="btn btn-warning"> Break In</button>
                        </form>
                        <form action="{{ route('admin.attendance.endBreak') }}" method="POST" class="mt-2">
                            @csrf
                            <button type="submit" class="btn btn-info">Break Out</button>
                        </form> --}}
                    @else
                        <!-- Already checked out -->
                        <p class="text-success">You’ve already checked out today.</p>
                    @endif

                </div>
            </div>
        </div>
    </div>


    <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between flex-wrap row-gap-3">
            <h5>Employee Attendance</h5>
            <div class="d-flex my-xl-auto right-content align-items-center flex-wrap row-gap-3">
                <div class="me-3">
                    <div class="dropdown-toggle btn btn-white d-inline-flex align-items-center">
                        <form id="monthForm" method="GET" action="{{ route('admin.attendance.index') }}" class="mb-3">
                            <input type="month" id="month" name="month"
                                value="{{ request('month') ?? now()->format('Y-m') }}" class="form-control"
                                style="width: 200px; display: inline-block;">
                        </form>
                        <i class="ti ti-chevron-down"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="custom-datatable-filter table-responsive">
                <table class="table datatable">
                    <thead class="thead-light">
                        <tr>
                            <th>Date</th>
                            <th>Check In</th>
                            <th>Status</th>
                            <th>Check Out</th>
                            <th>Break</th>
                            <th>Late</th>
                            <th>Production Hours</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($data as $row)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($row['date'])->format('d M Y') }}</td>

                                <td>
                                    {{ isset($row['check_in']) ? date('h:i A', strtotime($row['check_in'])) : '-' }}
                                </td>

                                <td>
                                    @if ($row['status'] == 'Absent')
                                        <span class="badge badge-danger-transparent">
                                            <i class="ti ti-point-filled me-1"></i> Absent
                                        </span>
                                    @elseif ($row['status'] == 'Checkout Missed')
                                        <span class="badge badge-warning-transparent text-dark">
                                            <i class="ti ti-point-filled me-1"></i> Checkout Missed
                                        </span>
                                    @else
                                        <span class="badge badge-success-transparent">
                                            <i class="ti ti-point-filled me-1"></i> Present
                                        </span>
                                    @endif
                                </td>

                                <td>
                                    {{ isset($row['check_out']) ? date('h:i A', strtotime($row['check_out'])) : '-' }}
                                </td>

                                <td>{{ $row['break'] ?? '-' }}</td>
                                <td>-</td>


                                <td>
                                    <span class="badge badge-success d-inline-flex align-items-center">
                                        <i class="ti ti-clock-hour-11 me-1"></i>
                                        {{ $row['working_hours'] ?? '0.00' }} Hrs
                                    </span>
                                </td>
                            </tr>
                        @endforeach

                    </tbody>
                </table>
            </div>
        </div>
    </div>

    </div>

    </div>
    <!-- /Page Wrapper -->

    @component('components.modal-popup')
    @endcomponent
@endsection

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    // When month changes, auto-submit the form
    document.getElementById('month').addEventListener('change', function() {
        document.getElementById('monthForm').submit();
    });
</script>

@if (session('success'))
    <script>
        Swal.fire({
            title: "Success!",
            text: "{{ session('success') }}",
            icon: "success",
            confirmButtonText: "OK"
        });
    </script>
@endif

@if (session('error'))
    <script>
        Swal.fire({
            title: "Oops!",
            text: "{{ session('error') }}",
            icon: "error",
            confirmButtonText: "OK"
        });
    </script>
@endif
