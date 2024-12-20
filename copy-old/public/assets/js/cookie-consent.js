(function () {
    "use strict";

    var cookieAlert = document.querySelector(".cookiealert");
    var acceptCookies = document.querySelector(".acceptcookies");

    if (!cookieAlert) {
        return;
    }

    cookieAlert.offsetHeight; // Force browser to trigger reflow (https://stackoverflow.com/a/39451131)

    // Show the alert if we cant find the "acceptCookies" cookie
    if (!getCookie("acceptCookies")) {
        cookieAlert.classList.add("show");
    }

    // When clicking on the agree button, create a 1 year
    // cookie to remember user's choice and close the banner
    acceptCookies.addEventListener("click", function () {
        setCookie("acceptCookies", true, 365);
        cookieAlert.classList.remove("show");

        // dispatch the accept event
        window.dispatchEvent(new Event("cookieAlertAccept"))
    });

    // Cookie functions from w3schools
    function setCookie(cname, cvalue, exdays) {
        var d = new Date();
        d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
        var expires = "expires=" + d.toUTCString();
        document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
    }

    function getCookie(cname) {
        var name = cname + "=";
        var decodedCookie = decodeURIComponent(document.cookie);
        var ca = decodedCookie.split(';');
        for (var i = 0; i < ca.length; i++) {
            var c = ca[i];
            while (c.charAt(0) === ' ') {
                c = c.substring(1);
            }
            if (c.indexOf(name) === 0) {
                return c.substring(name.length, c.length);
            }
        }
        return "";
    }
})();

/*
// Event-Listener für die Buttons
document.getElementById("accept-all").addEventListener("click", function() {
    setCookie("cookieConsent", "all", 365);
    document.getElementById("cookie-banner").style.display = "none";
});
document.getElementById("accept-essential").addEventListener("click", function() {
    setCookie("cookieConsent", "essential", 365);
    document.getElementById("cookie-banner").style.display = "none";
});
document.getElementById("cookie-settings").addEventListener("click", function() {
    // Hier können die Cookie-Einstellungen aufgerufen werden.
    alert("Cookie-Einstellungen öffnen.");
});
// Funktion zum Setzen eines Cookies
function setCookie(name, value, days) {
    var expires = "";
    if (days) {
        var date = new Date();
        date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
        expires = "; expires=" + date.toUTCString();
    }
    document.cookie = name + "=" + (value || "")  + expires + "; path=/";
}
// Überprüfen, ob der Benutzer bereits eine Entscheidung getroffen hat
function checkCookieConsent() {
    if (document.cookie.split(';').some((item) => item.trim().startsWith('cookieConsent='))) {
        document.getElementById("cookie-banner").style.display = "none";
    }
}
checkCookieConsent(); // Überprüfen, ob bereits eine Zustimmung erteilt wurde
*/
