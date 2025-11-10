@extends('layouts.app')

@section('content')

<div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
    @include('components.breadcrumb', ['page' => 'Employees', 'page_name' => 'Employees'])
    <div class="d-flex my-xl-auto right-content align-items-center flex-wrap">
        <div class="mb-2">
            <a href="{{ route('employees.create') }}" class="btn btn-primary d-flex align-items-center">
                <i class="ti ti-circle-plus me-2"></i>Add Employee
            </a>
        </div>
    </div>
</div>

@include('components.toastr')

<div class="card">
    <div class="card-header d-flex align-items-center justify-content-between flex-wrap row-gap-3">
        <h5>Employees List</h5>
    </div>
    <div class="card-body p-2">
        <div class="custom-datatable-filter table-responsive">
            <table class="table empDatatable">
                <thead class="thead-light">
                    <tr>
                        <th>Sl No</th>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Employee Code</th>
                        <th>Role</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($employees as $index => $employee)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $employee->first_name }}</td>
                            <td>{{ $employee->first_name }}</td>
                            <td>{{ $employee->emp_code }}</td>
                            <td>
                                @if($employee->user->roles->count())
                                    {{ $employee->user->roles->pluck('name')->join(', ') }}
                                @else
                                    N/A
                                @endif
                            </td>
                            <td>{{ $employee->user->email ?? '' }}</td>
                            <td>{{ $employee->user->phone ?? '' }}</td>
                            <td>
                                @if($employee->emp_status == 'active')
                                    <span class="badge badge-success">Active</span>
                                @else
                                    <span class="badge badge-danger">{{ ucfirst($employee->emp_status) }}</span>
                                @endif
                            </td>
                            <td>
                                <div class="action-icon d-inline-flex">
                                    <a href="{{ route('employees.edit', $employee->id) }}" class="me-2">
                                        <i class="ti ti-edit"></i>
                                    </a>
                                    <form action="{{ route('employees.destroy', $employee->id) }}" method="POST" class="delete-employee-form" style="display:inline-block;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn btn-link p-0 m-0 delete-btn">
                                            <i class="ti ti-trash text-danger"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>

                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('.empDatatable').DataTable({
                pageLength: 10,
                ordering: true,
                searching: true
            });

            $('.delete-btn').on('click', function(e) {
                e.preventDefault();
                let form = $(this).closest('form');

                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    </script>
@endpush

