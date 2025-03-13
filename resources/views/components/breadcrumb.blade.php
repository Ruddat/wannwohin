@if(!empty($breadcrumbs))
    <section class="bg-color-light py-2">
        <div class="container">
            <div class="row">
                <div class="col align-self-center p-static">
                    <ul class="breadcrumb d-block">
                        @foreach($breadcrumbs as $breadcrumb)
                            <li class="breadcrumb-item {{ $loop->last ? 'active' : '' }}">
                                @if ($loop->first)
                                    <a href="{{ $breadcrumb['url'] }}">
                                        <i class="fas fa-home"></i> @autotranslate('Home', app()->getLocale())
                                    </a>
                                @elseif (!$loop->last)
                                    <a href="{{ $breadcrumb['url'] }}">@autotranslate($breadcrumb['title'], app()->getLocale())</a>
                                @else
                                    @autotranslate($breadcrumb['title'], app()->getLocale())
                                @endif
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </section>
@endif
