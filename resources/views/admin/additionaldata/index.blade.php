@extends('layouts.app')

@section('content')
    <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
        @include('components.breadcrumb', [
            'page' => 'End User Additional Data',
            'page_name' => 'End User Additional Data',
        ])
        <div class="d-flex my-xl-auto right-content align-items-center flex-wrap">
            <div class="mb-2">
                <a href="javascript:void(0);" class="btn btn-primary d-flex align-items-center" id="addDataBtn">
                    <i class="ti ti-circle-plus me-2"></i>Add Section
                </a>
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between flex-wrap row-gap-3">
            <h5>Additional Data List</h5>
        </div>
        <div class="card-body p-0">
            <div class="custom-datatable-filter table-responsive">
                <table class="table datatable" id="dataTable">
                    <thead class="thead-light">
                        <tr>
                            <th>Sl No</th>
                            <th>Section Code</th>
                            <th>Section</th>
                            <th>Data</th>
                            <th>Created Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($records as $data)
                            <tr data-id="{{ $data->id }}">
                                <td>{{ $loop->iteration }}</td>
                                <td class="section-code">{{ $data->section_code }}</td>
                                <td class="section">{{ $data->section }}</td>
                                <td class="section-data">{{ Str::limit($data->data, 50) }}</td>
                                <td>{{ $data->created_at->format('d M Y') }}</td>
                                <td>
                                    <div class="action-icon d-inline-flex">
                                        <a href="#" class="me-2 editDataBtn" data-id="{{ $data->id }}">
                                            <i class="ti ti-edit"></i>
                                        </a>

                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">No Data Added</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Add/Edit Modal -->
    <div class="modal fade" id="dataModal">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="dataModalTitle">Add Section</h4>
                    <button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal"><i
                            class="ti ti-x"></i></button>
                </div>
                <form id="dataForm">
                    @csrf
                    <input type="hidden" name="id" id="data_id">
                    <div class="modal-body pb-0">
                        <div class="mb-3">
                            <label class="form-label">Section Code</label>
                            <input type="text" class="form-control" name="section_code" id="section_code" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Section</label>
                            <input type="text" class="form-control" name="section" id="section" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Data</label>
                            <textarea class="form-control" name="data" id="data" rows="4"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light me-2" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary" id="saveDataBtn">Save</button>
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
            $('#addDataBtn').click(function() {
                $('#dataForm')[0].reset();
                $('#data_id').val('');
                $('#dataModalTitle').text('Add Section');
                $('#dataModal').modal('show');
            });

            // Edit button
            $(document).on('click', '.editDataBtn', function() {
                var row = $(this).closest('tr');
                var id = $(this).data('id');
                var code = row.find('.section-code').text().trim();
                var section = row.find('.section').text().trim();
                var data = row.find('.section-data').text().trim();

                $('#data_id').val(id);
                $('#section_code').val(code);
                $('#section').val(section);
                $('#data').val(data);
                $('#dataModalTitle').text('Edit Section');
                $('#dataModal').modal('show');
            });

            // Save
            $('#dataForm').submit(function(e) {
                e.preventDefault();
                var id = $('#data_id').val();
                var url = id ? "{{ url('enduser-additional-data') }}/" + id :
                    "{{ url('enduser-additional-data') }}";
                var type = id ? 'PUT' : 'POST';

                $.ajax({
                    url: url,
                    type: type,
                    data: $(this).serialize(),
                    success: function(res) {
                        $('#dataModal').modal('hide');
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
