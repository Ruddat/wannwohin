@extends('layouts.main')

@section('content')
<div class="container py-5">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h2 class="fw-bold text-center mb-0">Impressum</h2>
        </div>
        <div class="card-body">
            <h4 class="fw-bold">Inhaltlich Verantwortlicher gemäß §55 Abs. 2 RStV</h4>
            <p>
                Martin Effe<br>
                Carl-Zeiss-Strasse 16<br>
                31073 Grünenplan<br>
                Deutschland
            </p>

            <h4 class="fw-bold mt-4">Konzeption & Marketing</h4>
            <p>hamburg-webart, Hamburg</p>

            <h4 class="fw-bold mt-4">Kontakt</h4>
            <p>Bei Anregungen oder Kritik schreiben Sie bitte eine E-Mail an <a href="mailto:service@klimatabelle.de">service@klimatabelle.de</a>.</p>

            <h4 class="fw-bold mt-4">Haftung für Inhalte</h4>
            <p>
                Die Inhalte unserer Seiten wurden mit größter Sorgfalt erstellt. Für die Richtigkeit, Vollständigkeit und Aktualität der Inhalte können wir jedoch keine Gewähr übernehmen. Als Diensteanbieter sind wir gemäß §7 Abs.1 TMG für eigene Inhalte auf diesen Seiten nach den allgemeinen Gesetzen verantwortlich.
            </p>
            <p>
                Nach den §§8 bis 10 TMG sind wir als Diensteanbieter jedoch nicht verpflichtet, übermittelte oder gespeicherte fremde Informationen zu überwachen oder nach Umständen zu forschen, die auf eine rechtswidrige Tätigkeit hinweisen. Verpflichtungen zur Entfernung oder Sperrung der Nutzung von Informationen nach den allgemeinen Gesetzen bleiben hiervon unberührt.
            </p>

            <h4 class="fw-bold mt-4">Haftung für Links</h4>
            <p>
                Unser Angebot enthält Links zu externen Webseiten Dritter, auf deren Inhalte wir keinen Einfluss haben. Deshalb können wir für diese fremden Inhalte auch keine Gewähr übernehmen.
            </p>

            <h4 class="fw-bold mt-4">Urheberrecht</h4>
            <p>
                Die durch die Seitenbetreiber erstellten Inhalte und Werke auf diesen Seiten unterliegen dem deutschen Urheberrecht. Die Vervielfältigung, Bearbeitung, Verbreitung und jede Art der Verwertung außerhalb der Grenzen des Urheberrechtes bedürfen der schriftlichen Zustimmung des jeweiligen Autors bzw. Erstellers.
            </p>

            <h4 class="fw-bold mt-4">Datenschutzerklärung</h4>
            <p>
                Nutzer erhalten mit dieser Datenschutzerklärung Information über die Art, den Umfang und Zweck der Erhebung und Verwendung ihrer Daten durch den verantwortlichen Anbieter.
            </p>
        </div>
        <div class="card-footer text-center">
            <p class="text-muted mb-0">
                <small>Dieses Impressum wurde zuletzt aktualisiert am {{ now()->format('d.m.Y') }}</small>
            </p>
        </div>
    </div>
</div>
@endsection
