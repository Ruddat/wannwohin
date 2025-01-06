<div class="container my-4">
    <div class="row">
        <!-- Interaktive Karte -->
        <div class="experience-info col-lg-3 col-sm-5 bg-color-primary p-0 article-img d-flex" style="border-radius: 8px;">
            <svg id="map" viewBox="0 0 100 400" style="width: 100%; height: 300px;"></svg>
        </div>

        <!-- Beschreibung -->
        <div class="experience-description col-lg-9 col-sm-7 bg-color-light px-4 py-3 rounded-end">
            <h4 class="text-color-dark font-weight-semibold">Lage und Klima</h4>
            <p class="text-black">
                Abu Dhabi befindet sich in einer subtropischen Klimazone. Die Sommer sind extrem heiß und trocken. Temperaturen um die 40°C in den Monaten Juli und August sind normal. Nachts sinken sie auf 26°C bis 29°C.
                Zwischen den Monaten November bis März sind die Temperaturen angenehmer und ab und zu fällt auch Regen. Der Persische Golf lädt allerdings zu jeder Jahreszeit zum Baden ein.
                Wenn auch im Februar Temperaturen um die 21°C zu erwarten sind, steigen sie jedoch recht schnell in den kommenden Monaten auf ca. 30°C.
            </p>
            <p class="text-black">
                Die Stadt ist die Hauptstadt des Emirates und der Vereinigten Arabischen Emirate. Über 1,5 Millionen Einwohner leben hier. Abu Dhabi gehört zu den reichsten Städten der Welt.
            </p>
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

    // Marker hinzufügen
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

    // Nadel-Icon hinzufügen (Faktor 4 vergrößert)
    const marker = svg.append("image")
        .attr("xlink:href", "/assets/img/push-pin-svgrepo-com.png") // Pfad zum Nadel-Icon
        .attr("x", width / 2 - 60) // Zentrieren in der Mitte der viewBox
        .attr("y", height / 2 - 200) // Zentrieren in der Mitte der viewBox
        .attr("width", 120) // Größe des Icons (Faktor 4)
        .attr("height", 200) // Größe des Icons (Faktor 4)

    // Tooltip hinzufügen
    const tooltip = d3.select("body")
        .append("div")
        .style("position", "absolute")
        .style("background-color", "white")
        .style("border", "1px solid #ccc")
        .style("padding", "5px")
        .style("border-radius", "5px")
        .style("font-size", "12px")
        .style("visibility", "hidden")
        .style("z-index", "1000"); // Sicherstellen, dass der Tooltip über anderen Elementen liegt

    marker.on("mouseover", function (event) {
        console.log("Mouseover event triggered"); // Debugging
        tooltip.style("visibility", "visible")
            .text(geoLocation.name);
    })
    .on("mousemove", function (event) {
        console.log("Mousemove event triggered"); // Debugging
        tooltip.style("top", (event.pageY - 10) + "px")
            .style("left", (event.pageX + 10) + "px");
    })
    .on("mouseout", function () {
        console.log("Mouseout event triggered"); // Debugging
        tooltip.style("visibility", "hidden");
    });
}).catch(error => {
    console.error("Fehler beim Laden der Weltkarte:", error);
});
</script>
