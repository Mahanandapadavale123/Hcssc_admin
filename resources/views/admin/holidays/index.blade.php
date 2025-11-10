@extends('layouts.app')

@section('content')

<div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
    @include('components.breadcrumb', ['page' => 'Holidays', 'page_name' => 'Holidays'])

    <div class="d-flex my-xl-auto right-content align-items-center flex-wrap">
        <div class="mb-2">
            <a href="javascript:void(0);" class="btn btn-primary d-flex align-items-center" id="addHolidayBtn">
                <i class="ti ti-circle-plus me-2"></i>Add Holiday
            </a>
        </div>
    </div>
</div>

<!-- Holidays Table -->
<div class="card">
    <div class="card-header d-flex align-items-center justify-content-between flex-wrap row-gap-3">
        <h5>Holiday List</h5>
    </div>
    <div class="card-body p-0">
        <div class="custom-datatable-filter table-responsive">
            <table class="table datatable" id="holidaysTable">
                <thead class="thead-light">
                    <tr>
                        <th>Sl No</th>
                        <th>Holiday Name</th>
                        <th>Holiday Date</th>
                        <th>Day</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($holidays as $holiday)
                        <tr data-id="{{ $holiday->id }}">
                            <td>{{ $loop->iteration }}</td>
                            <td class="holiday-title">{{ $holiday->title }}</td>
                            <td>{{ $holiday->date->format('d M Y') }}</td>
                            <td>{{ $holiday->date->format('l') }}</td>
                            <td>
                                @if ($holiday->status == 'active')
                                    <span class="badge badge-success"><i class="ti ti-point-filled me-1"></i> Active</span>
                                @else
                                    <span class="badge badge-danger"><i class="ti ti-point-filled me-1"></i> Inactive</span>
                                @endif
                            </td>
                            <td>
                                <div class="action-icon d-inline-flex">
                                    <a href="#" class="me-2 editHolidayBtn" data-id="{{ $holiday->id }}">
                                        <i class="ti ti-edit"></i>
                                    </a>
                                    <form action="{{ route('holidays.destroy', $holiday->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-link p-0 m-0 delete-btn">
                                            <i class="ti ti-trash text-danger"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">No Holidays Added</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add/Edit Holiday Modal -->
<div class="modal fade" id="holidayModal">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="holidayModalTitle">Add Holiday</h4>
                <button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal">
                    <i class="ti ti-x"></i>
                </button>
            </div>
            <form id="holidayForm">
                @csrf
                <input type="hidden" name="id" id="holiday_id">
                <div class="modal-body pb-0">
                    <div class="mb-3">
                        <label class="form-label">Holiday Title <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="title" id="holiday_title" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Holiday Date <span class="text-danger">*</span></label>
                        <input type="date" name="date" class="form-control" id="date" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" id="holiday_description"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status <span class="text-danger">*</span></label>
                        <select class="form-select" name="status" id="holiday_status" required>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light me-2" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="saveHolidayBtn">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('#addHolidayBtn').click(function() {
        $('#holidayForm')[0].reset();
        $('#holiday_id').val('');
        $('#holidayModalTitle').text('Add Holiday');
        $('#holidayModal').modal('show');
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
    $(document).on('click', '.editHolidayBtn', function() {
        var row = $(this).closest('tr');
        var id = $(this).data('id');
        var title = row.find('.holiday-title').text().trim();
        var date = row.find('td:eq(2)').text().trim();
        var status = row.find('.badge-success').length ? 'active' : 'inactive';

        $('#holiday_id').val(id);
        $('#holiday_title').val(title);
        $('#date').val(date);
        $('#holiday_status').val(status);
        $('#holidayModalTitle').text('Edit Holiday');
        $('#holidayModal').modal('show');
    });



    $('#holidayForm').submit(function(e) {
    e.preventDefault();

    var id = $('#holiday_id').val();
    var url = id ? "{{ url('holidays') }}/" + id : "{{ url('holidays') }}";
    var type = id ? 'PUT' : 'POST';

    $.ajax({
        url: url,
        type: type,
        data: $(this).serialize(),
        success: function(res) {
            $('#holidayModal').modal('hide');
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: res.message || 'Holiday saved successfully!',
            }).then(() => location.reload());
        },
        error: function(xhr) {
            if (xhr.status === 422) {
                let errors = xhr.responseJSON.errors;
                if (errors && errors.date) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Duplicate Date',
                        text: errors.date[0],
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Validation Error',
                        text: 'Please check your input fields.',
                    });
                }
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Something went wrong! Please try again later.',
                });
            }
        }
    });
});
});
</script>
@endpush
