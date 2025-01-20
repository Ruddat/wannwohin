<div class="card bg-light mb-3">
    <div class="card-header">Urlaub</div>
    <div class="card-body">
        <div class="row">
            @foreach($activities as $key => $activity)
                <div class="col-3">
                    <div class="form-group">
                        <div class="form-check d-flex align-items-center">
                            <input
                                name="activities[{{ $key }}]"
                                id="activity-{{ $key }}"
                                value="{{ $key }}"
                                class="form-check-input details_search_result_count"
                                type="checkbox"
                                @if(isset(request()->activities[$key]) && request()->activities[$key] == $key) checked @endif
                            >
                            <label class="form-check-label d-flex align-items-center ms-3" for="activity-{{ $key }}">
                                <span class="me-2">{{ $activity['title'] }}</span>
                                @if($key == "list_island")
                                    <img src="{{ asset('img/insel-icon.png') }}" alt="Insel" title="Insel" style="height: 30px;">
                                @else
                                    <i class="fas {{ $activity['icon'] }} fa-2x" title="{{ $activity['title'] }}"></i>
                                @endif
                            </label>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

<style>


</style>
