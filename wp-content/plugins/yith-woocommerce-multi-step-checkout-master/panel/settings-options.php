<?php
/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

return array(

    'settings' => apply_filters( 'yith_wcms_settings_options', array(

            'settings_options_start'    => array(
                'type' => 'sectionstart',
                'id'   => 'yith_wcms_settings_options_start'
            ),

            'settings_options_title'    => array(
                'title' => _x( 'General settings', 'Panel: page title', 'yith_wcms' ),
                'type'  => 'title',
                'desc'  => '',
                'id'    => 'yith_wcms_settings_options_title'
            ),

            'settings_enable_multistep' => array(
                'title'   => _x( 'Enable Multi-step Checkout', 'Admin option: Enable plugin', 'yith_wcms' ),
                'type'    => 'checkbox',
                'desc'    => _x( 'Check this option to enable plugin features', 'Admin option description: Enable plugin', 'yith_wcms' ),
                'id'      => 'yith_wcms_enable_multistep',
                'default' => 'yes'
            ),

            'settings_options_end'      => array(
                'type' => 'sectionend',
                'id'   => 'yith_wcms_settings_options_end'
            ),
        )
    )
);