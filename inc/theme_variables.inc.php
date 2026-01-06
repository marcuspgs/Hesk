<?php
/**
 *
 * This file is part of HESK - PHP Help Desk Software.
 *
 * (c) Copyright Klemen Stirn. All rights reserved.
 * https://www.hesk.com
 *
 * For the full copyright and license agreement information visit
 * https://www.hesk.com/eula.php
 *
 */

/* Check if this is a valid include */
if (!defined('IN_SCRIPT')) {die('Invalid attempt');}

/*
 * variable - name of the CSS variable
 * derivative - whether this color is a derivative of another color (i.e. via the use of var() or color-mix())
 */
$theme_color_settings = array(
    array('variable' => '--yellow-1', 'derivative' => false),
    array('variable' => '--yellow-2', 'derivative' => false),
    array('variable' => '--green-1', 'derivative' => false),
    array('variable' => '--red-1', 'derivative' => false),
    array('variable' => '--red-2', 'derivative' => false),

    array('variable' => '--success', 'derivative' => true),
    array('variable' => '--success-2', 'derivative' => false),
    array('variable' => '--error', 'derivative' => true),
    array('variable' => '--error-2', 'derivative' => false),
    array('variable' => '--error-3', 'derivative' => false),
    array('variable' => '--warning', 'derivative' => true),
    array('variable' => '--warning-2', 'derivative' => false),
    array('variable' => '--info', 'derivative' => false),
    array('variable' => '--info-2', 'derivative' => false),
    array('variable' => '--info-3', 'derivative' => false), // TODO sort of derivative, but from one of default color vars
    array('variable' => '--notification__clr', 'derivative' => true),

    array('variable' => '--primary', 'derivative' => false),
    array('variable' => '--secondary', 'derivative' => false),
    array('variable' => '--tertiary', 'derivative' => false),
    array('variable' => '--surface', 'derivative' => false),

    array('variable' => '--main-background', 'derivative' => false),
    array('variable' => '--font__pri-clr', 'derivative' => false),
    array('variable' => '--font__sec-clr', 'derivative' => false),

    array('variable' => '--header__bg', 'derivative' => true),
    array('variable' => '--header_logo__clr', 'derivative' => true),
    array('variable' => '--header_nav__clr', 'derivative' => true),
    array('variable' => '--header_nav__hover_clr', 'derivative' => true),
    array('variable' => '--input-bg', 'derivative' => false),
    array('variable' => '--input-clr', 'derivative' => false),
    array('variable' => '--link__pri-clr', 'derivative' => true),
    array('variable' => '--link__pri-hover-clr', 'derivative' => true),
    array('variable' => '--link__sec-clr', 'derivative' => true),
    array('variable' => '--link__sec-hover-clr', 'derivative' => true),
    array('variable' => '--footer__link-clr', 'derivative' => true),
    array('variable' => '--radio__bg', 'derivative' => true),
    array('variable' => '--radio__fill-clr', 'derivative' => true),
    array('variable' => '--radio__hover-bg', 'derivative' => true),
    array('variable' => '--radio__hover-fill-clr', 'derivative' => true),
    array('variable' => '--checkbox__bg', 'derivative' => true),
    array('variable' => '--checkbox__hover-bg', 'derivative' => true),

    // Navigation section
    array('variable' => '--breadcrumbs__a-clr', 'derivative' => true),
    array('variable' => '--breadcrumbs__a-hover-clr', 'derivative' => true),
    array('variable' => '--header_profile__clr', 'derivative' => true),
    array('variable' => '--header_profile__menu-bg', 'derivative' => true),
    array('variable' => '--header_profile__user-avatar-bg', 'derivative' => true),
    array('variable' => '--header_profile__mobile-user-avatar-bg', 'derivative' => true),
    array('variable' => '--navlink__bg', 'derivative' => true),
    array('variable' => '--navlink__clr', 'derivative' => true),
    array('variable' => '--navlink__hover-bg', 'derivative' => true),
    array('variable' => '--navlink__title-clr', 'derivative' => true),
    array('variable' => '--step_bar__item-clr', 'derivative' => true),

    // Articles/Previews
    array('variable' => '--preview__bg', 'derivative' => true),
    array('variable' => '--preview__border-clr', 'derivative' => true),
    array('variable' => '--preview__hover-bg', 'derivative' => true),
    array('variable' => '--preview__hover-icon-fill', 'derivative' => true),
    array('variable' => '--preview__title-clr', 'derivative' => true),
    array('variable' => '--suggest_preview__bg', 'derivative' => true),
    array('variable' => '--suggest_preview__clr', 'derivative' => true),
    array('variable' => '--suggest_preview__border-clr', 'derivative' => true),
    array('variable' => '--suggest_preview__hover-bg', 'derivative' => true),
    array('variable' => '--suggest_preview__hover-icon-fill', 'derivative' => true),
    array('variable' => '--suggest_preview__title-clr', 'derivative' => true),
    array('variable' => '--article_preview__hover-clr', 'derivative' => true),
    array('variable' => '--article_preview_suggestion_clr', 'derivative' => true),

    // Misc
    array('variable' => '--ticket_body__bg', 'derivative' => true),
    array('variable' => '--ticket_response__bg', 'derivative' => true),
    array('variable' => '--table_row__bg', 'derivative' => true),
    array('variable' => '--table_row__bg-even', 'derivative' => false), // TODO potentially change back later
    array('variable' => '--table_row__bg-hover', 'derivative' => false), // TODO sort of derivative, but from one of default color vars

    array('variable' => '--search__clr', 'derivative' => true),
    array('variable' => '--search__bg', 'derivative' => true),
    array('variable' => '--search__title-clr', 'derivative' => true),
    array('variable' => '--search__input-placeholder-clr', 'derivative' => true),
    array('variable' => '--search__icon-fill', 'derivative' => true),
    array('variable' => '--search__input-bg', 'derivative' => true),
    array('variable' => '--modal_body__bg', 'derivative' => true),
    array('variable' => '--btn__bg-clr-pri', 'derivative' => true),
    array('variable' => '--btn__clr-pri', 'derivative' => true),
    array('variable' => '--btn__border-clr-pri', 'derivative' => true),
    array('variable' => '--btn__disabled-bg-clr', 'derivative' => false), // TODO sort of derivative, but from one of default color vars
    array('variable' => '--btn__disabled-clr', 'derivative' => false), // TODO sort of derivative, but from one of default color vars
    array('variable' => '--dropdown__bg', 'derivative' => true),
    array('variable' => '--dropdown__clr', 'derivative' => true),
    array('variable' => '--dropdown__border-clr', 'derivative' => true),
    array('variable' => '--dropdown_ver1__border-clr', 'derivative' => false), // TODO sort of derivative, but from one of default color vars
    array('variable' => '--dropdown_ver2__border-clr', 'derivative' => true),
    array('variable' => '--dropdown__hover-bg', 'derivative' => true),
    array('variable' => '--dropdown__hover-clr', 'derivative' => true),
    array('variable' => '--dropdown__hover-border-clr', 'derivative' => true),
    array('variable' => '--dropdown_ver2__hover-border-clr', 'derivative' => true),
    array('variable' => '--datepicker_btn__bg', 'derivative' => true),
    array('variable' => '--datepicker_btn__clr', 'derivative' => true),
    array('variable' => '--datepicker__bg', 'derivative' => true),
    array('variable' => '--datepicker__clr', 'derivative' => true),
);

// Helper var that helps order the various settings into manageable/clear groups
/* Rough example of current variable counts:
Let's count the variables in theme_color_settings_groups:
1. main_brand: 7
2. main_elements: 17
3. buttons: 5
4. navigation: 11
5. dropdowns: 9
6. datepickers: 4
7. base_colors: 5
8. notifications: 11
9. search: 6
10. article_previews: 13
11. misc: 6
*/
$theme_color_settings_groups = array(
    'main_brand' => array('--primary', '--secondary', '--tertiary', '--surface', '--main-background', '--font__pri-clr', '--font__sec-clr'),
    'main_elements' => array('--header__bg', '--header_logo__clr', '--header_nav__clr', '--header_nav__hover_clr', '--input-bg', '--input-clr', '--link__pri-clr', '--link__pri-hover-clr', '--link__sec-clr', '--link__sec-hover-clr', '--footer__link-clr', '--radio__bg', '--radio__fill-clr', '--radio__hover-bg', '--radio__hover-fill-clr', '--checkbox__bg', '--checkbox__hover-bg' ),
    'buttons' => array('--btn__bg-clr-pri', '--btn__clr-pri', '--btn__border-clr-pri', '--btn__disabled-bg-clr', '--btn__disabled-clr'),
    'navigation' => array('--breadcrumbs__a-clr', '--breadcrumbs__a-hover-clr', '--header_profile__clr', '--header_profile__menu-bg', '--header_profile__user-avatar-bg', '--header_profile__mobile-user-avatar-bg', '--navlink__bg', '--navlink__clr', '--navlink__hover-bg', '--navlink__title-clr', '--step_bar__item-clr'),
    'dropdowns' => array('--dropdown__bg', '--dropdown__clr', '--dropdown__border-clr', '--dropdown_ver1__border-clr', '--dropdown_ver2__border-clr', '--dropdown__hover-bg', '--dropdown__hover-clr', '--dropdown__hover-border-clr', '--dropdown_ver2__hover-border-clr'),
    'datepickers' => array('--datepicker_btn__bg', '--datepicker_btn__clr', '--datepicker__bg', '--datepicker__clr'),
    'base_colors' => array('--yellow-1', '--yellow-2', '--green-1', '--red-1', '--red-2'),
    'notifications' => array('--success', '--success-2', '--error', '--error-2', '--error-3', '--warning', '--warning-2', '--info', '--info-2', '--info-3', '--notification__clr'),
    'search' => array('--search__clr', '--search__bg', '--search__title-clr', '--search__input-placeholder-clr', '--search__icon-fill', '--search__input-bg'),
    'article_previews' => array('--preview__bg', '--preview__border-clr', '--preview__hover-bg', '--preview__hover-icon-fill', '--preview__title-clr', '--suggest_preview__bg', '--suggest_preview__clr', '--suggest_preview__border-clr', '--suggest_preview__hover-bg', '--suggest_preview__hover-icon-fill', '--suggest_preview__title-clr', '--article_preview__hover-clr', '--article_preview_suggestion_clr'),
    'misc' => array('--ticket_body__bg', '--ticket_response__bg', '--table_row__bg', '--table_row__bg-even', '--table_row__bg-hover', '--modal_body__bg'),
);

function get_theme_color_setting($css_variable_key) {
    global $theme_color_settings;
    $color_setting = array_filter($theme_color_settings, function($setting) use ($css_variable_key) {
        return $setting['variable'] === $css_variable_key;
    });
    $color_setting = reset($color_setting);
    return $color_setting;
}

function hesk_is_valid_color_hex($hex)
{
    $hex = strtolower($hex);
    return preg_match('/^\#[a-f0-9]{6}$/', $hex) ? true : false;
} // END hesk_is_valid_color_hex()