<div class="row">
    <div class="form-group col text-center align-content-center">
        <div class="my-1">
            <i title="Tagestemperatur" class="fas fa-cloud-sun fa-2x"></i>
            <br>
            <label>Tagestemperatur</label>
        </div>
        <div>
            <div class="slider">
                <input step="5" type="range" min="0" max="35" value="25" oninput="rangeValue.innerText = this.value">
                <p id="rangeValue">25</p>
            </div>
        </div>
    </div>
</div>
