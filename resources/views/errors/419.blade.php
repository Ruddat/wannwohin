<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>419 - Sitzung abgelaufen</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            margin: 0;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background: url('/assets/img/house-3386450_1920.jpg') no-repeat center center fixed;
            background-size: cover;
            color: #fff;
        }
        .error-container {
            text-align: center;
            background-color: rgba(0, 0, 0, 0.6);
            padding: 40px;
            border-radius: 15px;
        }
        .error-title {
            font-size: 6rem;
            font-weight: 700;
        }
        .error-message {
            font-size: 1.5rem;
            margin-bottom: 20px;
        }
        .btn-home {
            margin-top: 20px;
            padding: 10px 30px;
            font-size: 1.2rem;
        }
        .pixabay-credit {
            font-size: 0.9rem;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <h1 class="error-title">419</h1>
        <p class="error-message">Die Sitzung ist abgelaufen.</p>
        <p class="error-message">Bitte lade die Seite neu oder gehe zurück zur Startseite.</p>
        <a href="/" class="btn btn-light btn-home">Zur Startseite</a>
        <a href="javascript:location.reload();" class="btn btn-warning btn-home">Seite neu laden</a>
        <p class="pixabay-credit">Bildquelle: Pixabay</p>
    </div>
</body>
</html>
