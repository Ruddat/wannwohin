<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Seite nicht gefunden</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            margin: 0;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background: url('/assets/img/beach-3369140_1920.jpg') no-repeat center center fixed;
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
        <h1 class="error-title">404</h1>
        <p class="error-message">Ups! Diese Seite scheint auf Reisen zu sein.</p>
        <p class="error-message">Lass uns zurück zur Startseite oder entdecke neue Reiseziele!</p>
        <a href="/" class="btn btn-light btn-home">Zur Startseite</a>
        <a href="/popular-destinations" class="btn btn-warning btn-home">Beliebte Reiseziele</a>
        <p class="pixabay-credit">Bildquelle: Pixabay</p>
    </div>
</body>
</html>
