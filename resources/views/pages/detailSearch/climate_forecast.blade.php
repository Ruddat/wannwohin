<div class="card">
    <div class="card-header">Klimavorhersage</div>
    <div class="card-body">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Monat</th>
                    <th>Tagestemp. (°C)</th>
                    <th>Nachttemp. (°C)</th>
                    <th>Sonnenstunden</th>
                    <th>Luftfeuchtigkeit (%)</th>
                    <th>Regentage</th>
                </tr>
            </thead>
            <tbody>
                @foreach($futureMonths as $month)
                    <tr>
                        <td>{{ $month['month'] }}</td>
                        <td>{{ number_format($month['daily_temperature'], 1) }}</td>
                        <td>{{ number_format($month['night_temperature'], 1) }}</td>
                        <td>{{ number_format($month['sunshine_per_day'], 1) }}</td>
                        <td>{{ number_format($month['humidity'], 1) }}</td>
                        <td>{{ number_format($month['rainy_days'], 1) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
