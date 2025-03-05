/**
 * Admin scripts for Sticky Phone Button plugin
 */
jQuery(document).ready(function($) {
    // Funkcja do aktualizacji podpowiedzi dla wartości odnośnika
    function updateLinkValueDescription() {
        var linkType = $('select[name="sticky_phone_button_settings[sticky_phone_button_link_type]"]').val();
        var description = '';
        
        switch(linkType) {
            case 'tel':
                description = 'Numer telefonu, np. +48501501501';
                break;
            case 'mailto':
                description = 'Adres e-mail, np. kontakt@example.com?subject=Zapytanie%20ofertowe';
                break;
            case 'url':
                description = 'Pełny adres strony internetowej, np. https://www.example.com';
                break;
            case 'sms':
                description = 'Numer telefonu do SMS, np. +48501501501?body=Proszę%20o%20kontakt%20w%20sprawie%20oferty';
                break;
            default:
                description = 'Wartość linku zależna od wybranego typu';
        }
        
        // Aktualizacja treści podpowiedzi
        $('input[name="sticky_phone_button_settings[sticky_phone_button_link_value]"]')
            .next('.description')
            .html(description);
    }
    
    // Obsługa zmiany typu linku
    $('select[name="sticky_phone_button_settings[sticky_phone_button_link_type]"]').on('change', function() {
        updateLinkValueDescription();
    });
    
    // Inicjalizacja podpowiedzi przy załadowaniu strony
    updateLinkValueDescription();
});
