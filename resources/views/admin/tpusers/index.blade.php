@extends('layouts.app')

@push('styles')

<style>
    #tpUsersTable {
        table-layout: fixed;
        width: 100%;
    }

    #tpUsersTable th,
    #tpUsersTable td {
        white-space: normal !important;
        word-wrap: break-word;
        vertical-align: top;
    }
</style>

@endpush

@section('content')
    <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
        @include('components.breadcrumb', ['page' => 'TP Users', 'page_name' => 'TP Users'])
    </div>

    @include('components.toastr')

    <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between flex-wrap row-gap-3" style="padding: 2rem 1.25rem 0rem;">
            <ul class="nav nav-tabs nav-tabs-bottom">

                @foreach($statuses as $key => $label)
                    <li class="nav-item">
                        <a href="{{ route('tpusers', $key) }}"
                            class="nav-link {{ $status == $key ? 'active' : '' }}">
                            {{ $label }}
                        </a>
                    </li>
                @endforeach

            </ul>
        </div>


        <div class="card-body p-2">
            <div class="custom-datatable-filter table-responsive">
                <table  class="table tpUsersTable table-striped w-100">
                    <thead class="thead-light">
                        <tr>
                            <th>Sl No</th>
                            <th>Username</th>
                            <th >TP Information</th>
                            <th>TCenter Information</th>
                            <th >Registered Date</th>
                            <th class="text-center">Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>


    </div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('.tpUsersTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('tpusers.data', $status ?? 'all') }}",
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', width: '40px', orderable: false, searchable: false },
                { data: 'username', name: 'user.username', width: '100px' },
                { data: 'tp_info', name: 'tp_info', width: '300px' },
                { data: 'tc_info', name: 'tc_info', width: '300px' },
                { data: 'created_at', name: 'created_at', width: '100px' },
                { data: 'status', name: 'status', width: '100px' },
                { data: 'actions', name: 'actions', width: '80px', orderable: false, searchable: false },
            ],
            columnDefs: [
                { className: 'text-center align-middle', targets: [0, 1, 4, 5, 6] },
            ],
            autoWidth: true,
            fixedColumns: false
        });

    });
</script>
@endpush
