// Cookie-Helferfunktionen
function setCookie(name, value, days) {
    const date = new Date();
    date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
    const expires = "expires=" + date.toUTCString();
    document.cookie = name + "=" + value + ";" + expires + ";path=/";
}

function getCookie(name) {
    const value = "; " + document.cookie;
    const parts = value.split("; " + name + "=");
    if (parts.length === 2) return parts.pop().split(";").shift();
}

// Skripte laden basierend auf Zustimmung
function loadExternalScripts(consent) {
    if (consent.analytics) {
        const script = document.createElement("script");
        script.async = true;
        script.src = "https://www.googletagmanager.com/gtag/js?id=UA-XXXXX-Y"; // Deine Analytics-ID
        document.head.appendChild(script);

        window.dataLayer = window.dataLayer || [];
        function gtag() { dataLayer.push(arguments); }
        gtag("js", new Date());
        gtag("config", "UA-XXXXX-Y");
    }
    if (consent.maps) {
        // Beispiel: Google Maps laden (füge hier deinen Maps-Code ein)
        console.log("Google Maps würde hier geladen werden.");
    }
}

// Modal-Logik
document.addEventListener("DOMContentLoaded", function () {
    console.log("DOM geladen, prüfe Cookie-Consent...");
    const modalElement = document.getElementById("cookie-modal");
    if (!modalElement) {
        console.error("Modal nicht gefunden!");
        return;
    }

    const modal = new bootstrap.Modal(modalElement, {
        backdrop: "static",
        keyboard: false
    });

    const acceptBtn = document.getElementById("accept-cookies");
    const declineBtn = document.getElementById("decline-cookies");
    const saveBtn = document.getElementById("save-cookies");
    const analyticsCheckbox = document.getElementById("analytics-cookies");
    const mapsCheckbox = document.getElementById("maps-cookies");
    const consent = getCookie("cookie_consent");

    if (!acceptBtn || !declineBtn || !saveBtn) {
        console.error("Ein oder mehrere Buttons nicht gefunden!");
        return;
    }

    console.log("Aktueller Consent-Wert:", consent);

    // Vorhandene Zustimmung parsen
    let consentObj = consent ? JSON.parse(consent) : null;

    // Modal anzeigen, wenn keine Zustimmung
    if (!consent) {
        console.log("Kein Consent, zeige Modal...");
        modal.show();
    }

    // Akzeptieren (alles)
    acceptBtn.addEventListener("click", function () {
        console.log("Alle Cookies akzeptiert");
        const fullConsent = { essential: true, analytics: true, maps: true };
        setCookie("cookie_consent", JSON.stringify(fullConsent), 365);
        modal.hide();
        loadExternalScripts(fullConsent);
    });

    // Ablehnen
    declineBtn.addEventListener("click", function () {
        console.log("Cookies abgelehnt");
        const minimalConsent = { essential: true, analytics: false, maps: false };
        setCookie("cookie_consent", JSON.stringify(minimalConsent), 365);
        modal.hide();
    });

    // Speichern (benutzerdefinierte Einstellungen)
    saveBtn.addEventListener("click", function () {
        console.log("Einstellungen gespeichert");
        const customConsent = {
            essential: true,
            analytics: analyticsCheckbox.checked,
            maps: mapsCheckbox.checked
        };
        setCookie("cookie_consent", JSON.stringify(customConsent), 365);
        modal.hide();
        loadExternalScripts(customConsent);
    });

    // Skripte laden, wenn bereits akzeptiert
    if (consentObj) {
        console.log("Consent vorhanden, lade Skripte...");
        loadExternalScripts(consentObj);
    }
});
