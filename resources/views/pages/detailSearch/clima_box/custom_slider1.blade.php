<div class="row">
    <div class="form-group col text-center align-content-center">
        <div class="my-1">
            <i title="Nachttemperatur" class="fas fa-cloud-moon fa-2x"></i>
            <br>
            <label>Nachttemperatur</label>
        </div>
        <div>
            <div slider id="slider-distance">
                <div>
                    <div inverse-left style="width:70%;"></div>
                    <div inverse-right style="width:70%;"></div>
                    <div range style="left:30%;right:40%;"></div>
                    <span thumb style="left:30%;"></span>
                    <span thumb style="left:60%;"></span>
                    <div sign style="left:28%;">
                        <span id="value">10</span>
                    </div>
                    <div sign style="left:85%;">
                        <span id="value">30</span>
                    </div>
                </div>
                <input type="range" tabindex="0" value="10" max="35" min="0" step="1" oninput="
  this.value=Math.min(this.value,this.parentNode.childNodes[5].value-1);
  var value=(100/(parseInt(this.max)-parseInt(this.min)))*parseInt(this.value)-(100/(parseInt(this.max)-parseInt(this.min)))*parseInt(this.min);
  var children = this.parentNode.childNodes[1].childNodes;
  children[1].style.width=value+'%';
  children[5].style.left=value+'%';
  children[7].style.left=value+'%';children[11].style.left=value+'%';
  children[11].childNodes[1].innerHTML=this.value;" />

                <input type="range" tabindex="0" value="30" max="35" min="0" step="1" oninput="
  this.value=Math.max(this.value,this.parentNode.childNodes[3].value-(-1));
  var value=(100/(parseInt(this.max)-parseInt(this.min)))*parseInt(this.value)-(100/(parseInt(this.max)-parseInt(this.min)))*parseInt(this.min);
  var children = this.parentNode.childNodes[1].childNodes;
  children[3].style.width=(100-value)+'%';
  children[5].style.right=(100-value)+'%';
  children[9].style.left=value+'%';children[13].style.left=value+'%';
  children[13].childNodes[1].innerHTML=this.value;" />
            </div>


        </div>
    </div>
</div>

