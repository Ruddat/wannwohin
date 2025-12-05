<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wartungsmodus | Wann-Wohin.de</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', Arial, sans-serif;
            background: url('{{ asset('assets/img/person-putting-travel-word.jpg') }}') no-repeat center center fixed;
            background-size: cover;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            color: #fff;
            text-align: center;
            position: relative;
        }

        /* Overlay für bessere Lesbarkeit */
        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5); /* Dunkelgrauer Schleier */
            z-index: 1;
        }

        .content {
            position: relative;
            z-index: 2;
            background: rgba(255, 255, 255, 0.1);
            padding: 40px;
            border-radius: 15px;
            backdrop-filter: blur(5px); /* Glas-Effekt */
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
            max-width: 600px;
        }

        h1 {
            font-size: 2.5rem;
            margin-bottom: 20px;
            color: #fff;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
        }

        p {
            font-size: 1.2rem;
            line-height: 1.6;
            color: #ddd;
            margin-bottom: 20px;
        }

        .countdown {
            font-size: 1.5rem;
            color: #ffd700; /* Goldgelb für Countdown */
            margin-top: 20px;
        }

        /* Animation für den Titel */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        h1, p {
            animation: fadeIn 1s ease-in-out;
        }

        /* Button-Styling */
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background: #ff6f61; /* Korallenrot, reisetauglich */
            color: #fff;
            text-decoration: none;
            border-radius: 25px;
            font-weight: bold;
            transition: background 0.3s;
        }

        .btn:hover {
            background: #e65b50;
        }
    </style>

<style>
    .loader {
        border: 4px solid #f3f3f3;
        border-top: 4px solid #ff6f61;
        border-radius: 50%;
        width: 30px;
        height: 30px;
        animation: spin 1s linear infinite;
        margin: 20px auto;
    }
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
</style>

</head>
<body>
    <div class="content">
        <img src="{{ asset('assets/img/wannwohin-small.jpg') }}" alt="Wann-Wohin.de" style="max-width: 150px; margin-bottom: 20px;">
        <div class="loader" style="margin-top: 20px;"></div>

        <h1>Wir machen uns reisefertig!</h1>
        <p>{{ $message }}</p>
        <p>Unsere Seite wird gerade für dein nächstes Abenteuer vorbereitet. Wir sind bald wieder für dich da!</p>

        <!-- Optionaler Countdown -->
        @if ($endAt = \App\Models\ModSiteSettings::get('maintenance_end_at'))
            <div class="countdown" id="countdown"></div>
            <script>
                const endTime = new Date('{{ $endAt }}').getTime();
                const countdownElement = document.getElementById('countdown');

                function updateCountdown() {
                    const now = new Date().getTime();
                    const distance = endTime - now;

                    if (distance <= 0) {
                        countdownElement.innerHTML = 'Wir sind gleich wieder da!';
                        return;
                    }

                    const days = Math.floor(distance / (1000 * 60 * 60 * 24));
                    const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                    const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                    const seconds = Math.floor((distance % (1000 * 60)) / 1000);

                    countdownElement.innerHTML = `Noch ${days} Tage, ${hours} Stunden, ${minutes} Minuten und ${seconds} Sekunden`;
                }

                updateCountdown();
                setInterval(updateCountdown, 1000);
            </script>
        @endif

        <a href="/" class="btn">Zurück zur Startseite</a>
    </div>
</body>
</html>
