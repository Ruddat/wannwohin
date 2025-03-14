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
// resources/backend/js/app.js
import Swal from 'sweetalert2';
import ApexCharts from 'apexcharts';
window.Swal = Swal;
window.ApexCharts = ApexCharts;

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
