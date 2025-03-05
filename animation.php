<?php

/**
 * Animacja pulsowania przycisku CTA.
 * Przycisk będzie pulsował, zmieniając rozmiar i przezroczystość, z pauzą między pulsowaniami.
 */
function sticky_phone_button_animation_css()
{
    $options = sticky_phone_button_get_settings();
    $blink_time_ms = isset($options['sticky_phone_button_blink_time']) ? $options['sticky_phone_button_blink_time'] : '4000';

    // Konwersja z milisekund na sekundy dla CSS
    $blink_time_sec = $blink_time_ms / 1000;

?> <style>
        @keyframes pulse {
            0%, 100% {
                transform: scale(1);
                opacity: 1;
            }
            10% {
                transform: scale(1.3);
                opacity: 1;
            }
            20% {
                transform: scale(1);
                opacity: 1;
            }
            /* Od 20% do 100% przycisk pozostaje bez zmian - pauza między pulsowaniami */
        }
        
        /* Nowa klasa do aktywacji animacji - nie będzie stosowana automatycznie */
        .animate-pulse {
            animation: pulse <?php echo esc_attr($blink_time_sec); ?>s infinite;
        }
    </style> <?php
}

add_action('wp_head', 'sticky_phone_button_animation_css');
