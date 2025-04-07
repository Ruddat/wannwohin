<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>{{ $tripName ?? 'Mein Reiseplan' }}</title>
    <style>
        @page {
            margin: 0.5cm;
            size: A4;
        }

        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            color: #333;
            font-size: 9pt;
            line-height: 1.3;
        }

        .header {
            text-align: center;
            padding: 10px 0;
            margin-bottom: 10px;
            border-bottom: 2px solid #1e40af;
        }

        .header h1 {
            margin: 0;
            font-size: 14pt;
            font-weight: bold;
        }

        .header p {
            margin: 3px 0 0;
            font-size: 10pt;
        }

        .location-info {
            padding: 5px 0;
            margin-bottom: 8px;
            text-align: center;
            font-size: 9pt;
        }

        /* 2-Spalten Layout */
        .day-container {
            width: 100%;
            column-count: 2;
            column-gap: 10px;
        }

        .day {
            break-inside: avoid;
            page-break-inside: avoid;
            margin-bottom: 10px;
            border: 1px solid #d1d5db;
            border-radius: 4px;
            background: white;
        }

        .day-header {
            padding: 5px 8px;
            background: #1e40af;
            color: white;
            font-weight: bold;
            font-size: 10pt;
        }

        .day-content {
            padding: 8px;
        }

        .activity-item {
            margin-bottom: 6px;
            padding-bottom: 6px;
            border-bottom: 1px dotted #e5e7eb;
            font-size: 8pt;
        }

        .activity-item:last-child {
            margin-bottom: 0;
            padding-bottom: 0;
            border-bottom: none;
        }

        .activity-title {
            font-weight: bold;
            color: #1e40af;
        }

        .activity-meta {
            margin-top: 2px;
        }

        .activity-meta div {
            display: inline-block;
            margin-right: 8px;
        }

        .notes {
            margin-top: 6px;
            padding: 5px;
            background: #f8fafc;
            font-size: 8pt;
            border-left: 2px solid #dbeafe;
        }

        .footer {
            text-align: center;
            margin-top: 10px;
            padding-top: 5px;
            font-size: 7pt;
            color: #6b7280;
            border-top: 1px solid #e5e7eb;
        }

        /* Farben für Tage */
        .day-1 .day-header { background: #1e40af; }
        .day-2 .day-header { background: #065f46; }
        .day-3 .day-header { background: #9a3412; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $tripName ?? 'Mein Reiseplan' }}</h1>
        <p>Reise nach {{ $locationTitle }}</p>
    </div>

    <div class="location-info">
        {{ $locationTitle }} • {{ count($tripDays) }} Tage • Erstellt am {{ date('d.m.Y') }}
    </div>

    <div class="day-container">
        @foreach($tripDays as $index => $day)
            <div class="day day-{{ $index + 1 }}">
                <div class="day-header">{{ $day['name'] ?? 'Tag ' . ($index + 1) }}</div>
                <div class="day-content">
                    @forelse($day['activities'] as $activity)
                        <div class="activity-item">
                            <div class="activity-title">{{ $activity['title'] }}</div>
                            <div class="activity-meta">
                                <div><strong>Dauer:</strong> {{ $activity['duration'] }}</div>
                                @if(!empty($activity['category']))
                                    <div><strong>Kategorie:</strong> {{ $activity['category'] }}</div>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="activity-item">
                            <div class="activity-title">Keine Aktivitäten</div>
                        </div>
                    @endforelse

                    @if(!empty($day['notes']))
                        <div class="notes">
                            {{ $day['notes'] }}
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    <div class="footer">
        Erstellt mit WannWohin Reiseplaner • {{ date('d.m.Y H:i') }}
    </div>
</body>
</html>
