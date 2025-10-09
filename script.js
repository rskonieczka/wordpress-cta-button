document.addEventListener('DOMContentLoaded', function () {
    // Sprawdzamy, czy mamy dostęp do zmiennej stickyPhoneButtonData
    if (typeof stickyPhoneButtonData === 'undefined') {
        console.error('Error: Missing stickyPhoneButtonData variable. Script will not run.');
        return;
    }
    
    // Sprawdź czy włączone jest debugowanie
    const enableDebug = stickyPhoneButtonData && stickyPhoneButtonData.enableDebug || false;
    
    // Funkcja do wypisywania logów, wykorzystuje flagę enableDebug
    function log() {
        if (enableDebug && console && console.log) {
            console.log.apply(console, arguments);
        }
    }
    
    // Funkcja do wypisywania błędów - zawsze działa, niezależnie od flagi debug
    function error() {
        if (console && console.error) {
            console.error.apply(console, arguments);
        }
    }
    
    // Funkcja do wypisywania ostrzeżeń - tylko gdy debugowanie włączone
    function warn() {
        if (enableDebug && console && console.warn) {
            console.warn.apply(console, arguments);
        }
    }
    
    // Flaga wskazująca czy strona została w pełni załadowana
    let pageFullyLoaded = false;
    
    // Mapowanie angielskich nazw dni na polskie (i odwrotnie)
    const dayNameMapping = {
        'Monday': 'Monday',
        'Tuesday': 'Tuesday',
        'Wednesday': 'Wednesday',
        'Thursday': 'Thursday',
        'Friday': 'Friday',
        'Saturday': 'Saturday',
        'Sunday': 'Sunday',
        // Reverse mapping 
        'Poniedziałek': 'Monday',
        'Wtorek': 'Tuesday',
        'Środa': 'Wednesday',
        'Czwartek': 'Thursday',
        'Piątek': 'Friday',
        'Sobota': 'Saturday',
        'Niedziela': 'Sunday'
    };
    
    // Mapowanie dni tygodnia (indeks -> nazwa)
    const dayIndexToName = {
        0: 'Sunday',
        1: 'Monday',
        2: 'Tuesday',
        3: 'Wednesday',
        4: 'Thursday',
        5: 'Friday',
        6: 'Saturday'
    };
    
    // Mapowanie dni tygodnia na język polski (indeks -> nazwa)
    const dayIndexToNamePL = {
        0: 'Sunday',
        1: 'Monday',
        2: 'Tuesday',
        3: 'Wednesday',
        4: 'Thursday',
        5: 'Friday',
        6: 'Saturday'
    };
    
    // Funkcja do sprawdzania czy przycisk powinien być widoczny
    function checkButtonVisibility(settings) {
        log('Sprawdzanie widoczności przycisku:', settings);
        
        // Sprawdzenie dla trybu testowego - wymuś widoczność
        if (window.location.href.includes('alwaysShowCTA=1') || window.location.href.includes('forceCTA=1')) {
            warn('TRYB WYMUSZONY: Pokazuję przycisk CTA');
            return true;
        }
        
        // UWAGA: Zmiana zachowania - domyślnie pokazuj przycisk, nawet jeśli brak ustawień
        if (!settings || typeof settings !== 'object') {
            warn('Missing or invalid CTA button settings - showing by default');
            return true;
        }

        // Dane ustawień
        log('Dane ustawień:', {
            hasSettings: !!settings,
            isObject: typeof settings === 'object',
            hasDisplayDays: settings && typeof settings.display_days !== 'undefined',
            displayDays: settings.display_days
        });

        // UWAGA: Zmiana zachowania - jeśli nie ma ustawień dni, zawsze pokazuj przycisk
        if (!settings.display_days || typeof settings.display_days !== 'object') {
            warn('Brak ustawień dni wyświetlania dla przycisku CTA - pokazuję domyślnie');
            return true;
        }

        // Sprawdź filtrowanie URL-i
        const currentUrl = window.location.href;
        const urlInclude = settings.sticky_phone_button_url_include || '';
        const urlExclude = settings.sticky_phone_button_url_exclude || '';
        
        log('Sprawdzanie filtrowania URL-i:', {
            currentUrl: currentUrl,
            urlInclude: urlInclude,
            urlExclude: urlExclude
        });
        
        // Sprawdź reguły wykluczania (blacklist) - mają priorytet
        if (urlExclude.trim() !== '') {
            const excludeRules = urlExclude.split('\n').map(rule => rule.trim()).filter(rule => rule !== '');
            for (const rule of excludeRules) {
                if (currentUrl.includes(rule)) {
                    // ZMIANA: W trybie debug, ignoruj reguły wykluczania
                    if (window.location.href.includes('debug=1') || 
                        window.location.href.includes('forceCTA=1') || 
                        window.location.href.includes('show=1')) {
                        warn(`Debug: URL zawiera wykluczony fragment "${rule}", ale wymuszam wyświetlenie`);
                        break; // Przerwij sprawdzanie wykluczeń, ale kontynuuj inne sprawdzenia
                    } else {
                        log(`URL zawiera wykluczony fragment "${rule}" - ukrywam przycisk`);
                        return false;
                    }
                }
            }
        }
        
        // Sprawdź reguły włączania (whitelist)
        if (urlInclude.trim() !== '') {
            const includeRules = urlInclude.split('\n').map(rule => rule.trim()).filter(rule => rule !== '');
            let urlMatches = false;
            
            for (const rule of includeRules) {
                if (currentUrl.includes(rule)) {
                    log(`URL zawiera wymagany fragment "${rule}" - kontynuuję sprawdzanie`);
                    urlMatches = true;
                    break;
                }
            }
            
            if (!urlMatches) {
                // ZMIANA: W trybie debug, ignoruj reguły włączania
                if (window.location.href.includes('debug=1') || 
                    window.location.href.includes('forceCTA=1') || 
                    window.location.href.includes('show=1')) {
                    warn('Debug: URL nie zawiera wymaganych fragmentów, ale wymuszam wyświetlenie');
                } else {
                    log('URL nie zawiera żadnego z wymaganych fragmentów - ukrywam przycisk');
                    return false;
                }
            }
        }

        // Sprawdź ustawienia urządzenia
        const isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
        const isDesktop = !isMobile;
        
        // Obsługa różnych formatów pola display_device
        const displayDevice = settings.sticky_phone_button_display_device || settings.display_device;
        
        log('Sprawdzanie typu urządzenia:', {
            deviceSettings: displayDevice,
            isMobile: isMobile,
            isDesktop: isDesktop,
            userAgent: navigator.userAgent
        });
        
        // ZMIANA: Tryb debug zawsze pokazuje przycisk niezależnie od typu urządzenia
        if (window.location.href.includes('debug=1') || 
            window.location.href.includes('forceCTA=1') || 
            window.location.href.includes('show=1') ||
            window.location.href.includes('alwaysShowCTA=1') ||
            window.location.href.includes('forceInit=1') ||
            (settings && settings.force_display)) {
            warn('Debug: Ignoruję ustawienia typu urządzenia i pokazuję przycisk');
            // Kontynuuj sprawdzanie innych warunków
        }
        // Pokazuj na telefonach jeśli jesteśmy na urządzeniu mobilnym
        else if ((displayDevice === 'phones' || displayDevice === 'mobile')) {
            if (isMobile) {
                log('Urządzenie mobilne i ustawienie dla telefonów - pokazuję przycisk');
                // Kontynuuj sprawdzanie innych warunków
            } else {
                warn('Ustawienie tylko dla telefonów, ale wykryto urządzenie stacjonarne - ukrywam przycisk');
                return false;
            }
        } 
        // Pokazuj na desktopach jeśli jesteśmy na urządzeniu stacjonarnym
        else if ((displayDevice === 'desktops' || displayDevice === 'desktop')) {
            if (isDesktop) {
                log('Urządzenie stacjonarne i ustawienie dla desktopów - pokazuję przycisk');
                // Kontynuuj sprawdzanie innych warunków
            } else {
                warn('Ustawienie tylko dla desktopów, ale wykryto urządzenie mobilne - ukrywam przycisk');
                return false;
            }
        }
        // Pokazuj na wszystkich urządzeniach
        else if (displayDevice === 'all' || displayDevice === 'both' || !displayDevice) {
            log('Przycisk widoczny na wszystkich urządzeniach');
            // Kontynuuj sprawdzanie innych warunków
        } 
        // Nieznany typ urządzenia - awaryjnie pokazuj przycisk
        else {
            warn(`Nieznany typ urządzenia w ustawieniach: ${displayDevice} - awaryjnie pokazuję przycisk`);
            // Kontynuuj sprawdzanie innych warunków
        }

        // Uzyskaj aktualny dzień tygodnia i czas
        const today = new Date();
        const currentDayIndex = today.getDay(); // 0-Niedziela, 1-Poniedziałek, itd.
        
        // Pobierz nazwy dni z indeksu (EN i PL)
        const currentDayEn = dayIndexToName[currentDayIndex];
        const currentDayPl = dayIndexToNamePL[currentDayIndex];
        
        log('Obecny dzień i czas:', currentDayEn, currentDayPl, today.toLocaleTimeString());
        
        // Dane dotyczące bieżącego dnia
        log('Dane dotyczące bieżącego dnia:', { 
            en: currentDayEn, 
            pl: currentDayPl,
            dayIndex: currentDayIndex,
            fullDate: today.toLocaleString()
        });
        
        // Sprawdź czy mamy dane dla bieżącego dnia (najpierw sprawdź po angielsku, potem po polsku)
        let daySettings = settings.display_days[currentDayEn];
        
        // Sprawdź ustawienia w języku polskim, jeśli ustawienia w języku angielskim nie są dostępne
        if (!daySettings) {
            log(`Brak ustawień dla dnia w języku angielskim (${currentDayEn}), sprawdzam w języku polskim (${currentDayPl})`);
            daySettings = settings.display_days[currentDayPl];
        }
        
        // Awaryjne rozwiązanie: Jeśli nie ma ustawień dla żadnego dnia, zawsze wyświetlaj przycisk
        if (!daySettings) {
            warn(`Brak ustawień dla bieżącego dnia (${currentDayEn}/${currentDayPl}) - AWARYJNIE POKAZUJĘ PRZYCISK`);
            return true;
        }
        
        // ZMIANA: Jeśli brak enabled lub ustawienie jest false, ale w trybie debug, pokazuj przycisk
        if (!daySettings.enabled) {
            if (window.location.href.includes('debug=1')) {
                warn(`Debug: Przycisk CTA nie jest włączony dla dzisiejszego dnia (${currentDayEn}), ale wymuszam wyświetlenie`);
                return true;
            }
            
            warn(`Przycisk CTA nie jest włączony dla dzisiejszego dnia (${currentDayEn})`);
            return false;
        }

        // Sprawdź godziny wyświetlania przycisku
        if (daySettings.time_slots && daySettings.time_slots.length > 0) {
            // Aktualna godzina
            const currentTime = today.getHours() * 60 + today.getMinutes();  // Czas w minutach od początku dnia
            
            log('Sprawdzanie czasu:', {
                currentTime: currentTime,
                hours: today.getHours(),
                minutes: today.getMinutes(),
                timeSlots: daySettings.time_slots
            });

            // Sprawdź czy aktualna godzina mieści się w którymś z przedziałów czasowych
            let isTimeInSlot = false;
            
            for (const slot of daySettings.time_slots) {
                // Pobierz czasy rozpoczęcia i zakończenia
                const startTime = parseTimeString(slot.start_time);
                const endTime = parseTimeString(slot.end_time);
                
                // Dla celów debugowania
                log('Przedział czasowy:', {
                    startTime: slot.start_time,
                    endTime: slot.end_time,
                    startTimeMinutes: startTime,
                    endTimeMinutes: endTime,
                    currentTimeMinutes: currentTime
                });
                
                // Sprawdź czy aktualna godzina mieści się w tym przedziale
                if (currentTime >= startTime && currentTime <= endTime) {
                    log(`Bieżący czas (${currentTime}) mieści się w przedziale czasowym (${startTime}-${endTime}) - pokazuję przycisk`);
                    isTimeInSlot = true;
                    break;
                }
            }
            
            // Jeśli nie znaleźliśmy odpowiedniego przedziału czasowego
            if (!isTimeInSlot) {
                // ZMIANA: W trybie debug, pokazuj przycisk mimo niedopasowania czasu
                if (window.location.href.includes('debug=1')) {
                    warn('Debug: Bieżący czas nie mieści się w żadnym przedziale, ale wymuszam wyświetlenie przycisku');
                    return true;
                }
                
                log('Bieżący czas nie mieści się w żadnym przedziale - ukrywam przycisk');
                return false;
            }
        } else {
            log('Brak przedziałów czasowych dla dzisiejszego dnia - pokazuję przycisk przez cały dzień');
        }
        
        // Przycisk powinien być widoczny, jeśli przeszliśmy wszystkie sprawdzenia
        log('Wszystkie warunki spełnione - pokazuję przycisk CTA');
        return true;
    }

    // Funkcja pomocnicza do parsowania czasu w formacie "HH:MM" do minut
    function parseTimeString(timeString) {
        if (!timeString || typeof timeString !== 'string') {
            warn('Nieprawidłowy format czasu:', timeString);
            return 0;
        }
        
        // Obsługa różnych formatów czasu (HH:MM, H:MM, etc.)
        const parts = timeString.trim().split(':');
        
        if (parts.length !== 2) {
            warn('Nieprawidłowy format czasu (brak dwukropka):', timeString);
            return 0;
        }
        
        const hours = parseInt(parts[0], 10);
        const minutes = parseInt(parts[1], 10);
        
        if (isNaN(hours) || isNaN(minutes)) {
            warn('Nieprawidłowy format czasu (nieprawidłowe liczby):', timeString, hours, minutes);
            return 0;
        }
        
        // Konwersja do minut od początku dnia
        return hours * 60 + minutes;
    }

    // Funkcja do znajdowania przycisku na stronie
    function findPhoneButton(settings) {
        if (!settings) {
            log('Nie można znaleźć przycisku CTA: brak ustawień');
            return document.querySelector('.sticky-phone-button');
        }

        // Pobierz niestandardowe ID, jeśli istnieje
        const customId = settings.sticky_phone_button_custom_id || '';
        
        // Najpierw próbuj znaleźć przycisk za pomocą niestandardowego ID
        if (customId) {
            const buttonWithCustomId = document.getElementById(customId);
            if (buttonWithCustomId) {
                log(`Znaleziono przycisk CTA z niestandardowym ID: ${customId}`);
                return buttonWithCustomId;
            } else {
                error(`Nie znaleziono przycisku CTA z niestandardowym ID: ${customId}`);
            }
        }
        
        // Jeśli nie znaleziono przycisku z niestandardowym ID lub nie podano ID, szukaj domyślnego ID
        const defaultButton = document.getElementById('sticky-phone-button');
        if (defaultButton) {
            log('Znaleziono przycisk CTA z domyślnym ID');
            return defaultButton;
        }
        
        // Szukaj przycisku po atrybucie data-default-id
        const buttonsByData = document.querySelector('[data-default-id="sticky-phone-button"]');
        if (buttonsByData) {
            log('Znaleziono przycisk po atrybucie data-default-id');
            return buttonsByData;
        }
        
        log('Nie znaleziono przycisku CTA na stronie');
        return null;
    }

    // Funkcja do znajdowania kontenera CTA
    function findCTAContainer() {
        const container = document.querySelector('.sticky-cta-container');
        
        if (container) {
            log('Znaleziono kontener CTA');
        } else {
            error('Nie znaleziono kontenera CTA na stronie - DOM może nie być w pełni załadowany');
            
            // Spróbujmy wyświetlić wszystkie elementy z klasą, która zawiera "container"
            const allContainers = document.querySelectorAll('[class*="container"]');
            if (allContainers.length > 0) {
                log('Znaleziono inne kontenery na stronie:', allContainers.length);
            } else {
                log('Nie znaleziono żadnych kontenerów na stronie');
            }
        }
        
        return container;
    }

    // Funkcja do aktualizacji klas i stylów przycisku
    function updateButtonClasses(button, settings) {
        if (!button || !settings) return;
        
        // Pobierz niestandardową klasę z ustawień
        const customClass = settings.sticky_phone_button_custom_class || '';
        
        // Pobierz aktualne klasy przycisku i podziel je na tablicę
        const currentClasses = button.className.split(' ');
        
        // Filtruję klasy, zostawiając tylko te które nie są niestandardowe (zaczynając od trzech klasy domyślnych)
        const baseClasses = currentClasses.filter(cls => 
            cls === 'sticky-phone-button' || 
            cls.startsWith('sticky-') || 
            cls === settings.sticky_phone_button_display_device || 
            cls === settings.display_device
        );
        
        // Dodaj nowe niestandardowe klasy
        const customClasses = customClass.split(' ').filter(cls => cls.trim() !== '');
        
        // Połącz wszystkie klasy
        const allClasses = [...baseClasses, ...customClasses];
        
        // Ustaw nowe klasy przycisku
        button.className = allClasses.join(' ');
        
        // Aktualizuj kolor tła
        const backgroundColor = settings.sticky_phone_button_background_color || '#000000';
        button.style.backgroundColor = backgroundColor;
        
        // Aktualizuj ID jeśli potrzeba
        const customId = settings.sticky_phone_button_custom_id;
        if (customId && customId.trim() !== '' && button.id !== customId) {
            log('Aktualizuję ID przycisku z', button.id, 'na', customId);
            button.id = customId;
        }
        
        log('Zaktualizowano klasy przycisku:', button.className);
    }

    // Funkcja do pokazywania lub ukrywania przycisku
    function showOrHideButton(isVisible) {
        // Znajdź przycisk i kontener
        const button = findPhoneButton(stickyPhoneButtonData.settings);
        const container = findCTAContainer();
        
        if (!button || !container) {
            error('Nie można znaleźć przycisku lub kontenera CTA');
            return;
        }

        // Wymuszane pokazanie przycisku w trybie testowym
        if (window.location.href.includes('forceCTA=1')) {
            log('TRYB WYMUSZONY: Pokazuję przycisk CTA niezależnie od ustawień');
            isVisible = true;
        }

        // Jeśli strona nie jest jeszcze w pełni załadowana, ukryj kontener
        if (!pageFullyLoaded) {
            log('Strona nie jest jeszcze w pełni załadowana, ukrywam kontener');
            container.style.display = "none";
            container.style.visibility = "hidden";
            button.style.display = "none";
            button.style.visibility = "hidden";
            button.classList.remove('visible');
            return;
        }

        if (isVisible) {
            log('Pokazuję przycisk');
            
            // Najpierw ustaw style wyświetlania
            container.style.display = "inline-flex";
            container.style.visibility = "visible";
            button.style.display = "flex";
            button.style.visibility = "visible";
            
            // Dodaj klasę visible z krótkim opóźnieniem aby zapewnić płynne pojawienie się
            setTimeout(() => {
                button.classList.add('visible');
                
                // Dodaj animację pulse po 3 sekundach od pokazania przycisku
                setTimeout(() => {
                    log('Dodaję animację pulse');
                    button.classList.add('animate-pulse');
                }, 3000);
            }, 10);
        } else {
            log('Ukrywam przycisk');
            
            // Usuń klasy przed ukryciem elementu
            button.classList.remove('visible');
            button.classList.remove('animate-pulse');
            
            // Po krótkiej chwili ukryj elementy całkowicie
            setTimeout(() => {
                container.style.display = "none";
                container.style.visibility = "hidden";
                button.style.display = "none";
                button.style.visibility = "hidden";
            }, 300); // Czekaj na zakończenie animacji opacity (300ms)
        }
    }

    // Funkcja do aktualizacji widoczności przycisku
    function updateButtonVisibility() {
        log('=== Aktualizacja widoczności przycisku ===');
        log('- stickyPhoneButtonData:', stickyPhoneButtonData);
        log('- pageFullyLoaded:', pageFullyLoaded);

        // Znajdź przycisk na stronie
        const button = findPhoneButton(stickyPhoneButtonData.settings);
        
        if (!button) {
            warn('Nie znaleziono przycisku CTA na stronie - przerywam aktualizację widoczności');
            return;
        }
        
        // Sprawdź czy przycisk ma klasę 'force-display' - jeśli tak, zawsze pokaż
        if (button.classList.contains('force-display')) {
            warn('Przycisk ma klasę force-display - wymuszam pokazanie');
            showOrHideButton(true);
            return;
        }
        
        // Sprawdź czy jest ustawiona flaga force_display w ustawieniach
        if (stickyPhoneButtonData.settings && stickyPhoneButtonData.settings.force_display) {
            warn('Ustawienia mają flagę force_display - wymuszam pokazanie');
            showOrHideButton(true);
            return;
        }
        
        // Standardowe sprawdzenie widoczności
        const isVisible = checkButtonVisibility(stickyPhoneButtonData.settings);
        log('Czy przycisk powinien być widoczny:', isVisible);
        
        // Pokaż lub ukryj przycisk
        showOrHideButton(isVisible);
        
        // Aktualizuj klasy i style przycisku - po sprawdzeniu widoczności
        updateButtonClasses(button, stickyPhoneButtonData.settings);
        
        log('Widoczność przycisku zaktualizowana:', isVisible);
    }
    
    // Nowa funkcja do pobierania świeżych ustawień z REST API
    function fetchFreshSettings() {
        log('Pobieranie świeżych ustawień z REST API');
        
        // Pobierz adres REST API z konfiguracji
        const apiUrl = stickyPhoneButtonData.restApiUrl || '/wp-json/sticky-phone-button/v1/settings';
        
        // Dodaj losowy parametr, aby uniknąć cache przeglądarki
        const urlWithCache = apiUrl + '?_=' + new Date().getTime();
        
        log('Pobieranie ustawień z:', urlWithCache);
        
        // Wykonaj zapytanie do REST API
        fetch(urlWithCache, {
            method: 'GET',
            headers: {
                'Cache-Control': 'no-cache, no-store, must-revalidate',
                'Pragma': 'no-cache',
                'Expires': '0'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Błąd pobierania ustawień: ' + response.status);
            }
            return response.json();
        })
        .then(data => {
            log('Otrzymano ustawienia:', data);
            
            // Aktualizuj dane w pamięci
            if (data && typeof data === 'object') {
                // Zaktualizuj ustawienia w globalnym obiekcie
                stickyPhoneButtonData.settings = data;
                
                // Aktualizuj widoczność przycisku z nowymi ustawieniami
                if (pageFullyLoaded) {
                    log('Strona załadowana, aktualizuję widoczność przycisku z nowymi ustawieniami');
                    updateButtonVisibility();
                } else {
                    log('Strona nie załadowana, odkładam aktualizację widoczności');
                }
            }
        })
        .catch(error => {
            error('Błąd podczas pobierania ustawień z REST API:', error);
        });
    }
    
    // Funkcja sprawdzająca i aktualizująca widoczność przycisku co minutę
    function setupPeriodicCheck() {
        log('Inicjalizacja sprawdzania widoczności przycisku');
        
        // Natychmiastowo pobierz świeże ustawienia z REST API
        fetchFreshSettings();
        
        // Ustaw interwał sprawdzania co minutę (60000 ms)
        setInterval(() => {
            log('Okresowe sprawdzanie widoczności przycisku');
            
            // Sprawdź czy strona jest już załadowana
            if (pageFullyLoaded) {
                // Najpierw sprawdź z istniejącymi ustawieniami
                updateButtonVisibility();
                
                // Pobierz nowe ustawienia z REST API - co 5 minut
                // Używamy modulo, aby nie wysyłać zbyt wielu żądań
                const minutes = new Date().getMinutes();
                if (minutes % 5 === 0) {
                    fetchFreshSettings();
                }
            }
        }, 60000);
    }

    // Specjalna funkcja do wymuszenia inicjalizacji przycisku CTA
    function forceButtonInitialization() {
        log('Wymuszanie inicjalizacji przycisku CTA...');
        
        // Pokaż kontener CTA
        const container = findCTAContainer();
        if (container) {
            container.style.display = "inline-flex";
            container.style.visibility = "visible";
            log('Kontener CTA widoczny');
            
            // Znajdź przycisk i ustaw jego właściwości
            const button = findPhoneButton(stickyPhoneButtonData.settings);
            if (button) {
                button.style.display = "flex";
                button.style.visibility = "visible";
                
                // Dodaj z opóźnieniem klasę visible
                setTimeout(() => {
                    button.classList.add('visible');
                    log('Przycisk CTA widoczny (klasa visible dodana)');
                }, 500);
            } else {
                error('Nie można znaleźć przycisku CTA');
            }
        } else {
            error('Nie można znaleźć kontenera CTA');
        }
    }

    // Funkcja zawsze pokazująca przycisk CTA - ignoruje warunki widoczności
    function alwaysShowCTAButton() {
        warn('WYMUSZAM POKAZANIE PRZYCISKU CTA');
        
        // Znajdź przycisk i kontener
        const button = document.querySelector('.sticky-phone-button');
        const container = document.querySelector('.sticky-cta-container');
        
        if (!button || !container) {
            error('Nie można znaleźć przycisku lub kontenera CTA');
            return;
        }
        
        // Pokaż kontener
        container.style.display = "inline-flex";
        container.style.visibility = "visible";
        
        // Pokaż przycisk
        button.style.display = "flex";
        button.style.visibility = "visible";
        
        // Dodaj klasę visible
        button.classList.add('visible');
        
        log('Przycisk CTA jest teraz widoczny');
    }

    // Funkcje globalne do debugowania i testowania
    window.alwaysShowCTAButton = function() {
        if (typeof stickyPhoneButtonData === 'undefined' || !stickyPhoneButtonData.settings) {
            error('Błąd: Brak obiektu stickyPhoneButtonData lub jego ustawień. Nie można zmodyfikować walidacji urządzenia.');
            return false;
        }
        
        warn('Wymuszam pokazanie przycisku CTA niezależnie od ustawień...');
        
        // Znajdź przycisk
        const button = findPhoneButton(stickyPhoneButtonData.settings);
        if (!button) {
            error('Nie można znaleźć przycisku CTA na stronie.');
            return false;
        }
        
        // Dodaj specjalną klasę
        button.classList.add('force-display');
        
        // Ustaw flagę w ustawieniach
        stickyPhoneButtonData.settings.force_display = true;
        
        // Wywołaj aktualizację widoczności
        updateButtonVisibility();
        
        // Pokaż komunikat o sukcesie
        log('✅ Przycisk CTA jest teraz widoczny niezależnie od warunków!');
        return true;
    };

    window.forceCTAInit = function() {
        if (stickyPhoneButtonData && stickyPhoneButtonData.settings) {
            warn('Wymuszam inicjalizację przycisku CTA...');
            forceButtonInitialization();
            return true;
        } else {
            error('Brak obiektu stickyPhoneButtonData - nie można zainicjalizować przycisku.');
            return false;
        }
    };

    // Aktualizuj widoczność przycisku na starcie
    log('Inicjalizacja sticky CTA button');
    setupPeriodicCheck();
    
    // Dla potrzeb debugowania, dodaj przycisk CTA do globalnego obiektu window
    window.showCTA = alwaysShowCTAButton;
    
    // Ustaw flagę pageFullyLoaded na true po załadowaniu strony
    window.addEventListener('load', () => {
        log('STRONA ZAŁADOWANA - ustawiam pageFullyLoaded=true');
        pageFullyLoaded = true;
        
        // Wymuszamy natychmiastową aktualizację widoczności przycisku po załadowaniu
        setTimeout(() => {
            log('Wymuszam aktualizację widoczności przycisku po załadowaniu');
            updateButtonVisibility();
            
            // Sprawdź czy tryb testowy jest włączony
            if (window.location.href.includes('forceInit=1')) {
                log('Tryb wymuszania inicjalizacji włączony');
                forceButtonInitialization();
            }
            
            // Sprawdź czy tryb "zawsze pokaż" jest włączony
            if (window.location.href.includes('show=1')) {
                log('Tryb "zawsze pokaż" włączony - pokazuję przycisk CTA');
                alwaysShowCTAButton();
            }
            
            // Dodaj specjalne zachowanie do sprawdzania ustawień
            setTimeout(() => {
                log('=== SPRAWDZANIE PRZYCZYNY PROBLEMÓW ===');
                const button = document.querySelector('.sticky-phone-button');
                const container = document.querySelector('.sticky-cta-container');
                
                if (button && container) {
                    log('Stan przycisku CTA:', {
                        buttonStyle: button.style,
                        buttonDisplay: window.getComputedStyle(button).display,
                        buttonVisibility: window.getComputedStyle(button).visibility,
                        buttonOpacity: window.getComputedStyle(button).opacity,
                        containerStyle: container.style,
                        containerDisplay: window.getComputedStyle(container).display,
                        containerVisibility: window.getComputedStyle(container).visibility
                    });
                }
                
                // Sprawdź ponownie ustawienia
                if (stickyPhoneButtonData && stickyPhoneButtonData.settings) {
                    const isVisible = checkButtonVisibility(stickyPhoneButtonData.settings);
                    log('Ponowne sprawdzenie widoczności:', isVisible);
                    
                    if (!isVisible) {
                        // Jeśli przycisk nadal powinien być ukryty, sprawdź dokładnie dlaczego
                        warn('PRZYCISK NADAL UKRYTY - WYMUSZAM POKAZANIE W TRYBIE AWARYJNYM');
                        alwaysShowCTAButton();
                    }
                }
            }, 2000);
        }, 500);
    });
    
    // Dodaj specjalną obsługę dla przycisku debugującego w panelu admina
    if (typeof jQuery !== 'undefined') {
        jQuery(document).on('click', '#debug-cta-button', function(e) {
            e.preventDefault();
            log('Kliknięto przycisk debugowania CTA');
            forceButtonInitialization();
        });
    }
});
