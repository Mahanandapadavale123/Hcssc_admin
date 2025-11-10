@extends('layouts.app')

@section('content')
    <style>
        /* 🌟 Make table look clean */
        #equipmentTable th,
        #equipmentTable td {
            vertical-align: middle;
        }

        /* 🔹 Limit width of Equipment Name */
        #equipmentTable td:nth-child(3),
        #equipmentTable th:nth-child(3) {
            max-width: 200px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* 🔹 Limit width of Qualification Code column too */
        #equipmentTable td:nth-child(2),
        #equipmentTable th:nth-child(2) {
            max-width: 120px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
    </style>

    <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
        @include('components.breadcrumb', [
            'page' => 'Master Equipment',
            'page_name' => 'Master Equipment',
        ])
        <div class="d-flex my-xl-auto right-content align-items-center flex-wrap">
            <div class="mb-2">
                <a href="javascript:void(0);" class="btn btn-primary d-flex align-items-center" id="addEquipmentBtn">
                    <i class="ti ti-circle-plus me-2"></i>Add Equipment
                </a>
            </div>
        </div>
    </div>

    <!-- Equipment Table -->
    <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between flex-wrap row-gap-3">
            <h5>Equipment List</h5>
        </div>
        <div class="card-body p-0">
            <div class="custom-datatable-filter table-responsive">
                <table class="table datatable" id="equipmentTable">
                    <thead class="thead-light">
                        <tr>
                            <th>Sl No</th>
                            <th>Qualification Code</th>
                            <th>Equipment Name</th>
                            <th>Quantity Required</th>
                            <th>Status</th>
                            <th>Created Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($equipments as $equipment)
                            <tr data-id="{{ $equipment->id }}">
                                <td>{{ $loop->iteration }}</td>
                                <td class="qual-code">{{ $equipment->qual_code }}</td>
                                <td class="equipment-name" title="{{ $equipment->equipmentName }}">
                                    {{ $equipment->equipmentName }}</td>
                                <td class="quantity">{{ $equipment->quantityRequired }}</td>
                                <td class="equipment-status">
                                    @if ($equipment->status == 'active')
                                        <span class="badge badge-success d-inline-flex align-items-center badge-xs">
                                            <i class="ti ti-point-filled me-1"></i> Active
                                        </span>
                                    @else
                                        <span class="badge badge-danger d-inline-flex align-items-center badge-xs">
                                            <i class="ti ti-point-filled me-1"></i> Inactive
                                        </span>
                                    @endif
                                </td>
                                <td>{{ $equipment->created_at->format('d M Y') }}</td>
                                <td>
                                    <div class="action-icon d-inline-flex">
                                        <a href="#" class="me-2 editEquipmentBtn" data-id="{{ $equipment->id }}">
                                            <i class="ti ti-edit"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">No Equipment Added</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Add/Edit Modal -->
    <div class="modal fade" id="equipmentModal">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="equipmentModalTitle">Add Equipment</h4>
                    <button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal">
                        <i class="ti ti-x"></i></button>
                </div>
                <form id="equipmentForm">
                    @csrf
                    <input type="hidden" name="id" id="equipment_id">
                    <div class="modal-body pb-0">
                        <div class="mb-3">
                            <label class="form-label">Qualification Code</label>
                            <input type="text" class="form-control" name="qual_code" id="qual_code" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Equipment Name</label>
                            <input type="text" class="form-control" name="equipmentName" id="equipmentName" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Quantity Required</label>
                            <input type="text" class="form-control" name="quantityRequired" id="quantityRequired"
                                placeholder="e.g. 5 (Eqpt Nos)" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select class="form-select" name="status" id="equipment_status" required>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light me-2" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary" id="saveEquipmentBtn">Save</button>
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

            // Add button
            $('#addEquipmentBtn').click(function() {
                $('#equipmentForm')[0].reset();
                $('#equipment_id').val('');
                $('#equipmentModalTitle').text('Add Equipment');
                $('#equipmentModal').modal('show');
            });

            // Edit button
            $(document).on('click', '.editEquipmentBtn', function() {
                var row = $(this).closest('tr');
                var id = $(this).data('id');
                var code = row.find('.qual-code').text().trim();
                var name = row.find('.equipment-name').text().trim();
                var qty = row.find('.quantity').text().trim();
                var status = row.find('.badge-success').length ? 'active' : 'inactive';

                $('#equipment_id').val(id);
                $('#qual_code').val(code);
                $('#equipmentName').val(name);
                $('#quantityRequired').val(qty);
                $('#equipment_status').val(status);
                $('#equipmentModalTitle').text('Edit Equipment');
                $('#equipmentModal').modal('show');
            });

            // Save (Add or Update)
            $('#equipmentForm').submit(function(e) {
                e.preventDefault();

                var id = $('#equipment_id').val();
                var url = id ? "{{ url('master-equipment') }}/" + id : "{{ url('master-equipment') }}";
                var type = id ? 'PUT' : 'POST';

                $.ajax({
                    url: url,
                    type: type,
                    data: $(this).serialize(),
                    success: function(res) {
                        $('#equipmentModal').modal('hide');
                        toastr.success(res.message || 'Saved successfully');
                        setTimeout(() => location.reload(), 1000);
                    },
                    error: function(xhr) {
                        toastr.error('Something went wrong.');
                    }
                });
            });
        });
    </script>
@endpush
