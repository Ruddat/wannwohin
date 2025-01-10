<div id="ex_footer">
    <div class="row align-items-center">
        <!-- Social Media Icons -->
        <div class="col-6">
            <div class="footer-ribbon">
                @include('layouts.social-icons')
            </div>
        </div>

        <!-- Abstandhalter -->
        <div class="col-2"></div>

        <!-- Impressum und Datenschutz -->
        <div class="col-4">
            <div class="footer-ribbon-right">
                <span><a href="{{ route('impressum') }}">Impressum und Datenschutz</a></span>
            </div>
        </div>
    </div>
</div>



<style>
    #ex_footer{
    height: 130px;
    background: #0e0e0e;
    border-top: 4px solid #0e0e0e;
    font-size: .9em;
    position: relative;
    clear: both;

}
#ex_footer .footer-ribbon {
    background: #0088cc;
    position: absolute;
    padding: 10px 20px 6px 20px;
    float: right;
    height: 70px;
    top: -20px;
    margin-left: 261px;
}

#ex_footer .footer-ribbon:before {
    border-right: 10px solid #646464;
    border-top: 16px solid transparent;
    border-right-color: #005580 !important;
    content: "";
    display: block;
    height: 0;
    left: -10px;
    position: absolute;
    width: 7px;
    top: 0px;
}

#ex_footer .footer-ribbon span {
    color: #FFF;
    font-size: 1.6em;
    font-family: "Shadows Into Light", cursive;
}

#ex_footer .footer-ribbon-right {
    background: #c7af19;
    position: absolute;
    padding: 10px 20px 6px 20px;
    color: white;
    font-weight: bold;
    height: 40px;
    top: -20px;
}

#ex_footer .footer-ribbon-right:before {
    border-right: 10px solid #aa9300;
    border-top: 16px solid transparent;
    content: "";
    display: block;
    height: 0;
    left: -10px;
    position: absolute;
    top: 0;
    width: 7px;
}

#ex_footer #share {
    /* background: rgba(0, 0, 0, 0.5);*/
    position: relative;
    margin: 0 auto;
    width: 520px;
    height: 60px;
    background: #0088cc;
    background: #08c;
}
#ex_footer #share span {
    width: 200px;
    height: 55px;
    float: left;
    line-height: 55px;
    text-align: center;
    color: white;
}



#ex_footer .icon-container {
    position: relative;
    display: inline-block;
    width: 60px;
    height: 65px;
    line-height: 60px;
    padding-top: 0px;
    text-align: center;
    margin: 0 auto;
}
#ex_footer .icon-container .circle {
    fill: none;
    stroke: #ffffff;
    stroke-width: 5px;
    stroke-dasharray: 40;
    transition: all 0.2s ease-in-out;
    -webkit-animation: outWaveOut 1s cubic-bezier(0.42, 0, 0.58, 1) forwards;
    animation: outWaveOut 1s cubic-bezier(0.42, 0, 0.58, 1) forwards;
}
#ex_footer .icon-container .social {
    color: white;
    font-size: 1.8em;
    position: absolute;
    top: 0px;
    left: 0px;
    width: 100%;
    height: 100%;
    transition: all 0.5s ease-in-out;
}
#ex_footer .icon-container:hover {
    cursor: pointer;
}
#ex_footer .twitter:hover .circle {
    fill: #ffffff;
    fill-opacity: 1;
    -webkit-animation: outWaveIn 1s cubic-bezier(0.42, 0, 0.58, 1) forwards, colorTwitter 1s linear forwards;
    animation: outWaveIn 1s cubic-bezier(0.42, 0, 0.58, 1) forwards, colorTwitter 1s linear forwards;
}
#ex_footer .twitter:hover .social {
    color: #3aaae1;
}
#ex_footer .facebook:hover .circle {
    fill: #ffffff;
    fill-opacity: 1;
    -webkit-animation: outWaveIn 1s cubic-bezier(0.42, 0, 0.58, 1) forwards, colorFacebook 1s linear forwards;
    animation: outWaveIn 1s cubic-bezier(0.42, 0, 0.58, 1) forwards, colorFacebook 1s linear forwards;
}
#ex_footer .facebook:hover .social {
    color: #3b5998;
}
#ex_footer .google:hover .circle {
    fill: #ffffff;
    fill-opacity: 1;
    -webkit-animation: outWaveIn 1s cubic-bezier(0.42, 0, 0.58, 1) forwards, colorGoogle 1s linear forwards;
    animation: outWaveIn 1s cubic-bezier(0.42, 0, 0.58, 1) forwards, colorGoogle 1s linear forwards;
}
#ex_footer .google:hover .social {
    color: #dd4b39;
}
#ex_footer .pinterest:hover .circle {
    fill: #ffffff;
    fill-opacity: 1;
    -webkit-animation: outWaveIn 1s cubic-bezier(0.42, 0, 0.58, 1) forwards, colorPinterest 1s linear forwards;
    animation: outWaveIn 1s cubic-bezier(0.42, 0, 0.58, 1) forwards, colorPinterest 1s linear forwards;
}
#ex_footer .pinterest:hover .social {
    color: #cb2027;
}
#ex_footer .linkedin:hover .circle {
    fill: #ffffff;
    fill-opacity: 1;
    -webkit-animation: outWaveIn 1s cubic-bezier(0.42, 0, 0.58, 1) forwards, colorLinkedin 1s linear forwards;
    animation: outWaveIn 1s cubic-bezier(0.42, 0, 0.58, 1) forwards, colorLinkedin 1s linear forwards;
}
#ex_footer .linkedin:hover .social {
    color: #007bb6;
}
@-webkit-keyframes colorTwitter {
    from {
        stroke: #ffffff;
    }
    to {
        stroke: #3aaae1;
    }
}
@keyframes colorTwitter {
    from {
        stroke: #ffffff;
    }
    to {
        stroke: #3aaae1;
    }
}
@-webkit-keyframes colorFacebook {
    from {
        stroke: #ffffff;
    }
    to {
        stroke: #3b5998;
    }
}
@keyframes colorFacebook {
    from {
        stroke: #ffffff;
    }
    to {
        stroke: #3b5998;
    }
}
@-webkit-keyframes colorGoogle {
    from {
        stroke: #ffffff;
    }
    to {
        stroke: #dd4b39;
    }
}
@keyframes colorGoogle {
    from {
        stroke: #ffffff;
    }
    to {
        stroke: #dd4b39;
    }
}
@-webkit-keyframes colorPinterest {
    from {
        stroke: #ffffff;
    }
    to {
        stroke: #cb2027;
    }
}
@keyframes colorPinterest {
    from {
        stroke: #ffffff;
    }
    to {
        stroke: #cb2027;
    }
}
@-webkit-keyframes colorLinkedin {
    from {
        stroke: #ffffff;
    }
    to {
        stroke: #007bb6;
    }
}
@keyframes colorLinkedin {
    from {
        stroke: #ffffff;
    }
    to {
        stroke: #007bb6;
    }
}
@-webkit-keyframes outWaveIn {
    to {
        stroke-width: 10px;
        stroke-dasharray: 400;
    }
}
@keyframes outWaveIn {
    to {
        stroke-width: 10px;
        stroke-dasharray: 400;
    }
}
@-webkit-keyframes outWaveOut {
    from {
        stroke-width: 10px;
        stroke-dasharray: 400;
    }
    to {
        stroke: #ffffff;
        stroke-width: 5px;
        stroke-dasharray: 40;
    }
}
@keyframes outWaveOut {
    from {
        stroke-width: 10px;
        stroke-dasharray: 400;
    }
    to {
        stroke: #ffffff;
        stroke-width: 5px;
        stroke-dasharray: 40;
    }
}






#ex_footer .icon-container {
    position: relative;
    display: inline-block;
    width: 60px;
    height: 65px;
    line-height: 60px;
    padding-top: 0;
    text-align: center;
    margin: 0 auto;
}

#ex_footer a, #ex_footer h1, #ex_footer h2, #ex_footer h3, #ex_footer h4 {
    color: #fff!important;
}
#ex_footer .icon-container .circle {
    fill: none;
    stroke: #fff;
    stroke-width: 5px;
    stroke-dasharray: 40;
    transition: all .2s ease-in-out;
    -webkit-animation: outWaveOut 1s cubic-bezier(0.42,0,0.58,1) forwards;
    animation: outWaveOut 1s cubic-bezier(0.42,0,0.58,1) forwards;
}

svg:not(:root) {
    overflow: hidden;
}


#ex_footer .icon-container .social {
    color: #fff;
    font-size: 1.8em;
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    transition: all .5s ease-in-out;
}

.fab {
    font-family: "Font Awesome 5 Brands";
}
.fa, .fab, .far, .fas {
    -moz-osx-font-smoothing: grayscale;
    -webkit-font-smoothing: antialiased;
    display: inline-block;
    font-style: normal;
    font-variant: normal;
    text-rendering: auto;
    line-height: 1;
}

/* footer style*/










@media (max-width: 768px){
    #ex_footer .icon-container {
        width: 30px;
        line-height: 25px;
    }
    #ex_footer .icon-container .social {
        font-size: 0.8em;
    }
    #ex_footer #share span {
        width: 130px;
        font-size: 1em;
        line-height: 28px;
    }
    #ex_footer  #share {
        width: 300px;
        background: #0088cc;
    }
    #ex_footer .footer-ribbon, #ex_footer , #ex_footer #share,  #ex_footer #share span,   #ex_footer .icon-container   {
        height: 30px;
    }
    #ex_footer .footer-ribbon-right {
        height: 30px;
        font-size: 1em;
        /* width: 80%;*/
        text-align: center;
        margin: -38px 0 0 0px;
        line-height: 14px;
        margin-left: auto;
        margin-right: auto;
    }
    #ex_footer .footer-ribbon-right:before{
        border: none;
    }
}

</style>
