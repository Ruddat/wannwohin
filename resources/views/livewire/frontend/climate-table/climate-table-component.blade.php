<div class="compact-climate-container">
    <div class="d-flex align-items-center mb-2">
        <label for="year" class="me-2 fw-semibold">Jahr:</label>
        <select id="year" wire:model.change="year" class="form-select form-select-sm w-auto">
            @foreach($availableYears as $availableYear)
                <option value="{{ $availableYear }}">{{ $availableYear }}</option>
            @endforeach
        </select>
    </div>

    <table class="table table-striped table-bordered table-hover table-sm climate-table">
        <thead>
            <tr>
                <th><i class="far fa-calendar-alt"></i></th>
                <th><i class="fas fa-cloud-sun"></i></th>
                <th><i class="fas fa-cloud-moon"></i></th>
                <th><i class="fas fa-sun"></i></th>
                <th><i class="fas fa-umbrella"></i></th>
                <th><i class="fas fa-cloud-rain"></i></th>
            </tr>
        </thead>
        <tbody>
            @foreach($monthlyData as $data)
                <tr>
                    <td>{{ substr($data->month_name, 0, 3) }}</td>
                    <td>{{ number_format($data->daily_temperature, 0, ',', '.') }}°</td>
                    <td>{{ number_format($data->night_temperature, 0, ',', '.') }}°</td>
                    <td>{{ $data->sunshine_per_day ? round($data->sunshine_per_day / 1000) . 'h' : '-' }}</td>
                    <td>{{ round($data->rainfall) }}mm</td>
                    <td>{{ $data->rainfall ? round(min($data->rainfall / 10, 15)) . 't' : '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

<style>
.compact-climate-container {
    max-width: 100%;
    overflow-x: auto; /* Horizontaler Scroll bei Bedarf */
}

.climate-table {
    font-size: 0.85rem; /* Kleinere Schrift */
    margin-bottom: 1rem;
}

.climate-table th,
.climate-table td {
    padding: 0.3rem; /* Weniger Padding */
    text-align: center;
    vertical-align: middle;
    white-space: nowrap; /* Kein Zeilenumbruch */
}

.climate-table th {
    background-color: #f8f9fa;
}

.form-select-sm {
    padding: 0.25rem 0.5rem; /* Kompakter Dropdown */
    font-size: 0.875rem;
}

@media (max-width: 576px) {
    .climate-table {
        font-size: 0.75rem; /* Noch kleinere Schrift auf Mobile */
    }
    .climate-table th,
    .climate-table td {
        padding: 0.2rem;
    }
}
</style>
</div>
