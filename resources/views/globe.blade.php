@extends('layouts.main')

@section('content')
    <div class="container mt-5">
        <h1 class="text-center mb-4 fw-bold">Entdecke Reiseziele weltweit</h1>
        <div id="globeViz" class="shadow-lg rounded"></div>
        <div id="info" class="mt-3 text-center text-white"></div>
        <div id="location-details" class="mt-4 p-4 bg-dark text-white rounded" style="display: none;">
            <div class="row">
                <div class="col-md-4">
                    <img id="location-image" src="" class="img-fluid rounded" alt="Location Image" style="display: none;">
                </div>
                <div class="col-md-8">
                    <h3 id="location-title"></h3>
                    <p id="location-description"></p>
                </div>
            </div>
        </div>
        <!-- Tooltip für Hover -->
        <div id="tooltip" style="position: absolute; background: rgba(0, 0, 0, 0.8); color: white; padding: 10px; border-radius: 5px; display: none; pointer-events: auto; cursor: pointer; z-index: 1000;">
            <h4 id="tooltip-title" style="margin: 0 0 5px 0;"></h4>
            <img id="tooltip-image" src="" style="max-width: 100px; max-height: 100px; display: none;" alt="Tooltip Image">
            <p id="tooltip-description" style="margin: 5px 0 0 0;"></p>
        </div>
    </div>

    <style>
        #globeViz {
            width: 100%;
            height: 600px;
            background: #1e3a8a;
            cursor: grab;
        }
        #globeViz:active {
            cursor: grabbing;
        }
        #info {
            background: rgba(0, 0, 0, 0.7);
            padding: 10px;
            border-radius: 5px;
            display: inline-block;
        }
        #location-details {
            transition: all 0.3s ease;
        }
        .img-fluid {
            max-height: 200px;
            object-fit: cover;
        }
    </style>
@endsection

@section('js')
    <script src="https://unpkg.com/three@0.132.2/build/three.min.js"></script>
    <script src="https://unpkg.com/three-globe@2.24.7/dist/three-globe.min.js"></script>
    <script src="https://unpkg.com/three@0.132.2/examples/js/controls/OrbitControls.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/tween.js/18.6.4/tween.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        let globe, camera, scene, renderer, controls, raycaster, mouse;
        let activePoint = null;
        let autoRotateTimeout;
        let hoveredPoint = null;

        const colorScale = ['#ff0000', '#ff4000', '#ff8000', '#ffbf00', '#ffff00', '#bfff00', '#80ff00', '#40ff00', '#00ff00'];
        const tooltip = document.getElementById('tooltip');

        init();
        loadData();

        function init() {
            scene = new THREE.Scene();
            scene.background = new THREE.Color(0x1e3a8a);

            renderer = new THREE.WebGLRenderer({ antialias: true });
            renderer.setSize(document.getElementById('globeViz').clientWidth, document.getElementById('globeViz').clientHeight);
            document.getElementById('globeViz').appendChild(renderer.domElement);

            camera = new THREE.PerspectiveCamera(75, document.getElementById('globeViz').clientWidth / document.getElementById('globeViz').clientHeight, 0.1, 1000);
            camera.position.z = 300;

            globe = new ThreeGlobe()
                .globeImageUrl('https://unpkg.com/three-globe@2.24.7/example/img/earth-blue-marble.jpg')
                .bumpImageUrl('https://unpkg.com/three-globe@2.24.7/example/img/earth-topology.png')
                .showAtmosphere(true)
                .atmosphereColor('#60a5fa')
                .atmosphereAltitude(0.25);

            scene.add(globe);

            const ambientLight = new THREE.AmbientLight(0x60a5fa, 0.6);
            scene.add(ambientLight);

            const directionalLight = new THREE.DirectionalLight(0xffffff, 0.8);
            directionalLight.position.set(1, 1, 1);
            scene.add(directionalLight);

            controls = new THREE.OrbitControls(camera, renderer.domElement);
            controls.enableDamping = true;
            controls.dampingFactor = 0.05;
            controls.minDistance = 50;
            controls.maxDistance = 500;
            controls.autoRotate = true;
            controls.autoRotateSpeed = 0.5;
            controls.enablePan = false;

            raycaster = new THREE.Raycaster();
raycaster.params.Points = { threshold: 0.6 };
mouse = new THREE.Vector2();

            function animate() {
                requestAnimationFrame(animate);
                if (typeof TWEEN !== 'undefined') {
                    TWEEN.update();
                }
                controls.update();
                renderer.render(scene, camera);
            }
            animate();

            window.addEventListener('resize', function() {
                camera.aspect = document.getElementById('globeViz').clientWidth / document.getElementById('globeViz').clientHeight;
                camera.updateProjectionMatrix();
                renderer.setSize(document.getElementById('globeViz').clientWidth, document.getElementById('globeViz').clientHeight);
            });

            // Rotation bei Interaktion pausieren
            document.getElementById('globeViz').addEventListener('mousedown', () => {
                controls.autoRotate = false;
                clearTimeout(autoRotateTimeout);
            });

            document.getElementById('globeViz').addEventListener('mouseup', () => {
                autoRotateTimeout = setTimeout(() => {
                    controls.autoRotate = true;
                }, 2000);
            });

            // Hover-Interaktion
            document.getElementById('globeViz').addEventListener('mousemove', onMouseMove);
            document.getElementById('globeViz').addEventListener('mouseout', () => {
                tooltip.style.display = 'none';
                hoveredPoint = null;
                document.getElementById('info').textContent = '';
            });

            // Klick auf Tooltip
            tooltip.addEventListener('click', onTooltipClick);
        }

        function onMouseMove(event) {
            event.preventDefault();

            mouse.x = ((event.clientX - renderer.domElement.getBoundingClientRect().left) / renderer.domElement.clientWidth) * 2 - 1;
            mouse.y = -((event.clientY - renderer.domElement.getBoundingClientRect().top) / renderer.domElement.clientHeight) * 2 + 1;

            raycaster.setFromCamera(mouse, camera);
            const intersects = raycaster.intersectObjects(globe.points().children.concat(globe.labels().children));

            if (intersects.length > 0) {
                const point = intersects[0].object.userData;
                hoveredPoint = point;

                // Tooltip anzeigen
                document.getElementById('tooltip-title').textContent = point.name || 'Unbekannt';
                document.getElementById('tooltip-description').textContent = point.description || 'Keine Beschreibung verfügbar';

                const imgElement = document.getElementById('tooltip-image');
                if (point.image_url) {
                    imgElement.src = point.image_url;
                    imgElement.style.display = 'block';
                } else {
                    imgElement.style.display = 'none';
                }

                tooltip.style.display = 'block';
                tooltip.style.left = `${event.clientX + 10}px`;
                tooltip.style.top = `${event.clientY + 10}px`;

                document.getElementById('info').textContent = `${point.name} (${point.latitude.toFixed(2)}°, ${point.longitude.toFixed(2)}°}`;
            } else {
                tooltip.style.display = 'none';
                hoveredPoint = null;
                document.getElementById('info').textContent = '';
            }
        }

        function onTooltipClick() {
            if (!hoveredPoint) return;

            const point = hoveredPoint;
            console.log('Tooltip geklickt für:', point);

            document.getElementById('location-title').textContent = point.name || 'Unbekannt';
            document.getElementById('location-description').textContent = point.description || 'Keine Beschreibung verfügbar';

            const imgElement = document.getElementById('location-image');
            if (point.image_url) {
                console.log('Bild-URL:', point.image_url);
                imgElement.src = point.image_url;
                imgElement.style.display = 'block';
            } else {
                imgElement.style.display = 'none';
            }

            document.getElementById('location-details').style.display = 'block';

            const latRad = point.latitude * (Math.PI / 180);
            const lngRad = -point.longitude * (Math.PI / 180);
            const radius = 50;

            const targetPosition = new THREE.Vector3(
                radius * Math.cos(latRad) * Math.cos(lngRad),
                radius * Math.sin(latRad),
                radius * Math.cos(latRad) * Math.sin(lngRad)
            );

            if (typeof TWEEN !== 'undefined') {
                new TWEEN.Tween(camera.position)
                    .to(targetPosition, 1000)
                    .easing(TWEEN.Easing.Quadratic.Out)
                    .onUpdate(() => camera.lookAt(0, 0, 0))
                    .start();
            } else {
                camera.position.copy(targetPosition);
                camera.lookAt(0, 0, 0);
            }

            if (activePoint) {
                globe.pointColor('color');
            }
            activePoint = point;
            globe.pointColor(d => d === point ? '#ff0000' : d.color);

            tooltip.style.display = 'none'; // Tooltip ausblenden nach Klick
        }

        function loadData() {
            fetch('{{ url('/api/locations') }}')
                .then(response => {
                    if (!response.ok) throw new Error('API Fehler: ' + response.status);
                    return response.json();
                })
                .then(locations => {
                    if (!locations || locations.length === 0) {
                        console.warn('Keine Locations erhalten');
                        return;
                    }

                    console.log('Geladene Locations:', locations);

                    const pointsData = locations.map(loc => ({
                        ...loc,
                        size: Math.random() * 0.2 + 0.1,
                        color: colorScale[Math.floor(Math.random() * colorScale.length)]
                    }));

                    globe.pointsData(pointsData);
                    globe.pointLat('latitude');
                    globe.pointLng('longitude');
                    globe.pointColor('color');
                    globe.pointRadius('size');
                    globe.pointAltitude(0.01);

                    globe.labelsData(pointsData);
                    globe.labelLat('latitude');
                    globe.labelLng('longitude');
                    globe.labelText('name');
                    globe.labelSize(0.8);
                    globe.labelColor(() => '#ffffff');
                    globe.labelAltitude(0.02);
                    globe.labelResolution(2);
                    globe.labelDotRadius(0.3);

                    controls.autoRotate = true;
                })
                .catch(err => console.error('Fehler beim Laden der Daten:', err));
        }
    });
    </script>
@endsection
