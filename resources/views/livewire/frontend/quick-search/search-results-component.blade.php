<div class="container mt-5">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3">
            <div class="card">
                <div class="card-header">
                    Filter Options
                </div>
                <div class="card-body">
                    <p><strong>Continent:</strong> {{ $continent }}</p>
                    <p><strong>Price:</strong> {{ $price }}</p>
                    <p><strong>Travel Month:</strong> {{ $urlaub }}</p>
                    <p><strong>Sunshine Hours:</strong> {{ $sonnenstunden }}</p>
                    <p><strong>Water Temperature:</strong> {{ $wassertemperatur }}</p>
                </div>
            </div>
        </div>

        <!-- Search Results -->
        <div class="col-md-9">
            <div class="card">
                <div class="card-header">
                    Search Results ({{ $locations->count() }} locations found)
                </div>
                <div class="card-body">
                    @if($locations->count() > 0)
                        <div class="row">
                            @foreach($locations as $location)
                                <div class="col-md-4 mb-4">
                                    <div class="card">
                                        <img src="{{ $location->text_pic1 ?? 'https://via.placeholder.com/600x400?text=No+Image' }}" class="card-img-top" alt="{{ $location->title }}">
                                        <div class="card-body">
                                            <h5 class="card-title">{{ $location->title }}</h5>
                                            <p class="card-text">{{ $location->text_short }}</p>
                                            <a href="#" class="btn btn-primary">View Details</a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-center">No results found. Adjust your filters and try again.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
