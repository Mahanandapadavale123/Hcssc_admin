@extends('layouts.app')

@section('content')
    <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
        @include('components.breadcrumb', ['page' => 'Roles', 'page_name' => 'Roles'])
        <div class="d-flex my-xl-auto right-content align-items-center flex-wrap">
            <div class="mb-2">
                <a href="javascript:void(0);" class="btn btn-primary d-flex align-items-center" id="addRoleBtn">
                    <i class="ti ti-circle-plus me-2"></i>Add Role
                </a>
            </div>
        </div>
    </div>

    <!-- Roles Table -->
    <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between flex-wrap row-gap-3">
            <h5>Roles List</h5>
        </div>
        <div class="card-body p-0">
            <div class="custom-datatable-filter table-responsive">
                <table class="table datatable" id="rolesTable">
                    <thead class="thead-light">
                        <tr>
                            <th>Sl No</th>
                            <th>Role Name</th>
                            <th>Group Type</th>
                            <th>Created Date</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($roles as $role)
                            <tr data-id="{{ $role->id }}">
                                <td>{{ $loop->iteration }}</td>
                                <td class="role-name">{{ $role->name }}</td>
                                <td class="role-group">{{ $role->group_type ?? 'N/A' }}</td>
                                <td>{{ $role->created_at->format('d M Y') }}</td>
                                <td class="role-status">
                                    @if ($role->status == 'active')
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
                                        <a href="{{ url('roles/'. $role->id.'/permissions') }}" class="me-2" data-bs-toggle="tooltip" data-bs-original-title="Role Permissions">
                                            <i class="ti ti-shield"></i>
                                        </a>
                                        <a href="#" class="me-2 editRoleBtn" data-id="{{ $role->id }}"><i
                                                class="ti ti-edit"></i></a>
                                        <a href="#" class="deleteRoleBtn" data-id="{{ $role->id }}"><i
                                                class="ti ti-trash"></i></a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">No Role Added</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Add/Edit Role Modal -->
    <div class="modal fade" id="roleModal">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="roleModalTitle">Add Role</h4>
                    <button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal"><i
                            class="ti ti-x"></i></button>
                </div>
                <form id="roleForm">
                    @csrf
                    <input type="hidden" name="id" id="role_id">
                    <div class="modal-body pb-0">
                        <div class="mb-3">
                            <label class="form-label">Role Name</label>
                            <input type="text" class="form-control" name="name" id="role_name" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select class="form-select" name="status" id="role_status" required>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light me-2" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary" id="saveRoleBtn">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Confirm Modal -->
    <div class="modal fade" id="deleteModal">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body text-center">
                    <span class="avatar avatar-xl bg-transparent-danger text-danger mb-3">
                        <i class="ti ti-trash-x fs-36"></i>
                    </span>
                    <h4 class="mb-1">Confirm Delete</h4>
                    <p class="mb-3">Are you sure you want to delete this role?</p>
                    <div class="d-flex justify-content-center">
                        <button class="btn btn-light me-3" data-bs-dismiss="modal">Cancel</button>
                        <button class="btn btn-danger" id="confirmDeleteBtn">Yes, Delete</button>
                    </div>
                </div>
            </div>
        </div>
    </div>


@endsection



@push('scripts')
    <script>
        $(document).ready(function() {

            var deleteId = null;

            $('#addRoleBtn').click(function() {
                console.log("clicked")
                $('#roleForm')[0].reset();
                $('#role_id').val('');
                $('#roleModalTitle').text('Add Role');
                $('#roleModal').modal('show');
            });

            $(document).on('click', '.editRoleBtn', function() {
                var row = $(this).closest('tr');
                var id = $(this).data('id');
                var name = row.find('.role-name').text().trim();
                var group = row.find('.role-group').text().trim();
                var status = row.find('.badge-success').length ? 'active' : 'inactive';

                $('#role_id').val(id);
                $('#role_name').val(name);
                $('#role_status').val(status);
                $('#roleModalTitle').text('Edit Role');
                $('#roleModal').modal('show');
            });

            $('#roleForm').submit(function(e) {
                e.preventDefault();

                var id = $('#role_id').val();
                var url = id ? "{{ url('roles') }}/" + id : "{{ url('roles') }}";
                var type = id ? 'PUT' : 'POST';

                $.ajax({
                    url: url,
                    type: type,
                    data: $(this).serialize(),
                    success: function(res) {
                        $('#roleModal').modal('hide');
                        toastr.success(res.message || 'Saved successfully');
                        setTimeout(() => location.reload(), 1000);
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            var errors = xhr.responseJSON.errors;
                            $('#roleForm .text-danger').remove();

                            $.each(errors, function(field, messages) {
                                var input = $('#roleForm [name="' + field + '"]');
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

            $(document).on('click', '.deleteRoleBtn', function() {
                deleteId = $(this).data('id');
                $('#deleteModal').modal('show');
            });

            $('#confirmDeleteBtn').click(function() {
                if (!deleteId) return;
                $.ajax({
                    url: "{{ url('roles') }}/" + deleteId,
                    type: 'DELETE',
                    data: {
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(res) {
                        $('#deleteModal').modal('hide');
                        toastr.success(res.message || 'Deleted successfully');
                        $('tr[data-id="' + deleteId + '"]').remove();
                    },
                    error: function(xhr) {
                        $('#deleteModal').modal('hide');
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            toastr.error(xhr.responseJSON.message);
                        } else {
                            toastr.error('Delete failed.');
                        }
                    }
                });
            });
        });
    </script>
@endpush
