import 'bootstrap';
import ClassicEditor from '@ckeditor/ckeditor5-build-classic';
import { jarallax } from 'jarallax';
import 'animate.css';
import AOS from 'aos';
import Swal from 'sweetalert2';
import GLightbox from 'glightbox';
import 'glightbox/dist/css/glightbox.min.css'; // Import the CSS

console.log('Bootstrap JS geladen'); // Prüfe, ob das Skript lädt


document.addEventListener('DOMContentLoaded', () => {
    const lightbox = GLightbox({
        selector: '.glightbox', // Matches your <a class="glightbox"> elements
        loop: true,
        touchNavigation: true,
        zoomable: true,
        openEffect: 'zoom',
        closeEffect: 'fade',
        slideThumbnails: true,
        touchFollowAxis: true
    });
});


AOS.init({
    duration: 1000,
    once: true,
});

document.addEventListener('DOMContentLoaded', () => {
    jarallax(document.querySelectorAll('[data-jarallax]'), {
        speed: 0.5
    });
    console.log('Jarallax erfolgreich initialisiert');
});

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

window.Swal = Swal;

// Verweildauer-Tracking
let startTime = Date.now();

function sendDwellTime() {
    const dwellTime = Math.floor((Date.now() - startTime) / 1000);
    const sessionId = document.querySelector('meta[name="session-id"]').content;
    const pageUrl = window.location.href;

    fetch('/track-dwell-time', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        },
        body: JSON.stringify({
            session_id: sessionId,
            dwell_time: dwellTime,
            page_url: pageUrl,
        }),
    }).catch(error => console.error('Fehler beim Senden der Verweildauer:', error));
}

window.addEventListener('beforeunload', sendDwellTime);

setInterval(() => {
    sendDwellTime();
    startTime = Date.now();
}, 10000);
