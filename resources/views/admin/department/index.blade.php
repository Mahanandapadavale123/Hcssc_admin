@extends('layouts.app')

@section('content')


    <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
        @include('components.breadcrumb', ['page' => 'Departments', 'page_name' => 'Departments'])
        <div class="d-flex my-xl-auto right-content align-items-center flex-wrap">
            <div class="mb-2">
                <a href="javascript:void(0);" class="btn btn-primary d-flex align-items-center" id="addDeptBtn">
                    <i class="ti ti-circle-plus me-2"></i>Add Department
                </a>
            </div>
        </div>
    </div>

    <!-- Roles Table -->
    <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between flex-wrap row-gap-3">
            <h5>Department List</h5>
        </div>
        <div class="card-body p-0">
            <div class="custom-datatable-filter table-responsive">
                <table class="table datatable" id="rolesTable">
                    <thead class="thead-light">
                        <tr>
                            <th>Sl No</th>
                            <th>Department Name</th>
                            <th>Created Date</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($departments as $dept)
                            <tr data-id="{{ $dept->id }}">
                                <td>{{ $loop->iteration }}</td>
                                <td class="dept-name">{{ $dept->dept_name }}</td>
                                <td>{{ $dept->created_at->format('d M Y') }}</td>
                                <td class="dept-status">
                                    @if ($dept->status == 'active')
                                        <span class="badge badge-success d-inline-flex align-items-center badge-xs">
                                            <i class="ti ti-point-filled me-1"></i> Active
                                        </span>
                                    @else
                                        <span class="badge badge-danger d-inline-flex align-items-center badge-xs">
                                            <i class="ti ti-point-filled me-1"></i> Inactive
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <div class="action-icon d-inline-flex">
                                        <a href="#" class="me-2 editDeptBtn" data-id="{{ $dept->id }}">
                                            <i class="ti ti-edit"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">No Department Added</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Add/Edit Role Modal -->
    <div class="modal fade" id="deptModal">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="deptModalTitle">Add Department</h4>
                    <button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal"><i
                            class="ti ti-x"></i></button>
                </div>
                <form id="deptForm">
                    @csrf
                    <input type="hidden" name="id" id="dept_id">
                    <div class="modal-body pb-0">
                        <div class="mb-3">
                            <label class="form-label">Department Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="dept_name" id="dept_name" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Status <span class="text-danger">*</span></label>
                            <select class="form-select" name="status" id="dept_status" required>

                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light me-2" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary" id="saveDeptBtn">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection



@push('scripts')
    <script>
        $(document).ready(function() {

            var deleteId = null;

            $('#addDeptBtn').click(function() {
                console.log("clicked")
                $('#deptForm')[0].reset();
                $('#dept_id').val('');
                $('#deptModalTitle').text('Add Department');
                $('#deptModal').modal('show');
            });

            $(document).on('click', '.editDeptBtn', function() {
                var row = $(this).closest('tr');
                var id = $(this).data('id');
                var name = row.find('.dept-name').text().trim();
                var group = row.find('.dept-group').text().trim();
                var status = row.find('.badge-success').length ? 'active' : 'inactive';

                $('#dept_id').val(id);
                $('#dept_name').val(name);
                $('#dept_status').val(status);
                $('#deptModalTitle').text('Edit Department');
                $('#deptModal').modal('show');
            });

            $('#deptForm').submit(function(e) {
                e.preventDefault();

                var id = $('#dept_id').val();
                var url = id ? "{{ url('departments') }}/" + id : "{{ url('departments') }}";
                var type = id ? 'PUT' : 'POST';

                $.ajax({
                    url: url,
                    type: type,
                    data: $(this).serialize(),
                    success: function(res) {
                        $('#deptModal').modal('hide');
                        toastr.success(res.message || 'Saved successfully');
                        setTimeout(() => location.reload(), 1000);
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            var errors = xhr.responseJSON.errors;
                            $('#deptForm .text-danger').remove();

                            $.each(errors, function(field, messages) {
                                var input = $('#deptForm [name="' + field + '"]');
                                if (input.length) {
                                    $(input).addClass('is-invalid');
                                    input.after('<span class="text-danger">' + messages[0] + '</span>');
                                }
                            });

                        } else {
                            toastr.error('Something went wrong. Please try again.');
                        }
                    }
                });
            });


        });
    </script>
@endpush
