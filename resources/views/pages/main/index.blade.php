@extends('layouts.main')

<!-- Ladeanzeige -->
<div id="loading-screen" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: linear-gradient(135deg, #4CAF50, #2196F3); z-index: 9999; display: flex; align-items: center; justify-content: center; flex-direction: column;">
    <div class="loading-animation">
        <svg xmlns="http://www.w3.org/2000/svg" width="80" height="80" fill="#ffffff" class="bi bi-globe" viewBox="0 0 16 16">
            <path d="M8.001 15.999c3.866 0 7-3.133 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zM2.318 8c0 3.13 2.55 5.681 5.682 5.681.176 0 .356-.005.533-.015-.008-.115-.013-.23-.017-.346-.044-1.423-.133-4.58-2.134-5.885-.956-.636-2.1-.804-3.159-.435-.174-.996-.26-2.068-.26-3.117 0-.063.002-.125.003-.188A5.58 5.58 0 0 1 2.318 8zm11.364 0a5.58 5.58 0 0 1-1.64 3.966 5.53 5.53 0 0 1-1.25-2.847A15.67 15.67 0 0 1 12.41 8c-.037-.36-.09-.714-.16-1.062-.32-.036-.648-.059-.979-.059-.2 0-.399.006-.595.016-.094-.392-.196-.781-.307-1.166a5.58 5.58 0 0 1 3.011 1.184 5.58 5.58 0 0 1 1.442 1.818c.054.093.102.186.146.28.098-.292.17-.593.208-.896z"/>
        </svg>
    </div>
    <p style="color: #fff; font-size: 1.5rem; margin-top: 20px;">Laden... Wir bereiten alles für Sie vor!</p>
    <div class="loading-bar" style="width: 80%; margin-top: 20px;">
        <div class="progress"></div>
    </div>
</div>

@section('content')
    <div role="main" class="main">
        @include('pages.main.sections.aboutMe')
        @include('pages.main.sections.experience')
        @include('pages.main.sections.blog')
    </div>
@endsection

<style>
    /* Animation Styles */
    #loading-screen {
        animation: fadeOut 0.5s ease-in-out forwards;
    }

    .loading-animation {
        text-align: center;
        animation: bounce 1.5s infinite;
    }

    .loading-bar {
        background: #ddd;
        height: 10px;
        border-radius: 5px;
        overflow: hidden;
        position: relative;
    }

    .progress {
        width: 0%;
        height: 100%;
        background: linear-gradient(90deg, #ff5722, #ff9800, #ffc107);
        animation: progress 2s infinite;
    }

    @keyframes fadeOut {
        0% {
            opacity: 1;
        }
        100% {
            opacity: 0;
            visibility: hidden;
        }
    }

    @keyframes bounce {
        0%, 100% {
            transform: translateY(0);
        }
        50% {
            transform: translateY(-10px);
        }
    }

    @keyframes progress {
        0% {
            width: 0%;
        }
        50% {
            width: 80%;
        }
        100% {
            width: 100%;
        }
    }
</style>

<script>
    document.addEventListener("DOMContentLoaded", () => {
        const loadingScreen = document.getElementById("loading-screen");
        window.onload = () => {
            setTimeout(() => {
                loadingScreen.style.display = "none";
            }, 500); // Animation 0.5 Sekunden
        };
    });
</script>
