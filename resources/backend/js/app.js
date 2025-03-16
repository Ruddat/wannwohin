import './bootstrap';
import ClassicEditor from '@ckeditor/ckeditor5-build-classic';
import Swal from 'sweetalert2';
import ApexCharts from 'apexcharts';
import L from 'leaflet';
import 'leaflet/dist/leaflet.css';
import 'animate.css'; // Animate.css importieren
import AOS from 'aos';
import 'bootstrap'; // Lädt das gesamte Bootstrap-JS (inkl. Popper.js)

// Globale Zuweisungen
window.Swal = Swal;
window.ApexCharts = ApexCharts;
window.L = L;

// Leaflet Icon-Pfade (statische Lösung)
delete L.Icon.Default.prototype._getIconUrl;
L.Icon.Default.mergeOptions({
    iconRetinaUrl: '/images/leaflet/marker-icon-2x.png',
    iconUrl: '/images/leaflet/marker-icon.png',
    shadowUrl: '/images/leaflet/marker-shadow.png',
});

AOS.init({
    duration: 800, // Animation-Dauer in Millisekunden
    once: true,    // Animation nur einmal ausführen
    // Weitere Optionen: https://github.com/michalsnik/aos#initialization
});

document.addEventListener('DOMContentLoaded', () => {
    // CKEditor Initialisierung
    const editorElement = document.querySelector('#editor');
    if (editorElement) {
        ClassicEditor.create(editorElement)
            .then(editor => console.log('CKEditor initialized:', editor))
            .catch(error => console.error('CKEditor error:', error));
    }

    // Traffic Summary Chart mit ApexCharts
    const trafficChartElement = document.querySelector('#trafficSummaryChart');
    if (trafficChartElement) {
        console.log('Traffic Data:', window.trafficSummaryData);
        console.log('Traffic Months:', window.trafficSummaryMonths);
        if (!window.trafficSummaryData || !window.trafficSummaryMonths || !Array.isArray(window.trafficSummaryData) || !Array.isArray(window.trafficSummaryMonths)) {
            console.error('Traffic Summary Daten ungültig:', {
                data: window.trafficSummaryData,
                months: window.trafficSummaryMonths
            });
            trafficChartElement.innerHTML = '<p class="text-muted">Keine Daten verfügbar</p>';
        } else {
            const trafficSummaryOptions = {
                series: [{
                    name: 'Searches',
                    data: window.trafficSummaryData
                }],
                chart: {
                    type: 'bar',
                    height: 350,
                    toolbar: { show: false },
                    animations: { enabled: true, easing: 'easeinout', speed: 800 }
                },
                colors: ['#206bc4'],
                xaxis: {
                    categories: window.trafficSummaryMonths
                },
                responsive: [
                    {
                        breakpoint: 768,
                        options: {
                            chart: { height: 300 },
                            xaxis: { labels: { style: { fontSize: '10px' } } }
                        }
                    }
                ]
            };
            try {
                const trafficSummaryChart = new ApexCharts(trafficChartElement, trafficSummaryOptions);
                trafficSummaryChart.render();
            } catch (error) {
                console.error('ApexCharts Render-Fehler:', error);
                trafficChartElement.innerHTML = '<p class="text-danger">Fehler beim Rendern des Diagramms</p>';
            }
        }
    }

    // Locations Map mit Leaflet
    const mapElement = document.querySelector('#map-world');
    if (mapElement) {
        console.log('Top Locations:', window.topLocations);
        if (!window.topLocations || !Array.isArray(window.topLocations)) {
            console.error('Top Locations Daten ungültig:', window.topLocations);
            mapElement.innerHTML = '<p class="text-muted">Keine Karten-Daten verfügbar</p>';
        } else {
            const map = L.map(mapElement).setView([20, 0], 2);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 18,
                attribution: '© OpenStreetMap contributors'
            }).addTo(map);

            window.topLocations.forEach(location => {
                if (location.lat && location.lon) {
                    L.marker([location.lat, location.lon])
                        .addTo(map)
                        .bindPopup(`<b>${location.title}</b><br>Suchen: ${location.search_count}`);
                } else {
                    console.warn('Ungültige Koordinaten für:', location);
                }
            });
        }
    }
});

// Livewire Toast-Handling
document.addEventListener('livewire:init', () => {
    Livewire.on('show-toast', ({ type, message }) => {
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: type,
            title: message,
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
        });
    });
});
