@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">Városok szűrése ABC szerint</h2>

    <div class="card mb-4">
        <div class="card-body">
            <label class="form-label fw-bold">1. Válassz Megyét:</label>
            <select id="county-select" class="form-select">
                <option value="">-- Válassz egy megyét --</option>
                @foreach($counties as $county)
                    <option value="{{ $county['id'] }}">{{ $county['name'] }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div id="initials-wrapper" class="card mb-4 d-none">
        <div class="card-body">
            <label class="form-label fw-bold">2. Válassz kezdőbetűt:</label>
            <div id="initials-container" class="d-flex flex-wrap gap-2">
                </div>
        </div>
    </div>

    <div id="cities-wrapper" class="card d-none">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <span>Találatok</span>
            
            <div id="export-buttons" class="d-none">
                <a id="btn-export-pdf" href="#" class="btn btn-sm btn-light text-danger fw-bold">PDF</a>
                <a id="btn-export-csv" href="#" class="btn btn-sm btn-light text-success fw-bold">CSV</a>
            </div>
        </div>
        <div class="card-body">
            <ul id="cities-list" class="list-group list-group-flush">
                </ul>
        </div>
    </div>
</div>

<script>
    // Ha localhost-on vagy és 127.0.0.1-et használsz, itt is azt kell
    // De a biztonság kedvéért dinamikusan kérjük le az env-ből, vagy relatív útvonalat használunk
    const apiBaseUrl = "{{ env('API_URL') }}"; 
    
    const countySelect = document.getElementById('county-select');
    const initialsWrapper = document.getElementById('initials-wrapper');
    const initialsContainer = document.getElementById('initials-container');
    const citiesWrapper = document.getElementById('cities-wrapper');
    const citiesList = document.getElementById('cities-list');
    const exportButtons = document.getElementById('export-buttons');
    const btnPdf = document.getElementById('btn-export-pdf');
    const btnCsv = document.getElementById('btn-export-csv');

    let currentCities = []; 

    // 1. Amikor megyét választ a felhasználó
    countySelect.addEventListener('change', function() {
        const countyId = this.value;
        
        // Resetelünk mindent
        initialsWrapper.classList.add('d-none');
        citiesWrapper.classList.add('d-none');
        initialsContainer.innerHTML = '';
        citiesList.innerHTML = '';

        if (!countyId) return;

        // Betöltés jelzése
        initialsContainer.innerHTML = '<span class="text-muted">Betöltés...</span>';
        initialsWrapper.classList.remove('d-none');

        // Lekérjük az összes települést, és JS-ben szűrünk (mivel nincs speciális API végpont)
        fetch(`${apiBaseUrl}/settlements`)
            .then(response => response.json())
            .then(data => {
                // Szűrés a kiválasztott megyére
                // Figyelem: az API válasza lehet, hogy sima tömb, vagy {data: [...]} szerkezetű
                const settlements = data.data ? data.data : data;

                currentCities = settlements.filter(c => {
                    // Ellenőrizzük, hogy county_id mező van-e, vagy beágyazott county objektum
                    if (c.county_id == countyId) return true;
                    if (c.county && c.county.id == countyId) return true;
                    return false;
                });
                
                if (currentCities.length > 0) {
                    renderInitials(currentCities);
                } else {
                    initialsContainer.innerHTML = '<span class="text-danger">Ebben a megyében nincsenek városok.</span>';
                }
            })
            .catch(error => {
                console.error('Hiba:', error);
                initialsContainer.innerHTML = '<span class="text-danger">Hiba történt az adatok betöltésekor.</span>';
            });
    });

    // 2. Betűk kirajzolása
    function renderInitials(cities) {
        // Kigyűjtjük az egyedi kezdőbetűket
        const initials = [...new Set(cities.map(c => c.name.charAt(0).toUpperCase()))].sort();

        initialsContainer.innerHTML = '';
        initials.forEach(letter => {
            const btn = document.createElement('button');
            btn.className = 'btn btn-outline-dark px-3';
            btn.textContent = letter;
            btn.onclick = () => renderCities(letter);
            initialsContainer.appendChild(btn);
        });
    }

    // 3. Városok listázása betű alapján
    function renderCities(letter) {
        const countyId = countySelect.value; // Aktuális megye ID
        
        // 1. Szűrés a böngészőben (hogy gyorsan lássa a listát)
        const filteredCities = currentCities.filter(c => c.name.charAt(0).toUpperCase() === letter);
        
        citiesList.innerHTML = '';
        filteredCities.forEach(city => {
            const li = document.createElement('li');
            li.className = 'list-group-item d-flex justify-content-between align-items-center';
            const zip = city.postal_code || city.zip_code || '';
            li.innerHTML = `
                <span>${city.name}</span>
                <span class="badge bg-secondary rounded-pill">${zip}</span>
            `;
            citiesList.appendChild(li);
        });

        citiesWrapper.classList.remove('d-none');

        // --- ÚJ RÉSZ: Export gombok beállítása ---
        
        // Alap útvonalak (Laravel route helperrel)
        const pdfBaseUrl = "{{ route('settlements.export.pdf') }}";
        const csvBaseUrl = "{{ route('settlements.export.csv') }}";

        // Paraméterek összeállítása
        const params = `?county_id=${countyId}&letter=${letter}`;

        // Linkek frissítése
        btnPdf.href = pdfBaseUrl + params;
        btnCsv.href = csvBaseUrl + params;

        // Gombok megjelenítése
        exportButtons.classList.remove('d-none');
    }
</script>
@endsection