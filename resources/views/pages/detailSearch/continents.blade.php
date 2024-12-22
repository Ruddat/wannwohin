<div class="card bg-light mb-3">
    <div class="card-header">Kontinent</div>
    <div class="card-body">
        <div class="row">
            @foreach($continents as $continent)
                <div class="col-6 col-md-4 col-lg-3 mb-4">
                    <div class="continent-card position-relative">
                        <!-- Checkbox Input -->
                        <input
                            name="continents[{{$continent->id}}]"
                            id="continent_{{$continent->id}}"
                            value="{{$continent->id}}"
                            class="form-check-input continent-checkbox"
                            type="checkbox"
                            @if(request()->has('continents') && in_array($continent->id, array_keys(request()->continents)))
                                checked
                            @endif
                        >
                        <!-- Hintergrundbild -->
                        <div
                            class="continent-image"
                            style="background-image: url('{{ asset("img/location_main_img/".$continent->alias.".png") }}')"
                        ></div>
                        <!-- Overlay mit Titel -->
                        <div class="continent-overlay">
                            <span class="continent-title">{{ $continent->title }}</span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

<style>
    .continent-card {
    position: relative;
    width: 100%;
    height: 150px;
    border-radius: 8px;
    overflow: hidden;
    cursor: pointer;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.continent-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 12px rgba(0, 0, 0, 0.2);
}

.continent-image {
    width: 100%;
    height: 100%;
    background-size: cover;
    background-position: center;
    filter: grayscale(100%);
    transition: filter 0.3s ease;
}

.continent-card:hover .continent-image {
    filter: grayscale(0%);
}

.continent-overlay {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    background: rgba(0, 0, 0, 0.5);
    color: #fff;
    text-align: center;
    padding: 10px 0;
    font-size: 1rem;
    font-weight: bold;
    transition: background 0.3s ease;
}

.continent-card:hover .continent-overlay {
    background: rgba(0, 0, 0, 0.7);
}

.continent-checkbox {
    position: absolute;
    top: 10px;
    left: 10px;
    width: 20px;
    height: 20px;
    z-index: 2;
}

</style>
