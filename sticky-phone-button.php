<?php

/*
 * Plugin Name: Wordpress CTA Button - by WirtualnyHandlowiec.pl
 * Description: Displays a sticky CTA (Call to Action) button on the sides of the screen on all devices, allowing you to instantly call or take action. Customize location, phone number, days and times of display, and colors.
 * Version: 1.4
 * Author: Wirtualny Handlowiec
 * Author URI: http://wirtualnyhandlowiec.pl/
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Enqueue style and script for frontend
 */
function sticky_phone_button_enqueue_scripts()
{
    // Rejestruj skrypt JavaScript z unikalną wersją bazującą na czasie
    wp_register_script(
        'sticky-phone-button-script',
        plugins_url('script.js', __FILE__),
        array('jquery'),
        time(),  // Używaj czasu jako wersji, aby uniknąć cache
        true
    );

    // Rejestruj arkusz stylów CSS
    wp_register_style(
        'sticky-phone-button-style',
        plugins_url('style.css', __FILE__),
        array(),
        time()  // Używaj czasu jako wersji, aby uniknąć cache
    );

    // Pobierz wszystkie ustawienia
    $settings = sticky_phone_button_get_settings();

    // Sprawdź, czy włączone debugowanie
    $enable_debug = isset($settings['sticky_phone_button_enable_debug']) ? true : false;

    // Przekaż ustawienia do skryptu JS w formacie JSON
    wp_localize_script('sticky-phone-button-script', 'stickyPhoneButtonData', array(
        'settings' => $settings,
        'apiUrl' => admin_url('admin-ajax.php') . '?action=sticky_phone_button_get_settings',
        'restApiUrl' => rest_url('sticky-phone-button/v1/settings'),
        'enableDebug' => $enable_debug,
        'currentTimestamp' => time()
    ));

    // Dołącz skrypt JS do strony
    wp_enqueue_script('sticky-phone-button-script');

    // Dołącz arkusz stylów CSS do strony
    wp_enqueue_style('sticky-phone-button-style');
}

add_action('wp_enqueue_scripts', 'sticky_phone_button_enqueue_scripts');

/**
 * Enqueue admin scripts for the plugin settings page.
 */
function sticky_phone_button_admin_scripts()
{
    $screen = get_current_screen();
    if ($screen && $screen->id === 'toplevel_page_sticky-phone-button') {
        wp_enqueue_script('sticky-phone-button-admin-script', plugins_url('admin-script.js', __FILE__), array('jquery'), '1.0.0', true);
    }
}

add_action('admin_enqueue_scripts', 'sticky_phone_button_admin_scripts');

/**
 * Add a page to the WordPress admin menu for the sticky phone button settings
 */
function sticky_phone_button_add_admin_menu()
{
    /**
     * Add an options page to the WordPress admin menu
     * @param string $page_title The title of the page
     * @param string $menu_title The title of the menu item
     * @param string $capability The capability required to access the page
     * @param string $menu_slug The slug of the menu item
     * @param callable $function The function to call when the page is accessed
     */
    add_options_page(
        __('Settings', 'sticky-phone-button'),
        __('Wordpress CTA Button', 'sticky-phone-button'),
        'manage_options',
        'sticky-phone-button',
        'sticky_phone_button_options_page'
    );
}

add_action('admin_menu', 'sticky_phone_button_add_admin_menu');

/**
 * Register the settings for the sticky phone button plugin.
 */
function sticky_phone_button_settings_init()
{
    // Register the settings for the plugin with disabled cache
    register_setting('stickyPhoneButtonSettings', 'sticky_phone_button_settings', array(
        'type' => 'array',
        'sanitize_callback' => 'sticky_phone_button_sanitize_settings',
        'default' => array()
    ));

    // Add a section for the phone button settings
    add_settings_section(
        'sticky_phone_button_settings_section',
        __('Settings', 'sticky-phone-button'),

        /**
         * Callback function for displaying a description of the settings section.
         * This function is optional and can be used to provide additional information
         * to the user about this section.
         */
        function () {
            // Do nothing
        },
        'stickyPhoneButtonSettings'
    );

    // Add a field for selecting the display device
    add_settings_field(
        'sticky_phone_button_display_device',
        __('Display on', 'sticky-phone-button'),

        /**
         * Callback function to render the select field for the device type.
         * This field allows users to choose on which devices the button will be displayed.
         * @return void
         */
        'sticky_phone_button_display_device_render',
        'stickyPhoneButtonSettings',
        'sticky_phone_button_settings_section'
    );

    // Add a field for selecting the link type
    add_settings_field(
        'sticky_phone_button_link_type',
        __('Link type', 'sticky-phone-button'),

        /**
         * Callback function to render the select field for the link type.
         * This field allows users to specify the type of the link that will be used.
         * @return void
         */
        'sticky_phone_button_link_type_render',
        'stickyPhoneButtonSettings',
        'sticky_phone_button_settings_section'
    );

    // Add a field for selecting the target attribute
    add_settings_field(
        'sticky_phone_button_target',
        __('Link target', 'sticky-phone-button'),

        /**
         * Callback function to render the select field for the target attribute.
         * This field allows users to specify the target attribute for the link.
         * @return void
         */
        'sticky_phone_button_target_render',
        'stickyPhoneButtonSettings',
        'sticky_phone_button_settings_section'
    );

    // Add a field for entering the link value
    add_settings_field(
        'sticky_phone_button_link_value',
        __('Link value', 'sticky-phone-button'),

        /**
         * Callback function to render the text field for the link value.
         * This field allows users to specify the value that will be used for the link.
         * @return void
         */
        'sticky_phone_button_link_value_render',
        'stickyPhoneButtonSettings',
        'sticky_phone_button_settings_section'
    );

    // Add a field for selecting the button position
    add_settings_field(
        'sticky_phone_button_position',
        __('Position', 'sticky-phone-button'),

        /**
         * Callback function to render the select field for the button position.
         * This field allows users to specify where the button will appear on the screen.
         * @return void
         */
        'sticky_phone_button_position_render',
        'stickyPhoneButtonSettings',
        'sticky_phone_button_settings_section'
    );

    // Add a field for entering the CTA text
    add_settings_field(
        'sticky_phone_button_cta_text',
        __('CTA text', 'sticky-phone-button'),
        'sticky_phone_button_cta_text_render',
        'stickyPhoneButtonSettings',
        'sticky_phone_button_settings_section'
    );

    // Add fields for selecting display days and hours
    add_settings_field(
        'sticky_phone_button_display_days',
        __('Display days and hours', 'sticky-phone-button'),
        'sticky_phone_button_display_days_render',
        'stickyPhoneButtonSettings',
        'sticky_phone_button_settings_section'
    );

    // Add a field for selecting the text color of the CTA text
    add_settings_field(
        'sticky_phone_button_cta_text_color',
        __('CTA text color', 'sticky-phone-button'),
        'sticky_phone_button_cta_text_color_render',
        'stickyPhoneButtonSettings',
        'sticky_phone_button_settings_section'
    );

    // Add a field for selecting the font weight of the CTA text
    add_settings_field(
        'sticky_phone_button_font_weight',
        __('CTA font weight', 'sticky-phone-button'),
        'sticky_phone_button_font_weight_render',
        'stickyPhoneButtonSettings',
        'sticky_phone_button_settings_section'
    );

    // Add a field for selecting the background color
    add_settings_field(
        'sticky_phone_button_background_color',
        __('Background color', 'sticky-phone-button'),

        /**
         * Callback function to render the color picker for the background color.
         * This field allows users to choose the background color of the button.
         * @return void
         */
        'sticky_phone_button_background_color_render',
        'stickyPhoneButtonSettings',
        'sticky_phone_button_settings_section'
    );

    // Add a field for selecting the icon color
    add_settings_field(
        'sticky_phone_button_icon_color',
        __('Icon color', 'sticky-phone-button'),
        'sticky_phone_button_icon_color_render',
        'stickyPhoneButtonSettings',
        'sticky_phone_button_settings_section'
    );

    // Add a field for setting the blink time
    add_settings_field(
        'sticky_phone_button_blink_time',
        __('Blink time', 'sticky-phone-button'),

        /**
         * Callback function to render the text field for the blink time.
         * This field allows users to specify the time interval between blinks of the button.
         * @return void
         */
        'sticky_phone_button_blink_time_render',
        'stickyPhoneButtonSettings',
        'sticky_phone_button_settings_section'
    );

    // Add a field for custom CSS class
    add_settings_field(
        'sticky_phone_button_custom_class',
        __('Custom CSS class', 'sticky-phone-button'),

        /**
         * Callback function to render the text field for the custom CSS class.
         * This field allows users to specify custom CSS classes for the button.
         * @return void
         */
        'sticky_phone_button_custom_class_render',
        'stickyPhoneButtonSettings',
        'sticky_phone_button_settings_section'
    );

    // Add a field for custom ID
    add_settings_field(
        'sticky_phone_button_custom_id',
        __('Custom ID', 'sticky-phone-button'),

        /**
         * Callback function to render the text field for the custom ID.
         * This field allows users to specify custom ID for the button.
         * @return void
         */
        'sticky_phone_button_custom_id_render',
        'stickyPhoneButtonSettings',
        'sticky_phone_button_settings_section'
    );

    // Add a field for enabling console logs
    add_settings_field(
        'sticky_phone_button_enable_debug',
        __('Enable console logs', 'sticky-phone-button'),

        /**
         * Callback function to render the checkbox for enabling console logs.
         * This field allows users to enable or disable JavaScript console logs for debugging.
         * @return void
         */
        'sticky_phone_button_enable_debug_render',
        'stickyPhoneButtonSettings',
        'sticky_phone_button_settings_section'
    );

    // Add a field for selecting the icon name
    add_settings_field(
        'sticky_phone_button_icon_name',
        __('Icon name', 'sticky-phone-button'),
        'sticky_phone_button_icon_name_render',
        'stickyPhoneButtonSettings',
        'sticky_phone_button_settings_section'
    );
}

add_action('admin_init', 'sticky_phone_button_settings_init');

/**
 * Render a select field for the device type.
 * This field allows users to choose on which devices the button will be displayed.
 * @return void
 */
function sticky_phone_button_display_device_render()
{
    $options = sticky_phone_button_get_settings();
    // Get the current value of the display device setting
    $display_device = isset($options['sticky_phone_button_display_device']) ? $options['sticky_phone_button_display_device'] : 'both';
    // Render the select field with options for phones, desktops and both
    ?> <select name='sticky_phone_button_settings[sticky_phone_button_display_device]'>
        <option value='phones' <?php selected($display_device, 'phones'); ?>>Only on phones</option>
        <option value='desktops' <?php selected($display_device, 'desktops'); ?>>Only on desktops</option>
        <option value='both' <?php selected($display_device, 'both'); ?>>On both phones and desktops</option>
    </select> <?php
}

/**
 * Render a select field for the link type.
 * This field allows users to specify the type of the link that will be used.
 * @return void
 */
function sticky_phone_button_link_type_render()
{
    $options = sticky_phone_button_get_settings();
    // Get the current value of the link type setting
    $link_type = isset($options['sticky_phone_button_link_type']) ? $options['sticky_phone_button_link_type'] : 'tel';
    // Render the select field with options for link types
    ?> <select name='sticky_phone_button_settings[sticky_phone_button_link_type]'>
        <option value='tel' <?php selected($link_type, 'tel'); ?>>Phone</option>
        <option value='mailto' <?php selected($link_type, 'mailto'); ?>>Email</option>
        <option value='url' <?php selected($link_type, 'url'); ?>>Website</option>
        <option value='sms' <?php selected($link_type, 'sms'); ?>>SMS</option>
    </select>
    <p class="description">Select the type of link to use for the button.</p> <?php
}

/**
 * Render a select field for the target attribute.
 * This field allows users to specify the target attribute for the link.
 * @return void
 */
function sticky_phone_button_target_render()
{
    $options = sticky_phone_button_get_settings();
    // Get the current value of the target attribute setting
    $target = isset($options['sticky_phone_button_target']) ? $options['sticky_phone_button_target'] : '_self';
    // Render the select field with options for target attributes
    ?> <select name='sticky_phone_button_settings[sticky_phone_button_target]'>
        <option value='_self' <?php selected($target, '_self'); ?>>_self</option>
        <option value='_blank' <?php selected($target, '_blank'); ?>>_blank</option>
        <option value='_parent' <?php selected($target, '_parent'); ?>>_parent</option>
        <option value='_top' <?php selected($target, '_top'); ?>>_top</option>
    </select>
    <p class="description">Select the target attribute for the link.</p> <?php
}

/**
 * Render a text field for the link value.
 * This field allows users to specify the value that will be used for the link.
 * @return void
 */
function sticky_phone_button_link_value_render()
{
    $options = sticky_phone_button_get_settings();
    $link_type = isset($options['sticky_phone_button_link_type']) ? $options['sticky_phone_button_link_type'] : 'tel';
    $link_value = isset($options['sticky_phone_button_link_value']) ? $options['sticky_phone_button_link_value'] : '';

    ?> <input type='text' name='sticky_phone_button_settings[sticky_phone_button_link_value]'
            value='<?php echo esc_attr($link_value); ?>'>
    <p class="description">
    <?php
    switch ($link_type) {
        case 'tel':
            echo 'Phone number, e.g. +48501501501';
            break;
        case 'mailto':
            echo 'Email address, e.g. kontakt@example.com?subject=Zapytanie%20ofertowe';
            break;
        case 'url':
            echo 'Full website URL, e.g. https://www.example.com';
            break;
        case 'sms':
            echo 'Phone number for SMS, e.g. +48501501501?body=Proszę%20o%20kontakt%20w%20sprawie%20oferty';
            break;
        default:
            echo 'Link value depending on the selected type';
    }
    ?>
    </p> <?php
}

/**
 * Render a textarea field for the CTA text.
 * This field allows users to specify the text that will be used for the CTA.
 * @return void
 */
function sticky_phone_button_cta_text_render()
{
    $options = sticky_phone_button_get_settings();
    $cta_text = isset($options['sticky_phone_button_cta_text']) ? $options['sticky_phone_button_cta_text'] : '';

    ?> <textarea name='sticky_phone_button_settings[sticky_phone_button_cta_text]' rows='2' cols='40'><?php echo esc_textarea($cta_text); ?></textarea>
    <p class="description">Text to display on the button. Leave blank to show only the icon. <br/>&bull; &lt;b&gt; or &lt;strong&gt; to bold selected text, e.g. &lt;b&gt;Call&lt;/b&gt; now! <br/>&bull; You can enter text in multiple lines using  &lt;br/&gt; to manually add a new line</p> <?php
}

/**
 * Render a color picker for the text color of the CTA text.
 * This field allows users to choose the text color of the CTA text.
 * @return void
 */
function sticky_phone_button_cta_text_color_render()
{
    $options = sticky_phone_button_get_settings();
    $cta_text_color = isset($options['sticky_phone_button_cta_text_color']) ? $options['sticky_phone_button_cta_text_color'] : '#000000';

    ?> <input type='color' name='sticky_phone_button_settings[sticky_phone_button_cta_text_color]'
        value='<?php echo esc_attr($cta_text_color); ?>'>
    <?php
}

/**
 * Render a select field for the font weight of the CTA text.
 * This field allows users to choose the font weight of the CTA text.
 * @return void
 */
function sticky_phone_button_font_weight_render()
{
    $options = sticky_phone_button_get_settings();
    $font_weight = isset($options['sticky_phone_button_font_weight']) ? $options['sticky_phone_button_font_weight'] : 'normal';

    ?> <select name='sticky_phone_button_settings[sticky_phone_button_font_weight]'>
        <option value='normal' <?php selected($font_weight, 'normal'); ?>>Normal</option>
        <option value='bold' <?php selected($font_weight, 'bold'); ?>>Bold</option>
        <option value='lighter' <?php selected($font_weight, 'lighter'); ?>>Lighter</option>
        <option value='bolder' <?php selected($font_weight, 'bolder'); ?>>Bolder</option>
        <option value='100' <?php selected($font_weight, '100'); ?>>100</option>
        <option value='200' <?php selected($font_weight, '200'); ?>>200</option>
        <option value='300' <?php selected($font_weight, '300'); ?>>300</option>
        <option value='400' <?php selected($font_weight, '400'); ?>>400</option>
        <option value='500' <?php selected($font_weight, '500'); ?>>500</option>
        <option value='600' <?php selected($font_weight, '600'); ?>>600</option>
        <option value='700' <?php selected($font_weight, '700'); ?>>700</option>
        <option value='800' <?php selected($font_weight, '800'); ?>>800</option>
        <option value='900' <?php selected($font_weight, '900'); ?>>900</option>
    </select>
    <p class="description">Select the font weight for the CTA text.</p>
<?php
}

/**
 * Render a text field for the blink time.
 * This field allows users to enter the time (in milliseconds) of the blink animation for the button.
 * @return void
 */
function sticky_phone_button_blink_time_render()
{
    $options = sticky_phone_button_get_settings();
    // Default blink time is 4000 milliseconds (4 seconds)
    $blink_time = isset($options['sticky_phone_button_blink_time']) ? $options['sticky_phone_button_blink_time'] : '4000';
?> <input type="number" 
        name="sticky_phone_button_settings[sticky_phone_button_blink_time]" value="<?php echo esc_attr($blink_time); ?>"
        min="500" max="60000">
    <p class="description"> Time (in milliseconds) for the blink animation. <br> Value must be between 500-60000 ms.<br>
    Converter: 1 second = 1000 milliseconds (e.g. 4000 ms = 4 s, 5500 ms = 5.5 s).
    </p> <?php
}

/**
 * Render the dropdown field for selecting the button position.
 * This field allows users to choose where the sticky phone button will be displayed on the screen.
 *
 * @return void
 */
function sticky_phone_button_position_render()
{
    // Retrieve the plugin options from the database
    $options = sticky_phone_button_get_settings();

    // Define the available positions for the sticky phone button
    $positions = [
        'bottom-right' => 'Bottom right corner',
        'middle-right' => 'Right middle',
        'top-right' => 'Top right corner',
        'bottom-left' => 'Bottom left corner',
        'middle-left' => 'Left middle',
        'top-left' => 'Top left corner',
        'bottom-center' => 'Bottom center',
        'top-center' => 'Top center',
    ];
    ?> <select name='sticky_phone_button_settings[sticky_phone_button_position]'>
        <?php
        // Iterate over each position and render it as an option in the dropdown
        foreach ($positions as $value => $label):
            ?> <option value='<?php echo esc_attr($value); ?>'
                <?php selected($options['sticky_phone_button_position'], $value); ?>> <?php echo esc_html($label); ?> </option>
        <?php endforeach; ?> </select> <?php
}

// Renderowanie checkboxów
$days_of_week = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
$days_of_week_pl = [
    'Monday' => 'Monday',
    'Tuesday' => 'Tuesday',
    'Wednesday' => 'Wednesday',
    'Thursday' => 'Thursday',
    'Friday' => 'Friday',
    'Saturday' => 'Saturday',
    'Sunday' => 'Sunday'
];

/**
 * Renderowanie checkboxów dla dni i godzin
 *
 * This function renders checkboxes for each day of the week and a text field for specifying the hours of the day.
 * The function also retrieves the plugin options from the database and checks if the day is selected.
 * If the day is selected, the checkbox is checked and the text field is populated with the hours.
 * If the day is not selected, the checkbox is unchecked and the text field is empty.
 *
 * @return void
 */
function sticky_phone_button_display_days_render()
{
    global $days_of_week, $days_of_week_pl;
    $options = sticky_phone_button_get_settings();

    // Dodaj instrukcję
    echo '<p><strong>Note:</strong> Leaving the hours field blank will display the button throughout the day.</p>';

    // Dla każdego dnia wyświetl checkbox i pole godziny
    foreach ($days_of_week as $day) {
        // Sprawdzamy, czy dzień jest wybrany
        $checked = isset($options['display_days'][$day]['enabled']) ? 'checked' : '';

        // Sprawdzamy, czy godziny dla danego dnia są ustawione, jeśli nie, ustawiamy pustą wartość
        $hours = isset($options['display_days'][$day]['hours']) ? $options['display_days'][$day]['hours'] : '';

        // Podziel godziny na start i koniec
        $start_time = '';
        $end_time = '';
        if (!empty($hours) && strpos($hours, '-') !== false) {
            list($start_time, $end_time) = explode('-', $hours);
        }

        ?>
        <div class="time-range-row">
            <label class="day-checkbox">
                <input type='checkbox' name='sticky_phone_button_settings[display_days][<?php echo $day; ?>][enabled]'
                    <?php echo $checked; ?>> <?php echo $days_of_week_pl[$day]; ?> 
            </label>
            
            <div class="time-inputs">
                <input type='time' 
                    class="time-start" 
                    value="<?php echo esc_attr($start_time); ?>"
                    onchange="updateTimeRange(this)">
                    
                <span class="time-separator">-</span>
                
                <input type='time' 
                    class="time-end" 
                    value="<?php echo esc_attr($end_time); ?>"
                    onchange="updateTimeRange(this)">
                    
                <input type='hidden' 
                    name='sticky_phone_button_settings[display_days][<?php echo $day; ?>][hours]'
                    class="time-range-value"
                    value='<?php echo esc_attr($hours); ?>'>
            </div>
        </div>
        <?php
    }

    // Dodaj CSS i JS
    ?>
    <style>
        .time-range-row {
            margin-bottom: 10px;
            display: flex;
            align-items: center;
        }
        .day-checkbox {
            width: 120px;
            display: inline-block;
        }
        .time-inputs {
            display: inline-flex;
            align-items: center;
        }
        .time-separator {
            margin: 0 10px;
            font-weight: bold;
        }
        input[type="time"] {
            width: 110px;
            padding: 5px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
    </style>
    <script>
    function updateTimeRange(input) {
        var row = input.closest('.time-range-row');
        var startTime = row.querySelector('.time-start').value;
        var endTime = row.querySelector('.time-end').value;
        
        if (startTime && endTime) {
            row.querySelector('.time-range-value').value = startTime + '-' + endTime;
        }
    }
    
    // Inicjalizacja na starcie
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.time-range-row').forEach(function(row) {
            var hiddenValue = row.querySelector('.time-range-value').value;
            if (hiddenValue && hiddenValue.indexOf('-') !== -1) {
                var times = hiddenValue.split('-');
                row.querySelector('.time-start').value = times[0];
                row.querySelector('.time-end').value = times[1];
            }
        });
    });
    </script>
    <?php
}

/**
 * Renderowanie pola wyboru koloru ikony.
 *
 * This function renders a color picker field for the icon color.
 * The field is populated with the current value from the database.
 * If no value is set, the field is set to white (#FFF).
 *
 * @return void
 */
function sticky_phone_button_icon_color_render()
{
    $options = sticky_phone_button_get_settings();
    ?> <input type='color' name='sticky_phone_button_settings[sticky_phone_button_icon_color]'
        value='<?php echo esc_attr(isset($options['sticky_phone_button_icon_color']) ? $options['sticky_phone_button_icon_color'] : '#FFF'); ?>'>
    <p class="description">Icon color in the button.</p>
<?php
}

/**
 * Renderowanie pola wyboru koloru tła koła.
 *
 * This function renders a color picker field for the background color of the circle.
 * The field is populated with the current value from the database.
 * If no value is set, the field is set to a default green color (#10941f).
 *
 * @return void
 */
function sticky_phone_button_background_color_render()
{
    $options = sticky_phone_button_get_settings();
?> <input type='color' name='sticky_phone_button_settings[sticky_phone_button_background_color]'
        value='<?php echo esc_attr(isset($options['sticky_phone_button_background_color']) ? $options['sticky_phone_button_background_color'] : '#10941f'); ?>'>
<?php
}

/**
 * Renderowanie pola wyboru niestandardowej klasy CSS.
 *
 * This function renders a text field for the custom CSS class.
 * The field is populated with the current value from the database.
 * If no value is set, the field is empty.
 *
 * @return void
 */
function sticky_phone_button_custom_class_render()
{
    $options = sticky_phone_button_get_settings();
?> <input type='text' name='sticky_phone_button_settings[sticky_phone_button_custom_class]'
        value='<?php echo esc_attr(isset($options['sticky_phone_button_custom_class']) ? $options['sticky_phone_button_custom_class'] : ''); ?>'>
    <p class="description">Additional CSS classes separated by space, e.g. "myclass1 myclass2".</p>
<?php
}

/**
 * Renderowanie pola wyboru niestandardowego ID.
 *
 * This function renders a text field for the custom ID.
 * The field is populated with the current value from the database.
 * If no value is set, the field is empty.
 *
 * @return void
 */
function sticky_phone_button_custom_id_render()
{
    $options = sticky_phone_button_get_settings();
?> <input type='text' name='sticky_phone_button_settings[sticky_phone_button_custom_id]'
        value='<?php echo esc_attr(isset($options['sticky_phone_button_custom_id']) ? $options['sticky_phone_button_custom_id'] : ''); ?>'>
    <p class="description">Custom ID for the button. If blank, the default ID "sticky-phone-button" will be used.</p>
<?php
}

/**
 * Renderowanie checkboxa do włączania logów konsolowych.
 *
 * This function renders a checkbox for enabling console logs.
 * The checkbox is checked if the option is enabled in the database.
 * If no value is set, the checkbox is unchecked.
 *
 * @return void
 */
function sticky_phone_button_enable_debug_render()
{
    $options = sticky_phone_button_get_settings();
    $checked = isset($options['sticky_phone_button_enable_debug']) ? 'checked' : '';
?> <input type='checkbox' name='sticky_phone_button_settings[sticky_phone_button_enable_debug]' <?php echo $checked; ?>>
    <p class="description">Enable JavaScript console logs for debugging. Disable on production.</p>
<?php
}

/**
 * Dodaje pole wyboru ikony z Material Symbols
 */
function sticky_phone_button_icon_name_render()
{
    $options = sticky_phone_button_get_settings();
    $icon_name = isset($options['sticky_phone_button_icon_name']) ? $options['sticky_phone_button_icon_name'] : 'call';

    // Lista popularnych ikon Material Symbols przydatnych dla przycisku CTA
    $popular_icons = array(
        'call' => 'Phone',
        'phone_enabled' => 'Phone (alternative)',
        'smartphone' => 'Smartphone',
        'mail' => 'Email',
        'chat' => 'Chat',
        'forum' => 'Forum',
        'sms' => 'SMS',
        'support_agent' => 'Support agent',
        'headset_mic' => 'Headset with microphone',
        'contact_support' => 'Contact support',
        'contact_page' => 'Contact',
        'help' => 'Help',
        'info' => 'Info',
        'shopping_cart' => 'Shopping cart',
        'payments' => 'Payments',
        'local_offer' => 'Offer',
        'campaign' => 'Promotion',
        'home' => 'Home',
        'check' => 'Check',
        'chevron_right' => 'Right chevron',
        'chevron_left' => 'Left chevron',
        'done_outline' => 'Done outline',
        'featured_seasonal_and_gifts' => 'Featured, seasonal and gifts'
    );

?> <select name='sticky_phone_button_settings[sticky_phone_button_icon_name]'>
        <?php foreach ($popular_icons as $icon_value => $icon_label): ?>
            <option value='<?php echo esc_attr($icon_value); ?>' <?php selected($icon_name, $icon_value); ?>>
                <?php echo esc_html($icon_label); ?>
            </option>
        <?php endforeach; ?>
    </select>
    <div style="margin-top: 10px; font-size: 24px;">
        <span class="material-symbols-rounded" style="vertical-align: middle;"><?php echo esc_html($icon_name); ?></span>
        <span style="vertical-align: middle; margin-left: 10px;">Icon preview</span>
    </div>
    <p class="description">
        Select an icon for the button from the <a href="https://fonts.google.com/icons" target="_blank">Material Symbols</a> library. 
        If you want to use a different icon, enter its name in the plugin code.
    </p>
    <script>
        jQuery(document).ready(function($) {
            // Aktualizuj podgląd ikony na żywo podczas zmiany wyboru
            $('select[name="sticky_phone_button_settings[sticky_phone_button_icon_name]"]').on('change', function() {
                var selectedIcon = $(this).val();
                $('.material-symbols-rounded').text(selectedIcon);
            });
        });
    </script>
    <?php
}

/**
 * Enqueue Material Symbols
 */
function sticky_phone_button_enqueue_material_symbols()
{
    // Załaduj Material Symbols z Google Fonts
    wp_enqueue_style(
        'material-symbols',
        'https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200',
        array(),
        null
    );
}

add_action('wp_enqueue_scripts', 'sticky_phone_button_enqueue_material_symbols');
add_action('admin_enqueue_scripts', 'sticky_phone_button_enqueue_material_symbols');

/**
 * Generuje kod HTML dla ikony Material Symbols
 *
 * @param string $icon_name Nazwa ikony z biblioteki Material Symbols
 * @param array $options Dodatkowe opcje dla ikony (klasa, styl, itp.)
 * @return string Kod HTML ikony
 */
function sticky_phone_button_get_material_icon($icon_name = 'call', $options = array())
{
    // Domyślne opcje
    $defaults = array(
        'class' => 'material-symbols-rounded',
        'style' => '',
        'size' => '24px',
        'fill' => '0',
        'weight' => '400',
        'grade' => '0'
    );

    // Połącz opcje użytkownika z domyślnymi
    $options = array_merge($defaults, $options);

    // Wygeneruj style dla ikony
    $icon_style = '';
    if (!empty($options['size'])) {
        $icon_style .= 'font-size: ' . esc_attr($options['size']) . ';';
    }
    if (!empty($options['fill'])) {
        $icon_style .= '--msr-fill: ' . esc_attr($options['fill']) . ';';
    }
    if (!empty($options['weight'])) {
        $icon_style .= 'font-weight: ' . esc_attr($options['weight']) . ';';
    }
    if (!empty($options['grade'])) {
        $icon_style .= '--msr-grade: ' . esc_attr($options['grade']) . ';';
    }

    // Połącz style użytkownika z wygenerowanymi
    if (!empty($options['style'])) {
        $icon_style .= $options['style'];
    }

    // Wygeneruj kod HTML ikony
    $html = sprintf(
        '<span class="%s" style="%s">%s</span>',
        esc_attr($options['class']),
        esc_attr($icon_style),
        esc_html($icon_name)
    );

    return $html;
}

/*
 * sticky-cta-container
 * Displays the options page for the sticky phone button plugin in the WordPress admin area.
 * This function generates the HTML form with the plugin settings.
 *
 * @return void
 */
function sticky_phone_button_options_page()
{
    // Form with the plugin settings
    ?> <form action='options.php' method='post'>
        <h2>Wordpress CTA Button - by WirtualnyHandlowiec.pl</h2> <?php
    // Display the settings sections
    settings_fields('stickyPhoneButtonSettings');
    do_settings_sections('stickyPhoneButtonSettings');
    // Display the submit button
    submit_button();
    ?>
    </form> <?php
}

/**
 * Style CSS dla przycisku CTA
 */
function sticky_phone_button_css()
{
    $options = sticky_phone_button_get_settings();
    $icon_color = isset($options['sticky_phone_button_icon_color']) ? $options['sticky_phone_button_icon_color'] : '#ffffff';
    $background_color = isset($options['sticky_phone_button_background_color']) ? $options['sticky_phone_button_background_color'] : '#000000';

    ?> <style type="text/css">
        /* Style dla Material Symbols */
        .material-symbols-rounded {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
            font-size: 24px;
            color: <?php echo esc_attr($icon_color); ?>;
        }
        
        /* Podstawowe style dla przycisku CTA */
        .sticky-phone-button {
            position: relative; /* Zmiana z fixed na relative */
            background-color: <?php echo esc_attr($background_color); ?>;
            border-radius: 50px;
            padding: 10px 20px !important;
            display: flex !important;
            align-items: center;
            justify-content: center;
            flex-direction: row;
            z-index: 9909;
            text-decoration: none;
            box-shadow: 0px 0px 10px 0px rgba(0, 0, 0, 0.5);
            opacity: 0; /* Początkowo przycisk jest niewidoczny */
            transition: opacity 0.3s ease; /* Dodajemy płynne przejście */
        }
        
        .sticky-phone-button.visible {
            opacity: 1; /* Klasa do pokazania przycisku */
        }
        
        .sticky-phone-button.force-display {
            display: flex !important;
            visibility: visible !important;
            opacity: 1 !important;
        }
        
        .sticky-phone-button .cta-text {
            font-size: 1vh;
            line-height: 1.2;
            padding: 0 0 0 10px;
            margin: 0 5px; /* Dodaję marginesy z obu stron */
            display: block;
            justify-content: center;
            align-items: center;
            text-align: center;
            max-width: max-content;
            width: max-content;
            font-weight: <?php echo esc_attr(isset($options['sticky_phone_button_font_weight']) ? $options['sticky_phone_button_font_weight'] : 'normal'); ?> !important;
        }
        
        .sticky-phone-button.sticky-right .cta-text {
            padding: 0 10px 0 0; /* Odwrotny padding dla przycisku po prawej */
            order: -1; /* Zmienia kolejność - tekst przed ikoną */
        }
        
        .button-icon {
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: inherit;
        }
        
        .button-icon svg {
            width: 24px;
            height: 24px;
            z-index: 999; /* Wyższy z-index dla ikony */
        }
        
        /* Kontener dla przycisku i tekstu CTA */
        .sticky-cta-container {
            position: fixed;
            z-index: 9999990;
            display: inline-flex;
            align-items: center;
            right: 20px; /* Domyślna pozycja, jeśli inne klasy nie przesłonią */
        }
        
        /* Pozycje dla kontenera */
        .container-bottom-right {
            bottom: 10px;
            right: 10px;
            border-top-left-radius: 50px;
            border-bottom-left-radius: 50px;
        }
        
        .container-bottom-left {
            bottom: 10px;
            left: 10px;
            border-top-right-radius: 50px;
            border-bottom-right-radius: 50px;
        }
        
        .container-bottom-center {
            bottom: 10px;
            left: 50%;
            right: unset;
            transform: translateX(-50%);
        }
        
        .container-top-right {
            top: 10px;
            right: 10px;
            border-bottom-left-radius: 50px;
            border-top-left-radius: 50px;
        }
        
        .container-top-left {
            top: 10px;
            left: 10px;
            border-bottom-right-radius: 50px;
            border-top-right-radius: 50px;
        }
        
        .container-top-center {
            top: 10px;
            left: 50%;
            right: unset;
            transform: translateX(-50%);
        }
        
        .container-middle-right {
            top: 50%;
            right: 10px;
            transform: translateY(-50%);
            border-top-left-radius: 50px;
            border-bottom-left-radius: 50px;
        }
        
        .container-middle-left {
            top: 50%;
            left: 10px;
            transform: translateY(-50%);
            border-top-right-radius: 50px;
            border-bottom-right-radius: 50px;
        }
        
        .container-middle-center {
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }
        
        /* Style dla przycisku w trybie wymuszonym */
        .sticky-phone-button.force-display {
            display: flex !important;
            visibility: visible !important;
            opacity: 1 !important;
        }
        
        /* Dodajemy specjalny styl dla przycisku z prawej strony */
        .sticky-phone-button.right .cta-text {
            padding: 0 10px 0 0; /* Odwrotny padding dla przycisku po prawej */
            order: -1; /* Zmienia kolejność - tekst przed ikoną */
        }
    </style> <?php
}

add_action('wp_head', 'sticky_phone_button_css');

/**
 * Sanitize settings and prevent caching
 */
function sticky_phone_button_sanitize_settings($settings)
{
    // No cache headers
    nocache_headers();

    // Sanitize each setting
    if (isset($settings['sticky_phone_button_link_type'])) {
        $settings['sticky_phone_button_link_type'] = sanitize_text_field($settings['sticky_phone_button_link_type']);
    }

    if (isset($settings['sticky_phone_button_target'])) {
        // Upewnij się, że wartość target jest jedną z dozwolonych
        $allowed_targets = array('_self', '_blank', '_parent', '_top');
        if (in_array($settings['sticky_phone_button_target'], $allowed_targets)) {
            $settings['sticky_phone_button_target'] = $settings['sticky_phone_button_target'];
        } else {
            $settings['sticky_phone_button_target'] = '_self';  // Domyślna wartość
        }
    }

    if (isset($settings['sticky_phone_button_link_value'])) {
        $settings['sticky_phone_button_link_value'] = sanitize_text_field($settings['sticky_phone_button_link_value']);
    }

    // Sanitize CTA text
    if (isset($settings['sticky_phone_button_cta_text'])) {
        // Używamy wp_kses, aby umożliwić tagi <b>, <strong> i <br>
        $allowed_html = array(
            'b' => array(),
            'strong' => array(),
            'br' => array(),
        );
        $settings['sticky_phone_button_cta_text'] = wp_kses(stripslashes($settings['sticky_phone_button_cta_text']), $allowed_html);
    }

    // Sanitize CTA text color
    if (isset($settings['sticky_phone_button_cta_text_color'])) {
        $settings['sticky_phone_button_cta_text_color'] = sanitize_hex_color($settings['sticky_phone_button_cta_text_color']);
    }

    // Sanitize font weight
    if (isset($settings['sticky_phone_button_font_weight'])) {
        $allowed_weights = array('normal', 'bold', 'lighter', 'bolder', '100', '200', '300', '400', '500', '600', '700', '800', '900');
        if (in_array($settings['sticky_phone_button_font_weight'], $allowed_weights)) {
            $settings['sticky_phone_button_font_weight'] = $settings['sticky_phone_button_font_weight'];
        } else {
            $settings['sticky_phone_button_font_weight'] = 'normal';  // Domyślna wartość
        }
    }

    // Sanitize background color
    if (isset($settings['sticky_phone_button_background_color'])) {
        $settings['sticky_phone_button_background_color'] = sanitize_hex_color($settings['sticky_phone_button_background_color']);
    }

    // Wymuś odświeżenie cache'u opcji
    wp_cache_delete('sticky_phone_button_settings', 'options');

    // Wyłącz cache transients dla ustawień
    delete_transient('sticky_phone_button_settings_cache');

    return $settings;
}

/**
 * Pobierz aktualne ustawienia wtyczki
 */
function sticky_phone_button_get_settings()
{
    $settings = get_option('sticky_phone_button_settings', []);

    // Ustaw domyślne wartości, jeśli potrzebne
    if (empty($settings['sticky_phone_button_link_type'])) {
        $settings['sticky_phone_button_link_type'] = 'tel';
    }

    if (empty($settings['sticky_phone_button_link_value']) && !empty($settings['sticky_phone_button_phone_number'])) {
        // Przepisz stary numer telefonu do nowej wartości dla zachowania kompatybilności
        $settings['sticky_phone_button_link_value'] = $settings['sticky_phone_button_phone_number'];
    }

    // Sprawdź parametry URL dla trybu wymuszania
    $force_display = false;
    if (isset($_GET['show']) || isset($_GET['forceCTA']) || isset($_GET['alwaysShowCTA']) || isset($_GET['debug']) || isset($_GET['forceInit'])) {
        $force_display = true;
    }
    $settings['force_display'] = $force_display;

    // Upewnij się, że typ urządzenia ma prawidłową wartość
    if (empty($settings['sticky_phone_button_display_device']) ||
            !in_array($settings['sticky_phone_button_display_device'], array('all', 'both', 'phones', 'mobile', 'desktops', 'desktop'))) {
        // Ustaw domyślną wartość 'all'
        $settings['sticky_phone_button_display_device'] = 'all';
    }

    // Konwersja ze starych typów http/https na nowy url
    if ($settings['sticky_phone_button_link_type'] === 'http' || $settings['sticky_phone_button_link_type'] === 'https') {
        $settings['sticky_phone_button_link_type'] = 'url';
    }

    // Ustaw domyślne wartości dla tekstu CTA i koloru tła
    if (empty($settings['sticky_phone_button_cta_text'])) {
        $settings['sticky_phone_button_cta_text'] = '';
    }

    if (empty($settings['sticky_phone_button_cta_text_color'])) {
        $settings['sticky_phone_button_cta_text_color'] = '#000000';
    }

    if (empty($settings['sticky_phone_button_target'])) {
        $settings['sticky_phone_button_target'] = '_self';
    }

    if (empty($settings['sticky_phone_button_font_weight'])) {
        $settings['sticky_phone_button_font_weight'] = 'normal';
    }

    if (empty($settings['sticky_phone_button_background_color'])) {
        $settings['sticky_phone_button_background_color'] = '#10941f';
    }

    return $settings;
}

/**
 * Add REST API endpoint for settings
 */
function sticky_phone_button_register_rest_route()
{
    register_rest_route('sticky-phone-button/v1', '/settings', array(
        'methods' => 'GET',
        'callback' => 'sticky_phone_button_get_settings_json',
        'permission_callback' => '__return_true'
    ));
}

add_action('rest_api_init', 'sticky_phone_button_register_rest_route');

/**
 * REST API callback to get settings as JSON with cache headers
 */
function sticky_phone_button_get_settings_json()
{
    // Pobierz zawsze świeże ustawienia z bazy danych
    $options = get_option('sticky_phone_button_settings');

    // Upewnij się, że format danych jest poprawny dla JavaScript
    if (isset($options['display_days']) && is_array($options['display_days'])) {
        // Dodaj informacje do logów, jeśli włączone debugowanie
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log('Sticky Phone Button API: ' . json_encode($options));
        }
    }

    // Ustawienia nagłówków zapobiegających cache'owaniu
    header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
    header('Cache-Control: post-check=0, pre-check=0', false);
    header('Pragma: no-cache');
    header('Expires: 0');

    // Dodaj aktualny timestamp do odpowiedzi, aby upewnić się, że dane są świeże
    $options['timestamp'] = time();

    return rest_ensure_response($options);
}

/**
 * Dodaje skrypt do stopki strony
 * @return void
 */
// function sticky_phone_button_script()
// {
//     // Pobierz wszystkie ustawienia
//     $settings = sticky_phone_button_get_settings();
//
//     // Sprawdź, czy włączone debugowanie
//     $enable_debug = isset($settings['sticky_phone_button_enable_debug']) ? true : false;
//
//     // Przekaż ustawienia do skryptu JS w formacie JSON
//     wp_localize_script('sticky-phone-button-script', 'stickyPhoneButtonData', array(
//         'settings' => $settings,
//         'apiUrl' => admin_url('admin-ajax.php') . '?action=sticky_phone_button_get_settings',
//         'enableDebug' => $enable_debug
//     ));
//
//     // Dołącz skrypt JS do strony
//     wp_enqueue_script('sticky-phone-button-script');
//
//     // Dołącz arkusz stylów CSS do strony
//     wp_enqueue_style('sticky-phone-button-style');
// }

// add_action('wp_enqueue_scripts', 'sticky_phone_button_script');

/**
 * AJAX handler for retrieving sticky phone button settings
 */
function sticky_phone_button_ajax_get_settings()
{
    // Pobierz świeże ustawienia z bazy danych
    $options = get_option('sticky_phone_button_settings');

    // Ustawienia nagłówków zapobiegających cache'owaniu
    header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
    header('Cache-Control: post-check=0, pre-check=0', false);
    header('Pragma: no-cache');
    header('Expires: 0');

    // Dodaj aktualny timestamp do odpowiedzi, aby upewnić się, że dane są świeże
    $options['timestamp'] = time();

    // Zwróć dane w formacie JSON
    wp_send_json($options);
    exit;
}

add_action('wp_ajax_sticky_phone_button_get_settings', 'sticky_phone_button_ajax_get_settings');
add_action('wp_ajax_nopriv_sticky_phone_button_get_settings', 'sticky_phone_button_ajax_get_settings');

/**
 * Display the sticky phone button on the frontend with the options.
 */
function sticky_phone_button_html()
{
    // Usuń te linie:
    // nocache_headers();
    // header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
    // header('Cache-Control: post-check=0, pre-check=0', false);
    // header('Pragma: no-cache');

    // Usuń te linie:
    // wp_cache_delete('sticky_phone_button_html', 'options');
    // delete_transient('sticky_phone_button_html_cache');

    $options = sticky_phone_button_get_settings();

    // Retrieve the link type, link value, position, icon color, background color, and display device settings
    $link_type = isset($options['sticky_phone_button_link_type']) ? $options['sticky_phone_button_link_type'] : 'tel';
    $link_value = isset($options['sticky_phone_button_link_value']) ? $options['sticky_phone_button_link_value'] : '';
    $position = isset($options['sticky_phone_button_position']) ? $options['sticky_phone_button_position'] : 'bottom-right';
    $icon_color = isset($options['sticky_phone_button_icon_color']) ? $options['sticky_phone_button_icon_color'] : '#ffffff';
    $background_color = isset($options['sticky_phone_button_background_color']) ? $options['sticky_phone_button_background_color'] : '#000000';
    $display_device = isset($options['sticky_phone_button_display_device']) ? $options['sticky_phone_button_display_device'] : 'both';
    $custom_class = isset($options['sticky_phone_button_custom_class']) ? $options['sticky_phone_button_custom_class'] : '';
    $custom_id = isset($options['sticky_phone_button_custom_id']) && !empty($options['sticky_phone_button_custom_id'])
        ? $options['sticky_phone_button_custom_id']
        : 'sticky-phone-button';
    $cta_text = isset($options['sticky_phone_button_cta_text']) ? $options['sticky_phone_button_cta_text'] : '';
    $cta_text_color = isset($options['sticky_phone_button_cta_text_color']) ? $options['sticky_phone_button_cta_text_color'] : '#000000';
    $font_weight = isset($options['sticky_phone_button_font_weight']) ? $options['sticky_phone_button_font_weight'] : 'normal';
    $target = isset($options['sticky_phone_button_target']) ? $options['sticky_phone_button_target'] : '_self';
    $icon_name = isset($options['sticky_phone_button_icon_name']) ? $options['sticky_phone_button_icon_name'] : 'call';

    // Przygotuj odpowiedni format linku
    $href = '';
    switch ($link_type) {
        case 'tel':
            $href = 'tel:' . esc_attr($link_value);
            break;
        case 'mailto':
            $href = 'mailto:' . esc_attr($link_value);
            break;
        case 'sms':
            $href = 'sms:' . esc_attr($link_value);
            break;
        case 'url':
            // Sprawdź, czy adres URL ma prefiks protokołu
            $href = esc_attr($link_value);
            break;
        default:
            $href = esc_attr($link_type) . ':' . esc_attr($link_value);
    }

    // Zmień atrybuty aby ukryć przycisk domyślnie, JS go pokaże jeśli trzeba
    $button_type = '';
    $is_right_position = false;

    if (strpos($position, 'left') !== false) {
        $button_type = 'sticky-left';
    } else if (strpos($position, 'right') !== false) {
        $button_type = 'sticky-right';
        $is_right_position = true;
    } else {
        $button_type = '';  // centered position
    }

    echo '<div class="sticky-cta-container container-' . esc_attr($position) . '" style="display: none; visibility: hidden;">';

    // Dodaj klasę force-display, jeśli tryb wymuszony jest aktywny
    $force_class = '';
    if (isset($options['force_display']) && $options['force_display']) {
        $force_class = ' force-display';
    }

    echo '<a href="' . $href . '" id="' . esc_attr($custom_id) . '" data-default-id="sticky-phone-button" class="sticky-phone-button ' . esc_attr($button_type) . ' ' . esc_attr($display_device) . ' ' . esc_attr($custom_class) . $force_class . '" style="display: flex !important; visibility: visible !important; opacity: 0; background-color: ' . esc_attr($background_color) . ';" target="' . esc_attr($target) . '">';

    // Jeśli pozycja jest po prawej stronie, najpierw wyświetl tekst CTA, a potem ikonę
    if ($is_right_position) {
        // Wyświetlamy tekst CTA z obsługą HTML
        if (!empty($cta_text)) {
            echo '<span class="cta-text" style="color: ' . esc_attr($cta_text_color) . '; font-weight: ' . esc_attr($font_weight) . ';">'
                . $cta_text
                . '</span>';
        }

        echo '<span class="button-icon">
            ' . sticky_phone_button_get_material_icon($icon_name, array('size' => '24px', 'fill' => '0', 'weight' => '400', 'grade' => '0')) . '
        </span>';
    } else {
        // Dla lewej i środkowej pozycji, najpierw ikona, potem tekst (standardowo)
        echo '<span class="button-icon">
            ' . sticky_phone_button_get_material_icon($icon_name, array('size' => '24px', 'fill' => '0', 'weight' => '400', 'grade' => '0')) . '
        </span>';

        // Wyświetlamy tekst CTA z obsługą HTML
        if (!empty($cta_text)) {
            echo '<span class="cta-text" style="color: ' . esc_attr($cta_text_color) . '; font-weight: ' . esc_attr($font_weight) . ';">'
                . nl2br($cta_text)  // Przywracamy nl2br do konwersji znaków nowej linii na <br>
                . '</span>';
        }
    }

    echo '</a>';
    echo '</div>';
}

// Zmiana priorytetu wyświetlania na najwyższy i wyłączenie cache
remove_action('wp_footer', 'sticky_phone_button_html');
add_action('wp_footer', 'sticky_phone_button_html', 0);

// Dołączamy plik z animacją
require_once (plugin_dir_path(__FILE__) . 'animation.php');
