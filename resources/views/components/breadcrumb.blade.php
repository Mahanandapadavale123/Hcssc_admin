<div class="my-auto mb-2">
    <h2 class="mb-1">{{ $page }}</h2>
    <nav>
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item">
                <a href="{{ url('/') }}"><i class="ti ti-smart-home"></i></a>
            </li>
            @if(!empty($base))
                <li class="breadcrumb-item">
                    @if(!empty($base_url))
                        <a href="{{ $base_url }}">{{ $base }}</a>
                    @else
                        {{ $base }}
                    @endif
                </li>
            @endif
            <li class="breadcrumb-item"> {{ $page_name }} </li>
        </ol>
    </nav>
</div>
