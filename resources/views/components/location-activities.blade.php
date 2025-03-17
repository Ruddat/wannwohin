<div class="row d-flex flex-wrap align-items-center w-100 bg-white py-2 my-2 text-center box-shadow-2"
    style="margin-right: 0.10rem!important; margin-left: 0.10rem!important;">
    @if ($location->list_beach)
        <div class="col-4 d-flex border-right justify-content-start">
            <i class="fas fa-umbrella-beach fa-lg me-1" title="Strand"></i>
            <span class="ms-3 text-color-grey bold">Strand</span>
        </div>
    @endif
    @if ($location->list_citytravel)
        <div class="col-4 d-flex border-right justify-content-start">
            <i class="fas fa-city fa-lg me-1" title="Städtereise"></i>
            <span class="ms-3 text-color-grey bold">Städtereise</span>
        </div>
    @endif
    @if ($location->list_sports)
        <div class="col-4 d-flex border-right justify-content-start">
            <i class="fas fa-running fa-lg me-1" title="Sport"></i>
            <span class="ms-3 text-color-grey bold">Sport</span>
        </div>
    @endif

    @if ($location->list_island)
        <div class="col-4 d-flex border-right justify-content-start">
            <img src="{{ asset('img/insel-icon.png') }}" alt="Insel" title="Insel" style="height: 30px;">
            <span class="ms-3 text-color-grey bold">Insel</span>
        </div>
    @endif
    @if ($location->list_culture)
        <div class="col-4 d-flex border-right justify-content-start">
            <i class="fas fa-landmark fa-lg me-1" title="Kultur"></i>
            <span class="ms-3 text-color-grey bold">Kultur</span>
        </div>
    @endif
    @if ($location->list_nature)
        <div class="col-4 d-flex border-right justify-content-start">
            <i class="fas fa-tree fa-lg me-1" title="Natur"></i>
            <span class="ms-3 text-color-grey bold">Natur</span>
        </div>
    @endif
    @if ($location->list_watersport)
        <div class="col-4 d-flex border-right justify-content-start">
            <i class="fas fa-swimmer fa-lg me-1" title="Wassersport"></i>
            <span class="ms-3 text-color-grey bold">Wassersport</span>
        </div>
    @endif
    @if ($location->list_wintersport)
        <div class="col-4 d-flex border-right justify-content-start">
            <i class="fas fa-snowflake fa-lg me-1" title="Wintersport"></i>
            <span class="ms-3 text-color-grey bold">Wintersport</span>
        </div>
    @endif
    @if ($location->list_mountainsport)
        <div class="col-4 d-flex border-right justify-content-start">
            <i class="fas fa-hiking fa-lg me-1" title="Bergsport"></i>
            <span class="ms-3 text-color-grey bold">Bergsport</span>
        </div>
    @endif
    @if ($location->list_biking)
        <div class="col-4 d-flex border-right justify-content-start">
            <i class="fas fa-bicycle fa-lg me-1" title="Radfahren"></i>
            <span class="ms-3 text-color-grey bold">Radfahren</span>
        </div>
    @endif
    @if ($location->list_fishing)
        <div class="col-4 d-flex border-right justify-content-start">
            <i class="fas fa-fish fa-lg me-1" title="Angeln"></i>
            <span class="ms-3 text-color-grey bold">Angeln</span>
        </div>
    @endif
    @if ($location->list_amusement_park)
        <div class="col-4 d-flex border-right justify-content-start">
            <i class="fas fa-ticket-alt fa-lg me-1" title="Freizeitpark"></i>
            <span class="ms-3 text-color-grey bold">Freizeitpark</span>
        </div>
    @endif
    @if ($location->list_water_park)
        <div class="col-4 d-flex border-right justify-content-start">
            <i class="fas fa-water fa-lg me-1" title="Wasserpark"></i>
            <span class="ms-3 text-color-grey bold">Wasserpark</span>
        </div>
    @endif
    @if ($location->list_animal_park)
        <div class="col-4 d-flex border-right justify-content-start align-items-center">
            <i class="fas fa-paw fa-lg me-1" title="Tierpark"></i>
            <span class="ms-3 text-color-grey bold">Tierpark</span>
        </div>
    @endif
</div>

