@if(!empty($breadcrumbs))
    <section class="bg-color-light py-4">
        <div class="container">
            <div class="row">
                <div class="col align-self-center p-static">
                    <ul class="breadcrumb d-block">
                        @foreach($breadcrumbs as $breadcrumb)
                            <li class="breadcrumb-item {{ $loop->last ? 'active' : '' }}">
                                @if ($loop->first)
                                    <a href="{{ $breadcrumb['url'] }}">
                                        <i class="fas fa-home"></i> Home <!-- Haus-Icon + Text -->
                                    </a>
                                @elseif (!$loop->last)
                                    <a href="{{ $breadcrumb['url'] }}">{{ $breadcrumb['title'] }}</a>
                                @else
                                    {{ $breadcrumb['title'] }}
                                @endif
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </section>
@endif
