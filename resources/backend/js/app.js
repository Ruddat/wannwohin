import './bootstrap'; // Bootstrap-Initialisierung (falls nötig)
import ClassicEditor from '@ckeditor/ckeditor5-build-classic';
import Swal from 'sweetalert2';
import ApexCharts from 'apexcharts';
import L from 'leaflet';
import 'leaflet/dist/leaflet.css'; // Leaflet-CSS direkt importieren

// Globale Zuweisungen für externe Zugriffe (falls nötig)
window.Swal = Swal;
window.ApexCharts = ApexCharts;
window.L = L;

document.addEventListener('DOMContentLoaded', () => {
    // CKEditor Initialisierung
    const editorElement = document.querySelector('#editor');
    if (editorElement) {
        ClassicEditor.create(editorElement)
            .then(editor => {
                console.log('CKEditor initialized:', editor);
            })
            .catch(error => {
                console.error('CKEditor error:', error);
            });
    }

    // Traffic Summary Chart mit ApexCharts
    const trafficChartElement = document.querySelector('#trafficSummaryChart');
    if (trafficChartElement && window.trafficSummaryData && window.trafficSummaryMonths) {
        const trafficSummaryOptions = {
            series: [{
                name: 'Searches',
                data: window.trafficSummaryData
            }],
            chart: {
                type: 'bar',
                height: 350,
                toolbar: {
                    show: false
                },
                animations: {
                    enabled: true,
                    easing: 'easeinout',
                    speed: 800
                }
            },
            xaxis: {
                categories: window.trafficSummaryMonths
            },
            responsive: [
                {
                    breakpoint: 768,
                    options: {
                        chart: {
                            height: 300
                        },
                        xaxis: {
                            labels: {
                                show: true,
                                style: {
                                    fontSize: '10px'
                                }
                            }
                        }
                    }
                }
            ]
        };

        const trafficSummaryChart = new ApexCharts(trafficChartElement, trafficSummaryOptions);
        trafficSummaryChart.render();
    } else {
        console.warn('Traffic Summary Chart konnte nicht initialisiert werden: Daten oder Element fehlen.');
    }

    // Locations Map mit Leaflet
    const mapElement = document.querySelector('#map-world');
    if (mapElement && window.topLocations) {
        const map = L.map(mapElement).setView([20, 0], 2);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 18,
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        window.topLocations.forEach(location => {
            L.marker([location.lat, location.lon])
                .addTo(map)
                .bindPopup(`<b>${location.title}</b><br>Suchen: ${location.search_count}`);
        });
    } else {
        console.warn('Leaflet Map konnte nicht initialisiert werden: Daten oder Element fehlen.');
    }
});

// Livewire Toast-Handling
document.addEventListener('livewire:init', () => {
    Livelivewire.on('show-toast', ({ type, message }) => {
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
