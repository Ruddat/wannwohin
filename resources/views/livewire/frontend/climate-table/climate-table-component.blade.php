<div>
    <label for="year" class="block mb-2 font-semibold">Jahr auswählen:</label>
    <select id="year" wire:model.change="year" class="form-control mb-4">
        @foreach($availableYears as $availableYear)
            <option value="{{ $availableYear }}">{{ $availableYear }}</option>
        @endforeach
    </select>

    <table class="table table-striped table-bordered table-hover table-condensed location-climate-table climate-table mb-4">
        <thead>
            <tr>
                <th class="center"><i class="far fa-calendar-alt" title="Monat"></i></th>
                <th class="center"><i class="fas fa-cloud-sun" title="Tagesdurchschnittstemperatur"></i></th>
                <th class="center"><i class="fas fa-cloud-moon" title="Nachtdurchschnittstemperatur"></i></th>
                <th class="center"><i class="fas fa-sun" title="Sonnenstunden"></i></th>
                <th class="center"><i class="fas fa-umbrella" title="Niederschlag"></i></th>
                <th class="center"><i class="fas fa-cloud-rain" title="Regentage"></i></th>
            </tr>
        </thead>
        <tbody>
            @foreach($monthlyData as $data)
                <tr>
                    <td class="center">{{ $data->month_name }}</td>
                    <td class="center">{{ number_format($data->daily_temperature, 1, ',', '.') }} °C</td>
                    <td class="center">{{ number_format($data->night_temperature, 1, ',', '.') }} °C</td>
                    <td class="center">
                        {{ $data->sunshine_per_day ? number_format($data->sunshine_per_day / 1000, 1, ',', '.') . ' h' : '-' }}
                    </td>
                    <td class="center">{{ number_format($data->rainfall, 1, ',', '.') }} mm</td>
                    <td class="center">
                        @if($data->rainfall)
                            {{ round(min($data->rainfall / 10, 15)) }} t
                        @else
                            -
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
