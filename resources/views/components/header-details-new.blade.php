<section class="header-section parallax-section">
    <div class="container">
        <div class="row align-items-center">
            <!-- Hauptbild -->
            <div class="col-md-6">
                <div class="image-container">
                    <img src="{{ $pic3Text ?? asset('default-main.jpg') }}" alt="Reiseziel" class="img-fluid bordered-image">
                    <x-breadcrumb />
                </div>
            </div>

            <!-- Text mit Überschrift -->
            <div class="col-md-6">
                <div class="text-container">
                    <h2 class="travel-heading-with-bg">
                        STÄDTEREISE NACH
                    </h2>
                    <h1 class="travel-destination">
                        {!! app('autotranslate')->trans($headLine ?? 'Standardort', app()->getLocale()) !!}
                    </h1>
                </div>
            </div>
        </div>
    </div>

    <!-- Pulsierender Pfeil -->
    <div class="scroll-indicator">
        <a href="#next-section" class="scroll-down">
            <div class="arrow-down"></div>
        </a>
    </div>
</section>

<style>
/* Header-Sektion mit Parallax */
.header-section {
    position: relative;
    overflow: hidden;
    background: url('{{ $pic1Text ?? asset('default-bg.jpg') }}') no-repeat center center fixed;
    background-size: cover;
    height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.7);
}

/* Bildcontainer */
.image-container {
    position: relative;
    overflow: hidden;
    margin-bottom: 1rem;
    max-width: 90%; /* Begrenzen der Breite auf kleinen Geräten */
    margin-left: auto;
    margin-right: auto;
}

.bordered-image {
    border: 10px solid #fff;
    border-radius: 8px;
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
    width: 100%;
    height: auto;
    object-fit: cover;
}

/* Textcontainer */
.text-container {
    text-align: left;
    padding-left: 2rem;
}

.travel-heading-with-bg {
    font-size: 1.5rem;
    font-weight: bold;
    text-transform: uppercase;
    letter-spacing: 2px;
    color: #333;
    background-color: #fff;
    padding: 0.5rem 1rem;
    border-radius: 5px;
    display: inline-block;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    margin-bottom: 1rem;
}

.travel-destination {
    font-size: 3rem;
    font-weight: bold;
    text-transform: uppercase;
    color: #fff;
    background-color: rgba(0, 0, 0, 0.5);
    padding: 0.5rem 1rem;
    display: inline-block;
    transform: translateX(2ch);
    border-radius: 5px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
}

/* Scroll-Indikator */
.scroll-indicator {
    position: absolute;
    bottom: 10%;
    left: 50%;
    transform: translateX(-50%);
    text-align: center;
}

.scroll-down {
    display: inline-block;
    text-decoration: none;
    animation: pulse 1.5s infinite;
    opacity: 0.8;
}

.scroll-down:hover {
    opacity: 1;
}

.arrow-down {
    width: 0;
    height: 0;
    border-left: 15px solid transparent;
    border-right: 15px solid transparent;
    border-top: 20px solid rgb(236, 233, 18);
    margin: 0 auto;
}

/* Pulsieren */
@keyframes pulse {
    0% {
        transform: translateY(0);
        opacity: 0.8;
    }
    50% {
        transform: translateY(-10px);
        opacity: 1;
    }
    100% {
        transform: translateY(0);
        opacity: 0.8;
    }
}

/* Parallax-Fix für Mobilgeräte */
@media (max-width: 768px) {
    .header-section {
        background-attachment: scroll; /* Parallax deaktivieren */
    }

    .text-container {
        text-align: center; /* Zentrierter Text auf Mobilgeräten */
        padding-left: 0;
    }

    .travel-heading-with-bg {
        font-size: 1.2rem;
        padding: 0.4rem 0.8rem;
    }

    .travel-destination {
        font-size: 2rem;
        padding: 0.4rem 0.8rem;
        transform: none; /* Verschiebung entfernen */
    }

    .scroll-indicator {
        bottom: 5%; /* Position anpassen */
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', () => {
    // Parallax-Effekt
    const parallaxSection = document.querySelector('.header-section');
    window.addEventListener('scroll', () => {
        if (window.innerWidth > 768) { // Parallax nur auf größeren Geräten
            const scrollPosition = window.scrollY;
            parallaxSection.style.backgroundPositionY = `${scrollPosition * 0.5}px`;
        }
    });

    // Smooth-Scroll für den Pfeil
    const scrollLink = document.querySelector('.scroll-down');
    if (scrollLink) {
        scrollLink.addEventListener('click', (e) => {
            e.preventDefault();
            document.querySelector('#location_page')?.scrollIntoView({ behavior: 'smooth' });
        });
    }
});
</script>
