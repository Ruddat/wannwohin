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
    </div>
@endsection

@section('css')
    <style>
        #globeViz {
            width: 100%;
            height: 600px;
            background: #000;
            cursor: grab;
        }
        #globeViz:active {
            cursor: grabbing;
        }
        #info {
            background: rgba(0,0,0,0.7);
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
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        let globe, camera, scene, renderer, controls;
        let activePoint = null;

        const colorScale = ['#ff0000', '#ff4000', '#ff8000', '#ffbf00', '#ffff00', '#bfff00', '#80ff00', '#40ff00', '#00ff00'];

        init();
        loadData();

        function init() {
            scene = new THREE.Scene();
            scene.background = new THREE.Color(0x000000);

            renderer = new THREE.WebGLRenderer({ antialias: true });
            renderer.setSize(document.getElementById('globeViz').clientWidth, document.getElementById('globeViz').clientHeight);
            document.getElementById('globeViz').appendChild(renderer.domElement);

            camera = new THREE.PerspectiveCamera(75, document.getElementById('globeViz').clientWidth / document.getElementById('globeViz').clientHeight, 0.1, 1000);
            camera.position.z = 300;

            globe = new ThreeGlobe()
                .globeImageUrl('https://unpkg.com/three-globe@2.24.7/example/img/earth-dark.jpg')
                .bumpImageUrl('https://unpkg.com/three-globe@2.24.7/example/img/earth-topology.png')
                .showAtmosphere(true)
                .atmosphereColor('#00a8ff')
                .atmosphereAltitude(0.25);

            scene.add(globe);

            const ambientLight = new THREE.AmbientLight(0xffffff, 0.6);
            scene.add(ambientLight);

            const directionalLight = new THREE.DirectionalLight(0xffffff, 0.8);
            directionalLight.position.set(1, 1, 1);
            scene.add(directionalLight);

            controls = new THREE.OrbitControls(camera, renderer.domElement);
            controls.enableDamping = true;
            controls.dampingFactor = 0.05;
            controls.minDistance = 150;
            controls.maxDistance = 500;
            controls.autoRotate = true;
            controls.autoRotateSpeed = 0.5;

            function animate() {
                requestAnimationFrame(animate);
                controls.update();
                renderer.render(scene, camera);
            }
            animate();

            window.addEventListener('resize', function() {
                camera.aspect = document.getElementById('globeViz').clientWidth / document.getElementById('globeViz').clientHeight;
                camera.updateProjectionMatrix();
                renderer.setSize(document.getElementById('globeViz').clientWidth, document.getElementById('globeViz').clientHeight);
            });
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
                        size: Math.random() * 0.5 + 0.5,
                        color: colorScale[Math.floor(Math.random() * colorScale.length)]
                    }));

                    globe.pointsData(pointsData)
                        .pointLat('latitude')
                        .pointLng('longitude')
                        .pointColor('color')
                        .pointRadius('size')
                        .pointAltitude(0.05)
                        .labelsData(pointsData) // Explizit Labels setzen
                        .labelLat('latitude')
                        .labelLng('longitude')
                        .labelText('name')
                        .labelSize(0.8) // Größer für Sichtbarkeit
                        .labelColor(() => '#ffffff')
                        .labelAltitude(0.1)
                        .labelResolution(2)
                        .labelDotRadius(0.3)
                        .onPointHover(point => {
                            document.getElementById('info').textContent = point
                                ? `${point.name} (${point.latitude.toFixed(2)}°, ${point.longitude.toFixed(2)}°)`
                                : '';
                        })
                        .onPointClick(point => {
                            console.log('Geklickter Punkt:', point); // Debugging
                            if (point) {
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
                                const radius = 1.5;

                                camera.position.set(
                                    radius * Math.cos(latRad) * Math.cos(lngRad),
                                    radius * Math.sin(latRad),
                                    radius * Math.cos(latRad) * Math.sin(lngRad)
                                );
                                camera.lookAt(0, 0, 0);

                                if (activePoint) {
                                    globe.pointColor('color');
                                }
                                activePoint = point;
                                globe.pointColor(d => d === point ? '#ff0000' : d.color);
                            } else {
                                document.getElementById('location-details').style.display = 'none';
                                activePoint = null;
                                globe.pointColor('color');
                            }
                        });

                    controls.autoRotate = true;
                })
                .catch(err => console.error('Fehler beim Laden der Daten:', err));
        }
    });
    </script>
@endsection
