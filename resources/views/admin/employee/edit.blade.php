@extends('layouts.app')

@section('content')
<div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
    @include('components.breadcrumb', [
        'page' => 'Employees',
        'base' => 'Employees',
        'base_url' => route('employees.index'),
        'page_name' => 'Edit Employee',
    ])
</div>

<div class="row">
    <div class="col-md-12">

        @include('components.toastr')

        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Edit Employee</h5>
            </div>

            <div class="card-body">

                <form action="{{ route('employees.update', $employee->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <h5 class="mb-3">Account Information</h5>

                    <div class="row">
                        <div class="col-xl-4">
                            <div class="mb-3">
                                <label class="form-label">First Name <span class="text-danger">*</span></label>
                                <input type="text" name="first_name" value="{{ old('first_name', $employee->first_name ?? '') }}" class="form-control @error('first_name') is-invalid @enderror">
                                @error('first_name') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Last Name <span class="text-danger">*</span></label>
                                <input type="text" name="last_name" value="{{ old('last_name', $employee->last_name ?? '') }}" class="form-control @error('last_name') is-invalid @enderror">
                                @error('last_name') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Emp. Code <span class="text-danger">*</span></label>
                                <input type="text" name="emp_code" value="{{ old('emp_code', $employee->emp_code) }}" class="form-control text-uppercase @error('emp_code') is-invalid @enderror">
                                @error('emp_code') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                        </div>

                        <div class="col-xl-4">
                            <div class="mb-3">
                                <label class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" name="email" value="{{ old('email', $employee->user->email ?? '') }}" class="form-control @error('email') is-invalid @enderror">
                                @error('email') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Phone <span class="text-danger">*</span></label>
                                <input type="text" name="phone" value="{{ old('phone', $employee->user->phone ?? '') }}" class="form-control @error('phone') is-invalid @enderror">
                                @error('phone') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Date of Joining <span class="text-danger">*</span></label>
                                <input type="text" name="date_of_joining" value="{{ old('date_of_joining', $employee->date_of_joining ? \Carbon\Carbon::parse($employee->date_of_joining)->format('d-m-Y') : '') }}"
                                    class="form-control datepicker @error('date_of_joining') is-invalid @enderror" placeholder="DD/MM/YYYY">
                                @error('date_of_joining') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                        </div>

                        <div class="col-xl-4">
                            <div class="mb-3">
                                <label class="form-label">Password </label>
                                <input type="password" name="password" class="form-control @error('password') is-invalid @enderror">
                                @error('password') <small class="text-danger">{{ $message }}</small> @enderror
                                <small class="text-muted">Leave blank if you don't want to change the password.</small>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Status <span class="text-danger">*</span> </label>
                                <select name="status" class="form-select select @error('status') is-invalid @enderror">
                                    <option selected readonly disabled> Select Status</option>
                                    <option value="active" {{ old('status', $employee->user->status ?? '') == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ old('status', $employee->user->status ?? '') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                </select>
                                @error('status') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                        </div>
                    </div>

                    <hr>

                    <h5 class="mb-3">Employee Details</h5>

                    <div class="row">
                        <div class="col-xl-4">
                            <div class="mb-3">
                                <label class="form-label">Department <span class="text-danger">*</span>  </label>
                                <select name="dept_id" class="form-select select @error('dept_id') is-invalid @enderror">
                                    <option value readonly>Select Department</option>
                                    @foreach($departments as $dept)
                                        <option value="{{ $dept->id }}" {{ old('dept_id', $employee->dept_id) == $dept->id ? 'selected' : '' }}>
                                            {{ $dept->dept_name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('dept_id') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Select Role <span class="text-danger">*</span>  </label>
                                <select name="role_id" class="form-select select @error('role_id') is-invalid @enderror">
                                    <option value readonly>Select Role</option>
                                    @foreach($roles as $role)
                                        <option value="{{ $role->id }}" {{ old('role_id', $employee->user->role) == $role->name ? 'selected' : '' }}>
                                            {{ $role->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('role_id') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Designation</label>
                                <input type="text" name="designation" value="{{ old('designation', $employee->designation) }}" class="form-control @error('designation') is-invalid @enderror">
                                @error('designation') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>

                        </div>

                        <div class="col-xl-4">
                            <div class="mb-3">
                                <label class="form-label">Employee Type <span class="text-danger">*</span> </label>
                                <select name="emp_type" class="form-select select @error('emp_type') is-invalid @enderror">
                                    @foreach(['full_time', 'part_time', 'contract', 'intern'] as $type)
                                        <option value="{{ $type }}" {{ old('emp_type', $employee->emp_type) == $type ? 'selected' : '' }}>
                                            {{ ucwords(str_replace('_', ' ', $type)) }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('emp_type') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Gender <span class="text-danger">*</span></label>
                                <select name="gender" class="form-select select @error('gender') is-invalid @enderror">
                                    <option value="">Select</option>
                                    @foreach(['male', 'female', 'other'] as $gender)
                                        <option value="{{ $gender }}" {{ old('gender', $employee->gender) == $gender ? 'selected' : '' }}>
                                            {{ ucfirst($gender) }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('gender') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Date of Birth</label>
                                <input type="text" name="date_of_birth" value="{{ old('date_of_birth', $employee->date_of_birth ? \Carbon\Carbon::parse($employee->date_of_birth)->format('d-m-Y') : '') }}" class="form-control datepicker @error('date_of_birth') is-invalid @enderror">
                                @error('date_of_birth') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                        </div>

                        <div class="col-xl-4">
                            <div class="mb-3">
                                <label class="form-label">Blood Group</label>
                                <select name="blood_group" class="form-select select @error('blood_group') is-invalid @enderror">
                                    <option value selected disabled>Select</option>
                                    @foreach(['A+','A-','B+','B-','AB+','AB-','O+','O-'] as $bg)
                                        <option value="{{ $bg }}" {{ old('blood_group', $employee->blood_group) == $bg ? 'selected' : '' }}>{{ $bg }}</option>
                                    @endforeach
                                </select>
                                @error('blood_group') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Marital Status</label>
                                <select name="marital_status" class="form-select select @error('marital_status') is-invalid @enderror">
                                    <option disabled selected value>Select</option>
                                    @foreach(['single','married','divorced','widowed'] as $status)
                                        <option value="{{ $status }}" {{ old('marital_status', $employee->marital_status) == $status ? 'selected' : '' }}>{{ ucfirst($status) }}</option>
                                    @endforeach
                                </select>
                                @error('marital_status') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Employee Status <span class="text-danger">*</span>  </label>
                                <select name="emp_status" class="form-select select @error('emp_status') is-invalid @enderror">
                                    <option value disabled>Select</option>
                                    @foreach(['active','probation','resigned','terminated','inactive'] as $status)
                                        <option value="{{ $status }}" {{ old('emp_status', $employee->emp_status) == $status ? 'selected' : '' }}>{{ ucfirst($status) }}</option>
                                    @endforeach
                                </select>
                                @error('emp_status') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                        </div>

                        <div class="col-xl-12">
                            <div class="mb-3">
                                <label class="form-label">Full Address</label>
                                <input type="text" name="full_address" value="{{ old('full_address', $employee->full_address) }}" class="form-control">
                            </div>
                        </div>
                    </div>

                    <hr>
                    <h5 class="mb-3">Bank Information</h5>

                    <div class="row">
                        <div class=" col-3 mb-3">
                            <label class="form-label">Bank Account No</label>
                            <input type="text" name="bank_account_no" value="{{ old('bank_account_no', $employee->bank_account_no) }}" class="form-control @error('bank_account_no') is-invalid @enderror">
                            @error('bank_account_no') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                        <div class=" col-3 mb-3">
                            <label class="form-label">IFSC Code</label>
                            <input type="text" name="ifsc_code" value="{{ old('ifsc_code', $employee->ifsc_code) }}" class="form-control text-uppercase @error('ifsc_code') is-invalid @enderror">
                            @error('ifsc_code') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                        <div class=" col-3 mb-3">
                            <label class="form-label">Bank Name</label>
                            <input type="text" name="bank_name" value="{{ old('bank_name', $employee->bank_name) }}" class="form-control text-uppercase @error('bank_name') is-invalid @enderror">
                            @error('bank_name') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                        <div class=" col-3 mb-3">
                            <label class="form-label">PAN No</label>
                            <input type="text" name="pan_no" value="{{ old('pan_no', $employee->pan_no) }}" class="form-control text-uppercase @error('pan_no') is-invalid @enderror">
                            @error('pan_no') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                    </div>

                    <div class="text-end mt-4">
                        <button type="submit" class="btn btn-primary">Update Employee</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
@if ($errors->any())
    <script>
        $(function() {
            $('.form-select.is-invalid').each(function() {
                $(this).next('.select2').find('.select2-selection').addClass('is-invalid');
            });
        });
    </script>
@endif

<script>
   $(document).ready(function() {
    flatpickr(".datepicker", {
        dateFormat: "d-m-Y",
        maxDate: "today",
        defaultDate: "",
    });
});
</script>
@endpush
