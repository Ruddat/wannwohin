@if($breadcrumbShowStatus == true)
<section class="bg-color-light py-4">
    <div class="container">
        <div class="row">
            <div class="col align-self-center p-static">
                <ul class="breadcrumb d-block">
                    <li><a href="{{url('/')}}">Home</a></li>
                    @foreach($breadcrumbPaths as $breadcrumbPath)
                        @if($breadcrumbPath['class']=='active')
                            <li class="active">{{$breadcrumbPath['title']}}</li>
                        @else
                            <li class="{{$breadcrumbPath['class']}}"><a href="{{$breadcrumbPath['full_url']}}">{{$breadcrumbPath['title']}}</a></li>
                        @endif
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</section>
@endif
