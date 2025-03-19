<div class="row d-flex flex-wrap align-items-center w-100 bg-white py-2 my-2 box-shadow-2"
    style="margin-right: 0.10rem!important; margin-left: 0.10rem!important;">
    @if ($location->list_beach)
        <div class="col-4 border-right">
            <div class="d-flex align-items-center justify-content-start">
                <div class="col-4">
                    <i class="fas fa-umbrella-beach fa-lg" title="Strand"></i>
                </div>
                <div class="col text-start">
                    <span class="text-color-grey bold">Strand</span>
                </div>
            </div>
        </div>
    @endif
    @if ($location->list_citytravel)
        <div class="col-4 border-right">
            <div class="d-flex align-items-center justify-content-start">
                <div class="col-4">
                    <i class="fas fa-city fa-lg" title="Städtereise"></i>
                </div>
                <div class="col text-start">
                    <span class="text-color-grey bold">Städtereise</span>
                </div>
            </div>
        </div>
    @endif
    @if ($location->list_sports)
        <div class="col-4 border-right">
            <div class="d-flex align-items-center justify-content-start">
                <div class="col-4">
                    <i class="fas fa-running fa-lg" title="Sport"></i>
                </div>
                <div class="col text-start">
                    <span class="text-color-grey bold">Sport</span>
                </div>
            </div>
        </div>
    @endif
    @if ($location->list_island)
        <div class="col-4 border-right">
            <div class="d-flex align-items-center justify-content-start">
                <div class="col-4">
                    <img src="{{ asset('img/insel-icon.png') }}" alt="Insel" title="Insel" style="height: 30px;">
                </div>
                <div class="col text-start">
                    <span class="text-color-grey bold">Insel</span>
                </div>
            </div>
        </div>
    @endif
    @if ($location->list_culture)
        <div class="col-4 border-right">
            <div class="d-flex align-items-center justify-content-start">
                <div class="col-4">
                    <i class="fas fa-landmark fa-lg" title="Kultur"></i>
                </div>
                <div class="col text-start">
                    <span class="text-color-grey bold">Kultur</span>
                </div>
            </div>
        </div>
    @endif
    @if ($location->list_nature)
        <div class="col-4 border-right">
            <div class="d-flex align-items-center justify-content-start">
                <div class="col-4">
                    <i class="fas fa-tree fa-lg" title="Natur"></i>
                </div>
                <div class="col text-start">
                    <span class="text-color-grey bold">Natur</span>
                </div>
            </div>
        </div>
    @endif
    @if ($location->list_watersport)
        <div class="col-4 border-right">
            <div class="d-flex align-items-center justify-content-start">
                <div class="col-4">
                    <i class="fas fa-swimmer fa-lg" title="Wassersport"></i>
                </div>
                <div class="col text-start">
                    <span class="text-color-grey bold">Wassersport</span>
                </div>
            </div>
        </div>
    @endif
    @if ($location->list_wintersport)
        <div class="col-4 border-right">
            <div class="d-flex align-items-center justify-content-start">
                <div class="col-4">
                    <i class="fas fa-snowflake fa-lg" title="Wintersport"></i>
                </div>
                <div class="col text-start">
                    <span class="text-color-grey bold">Wintersport</span>
                </div>
            </div>
        </div>
    @endif
    @if ($location->list_mountainsport)
        <div class="col-4 border-right">
            <div class="d-flex align-items-center justify-content-start">
                <div class="col-4">
                    <i class="fas fa-hiking fa-lg" title="Bergsport"></i>
                </div>
                <div class="col text-start">
                    <span class="text-color-grey bold">Bergsport</span>
                </div>
            </div>
        </div>
    @endif
    @if ($location->list_biking)
        <div class="col-4 border-right">
            <div class="d-flex align-items-center justify-content-start">
                <div class="col-4">
                    <i class="fas fa-bicycle fa-lg" title="Radfahren"></i>
                </div>
                <div class="col text-start">
                    <span class="text-color-grey bold">Radfahren</span>
                </div>
            </div>
        </div>
    @endif
    @if ($location->list_fishing)
        <div class="col-4 border-right">
            <div class="d-flex align-items-center justify-content-start">
                <div class="col-4">
                    <i class="fas fa-fish fa-lg" title="Angeln"></i>
                </div>
                <div class="col text-start">
                    <span class="text-color-grey bold">Angeln</span>
                </div>
            </div>
        </div>
    @endif
    @if ($location->list_amusement_park)
        <div class="col-4 border-right">
            <div class="d-flex align-items-center justify-content-start">
                <div class="col-4">
                    <i class="fas fa-ticket-alt fa-lg" title="Freizeitpark"></i>
                </div>
                <div class="col text-start">
                    <span class="text-color-grey bold">Freizeitpark</span>
                </div>
            </div>
        </div>
    @endif
    @if ($location->list_water_park)
        <div class="col-4 border-right">
            <div class="d-flex align-items-center justify-content-start">
                <div class="col-4">
                    <i class="fas fa-water fa-lg" title="Wasserpark"></i>
                </div>
                <div class="col text-start">
                    <span class="text-color-grey bold">Wasserpark</span>
                </div>
            </div>
        </div>
    @endif
    @if ($location->list_animal_park)
        <div class="col-4 border-right">
            <div class="d-flex align-items-center justify-content-start">
                <div class="col-4">
                    <i class="fas fa-paw fa-lg" title="Tierpark"></i>
                </div>
                <div class="col text-start">
                    <span class="text-color-grey bold">Tierpark</span>
                </div>
            </div>
        </div>
    @endif
</div>
