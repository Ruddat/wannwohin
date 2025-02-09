<div class="container my-4">
    <div class="row">
        <!-- Interaktive Karte -->
        <div class="experience-info col-lg-3 col-sm-5 bg-color-primary p-0 article-img d-flex" style="border-radius: 8px;">
            <svg id="map" viewBox="0 0 100 400" preserveAspectRatio="xMidYMid meet" style="width: 100%; max-height: 300px; overflow: hidden;"></svg>
        </div>

        <!-- Beschreibung -->
        <div class="experience-description col-lg-9 col-sm-7 bg-color-light px-4 py-3 rounded-end">
            <h4 class="text-color-dark font-weight-semibold">
                @autotranslate('Lage und Klima', app()->getLocale())
                {!! app('autotranslate')->trans($location->text_headline, app()->getLocale()) !!}
            </h4>
            <div class="formatted-text">
                {!! app('autotranslate')->trans($location->text_location_climate, app()->getLocale()) !!}
            </div>
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
    lat: {{ $location->lat }}, // Breitengrad für Abu Dhabi
    lon: {{ $location->lon }},  // Längengrad für Abu Dhabi
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

// Nadel-Icon hinzufügen (Kleinere Größe und exakte Position)
const marker = svg.append("image")
    .attr("xlink:href", "/assets/img/push-pin-svgrepo-com.png") // Pfad zum Nadel-Icon
    .attr("x", x - 15) // Zentrieren des Icons basierend auf der tatsächlichen Position
    .attr("y", y - 50) // Höhe anpassen, damit der Pin korrekt zeigt
    .attr("width", 30) // Kleinere Größe
    .attr("height", 50);

        const tooltipContainer = d3.select(".container")
    .append("div")
    .attr("class", "tooltip-container") // Verwende die CSS-Klasse
    .style("visibility", "hidden"); // Standardmäßig versteckt

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
<style>
.tooltip-container {
    position: absolute; /* Absolute Positionierung */
    background-color: white;
    border: 1px solid #ccc;
    padding: 5px;
    border-radius: 5px;
    font-size: 12px;
    pointer-events: none; /* Verhindert Mausinteraktion */
    z-index: 1000; /* Sicherstellen, dass der Tooltip oben liegt */
    box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1); /* Leichter Schatten */
}

</style>
