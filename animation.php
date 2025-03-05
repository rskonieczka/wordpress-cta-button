<?php

/**
 * CTA button pulsing animation.
 * The button will pulse, changing size and opacity, with a pause between pulses.
 */
function sticky_phone_button_animation_css()
{
    $options = sticky_phone_button_get_settings();
    $blink_time_ms = isset($options['sticky_phone_button_blink_time']) ? $options['sticky_phone_button_blink_time'] : '4000';

    // Convert from milliseconds to seconds for CSS
    $blink_time_sec = $blink_time_ms / 1000;

?> <style>
        @keyframes pulse {
            0%, 100% {
                transform: scale(1);
                opacity: 1;
            }
            10% {
                transform: scale(1.05);
                opacity: 1;
            }
            20% {
                transform: scale(1);
                opacity: 1;
            }
            /* From 20% to 100% the button remains unchanged - pause between pulses */
        }
        
        /* New class for animation activation - will not be applied automatically */
        .animate-pulse {
            animation: pulse <?php echo esc_attr($blink_time_sec); ?>s infinite;
        }
    </style> <?php
}

add_action('wp_head', 'sticky_phone_button_animation_css');
