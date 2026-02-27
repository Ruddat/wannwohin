import './bootstrap';
import ClassicEditor from '@ckeditor/ckeditor5-build-classic';
import L from "leaflet";
import "leaflet.heat";


document.addEventListener('DOMContentLoaded', () => {
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
});
import Swal from 'sweetalert2';
window.Swal = Swal;


document.addEventListener("DOMContentLoaded", function () {

    if (!window.heatmapData || !window.heatmapData.length) return;

    const map = L.map('map-world').setView([20, 0], 2);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap'
    }).addTo(map);

    const heatPoints = window.heatmapData.map(p => [p.lat, p.lng, p.value]);

    L.heatLayer(heatPoints, {
        radius: 25,
        blur: 15,
        maxZoom: 6,
    }).addTo(map);
});


import Chart from "chart.js/auto";

const ctx = document.getElementById('statusChart');

if (ctx) {
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Active', 'Pending'],
            datasets: [{
                data: [
                    window.activeLocations,
                    window.pendingLocations
                ]
            }]
        }
    });
}
