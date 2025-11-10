@extends('layouts.app')

@push('styles')
    <style>
        .permission-group {
            transition: all 0.2s ease-in-out;
        }

        .permission-group .group-header:hover {
            background-color: #f8f9fa;
        }
        .form-check-input {
            width: 1.3rem;
            height: 1.3rem;
            background-color: #FFF;
            border: 1px solid #E5E7EB;
        }
        .form-check-label{
            align-items: end;
            gap: 5px;
            display: flex;
        }
    </style>
@endpush

@section('content')
    <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
        @include('components.breadcrumb', [
            'page' => 'Permissions',
            'base' => 'Roles',
            'base_url' => route('roles.index'),
            'page_name' => 'Permissions',
        ])
    </div>

    <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between flex-wrap row-gap-3">
            <h5>Permission Flags — <span class="text-primary">{{ $role->name }}</span></h5>

            <div>
                <button type="button" id="checkAll" class="btn btn-outline-primary btn-sm">All Permissions</button>
                <button type="button" id="expandAll" class="btn btn-outline-secondary btn-sm">Expand All</button>
                <button type="button" id="collapseAll" class="btn btn-outline-secondary btn-sm">Collapse All</button>
            </div>
        </div>
        <div class="card-body p-0">
            <form action="{{ route('roles.updatePermissions', $role->id) }}" method="POST">
                @csrf
                <div class="p-5">
                    @foreach ($permissions as $group => $perms)

                        @php
                            $checkedCount = $perms->filter(fn($p) => in_array($p->id, $rolePermissions))->count();
                            $allChecked = $checkedCount > 0;
                        @endphp

                        <div class="permission-group mb-3 border rounded bg-light">
                            <div class="p-3 d-flex justify-content-between align-items-center group-header" style="cursor:pointer;">
                                <div>
                                    <input type="checkbox" class="form-check-input me-2 group-checkbox" {{ $allChecked ? 'checked' : '' }} >
                                    <strong class="">{{ ucfirst($group) }}</strong>
                                </div>
                                <i class="bi bi-chevron-down"></i>
                            </div>
                            <div class="group-body bg-white p-3 row">
                                @foreach ($perms as $perm)
                                    <div class="col-md-3 mb-2">
                                        <label class="form-check-label">
                                            <input type="checkbox" name="permissions[]" value="{{ $perm->id }}"
                                                class="form-check-input child-checkbox"
                                                {{ in_array($perm->id, $rolePermissions) ? 'checked' : '' }}>
                                            {{ ucwords(str_replace('_', ' ', $perm->name)) }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach

                    <button type="submit" class="btn btn-primary mt-3">Save Permissions</button>
                </div>
            </form>


        </div>

    </div>
@endsection


@push('scripts')
    <script>
        $(document).ready(function() {
            $('.group-header').on('click', function() {
                const body = $(this).next('.group-body');
                body.slideToggle(200);
                $(this).find('i').toggleClass('bi-chevron-down bi-chevron-up');
            });

            $('#expandAll').click(function() {
                $('.group-body').slideDown();
                $('.group-header i').removeClass('bi-chevron-down').addClass('bi-chevron-up');
            });

            $('#collapseAll').click(function() {
                $('.group-body').slideUp();
                $('.group-header i').removeClass('bi-chevron-up').addClass('bi-chevron-down');
            });

            $('.group-checkbox').on('change', function() {
                $(this).closest('.permission-group').find('.child-checkbox').prop('checked', $(this).is(
                    ':checked'));
            });

            $('.group-checkbox').on('click', function(e) {
                e.stopPropagation();
            });

            $('#checkAll').on('click', function() {
                const allChecked = $('input.child-checkbox:checked').length === $('input.child-checkbox')
                    .length;
                $('input.child-checkbox, .group-checkbox').prop('checked', !allChecked);
            });
        });
    </script>
@endpush
