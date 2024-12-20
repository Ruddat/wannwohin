    <div id="share"><span>Mit Freunden teilen:</span>
        <a href="https://twitter.com/share?{{ Request::url()  }}" target="_blank" class="icon-container twitter">
            <svg  preserveAspectRatio="xMinYMin meet" viewBox="0 0 200 200" class="circle">
                <circle cx="100" cy="90" r="70"/>
            </svg>
            <div class="social">
                <i class="fab fa-twitter"></i>
            </div>
        </a>
        <a href="http://www.facebook.com/sharer.php?u={{ Request::url()  }}" target="_blank" class="icon-container facebook">
            <svg  preserveAspectRatio="xMinYMin meet" viewBox="0 0 200 200" class="circle">
                <circle cx="100" cy="90" r="70"/>
            </svg>
            <div class="social">
                <i class="fab fa-facebook-f"></i>
            </div>
        </a>
        <a href="http://www.linkedin.com/shareArticle?url={{ Request::url()  }}" target="_blank" class="icon-container linkedin">
            <svg  preserveAspectRatio="xMinYMin meet" viewBox="0 0 200 200" class="circle">
                <circle cx="100" cy="90" r="70"/>
            </svg>
            <div class="social">
                <i class="fab fa-linkedin-in"></i>
            </div>
        </a>
        <a href="https://api.whatsapp.com/send?text={{ Request::url()  }}" target="_blank" class="icon-container google">
            <svg  preserveAspectRatio="xMinYMin meet" viewBox="0 0 200 200" class="circle">
                <circle cx="100" cy="90" r="70"/>
            </svg>
            <div class="social">
                <i class="fab fa-whatsapp"></i>
            </div>
        </a>
        <a href="mailto:?subject=Klimatabelle @if(isset($continent) and $continent->title) $continent->title @elseif(isset($country) and $country->title) $country->title @else Klima Daten - Fakten und Charts @endif : Interessante Webseite gefunden.&body=Ich habe gerade diese Webseite beim Surfen gefunden, die Dir bestimmt auch gefällt: {{ Request::url()  }}  %0D%0A PS: Für {{ Request::url()  }}" class="icon-container pinterest">
            <svg  preserveAspectRatio="xMinYMin meet" viewBox="0 0 200 200" class="circle">
                <circle cx="100" cy="90" r="70"/>
            </svg>
            <div class="social">
                <i class="far fa-envelope"></i>
            </div>
        </a>

    </div>
