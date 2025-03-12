<div class="inspiration-section">
    <style>
    /* --- Hero & Layout-Anpassungen --- */
    .inspiration-section .container {
      max-width: 1000px;
      margin: 0 auto;
      padding: 10px;
      box-sizing: border-box;
    }

    .inspiration-section .hero {
      background: linear-gradient(135deg, #0f172a, #1e3a8a);
      color: white;
      border-radius: 8px;
      padding: 20px;
      text-align: center;
      margin-bottom: 20px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.2);
      width: 100%;
      box-sizing: border-box;
    }

    .inspiration-section .hero h1 {
      font-size: 2.5rem;
      font-weight: 800;
      margin-bottom: 0.5rem;
    }

    .inspiration-section .hero p {
      font-size: 1.125rem;
      font-weight: 500;
      max-width: 800px;
      margin: 0.5rem auto 0;
    }

    /* Reihen enger setzen */
    .inspiration-section .row {
      display: flex;
      gap: 15px;
      margin-bottom: 15px;
      width: 100%;
      margin-left: auto;
      margin-right: auto;
      padding-left: 0;
      padding-right: 0;
    }

    /* Bestehende Animationen */
    @keyframes fadeInUp {
      from { opacity: 0; transform: translateY(20px); }
      to { opacity: 1; transform: translateY(0); }
    }

    .inspiration-section .tile {
      opacity: 0;
      animation: fadeInUp 0.8s ease-out forwards;
    }

    .inspiration-section .row > .tile:nth-child(1) { animation-delay: 0.1s; }
    .inspiration-section .row > .tile:nth-child(2) { animation-delay: 0.3s; }
    .inspiration-section .row > .tile:nth-child(3) { animation-delay: 0.5s; }

    /* Kacheln */
    .inspiration-section .tile {
      flex: 1;
      padding: 20px;
      border-radius: 10px;
      color: white;
      text-align: center;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      transition: transform 0.3s;
      cursor: pointer;
    }
    .inspiration-section .tile:hover {
      transform: scale(1.05);
    }

    .inspiration-section .explanation {
      flex: 2;
      background: linear-gradient(135deg, #14b8a6, #dbeafe);
      color: #333;
    }

    .inspiration-section .inspiration { background-color: #FFD700; color: #333; }
    .inspiration-section .sport { background-color: #FF4500; }
    .inspiration-section .adventure { background-color: #228B22; }
    .inspiration-section .theme-park { background-color: #DA70D6; }
    .inspiration-section .subcategory-tile { background-color: #666; }
    .inspiration-section .random-tile { background-color: #4a90e2; position: relative; }

    /* Add-to-Trip Icon */
    .inspiration-section .add-icon {
      position: absolute;
      top: 5px;
      right: 5px;
      background: linear-gradient(135deg, rgba(0,0,0,0.7), rgba(0,0,0,0.4));
      border-radius: 50%;
      padding: 8px;
      cursor: pointer;
      transition: transform 0.2s, box-shadow 0.2s;
    }
    .inspiration-section .add-icon:hover {
      transform: scale(1.2);
      box-shadow: 0 2px 10px rgba(0,0,0,0.5);
    }

    @keyframes successPulse {
      0% { transform: scale(1); }
      50% { transform: scale(1.3); }
      100% { transform: scale(1); }
    }

    .inspiration-section .add-icon.success {
      background: linear-gradient(135deg, #25D366, #28a745);
      animation: successPulse 0.5s ease-in-out;
    }

    /* Icon-Größe */
    .inspiration-section .icon {
      font-size: 40px;
      margin-bottom: 10px;
    }

    /* Responsive */
    @media (max-width: 768px) {
      .inspiration-section .row {
        flex-direction: column;
        gap: 15px;
      }
      .inspiration-section .tile {
        padding: 15px;
        min-height: 100px;
      }
      .inspiration-section .explanation { flex: 1; }
      .inspiration-section .icon { font-size: 30px; }
      .inspiration-section p { font-size: 14px; }
    }

    /* Tripplaner-Zusammenfassung */
    .tripplan-summary {
      background: linear-gradient(135deg, #1e293b, #334155);
      color: white;
      padding: 20px;
      border-radius: 10px;
      margin-top: 20px;
      text-align: center;
      box-shadow: 0 2px 8px rgba(0,0,0,0.2);
      width: 100%;
      box-sizing: border-box;
    }

    .share-btn {
      background-color: #25D366;
      color: white;
      padding: 10px 20px;
      border: none;
      border-radius: 5px;
      margin-top: 10px;
      cursor: pointer;
      transition: background-color 0.3s;
    }

    .share-btn.bg-blue-500 {
      background-color: #3b82f6;
    }

    .share-btn:hover {
      background-color: #22c55e;
    }

    .share-btn.bg-blue-500:hover {
      background-color: #2563eb;
    }
    </style>

    <!-- Alpine.js & Livewire -->
    <div class="container"
         x-data="{ selectedType: @entangle('selectedType'), randomMode: @entangle('randomMode'), selected: @entangle('selected'), showTripPlan: true }">
        <!-- Neuer Hero-Bereich -->
        <div class="hero">
            <h1>Entdecke dein nächstes Abenteuer in {{ $locationTitle }}!</h1>
            <p>Tauche ein in die atemberaubenden Seiten dieser Stadt – entdecke verborgene Schätze und plane dein persönliches Abenteuer!</p>
        </div>

        <!-- Erste Zeile (Erklärung & Reset) -->
        <div class="row">
            <div class="tile explanation" wire:click="resetSelection">
                <p>Starte neu!<br>Klicke hier, um deine aktuelle Auswahl zurückzusetzen.<br>Oder wähle unten eine der drei Kacheln für dein nächstes Abenteuer.</p>
            </div>
            <button wire:click="randomInspiration" class="tile inspiration">
                <span class="icon">💡</span>
                <p>Lass dich inspirieren...</p>
            </button>
        </div>

        <!-- Zweite Zeile: Haupt-Kacheln -->
        <div class="row" x-show="!selectedType && !randomMode">
            <button wire:click="showCategories('Sport')" class="tile sport">
                <span class="icon">⚽</span>
                <p>Sport</p>
            </button>
            <button wire:click="showCategories('Erlebnis')" class="tile adventure">
                <span class="icon">⛰️</span>
                <p>Erlebnis</p>
            </button>
            <button wire:click="showCategories('Freizeitpark')" class="tile theme-park">
                <span class="icon">🎢</span>
                <p>Freizeitpark</p>
            </button>
        </div>

        <!-- Dritte Zeile: Unterkategorien -->
        <div class="row" x-show="selectedType" x-transition:enter="transition ease-out duration-500" x-transition:enter-start="opacity-0 transform -translate-y-4" x-transition:enter-end="opacity-100 transform translate-y-0" x-cloak>
            @if($selectedType && count($categories) > 0)
                @foreach($categories as $category)
                    <button wire:click="showCategorySuggestion('{{ $category }}')" class="tile subcategory-tile">
                        <span class="icon">✨</span>
                        <p>{{ $category }}</p>
                    </button>
                @endforeach
            @endif
        </div>

        <!-- Zeile: Zufällige Inspirationen -->
        <div class="row" x-show="randomMode" x-transition:enter="transition ease-out duration-500" x-transition:enter-start="opacity-0 transform translate-y-4" x-transition:enter-end="opacity-100 transform translate-y-0" x-cloak>
            @if(count($randomSuggestions) > 0)
                @foreach($randomSuggestions as $rs)
                <div class="tile random-tile">
                    <div class="add-icon" wire:click="addToTripPlan('{{ $rs['id'] }}')" title="Zum Tripplaner hinzufügen" x-data="{ added: false }" x-on:click="added = true; setTimeout(() => added = false, 1000)" x-bind:class="{ 'success': added }">
                        <i class="fa" x-bind:class="added ? 'fa-check' : 'fa-plus'" style="font-size:20px; color:white;"></i>
                    </div>
                    <h3 class="text-xl font-bold">{{ ucfirst($rs['text_type']) }} - {{ $rs['category'] }}</h3>
                    @if(!empty($rs['uschrift']))
                        <h4 class="text-lg font-semibold mt-1">{{ $rs['uschrift'] }}</h4>
                    @endif
                    <p class="mt-2">{{ $rs['text'] }}</p>
                </div>
                @endforeach
            @endif
        </div>

        <!-- Anzeige der ausgewählten Inspiration -->
        @if ($selected)
        <div class="mt-6 p-4 bg-white shadow rounded transition-opacity duration-500" x-show="selectedType && !randomMode && selected" x-transition:enter="transition ease-out duration-500" x-transition:enter-start="opacity-0 transform translate-y-4" x-transition:enter-end="opacity-100 transform translate-y-0" x-transition:leave="transition ease-in duration-500" x-transition:leave-start="opacity-100 transform translate-y-0" x-transition:leave-end="opacity-0 transform translate-y-4" x-cloak>
            <div style="position: relative;">
                <div class="add-icon" wire:click="addToTripPlan('{{ $selected['id'] }}')" title="Zum Tripplaner hinzufügen" x-data="{ added: false }" x-on:click="added = true; setTimeout(() => added = false, 1000)" x-bind:class="{ 'success': added }">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="width:20px; height:20px;" x-show="!added">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <i class="fa fa-check" style="font-size:20px; color:white;" x-show="added"></i>
                </div>
                <h3 class="text-xl font-bold text-black">{{ ucfirst($selected['text_type']) }} – {{ $selected['category'] }}</h3>
                <p class="text-black mt-2">{{ $selected['text'] }}</p>
            </div>
        </div>
        @endif

        <!-- Tripplaner -->
        <div x-data="{ showTripPlan: true }">
            <div class="text-center mb-4">
                <button @click="showTripPlan = !showTripPlan" class="share-btn">
                    <span x-text="showTripPlan ? 'Tripplan einklappen' : 'Tripplan ausklappen'"></span>
                </button>
            </div>

            <div x-show="showTripPlan" x-cloak>
                @if(count($tripPlan) > 0)
                <div class="row justify-content-center">
                    <div class="col-md-8">
                        <div class="card bg-dark text-white mb-4">
                            <div class="card-header">
                                <h3 class="card-title mb-0">Dein Tripplan</h3>
                            </div>
                            <ul class="list-group list-group-flush">
                                @foreach($tripPlan as $item)
                                <li class="list-group-item d-flex justify-content-between align-items-center bg-dark text-white">
                                    <span>
                                        <strong>{{ $item['category'] }}</strong>
                                        @if(!empty($item['uschrift']))
                                            - {{ $item['uschrift'] }}
                                        @endif
                                        : {{ \Illuminate\Support\Str::limit($item['text'], 250) }}
                                    </span>
                                    <button wire:click="removeFromTripPlan('{{ $item['id'] }}')" class="btn btn-sm" title="Entfernen">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="tripplan-summary">
                    <h3 class="text-xl font-bold">Gesamtübersicht</h3>
                    <p>In deinem Tripplan befinden sich {{ count($tripPlan) }} Aktivitäten.</p>
                    <button id="shareTripPlan" class="share-btn">Tripplan per WhatsApp teilen</button>
                    <button id="exportPdf" class="share-btn bg-blue-500">Als PDF exportieren</button>
                    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
                    <script>
                        document.getElementById('exportPdf')?.addEventListener('click', function() {
                            const { jsPDF } = window.jspdf;
                            const doc = new jsPDF();
                            let tripPlan = @json($tripPlan);
                            const currentUrl = window.location.href;

                            doc.setFont("helvetica", "bold");
                            doc.setFontSize(20);
                            doc.text("Mein Tripplan durch {{ $locationTitle }}", 105, 20, { align: "center" });

                            doc.setLineWidth(0.5);
                            doc.line(20, 25, 185, 25);

                            doc.setFont("helvetica", "normal");
                            doc.setFontSize(12);
                            let yPosition = 35;
                            const lineHeight = 7;
                            const margin = 20;
                            const maxWidth = 165;

                            tripPlan.forEach((item, index) => {
                                let text = `${index + 1}. ${item.category}`;
                                if (item.uschrift) {
                                    text += ` - ${item.uschrift}`;
                                }
                                text += `: ${item.text}`;

                                const splitText = doc.splitTextToSize(text, maxWidth);
                                if (yPosition + splitText.length * lineHeight > 280) {
                                    doc.addPage();
                                    yPosition = margin;
                                }
                                doc.text(splitText, margin, yPosition);
                                yPosition += splitText.length * lineHeight + 5;
                            });

                            doc.setFont("helvetica", "italic");
                            doc.setFontSize(10);
                            doc.text("Mehr Infos: " + currentUrl, 105, yPosition + 10, { align: "center" });

                            doc.save('tripplan.pdf');
                        });

                        document.getElementById('shareTripPlan')?.addEventListener('click', function() {
                            var tripPlan = @json($tripPlan);
                            var currentUrl = window.location.href;
                            var message = "Mein Tripplan durch {{ $locationTitle }}:\n";
                            tripPlan.forEach(function(item) {
                                var line = "- " + item.category;
                                if (item.uschrift) {
                                    line += " - " + item.uschrift;
                                }
                                line += ": " + item.text.substring(0, 250) + "...";
                                message += line + "\n";
                            });
                            message += "\nMehr Infos: " + currentUrl;
                            var encodedMessage = encodeURIComponent(message);
                            var isMobile = /Mobi|Android/i.test(navigator.userAgent);
                            var whatsappUrl = isMobile
                                ? "whatsapp://send?text=" + encodedMessage
                                : "https://web.whatsapp.com/send?text=" + encodedMessage;
                            window.open(whatsappUrl, '_blank');
                        });
                    </script>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>