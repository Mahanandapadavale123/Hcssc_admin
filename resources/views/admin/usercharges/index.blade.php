@extends('layouts.app')

@section('content')
<div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
    @include('components.breadcrumb', ['page' => 'End User Charges', 'page_name' => 'End User Charges'])
    <div class="d-flex my-xl-auto right-content align-items-center flex-wrap">
        <div class="mb-2">
            <a href="javascript:void(0);" class="btn btn-primary d-flex align-items-center" id="addChargeBtn">
                <i class="ti ti-circle-plus me-2"></i>Add Charge
            </a>
        </div>
    </div>
</div>

<!-- Charges Table -->
<div class="card">
    <div class="card-header d-flex align-items-center justify-content-between flex-wrap row-gap-3">
        <h5>Charges List</h5>
    </div>
    <div class="card-body p-0">
        <div class="custom-datatable-filter table-responsive">
            <table class="table datatable" id="chargesTable">
                <thead class="thead-light">
                    <tr>
                        <th>Sl No</th>
                        <th>User Type</th>
                        <th>Payment Type</th>
                        <th>Category</th>
                        <th>Description</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Created Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($charges as $charge)
                        <tr data-id="{{ $charge->id }}">
                            <td>{{ $loop->iteration }}</td>
                            <td class="user-type">{{ $charge->user_type ?? 'N/A' }}</td>
                            <td class="payment-type text-capitalize">
                                {{ str_replace('_', ' ', $charge->payment_type) }}
                            </td>
                            <td class="category">{{ $charge->category ?? 'N/A' }}</td>
                            <td class="description">{{ $charge->description }}</td>
                            <td class="amount">₹ {{ number_format($charge->amount, 2) }}</td>
                            <td class="status">
                                @if ($charge->status == 'active')
                                    <span class="badge badge-success"><i class="ti ti-point-filled me-1"></i> Active</span>
                                @else
                                    <span class="badge badge-danger"><i class="ti ti-point-filled me-1"></i> Inactive</span>
                                @endif
                            </td>
                            <td>{{ $charge->created_at->format('d M Y') }}</td>
                            <td>
                                <div class="action-icon d-inline-flex">
                                    <a href="#" class="me-2 editChargeBtn" data-id="{{ $charge->id }}"><i class="ti ti-edit"></i></a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="9" class="text-center">No Charges Found</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add/Edit Modal -->
<div class="modal fade" id="chargeModal">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="chargeModalTitle">Add Charge</h4>
                <button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal"><i class="ti ti-x"></i></button>
            </div>
            <form id="chargeForm">
                @csrf
                <input type="hidden" name="id" id="charge_id">
                <div class="modal-body pb-0">
                    <div class="mb-3">
                        <label class="form-label">User Type</label>
                        <input type="text" class="form-control" name="user_type" id="user_type" placeholder="e.g. Student, Teacher">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Payment Type</label>
                        <select class="form-select" name="payment_type" id="payment_type" required>
                            <option value="">Select Type</option>
                            <option value="initial_payment">Initial Payment</option>
                            <option value="final_payment">Final Payment</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Category</label>
                        <input type="text" class="form-control" name="category" id="category">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <input type="text" class="form-control" name="description" id="description" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Amount (₹)</label>
                        <input type="number" class="form-control" name="amount" id="amount" min="0" step="0.01" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select class="form-select" name="status" id="status" required>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light me-2" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="saveChargeBtn">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>


@endsection


@push('scripts')
<script>
$(document).ready(function () {
    let deleteId = null;

    // Add Charge
    $('#addChargeBtn').click(function () {
        $('#chargeForm')[0].reset();
        $('#charge_id').val('');
        $('#chargeModalTitle').text('Add Charge');
        $('#chargeModal').modal('show');
    });

    // Edit Charge
    $(document).on('click', '.editChargeBtn', function () {
        const row = $(this).closest('tr');
        $('#charge_id').val($(this).data('id'));
        $('#user_type').val(row.find('.user-type').text().trim());
        $('#payment_type').val(row.find('.payment-type').text().trim().replace(' ', '_'));
        $('#category').val(row.find('.category').text().trim());
        $('#description').val(row.find('.description').text().trim());
        $('#amount').val(row.find('.amount').text().replace('₹', '').trim());
        $('#status').val(row.find('.badge-success').length ? 'active' : 'inactive');
        $('#chargeModalTitle').text('Edit Charge');
        $('#chargeModal').modal('show');
    });

    // Save (Add or Update)
    $('#chargeForm').submit(function (e) {
        e.preventDefault();
        const id = $('#charge_id').val();
        const url = id ? "{{ url('enduser-charge') }}/" + id : "{{ url('enduser-charge') }}";
        const type = id ? 'PUT' : 'POST';

        $.ajax({
            url: url,
            type: type,
            data: $(this).serialize(),
            success: function (res) {
                $('#chargeModal').modal('hide');
                toastr.success(res.message);
                setTimeout(() => location.reload(), 1000);
            },
            error: function () {
                toastr.error('Something went wrong');
            }
        });
    });


});
</script>
@endpush
