@extends('layouts.app')

@section('content')
    <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
        @include('components.breadcrumb', ['page' => 'Qualifications', 'page_name' => 'Master Qualifications'])
        <div class="d-flex my-xl-auto right-content align-items-center flex-wrap">
            <div class="mb-2">
                <a href="javascript:void(0);" class="btn btn-primary d-flex align-items-center" id="addQualificationBtn">
                    <i class="ti ti-circle-plus me-2"></i>Add Qualification
                </a>
            </div>
        </div>
    </div>

    <!-- Qualifications Table -->
    <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between flex-wrap row-gap-3">
            <h5>Qualifications List</h5>
        </div>
        <div class="card-body p-0">
            <div class="custom-datatable-filter table-responsive">
                <table class="table datatable" id="qualificationTable">
                    <thead class="thead-light">
                        <tr>
                            <th>Sl No</th>
                            <th>Name</th>
                            <th>Code</th>
                            <th>Sub Section</th>
                            <th>Status</th>
                            <th>Created Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($qualifications as $qualification)
                            <tr data-id="{{ $qualification->id }}">
                                <td>{{ $loop->iteration }}</td>
                                <td class="mq-name">{{ $qualification->mq_name }}</td>
                                <td class="mq-code">{{ $qualification->mq_code }}</td>
                                <td class="mq-sub">{{ $qualification->mq_sub_section }}</td>
                                <td class="mq-status">
                                    @if ($qualification->status == 'active')
                                        <span class="badge badge-success d-inline-flex align-items-center badge-xs">
                                            <i class="ti ti-point-filled me-1"></i> Active
                                        </span>
                                    @else
                                        <span class="badge badge-danger d-inline-flex align-items-center badge-xs">
                                            <i class="ti ti-point-filled me-1"></i> Inactive
                                        </span>
                                    @endif
                                </td>
                                <td>{{ $qualification->created_at->format('d M Y') }}</td>
                                <td>
                                    <div class="action-icon d-inline-flex">
                                        <a href="#" class="me-2 editQualificationBtn" data-id="{{ $qualification->id }}">
                                            <i class="ti ti-edit"></i>
                                        </a>

                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">No Qualification Added</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Add/Edit Qualification Modal -->
    <div class="modal fade" id="qualificationModal">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="qualificationModalTitle">Add Qualification</h4>
                    <button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal"><i
                            class="ti ti-x"></i></button>
                </div>
                <form id="qualificationForm">
                    @csrf
                    <input type="hidden" name="id" id="qualification_id">
                    <div class="modal-body pb-0">
                        <div class="mb-3">
                            <label class="form-label">Qualification Name</label>
                            <input type="text" class="form-control" name="mq_name" id="mq_name" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Code</label>
                            <input type="text" class="form-control" name="mq_code" id="mq_code" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Sub Section</label>
                            <input type="text" class="form-control" name="mq_sub_section" id="mq_sub_section" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select class="form-select" name="status" id="mq_status" required>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light me-2" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary" id="saveQualificationBtn">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Modal -->

@endsection


@push('scripts')
    <script>
        $(document).ready(function() {
            var deleteId = null;

            // Add Qualification
            $('#addQualificationBtn').click(function() {
                $('#qualificationForm')[0].reset();
                $('#qualification_id').val('');
                $('#qualificationModalTitle').text('Add Qualification');
                $('#qualificationModal').modal('show');
            });

            // Edit Qualification
            $(document).on('click', '.editQualificationBtn', function() {
                var row = $(this).closest('tr');
                var id = $(this).data('id');
                $('#qualification_id').val(id);
                $('#mq_name').val(row.find('.mq-name').text().trim());
                $('#mq_code').val(row.find('.mq-code').text().trim());
                $('#mq_sub_section').val(row.find('.mq-sub').text().trim());
                var status = row.find('.badge-success').length ? 'active' : 'inactive';
                $('#mq_status').val(status);
                $('#qualificationModalTitle').text('Edit Qualification');
                $('#qualificationModal').modal('show');
            });

            // Save Qualification
            $('#qualificationForm').submit(function(e) {
                e.preventDefault();
                var id = $('#qualification_id').val();
                var url = id ? "{{ url('master-qualification') }}/" + id : "{{ url('master-qualification') }}";
                var type = id ? 'PUT' : 'POST';

                $.ajax({
                    url: url,
                    type: type,
                    data: $(this).serialize(),
                    success: function(res) {
                        $('#qualificationModal').modal('hide');
                        toastr.success(res.message || 'Saved successfully');
                        setTimeout(() => location.reload(), 1000);
                    },
                    error: function(xhr) {
                        toastr.error('Something went wrong. Please try again.');
                    }
                });
            });

            // Delete Qualification
            $(document).on('click', '.deleteQualificationBtn', function() {
                deleteId = $(this).data('id');
                $('#deleteModal').modal('show');
            });

            $('#confirmDeleteBtn').click(function() {
                if (!deleteId) return;
                $.ajax({
                    url: "{{ url('master-qualifications') }}/" + deleteId,
                    type: 'DELETE',
                    data: { _token: "{{ csrf_token() }}" },
                    success: function(res) {
                        $('#deleteModal').modal('hide');
                        toastr.success(res.message || 'Deleted successfully');
                        $('tr[data-id="' + deleteId + '"]').remove();
                    },
                    error: function() {
                        toastr.error('Delete failed.');
                    }
                });
            });
        });
    </script>
@endpush
