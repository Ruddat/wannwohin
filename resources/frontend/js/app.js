import './bootstrap';
import ClassicEditor from '@ckeditor/ckeditor5-build-classic';

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

// Verweildauer-Tracking
let startTime = Date.now();

function sendDwellTime() {
    const dwellTime = Math.floor((Date.now() - startTime) / 1000); // Sekunden
    const sessionId = document.querySelector('meta[name="session-id"]').content; // Session-ID aus Meta-Tag
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

// Sende Daten, wenn die Seite verlassen wird
window.addEventListener('beforeunload', sendDwellTime);

// Optional: Sende regelmäßig Updates (z. B. alle 10 Sekunden)
setInterval(() => {
    sendDwellTime();
    startTime = Date.now(); // Reset für nächste Messung
}, 10000);

