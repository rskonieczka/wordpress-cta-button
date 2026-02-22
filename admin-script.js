/**
 * Admin scripts for Sticky Phone Button plugin
 */
jQuery(document).ready(function($) {
    // Function to update link value description
    function updateLinkValueDescription() {
        var linkType = $('select[name="sticky_phone_button_settings[sticky_phone_button_link_type]"]').val();
        var description = '';
        
        switch(linkType) {
            case 'tel':
                description = 'Phone number, e.g. +48501501501';
                break;
            case 'mailto':
                description = 'Email address, e.g. kontakt@example.com?subject=Inquiry%20about%20offer';
                break;
            case 'url':
                description = 'Full website URL, e.g. https://www.example.com';
                break;
            case 'sms':
                description = 'Phone number for SMS, e.g. +48501501501?body=Please%20contact%20me%20regarding%20the%20offer';
                break;
            default:
                description = 'Link value depending on the selected type';
        }
        
        // Update description text
        $('input[name="sticky_phone_button_settings[sticky_phone_button_link_value]"]')
            .next('.description')
            .html(description);
    }
    
    // Handle link type change
    $('select[name="sticky_phone_button_settings[sticky_phone_button_link_type]"]').on('change', function() {
        updateLinkValueDescription();
    });

    // Alternate profile: update link value description when link type changes
    function updateAltLinkValueDescription() {
        var linkType = $('select[name="sticky_phone_button_settings[sticky_phone_button_alt_link_type]"]').val();
        var description = '';
        switch (linkType) {
            case 'tel':
                description = 'Phone number, e.g. +48501501501';
                break;
            case 'mailto':
                description = 'Email address, e.g. kontakt@example.com?subject=Zapytanie';
                break;
            case 'url':
                description = 'Full website URL or form page, e.g. https://example.com/kontakt';
                break;
            case 'sms':
                description = 'Phone number for SMS';
                break;
            default:
                description = 'Link value depending on the selected type';
        }
        $('.alt-link-value-desc').html(description);
    }
    $('select[name="sticky_phone_button_settings[sticky_phone_button_alt_link_type]"]').on('change', function() {
        updateAltLinkValueDescription();
    });

    // Initialize descriptions on page load
    updateLinkValueDescription();
    updateAltLinkValueDescription();
});
