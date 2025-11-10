

@extends('layouts.app')

@section('content')
<div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
    @include('components.breadcrumb', [
        'page' => 'Leave Type',
        'base' => 'Admin',
        'page_name' => 'Leave Type'
    ])
    <div>
        <a href="javascript:void(0);" class="btn btn-primary d-flex align-items-center" id="addLeaveTypeBtn">
            <i class="ti ti-circle-plus me-2"></i>Add Leave Type
        </a>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table datatable">
                <thead class="thead-light">
                    <tr>
                        <th>SL</th>
                        <th>Leave Type</th>
                        <th>Days</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($leaveTypes as $type)
                    <tr data-id="{{ $type->id }}">
                        <td>{{ $loop->iteration }}</td>
                        <td class="name">{{ $type->name }}</td>
                        <td class="total_days">{{ $type->total_days }}</td>
                        <td>
                            <span class="badge {{ $type->status == 'active' ? 'badge-success' : 'badge-danger' }}">
                                {{ ucfirst($type->status) }}
                            </span>
                        </td>
                        <td>
                            <div class="action-icon d-inline-flex">
                                <a href="#" class="me-2 editLeaveTypeBtn" data-id="{{ $type->id }}"><i class="ti ti-edit"></i></a>
                                <form action="{{ route('leave-type.destroy', $type->id) }}" method="POST" class="d-inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-link p-0 delete-btn"><i class="ti ti-trash text-danger"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="text-center">No leave types found</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="leaveTypeModal">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="leaveTypeForm">@csrf
                <input type="hidden" id="leaveType_id">
                <div class="modal-header">
                    <h4 class="modal-title">Add Leave Type</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label>Leave Type</label>
                        <input type="text" name="name" id="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Days</label>
                        <input type="number" name="total_days" id="total_days" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Status</label>
                        <select name="status" id="status" class="form-select">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function(){
    $('#addLeaveTypeBtn').click(function(){
        $('#leaveTypeForm')[0].reset();
        $('#leaveType_id').val('');
        $('#leaveTypeModal .modal-title').text('Add Leave Type');
        $('#leaveTypeModal').modal('show');
    });

    $('.editLeaveTypeBtn').click(function(){
        let row = $(this).closest('tr');
        $('#leaveType_id').val(row.data('id'));
        $('#name').val(row.find('.name').text());
        $('#total_days').val(row.find('.total_days').text());
        $('#status').val(row.find('.badge-success').length ? 'active' : 'inactive');
        $('#leaveTypeModal .modal-title').text('Edit Leave Type');
        $('#leaveTypeModal').modal('show');
    });

    $('#leaveTypeForm').submit(function(e){
        e.preventDefault();
        let id = $('#leaveType_id').val();
                      var url = id ? "{{ url('leave-type') }}/" + id : "{{ url('leave-type') }}";
                var type = id ? 'PUT' : 'POST';
        $.ajax({
            url: url,
            type: type,
            data: $(this).serialize(),
            success: function(res){
                Swal.fire('Success!', res.message, 'success').then(()=> location.reload());
            },
            error: function(err){
                Swal.fire('Error!', 'Something went wrong.', 'error');
            }
        });
    });
});
</script>
@endpush
