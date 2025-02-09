<div class="container my-4">
    <article class="timeline-box right custom-box-shadow-2 box-shadow-2">
        <div class="row">
            <!-- OpenStreetMap Section -->
            <div class="experience-info col-lg-3 col-sm-5 bg-color-primary p-0 article-map" id="openstreetmap" style="height: auto; border-radius: 8px;">
            </div>

            <!-- Description Section -->
            <div class="experience-description col-lg-9 col-sm-7 bg-color-light px-3 py-3 rounded-end">
                <h4 class="text-color-dark font-weight-semibold">{!! app('autotranslate')->trans($location->text_headline, app()->getLocale()) !!}</h4>

                <p class="text-black">{!! $location->text_short !!}</p>
                <p class="text-black">In einer Flugzeit von ca. {{ $location->flight_hours }} Stunden von Frankfurt ist man am Ziel.</p>
                <div class="d-flex">
                    <button class="ms-auto btn btn-primary" data-bs-toggle="modal" data-bs-target="#google_map_modal">
                        Position auf der Karte
                    </button>
                </div>
            </div>
        </div>
    </article>

    <!-- Google Maps Modal -->
    <div class="modal fade" id="google_map_modal" tabindex="-1" aria-labelledby="googleMapModalLabel" style="display: none;" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="googleMapModalLabel">Position auf der Karte</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <div class="modal-body">
                    <div class="row" style="min-height: 400px">
                        <div class="col-12">
                            <div class="mapouter">
                                <div class="gmap_canvas">
                                    <iframe
                                        width="100%"
                                        height="400"
                                        id="gmap_canvas"
                                        src="https://maps.google.com/maps?q={{ urlencode($location->title) }}&t=&z=10&ie=UTF8&iwloc=&output=embed"
                                        frameborder="0"
                                        scrolling="no"
                                        marginheight="0"
                                        marginwidth="0"
                                    ></iframe>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- OpenStreetMap Integration -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const lat = {{ $location->lat }};
        const lon = {{ $location->lon }};

        const map = L.map('openstreetmap').setView([lat, lon], 13);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
        }).addTo(map);

        L.marker([lat, lon]).addTo(map)
            .bindPopup('{{ $location->title }}')
            .openPopup();
    });
</script>
<style>
    .modal {
    z-index: 1055; /* Bootstrap's default z-index for modals is 1050, we make it slightly higher */
}

.modal-backdrop {
    z-index: -2; /* Ensures the backdrop is behind the modal */
}

.modal-content {
    position: relative;
    z-index: 1055;
}

body.modal-open {
    overflow: hidden; /* Verhindert, dass die Seite im Hintergrund scrollt */
}

</style>
