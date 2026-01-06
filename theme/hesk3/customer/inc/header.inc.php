<?php
global $hesk_settings, $hesklang;
/**
 * header.inc.php takes care of loading everything from
 * <DOCTYPE>, head, body,... all the way to the start of <main> element.
 *
 * USAGE: Wherever header.inc.php is loaded, after the custom page content is printed,
 * the footer.inc.php should be loaded after that to ensure the DOM structure is properly closed.
 */

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
if (!defined('IN_SCRIPT')) {
    die();
}

// Load any commonly used PHP functions/includes via defined constants
if (defined('ALERTS')) {
    require_once(TEMPLATE_PATH . 'customer/util/alerts.php');
}
if (defined('CUSTOM_FIELDS')) {
    require_once(TEMPLATE_PATH . 'customer/util/custom-fields.php');
}
if (defined('ATTACHMENTS')) {
    require_once(TEMPLATE_PATH . 'customer/util/attachments.php');
}
if (defined('KBSEARCH')) {
    require_once(TEMPLATE_PATH . 'customer/util/kb-search.php');
}
if (defined('RATING')) {
    require_once(TEMPLATE_PATH . 'customer/util/rating.php');
}
if (defined('ADD_REPLY')) {
    require_once(TEMPLATE_PATH . 'customer/view-ticket/partial/add-reply.php');
}
if (defined('PRIORITIES')) {
    require_once(HESK_PATH . 'inc/priorities.inc.php');
}
if (defined('MY_TICKETS_SEARCH')) {
    require_once(TEMPLATE_PATH . 'customer/util/my-tickets-search.php');
}
if (defined('PAGER')) {
    require_once(TEMPLATE_PATH . 'customer/util/pager.php');
}

$extraHtmlClasses = '';
if (defined('EXTRA_PAGE_CLASSES')) {
    // add extra space, to make sure it doesn't concatenate with any existing classes by mistake
    $extraHtmlClasses .= ' ' . EXTRA_PAGE_CLASSES;
}
if(isset($hesk_settings['customer_theme']) && $hesk_settings['customer_theme'] !== '') {
    $extraHtmlClasses .= ' theme_'. $hesk_settings['customer_theme'];
}

$assetVersion = $hesk_settings['hesk_version'];
if ($hesk_settings['debug_mode']) {
    // Note: For some reason, even with browser force cache refresh it's not always refreshing, so this is a way to force it additionally in debug mode if necessary.
    $assetVersion .='_' . time();
}
?>
<!DOCTYPE html>
<html lang="<?php echo $hesk_settings['languages'][$hesk_settings['language']]['folder'] ?>" class="<?php echo $extraHtmlClasses; ?>">
<head>
    <meta charset="utf-8" />
    <title><?php echo (
            // TODO absolutelyRework <- does tmp_title just exist/dynamically generated? or has to be defined in script?
            defined('TMP_TITLE') ? $hesk_settings['tmp_title'] : $hesk_settings['hesk_title']
        ); ?></title>
    <meta http-equiv="X-UA-Compatible" content="IE=Edge" />
    <meta name="viewport" content="width=device-width,minimum-scale=1.0,maximum-scale=1.0" />
    <?php include(HESK_PATH . 'inc/favicon.inc.php'); ?>
    <meta name="format-detection" content="telephone=no" />

    <!-- Note: The default vars are NOT bundled, but have to be loaded first to init vars, and then get overridden by theme vars, before further color calculations are done. -->
    <link rel="stylesheet" media="all" href="<?php echo TEMPLATE_PATH; ?>customer/css/core/0_00_default_theme_vars.css?<?php echo $assetVersion; ?>" />
    <?php
    // Check if use has any special CSS themes selected, and if they exist, load their CSS.
    // NOTE! Theme vars overrides have to be loaded in first, as they are then used as part of color calculations in 0_01_variables.css file!
    $attempt_load_theme = isset($hesk_settings['customer_theme']) && $hesk_settings['customer_theme'] !== '';
    if ($attempt_load_theme) {
        $loaded_theme = $hesk_settings['customer_theme'];

        $theme_path = TEMPLATE_PATH.'customer/css/themes/'.$loaded_theme.'.css';
        if (file_exists($theme_path)) { ?>
            <link rel="stylesheet" media="all" href="<?php echo $theme_path; ?>?<?php echo $assetVersion; ?>" />
            <?php
        }
    }
    ?>

    <!-- Now load any admin/setting overrides of theme variables values
    NOTE: these should be loaded even if it's production/not debug mode, on top of app.css!!! -->
    <?php if (!empty($hesk_settings['theme_overrides']) && is_array($hesk_settings['theme_overrides']))
    {
        // prepare CSS variables from any setting overrides
        $theme_overrides_css = ":root {\n";
        foreach ($hesk_settings['theme_overrides'] as $var => $value) {
            $theme_overrides_css .= "{$var}: {$value};\n";
        }
        $theme_overrides_css .= "}\n";
        echo '<style type="text/css">' . $theme_overrides_css . '</style>';
    }
    ?>
    <link rel="stylesheet" media="all" href="<?php echo TEMPLATE_PATH; ?>customer/css/theme_overrides.css?<?php echo $assetVersion; ?>" />

    <?php if ($hesk_settings['debug_mode']): ?>
    <!--
        IMPORTANT NOTE: For any potential future CSS file additions:
        Please make sure to add name prefixes with numbers in whatever order you want them to be loaded.
        This will ensure that when they are combined and minified, any order of CSS will be properly preserved.
    -->
        <!-- TODO Could potentially load these dynamically based on all that are in /core folder??? (Make sure to exclude loading 0_00_default_theme_vars here if so!)
        -->
    <link rel="stylesheet" media="all" href="<?php echo TEMPLATE_PATH; ?>customer/css/core/0_01_variables.css?<?php echo $assetVersion; ?>" />
    <link rel="stylesheet" media="all" href="<?php echo TEMPLATE_PATH; ?>customer/css/core/0_02_font_setup.css?<?php echo $assetVersion; ?>" />
    <link rel="stylesheet" media="all" href="<?php echo TEMPLATE_PATH; ?>customer/css/core/0_03_common.css?<?php echo $assetVersion; ?>" />
    <link rel="stylesheet" media="all" href="<?php echo TEMPLATE_PATH; ?>customer/css/core/0_04_layout.css?<?php echo $assetVersion; ?>" />
    <link rel="stylesheet" media="all" href="<?php echo TEMPLATE_PATH; ?>customer/css/core/0_05_layout_components.css?<?php echo $assetVersion; ?>" />
    <link rel="stylesheet" media="all" href="<?php echo TEMPLATE_PATH; ?>customer/css/core/0_06_header.css?<?php echo $assetVersion; ?>" />
    <link rel="stylesheet" media="all" href="<?php echo TEMPLATE_PATH; ?>customer/css/core/0_07_footer.css?<?php echo $assetVersion; ?>" />
    <link rel="stylesheet" media="all" href="<?php echo TEMPLATE_PATH; ?>customer/css/core/0_08_buttons.css?<?php echo $assetVersion; ?>" />
    <link rel="stylesheet" media="all" href="<?php echo TEMPLATE_PATH; ?>customer/css/core/0_09_forms.css?<?php echo $assetVersion; ?>" />
    <link rel="stylesheet" media="all" href="<?php echo TEMPLATE_PATH; ?>customer/css/core/0_10_input.css?<?php echo $assetVersion; ?>" />
    <link rel="stylesheet" media="all" href="<?php echo TEMPLATE_PATH; ?>customer/css/core/0_11_icons.css?<?php echo $assetVersion; ?>" />
    <link rel="stylesheet" media="all" href="<?php echo TEMPLATE_PATH; ?>customer/css/core/0_12_dropdowns.css?<?php echo $assetVersion; ?>" />
    <link rel="stylesheet" media="all" href="<?php echo TEMPLATE_PATH; ?>customer/css/core/0_13_datepickers.css?<?php echo $assetVersion; ?>" />
    <link rel="stylesheet" media="all" href="<?php echo TEMPLATE_PATH; ?>customer/css/core/0_14_modal.css?<?php echo $assetVersion; ?>" />
    <link rel="stylesheet" media="all" href="<?php echo TEMPLATE_PATH; ?>customer/css/core/0_15_navigation.css?<?php echo $assetVersion; ?>" />
    <link rel="stylesheet" media="all" href="<?php echo TEMPLATE_PATH; ?>customer/css/core/0_16_tooltips.css?<?php echo $assetVersion; ?>" />
    <link rel="stylesheet" media="all" href="<?php echo TEMPLATE_PATH; ?>customer/css/core/0_17_responsive.css?<?php echo $assetVersion; ?>" />

    <link rel="stylesheet" media="all" href="<?php echo TEMPLATE_PATH; ?>customer/css/core/0_18_tickets.css?<?php echo $assetVersion; ?>" />
    <link rel="stylesheet" media="all" href="<?php echo TEMPLATE_PATH; ?>customer/css/core/0_19_kb.css?<?php echo $assetVersion; ?>" />
    <link rel="stylesheet" media="all" href="<?php echo TEMPLATE_PATH; ?>customer/css/core/0_20_pages.css?<?php echo $assetVersion; ?>" />
    <link rel="stylesheet" media="all" href="<?php echo TEMPLATE_PATH; ?>customer/css/core/0_21_popups.css?<?php echo $assetVersion; ?>" />
    <link rel="stylesheet" media="all" href="<?php echo TEMPLATE_PATH; ?>customer/css/core/0_22_misc.css?<?php echo $assetVersion; ?>" />
    <!--  It's deprecated, generally we want to uncomment this out to test if it breaks anything we assume is deprecated anyway. -->
    <link rel="stylesheet" media="all" href="<?php echo TEMPLATE_PATH; ?>customer/css/core/0_23_deprecated.css?<?php echo $assetVersion; ?>" />
    <?php else: ?>
        <link rel="stylesheet" media="all" href="<?php echo TEMPLATE_PATH; ?>customer/dist/app<?php echo $hesk_settings['debug_mode'] ? '' : '.min'; ?>.css?<?php echo $assetVersion; ?>" />
    <?php endif; ?>

    <!-- NOTE: these should be loaded even if it's production/not debug mode, on top of app.css!!! -->
    <link rel="stylesheet" media="all" href="<?php echo TEMPLATE_PATH; ?>customer/css/core_overrides.css?<?php echo $assetVersion; ?>" />

    <?php
    if (defined('LOAD_CSS_MODAL')) {
        ?>
        <link rel="stylesheet" href="<?php echo TEMPLATE_PATH; ?>customer/css/jquery.modal.css" />
    <?php
    }
    if (defined('LOAD_CSS_DROPZONE')) {
        ?>
        <link rel="stylesheet" media="all" href="<?php echo TEMPLATE_PATH; ?>customer/css/dropzone.min.css?<?php echo $assetVersion; ?>" />
    <?php
    }
    if (defined('LOAD_PRISM')) {
        ?>
        <link rel="stylesheet" media="all" href="<?php echo TEMPLATE_PATH; ?>customer/css/prism.css" />
        <script src="<?php echo TEMPLATE_PATH; ?>customer/js/prism.js"></script>
    <?php
    }
    if (defined('LOAD_PRISM_OPTIONAL') && $hesk_settings['staff_ticket_formatting'] == 2) {
        ?>
        <script type="text/javascript" src="<?php echo HESK_PATH; ?>js/prism.js?<?php echo $assetVersion; ?>"></script>
        <link rel="stylesheet" media="all" href="<?php echo HESK_PATH; ?>css/prism.css?<?php echo $assetVersion; ?>">
    <?php
    }
    if (defined('LOAD_CSS_ZEBRA')) {
        ?>
        <link rel="stylesheet" href="./css/zebra_tooltips.css">
    <?php
    }
    ?>
    <style>
        <?php
        // TODO NOTE -> all these "page specific cssess will be reworked to use a page class instead and not be hardcode printed here
        if (defined('OUTPUT_SEARCH_STYLING')) {
            // TODO potentially rework into CSS files directly
            outputSearchStyling();
        }
        if (defined('VIEW_CATEGORY_CSS')) {
            // TODO absolutelyRework CSS

            // TODO notes -> due to injecting PHP these seem to probably have to stay here
            ?>
            /* suppress CssOverwrittenProperties */
            .topics__block {
                width: <?php echo $subcategoriesWidth; ?>;
            }
            .content .block__head {
                margin-bottom: <?php echo $currentCategory['id'] != 1 ? '0' : '16px' ?>;
            }

            <?php
        }
    ?>
    </style>
    <?php include(TEMPLATE_PATH . '../../head.txt'); ?>
</head>

<body class="cust-help">
    <?php include(TEMPLATE_PATH . '../../header.txt'); ?>
    <?php
    if (defined('RENDER_COMMON_ELEMENTS')) {
        ?>
        <a href="#maincontent" class="skiplink"><?php echo $hesklang['skip_to_main_content']; ?></a>
        <?php
    }
    ?>
    <div class="wrapper">
        <main class="main" id="maincontent">
            <?php
                // Print Main Navigation
                require_once(TEMPLATE_PATH . 'customer/inc/main-nav.inc.php');

                // IMPORTANT: Wherever loading header.inc.php, make sure to close off with footer.inc.php in the end as well.
            ?>
