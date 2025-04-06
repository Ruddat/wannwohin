<div class="trip-map-container">
    <svg id="tripMapGraphic" width="100%" height="500px" viewBox="0 0 800 500">
        <!-- Mittelpunkt der Karte (Standort) -->
        <circle cx="400" cy="250" r="12" fill="#0d6efd" stroke="#fff" stroke-width="3" />
        <text x="400" y="245" fill="white" font-size="12" text-anchor="middle" dominant-baseline="middle">Du</text>

        <!-- Dynamische Punkte und Linien werden via JS injected -->
    </svg>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const mapCenter = { x: 400, y: 250 };
        const radius = 200;

        @if (!empty($tripActivities))
            const activities = @json($tripActivities);
            const svg = document.getElementById('tripMapGraphic');

            activities.forEach((activity, index) => {
                if (!activity.latitude || !activity.longitude || !activity.distance) return;

                const angle = (360 / activities.length) * index * (Math.PI / 180);
                const x = mapCenter.x + radius * Math.cos(angle);
                const y = mapCenter.y + radius * Math.sin(angle);

                // Linie zeichnen
                const line = document.createElementNS('http://www.w3.org/2000/svg', 'line');
                line.setAttribute('x1', mapCenter.x);
                line.setAttribute('y1', mapCenter.y);
                line.setAttribute('x2', x);
                line.setAttribute('y2', y);
                line.setAttribute('stroke', '#ccc');
                line.setAttribute('stroke-width', 2);
                svg.appendChild(line);

                // Punkt zeichnen
                const circle = document.createElementNS('http://www.w3.org/2000/svg', 'circle');
                circle.setAttribute('cx', x);
                circle.setAttribute('cy', y);
                circle.setAttribute('r', 10);
                circle.setAttribute('fill', '#6b4e9c');
                svg.appendChild(circle);

                // Titel anzeigen
                const label = document.createElementNS('http://www.w3.org/2000/svg', 'text');
                label.setAttribute('x', x);
                label.setAttribute('y', y - 15);
                label.setAttribute('text-anchor', 'middle');
                label.setAttribute('fill', '#000');
                label.setAttribute('font-size', '12');
                label.textContent = `${activity.title}`;
                svg.appendChild(label);

                // Entfernung anzeigen
                const dist = document.createElementNS('http://www.w3.org/2000/svg', 'text');
                dist.setAttribute('x', x);
                dist.setAttribute('y', y + 20);
                dist.setAttribute('text-anchor', 'middle');
                dist.setAttribute('fill', '#555');
                dist.setAttribute('font-size', '10');
                dist.textContent = `${activity.distance} km`;
                svg.appendChild(dist);
            });
        @endif
    });
</script>

<style>
    .trip-map-container {
        background: #f8f9fa;
        border: 2px dashed #ddd;
        border-radius: 10px;
        padding: 1rem;
        margin-top: 2rem;
    }
</style>
