# Przegląd kodu – Wordpress CTA Button (sticky-phone-button)

Data: 2025-02-22

---

## 1. Wprowadzone poprawki

### 1.1 Błąd ładowania skryptu w panelu (PHP)
- **Problem:** `sticky_phone_button_admin_scripts()` sprawdzało `$screen->id === 'toplevel_page_sticky-phone-button'`. Strona jest dodawana przez `add_options_page()` (podmenu **Ustawienia**), więc poprawny screen ID to `settings_page_sticky-phone-button`. Skrypt admin-script.js nie ładował się na stronie ustawień.
- **Poprawka:** Zmiana warunku na `settings_page_sticky-phone-button`.

### 1.2 Próg przewijania – NaN (script.js)
- **Problem:** Gdy `sticky_phone_button_scroll_trigger_value` nie istnieje w ustawieniach, `parseInt(undefined, 10)` daje `NaN`; `!value` jest wtedy true, więc uznawano „brak progu”. Lepiej jawnie sprawdzać `NaN`.
- **Poprawka:** Warunek zmieniony na `isNaN(value) || value <= 0`.

### 1.3 Bezpieczeństwo – link typu URL (script.js)
- **Problem:** Dla `link_type === 'url'` do `href` trafiała wartość z ustawień. Teoretycznie możliwa była wartość np. `javascript:...` lub `data:...` (np. po ręcznej edycji w bazie).
- **Poprawka:** W `buildHref()` dla typu `url` odrzucane są protokoły `javascript:` i `data:`, dozwolone tylko `http(s):` lub względny URL z `//`; w razie niedozwolonej wartości ustawiane jest `#`.

---

## 2. Zalecenia i uwagi (bez zmian w kodzie)

### 2.1 PHP
- **Cache wersji:** W `wp_register_script` i `wp_register_style` używane jest `time()` jako wersja – przy każdym requestcie nowa wersja, brak cache przeglądarki. Na produkcji lepiej użyć np. `STICKY_PHONE_BUTTON_VERSION` (z głównego pliku wtyczki) lub hash pliku.
- **REST API:** Endpoint `sticky-phone-button/v1/settings` zwraca pełne ustawienia bez autoryzacji (`permission_callback => '__return_true'`). To celowe (front potrzebuje danych), ale upewnij się, że w ustawieniach nie przekazujesz wrażliwych danych.
- **Spójność tłumaczeń:** Część opisów w adminie jest po angielsku (np. „Only on phones”), część po polsku w `esc_html__()`. W razie planowanej lokalizacji warto scentralizować stringi i domenę.

### 2.2 JavaScript
- **Nieużywana zmienna:** `dayNameMapping` w script.js jest zdefiniowane, ale nieużywane (używane są `dayIndexToName` i `dayIndexToNamePL`). Można je usunąć w ramach porządkowania.
- **Stałe magiczne:** Wartości 100 ms (debounce scroll), 300 ms (opacity), 3000 ms (pulse) są na stałe w kodzie. Rozważ stałe na górze pliku (np. `SCROLL_DEBOUNCE_MS`) dla czytelności i ewentualnej konfiguracji.
- **applyProfile a innerHTML:** Tekst CTA jest ustawiany przez `ctaEl.innerHTML = ctaText`. Treść pochodzi z serwera (sanitowana przez `wp_kses`). Dla pełnej ostrożności przy treściach z zewnątrz można rozważyć ustawianie tylko dozwolonych tagów lub textContent + proste formatowanie, ale przy obecnym modelu (tylko admin, wp_kses) ryzyko jest niskie.

### 2.3 Ogólne
- **Testy:** Brak zautomatyzowanych testów (PHP/JS). W razie rozbudowy wtyczki warto dodać np. proste testy dla `sanitize_settings`, `getVisibleProfile` i `buildHref`.
- **Kompatybilność wsteczna:** Zachowana – brak kluczy `alt_*` w bazie = profil alt wyłączony, bez zmiany zachowania głównego przycisku.

---

## 3. Podsumowanie

- Naprawiono **ładowanie admin-script.js** (poprawny screen ID).
- Uodporniono **checkScrollTrigger** na brak/nieprawidłową wartość progu (NaN).
- Ograniczono **ryzyko XSS** w linku typu URL w JS (odrzucenie `javascript:` / `data:`).
- Reszta kodu jest spójna z założeniami wtyczki; powyższe zalecenia można wdrożyć stopniowo (cache wersji, stałe, usunięcie martwego kodu, testy).
