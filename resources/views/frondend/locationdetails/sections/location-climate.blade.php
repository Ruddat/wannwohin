<div class="card-body p-4">
    <!-- Interaktive Karte (volle Breite oben) -->
    <div class="map-container rounded shadow-lg mb-4" data-aos="fade-down">
        <svg id="map" viewBox="0 0 100 400" preserveAspectRatio="xMidYMid meet" style="width: 100%; height: 200px; overflow: hidden;"></svg>
    </div>

    <!-- Description Section -->
    <div class="content-section" data-aos="fade-up">
        <h4 class="text-color-dark fw-bold mb-3">
            <i class="fas fa-cloud-sun me-2"></i>
            @autotranslate("Lage und Klima für {$location->title}", app()->getLocale())
        </h4>
        <div class="formatted-text text-black">
            {!! app('autotranslate')->trans($location->text_location_climate, app()->getLocale()) !!}
        </div>
    </div>
</div>

<script src="https://d3js.org/d3.v7.min.js"></script>
<script src="https://d3js.org/topojson.v3.min.js"></script>
<script>
const svg = d3.select("#map"),
    width = parseInt(svg.style("width")),
    height = parseInt(svg.style("height"));

// Dynamische Koordinaten
const geoLocation = {
    lat: {{ $location->lat }}, // Breitengrad
    lon: {{ $location->lon }},  // Längengrad
    name: "{{ $location->title }}"
};

// Projektion anpassen, um die Karte zu zentrieren
const projection = d3.geoMercator()
    .center([geoLocation.lon, geoLocation.lat]) // Zentrieren auf die Koordinaten
    .scale(1000) // Skalierung anpassen (je größer, desto näher der Zoom)
    .translate([width / 2, height / 2]); // Verschiebe die Karte in die Mitte

const path = d3.geoPath().projection(projection);

// Weltkarte laden und zeichnen
d3.json("https://d3js.org/world-110m.v1.json").then(world => {
    // Länder hinzufügen
    svg.append("g")
        .selectAll("path")
        .data(topojson.feature(world, world.objects.countries).features)
        .enter().append("path")
        .attr("d", path)
        .attr("fill", "#e0e0e0")
        .attr("stroke", "#999");

    // Berechnete Koordinaten aus der Projektion verwenden
    const [x, y] = projection([geoLocation.lon, geoLocation.lat]);
    console.log("Marker-Koordinaten:", x, y);

    // Schatten-Filter für die Nadel
    svg.append("defs").append("filter")
        .attr("id", "drop-shadow")
        .attr("height", "130%")
        .append("feGaussianBlur")
        .attr("in", "SourceAlpha")
        .attr("stdDeviation", 3) // Weichheit des Schattens
        .attr("result", "blur");

    // Nadel-Icon hinzufügen
    const marker = svg.append("image")
        .attr("xlink:href", "/assets/img/push-pin-svgrepo-com.png") // Pfad zum Nadel-Icon
        .attr("x", x - 15) // Zentrieren des Icons
        .attr("y", y - 50) // Höhe anpassen
        .attr("width", 30) // Kleinere Größe
        .attr("height", 50);

    const tooltipContainer = d3.select(".card-body")
        .append("div")
        .attr("class", "tooltip-container")
        .style("visibility", "hidden");

    marker.on("mouseover", function () {
        console.log("Tooltip sichtbar gemacht");
        tooltipContainer.style("visibility", "visible").text(geoLocation.name);
    })
    .on("mousemove", function (event) {
        console.log("Maus bewegt:", { x: event.pageX, y: event.pageY });
        tooltipContainer.style("top", (event.pageY - 60) + "px") // Nach oben versetzen
        .style("left", (event.pageX + 10) + "px");
    })
    .on("mouseout", function () {
        console.log("Tooltip verborgen");
        tooltipContainer.style("visibility", "hidden");
    });

}).catch(error => {
    console.error("Fehler beim Laden der Weltkarte:", error);
});
</script>

<!-- Zusätzliches CSS -->
<style>
    .map-container {
        height: 200px; /* Feste Höhe für horizontale Darstellung */
        width: 100%; /* Volle Breite des Containers */
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s ease;
    }

    .map-container:hover {
        transform: scale(1.02);
    }

    .content-section {
        padding-top: 1rem; /* Abstand zwischen Karte und Inhalt */
    }

    .formatted-text {
        font-size: 1rem;
        line-height: 1.6;
        color: #333;
    }

    .tooltip-container {
        position: absolute;
        background-color: white;
        border: 1px solid #ccc;
        padding: 5px;
        border-radius: 5px;
        font-size: 12px;
        pointer-events: none;
        z-index: 1000;
        box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
    }

    @media (max-width: 768px) {
        .map-container {
            height: 150px; /* Kleinere Höhe auf mobilen Geräten */
        }

        .formatted-text {
            font-size: 0.95rem;
        }
    }

    @media (max-width: 576px) {
        .map-container {
            height: 120px; /* Noch komprimierter für sehr kleine Bildschirme */
        }
    }
</style>
