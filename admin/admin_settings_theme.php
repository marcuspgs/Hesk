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

define('IN_SCRIPT',1);
define('HESK_PATH','../');

define('LOAD_TABS',1);

define('LOAD_CUSTOMER_THEME_VARS',1); // Need this here to show off calculated colors as examples and make them work properly

// Make sure the install folder is deleted
if (is_dir(HESK_PATH . 'install')) {die('Please delete the <b>install</b> folder from your server for security reasons then refresh this page!');}

// Get all the required files and functions
require(HESK_PATH . 'hesk_settings.inc.php');

// Save the default language for the settings page before choosing user's preferred one
$hesk_settings['language_default'] = $hesk_settings['language'];
require(HESK_PATH . 'inc/common.inc.php');
require(HESK_PATH . 'inc/admin_functions.inc.php');
require(HESK_PATH . 'inc/setup_functions.inc.php');
hesk_load_database_functions();

hesk_session_start();
hesk_dbConnect();
hesk_isLoggedIn();

// Check permissions for this feature
hesk_checkPermission('can_man_settings');

// Load custom fields
require_once(HESK_PATH . 'inc/custom_fields.inc.php');

// Test languages function
if (isset($_GET['test_themes'])) {
    hesk_testTheme(0);
}
$help_folder = '../language/' . $hesk_settings['languages'][$hesk_settings['language']]['folder'] . '/help_files/';

$enable_save_settings   = 0;
$enable_use_attachments = 0;

// prefix to be used when referencing lang files and hesk settings
$php_css_var_prefix = 'theme_var';
require_once(HESK_PATH . 'inc/theme_variables.inc.php');
global $theme_color_settings_groups;

// Print header
require_once(HESK_PATH . 'inc/header.inc.php');

// Print main manage users page
require_once(HESK_PATH . 'inc/show_admin_nav.inc.php');

// Demo mode? Hide values of sensitive settings
if ( defined('HESK_DEMO') )
{
    require_once(HESK_PATH . 'inc/admin_settings_demo.inc.php');
}

/* This will handle error, success and notice messages */
hesk_handle_messages();
?>
<div class="main__content settings admin_settings_theme">

    <?php require_once(HESK_PATH . 'inc/admin_settings_status.inc.php'); ?>

    <script language="javascript" type="text/javascript"><!--
        function hesk_checkFields() {
            var d = document.form1;

            // DISABLE SUBMIT BUTTON
            d.submitbutton.disabled=true;

            return true;
        }

        function hesk_toggleLayer(nr,setto) {
            if (document.all)
                document.all[nr].style.display = setto;
            else if (document.getElementById)
                document.getElementById(nr).style.display = setto;
        }

        function hesk_testTheme()
        {
            window.open('admin_settings_theme.php?test_themes=1',"Hesk_window","height=400,width=500,menubar=0,location=0,toolbar=0,status=0,resizable=1,scrollbars=1");
            return false;
        }
        //-->
    </script>
    <form method="post" action="admin_settings_save.php" name="form1" onsubmit="return hesk_checkFields()">
        <div class="settings__form form" data-expanded="false">
            <section class="settings__form_block">
                <h3><?php echo $hesklang['laf']; ?></h3>

                <div role="alert" class="theme-changed-note notification blue added-left-offset">
                    <b><?php echo $hesklang['note']; ?>:</b> <?php echo $hesklang['theme_colors_note_co']; ?>
                </div>
                <?php if ( ! file_exists(HESK_PATH . 'disable_custom_html_ui.txt')): ?>
                <div class="form-group flex-row">
                    <label>
                        <span><?php echo $hesklang['custom_html']; ?></span>
                        <a onclick="hesk_window('<?php echo $help_folder; ?>theme.html#2','400','500')">
                            <div class="tooltype right">
                                <svg class="icon icon-info">
                                    <use xlink:href="<?php echo HESK_PATH; ?>img/sprite.svg#icon-info"></use>
                                </svg>
                            </div>
                        </a>
                    </label>
                    <a href="admin_settings_custom_html.php" class="btn btn--blue-border" ripple="ripple" style="margin-left: 24px"><?php echo $hesklang['custom_html_link']; ?></a>
                </div>
                <?php endif; ?>
                <div class="form-group flex-row">
                    <label>
                        <span><?php echo $hesklang['customer_theme']; ?></span>
                        <a onclick="hesk_window('<?php echo $help_folder; ?>theme.html#1','400','500')">
                            <div class="tooltype right">
                                <svg class="icon icon-info">
                                    <use xlink:href="<?php echo HESK_PATH; ?>img/sprite.svg#icon-info"></use>
                                </svg>
                            </div>
                        </a>
                    </label>
                    <div class="dropdown-select center out-close" style="background-color: #fff;">
                        <select name="s_customer_theme" onchange="hesk_customerThemeChanged(this.value)">
                            <?php echo hesk_displayCustomerThemes(1); ?>
                        </select>
                    </div>
                </div>
                <div role="alert" class="theme-changed-note notification blue added-left-offset d_hide">
                    <b><?php echo $hesklang['note']; ?>:</b> <?php echo $hesklang['theme_changed_note']; ?>
                </div>
            </section>
            <section class="settings__form_block theme-overrides empty-section">
                <h3><?php echo $hesklang['theme_overrides']; ?>
                    <a onclick="hesk_window('<?php echo $help_folder; ?>theme.html#2','400','500')">
                        <div class="tooltype right">
                            <svg class="icon icon-info">
                                <use xlink:href="<?php echo HESK_PATH; ?>img/sprite.svg#icon-info"></use>
                            </svg>
                        </div>
                    </a>
                </h3>
                <div role="alert" class="notification blue added-left-offset">
                    <?php echo $hesklang['theme_colors_note']; ?>
                </div>
                <button type="button" class="btn btn--blue-border reset-color" style="margin-left: 20px" ripple="ripple"
                        onclick="return hesk_resetAllThemeOverrides()">
                    <?php echo $hesklang['reset_all_overrides']; ?>
                </button>
            </section>
            <?php foreach ($theme_color_settings_groups as $group_key => $group_variables): ?>
                <section class="settings__form_block theme-overrides theme-group-<?php echo $group_key; ?>">
                    <h3><?php echo $hesklang['theme_group_' . $group_key]; ?><a onclick="hesk_window('<?php echo $help_folder; ?>theme.html#<?php echo $group_key;?>','400','500')">
                            <div class="tooltype right">
                                <svg class="icon icon-info">
                                    <use xlink:href="<?php echo HESK_PATH; ?>img/sprite.svg#icon-info"></use>
                                </svg>
                            </div>
                        </a>
                    </h3>
                    <?php
                    foreach ($group_variables as $css_variable) {
                        $color_setting = get_theme_color_setting($css_variable);
                        if (!$color_setting) {
                            continue;
                        }

                        $hesk_sett_value = isset($hesk_settings['theme_overrides'][$css_variable]) ? $hesk_settings['theme_overrides'][$css_variable] : '';
                        ?>
                        <div class="form-group row flex-row color">
                            <label>
                                <span><?php echo ($color_setting['derivative'] ? '<span class="left-asterix"></span>' : ''); ?><?php echo $hesklang[$php_css_var_prefix . $css_variable]; ?></span>
                            </label>
                            <?php $color = hesk_validate_color_hex(isset($hesk_sett_value) ? $hesk_sett_value : ''); ?>
                            <input type="text" class="form-control jscolor {hash:true, uppercase:false, onFineChange:'hesk_preview_color(this, \'<?php echo $css_variable; ?>\', \'<?php echo $php_css_var_prefix ?>\')'}" name="<?php echo $php_css_var_prefix . $css_variable; ?>">
                            <span id="<?php echo $php_css_var_prefix . $css_variable; ?>_preview" style="color:<?php echo $color; ?>"><?php echo $hesklang['clr_view']; ?></span>
                            <button type="button" class="btn btn--blue-border reset-color" style="margin-left: 20px" ripple="ripple"
                                    onclick="return hesk_resetThemeColor('<?php echo $css_variable; ?>')">
                                <?php echo $hesklang['reset']; ?>
                            </button>
                        </div>
                    <?php } ?>
                    <!-- We use a hidden input field, to which we only store any overwritten values, which are then posted on save. -->

                    <!-- After main_brand, we also print a view/hide advanced settings-->
                    <?php if ($group_key === 'main_brand') { ?>
                        <button type="button" class="btn btn--blue-border toggle-advanced-settings" style="margin-left: 20px" ripple="ripple"
                                onclick="return hesk_toggleAdvancedSettings(this)" data-expanded="false">
                        <span data-type="advanced">
                            <?php echo $hesklang['view_advanced']; ?>
                            <svg class="icon icon-chevron-down">
                                <use xlink:href="<?php echo HESK_PATH; ?>img/sprite.svg#icon-chevron-down"></use>
                            </svg>
                        </span>
                            <span data-type="basic">
                            <?php echo $hesklang['view_basic']; ?>
                            <svg class="icon icon-chevron-up">
                                <use xlink:href="<?php echo HESK_PATH; ?>img/sprite.svg#icon-chevron-down"></use>
                            </svg>
                        </span>
                        </button>
                    <?php } ?>
                </section>
            <?php endforeach; ?>

            <div class="settings__form_submit">
                <input type="hidden" name="s_theme_overrides" id="s_theme_overrides" value="">
                <input type="hidden" name="token" value="<?php hesk_token_echo(); ?>">
                <input type="hidden" name="section" value="THEME">
                <button id="submitbutton" style="display: inline-flex" type="submit" class="btn btn-full" ripple="ripple"
                    <?php echo $enable_save_settings ? '' : 'disabled'; ?>>
                    <?php echo $hesklang['save_changes']; ?>
                </button>

                <?php if (!$enable_save_settings): ?>
                    <p>&nbsp;</p>
                    <div role="alert" class="notification red">
                        <?php echo $hesklang['e_save_settings']; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </form>
</div>

<script type="text/javascript" src="<?php echo HESK_PATH; ?>inc/jscolor/jscolor.min.js"></script>
<script type="text/javascript">

    // Storing any used DOM refs to avoid re-querying the DOM for the same elements.
    // For performance/lag/stutter considerations, which might otherwise happens with 100s of refs being updated on color changes
    let cachedDomRefs = {};
    function getCachedDomRef(selector, useJquery = false, queryAll = false) {
        // just adding option for jQuery and regular, depending on implementation
        let cacheRef = (useJquery? "jq_" : "") + selector; // need to store them separately, as they might be used in both ways
        if (/*true ||*/ !cachedDomRefs[cacheRef]) {
            let domRef;
            if (useJquery) {
                domRef = $(selector);
            } else {
                if (queryAll) {
                    domRef = document.querySelectorAll(selector);
                } else {
                    domRef = document.querySelector(selector);
                }
            }
            if (domRef && (!useJquery || domRef.length > 0)) {
                // only store cache if the domElement exists (if jQuery selecting, also check for length > 0!)
                cachedDomRefs[cacheRef] = domRef;
            } else {
                // if not caching, still return the result, so any chaining in case of jQuery will still work properly
                return domRef;
            }
        }
        return cachedDomRefs[cacheRef];
    }

    function clearCachedDomRef(selector, useJquery = false, queryAll = false) {
        let cacheRef = (useJquery? "jq_" : "") + selector;
        if (cachedDomRefs[cacheRef]) {
            delete cachedDomRefs[cacheRef];
        }
    }

    function hesk_customerThemeChanged(themeName) {
        const templatePath = "<?php echo TEMPLATE_PATH; ?>";

        let themeLink = getCachedDomRef("#loaded_theme");
        if (!themeLink) {
            // themeLink not existing yet (i.e. happens if no specific theme is loaded)

            const defaultThemeLink = getCachedDomRef("#default_theme");
            if (!defaultThemeLink) {
                console.error("Default theme element not found at all, can't really load themes anyway!");
                return;
            }

            // If themeLink does not already exist, then create a new link element to load the loaded theme into it, after the defaultThemeLink element
            themeLink = document.createElement("link");
            themeLink.rel = "stylesheet";
            themeLink.type = "text/css";
            themeLink.id = "loaded_theme";

            // append newThemeLink after defaultThemeLink
            defaultThemeLink.insertAdjacentElement('afterend', themeLink);
        }

        if (themeName === '') {
            // If themeName is empty, then remove the loaded theme link
            themeLink.remove();

            // Need to clear cached ref, as otherwise on next theme select it won't be added properly
            clearCachedDomRef("#loaded_theme");

            // Finally, we have to recalculate all the input colors
            recalculateInputColors();
            attemptToggleChangedThemeNote();
            return;
        }

        // Construct the new href (adjust path if needed)
        const newHref = templatePath + 'customer/css/themes/' + themeName + '.css';

        // Set the new href to load the new theme
        themeLink.setAttribute("href", newHref);

        // wait for new CSS to load completely before we update the color preview
        themeLink.onload = function() {
            // Finally, we have to recalculate all the input colors
            recalculateInputColors();
            attemptToggleChangedThemeNote();
        };
    }

    function attemptToggleChangedThemeNote() {
        let $changedThemeNote = getCachedDomRef('.theme-changed-note', true);

        let showChangedThemeNote = Object.keys(themeOverrides).length > 0;
        $changedThemeNote.toggleClass('d_hide', !showChangedThemeNote);
    }

    function hesk_toggleAdvancedSettings(element) {
        element.setAttribute('data-expanded', element.dataset.expanded === 'false'? 'true' : 'false');

        // Get closest form, and add data-expanded attribute to the form
        const form = element.closest('.settings__form');
        form.setAttribute('data-expanded', element.dataset.expanded);
    }

    // Initialize the hidden field with an empty object
    // On any theme color changes, we will store them here, as this way we can identify if a setting has been overriden.
    // Note: we can directly use the variables HEX values, as they might just be dynamically calculated from other colors.
    // So we have to listen/check for these changes specifically.
    let themeOverrides = {};

    function updateThemeOverrides() {
        getCachedDomRef('#s_theme_overrides').value = JSON.stringify(themeOverrides);
    }
    function setInputOverrideClass(cssVariable, isOverridden = false) {
        const $input = getCachedDomRef(`input[name="<?php echo $php_css_var_prefix ?>${cssVariable}"]`, true);
        $input.closest('.form-group').toggleClass('is-overridden', isOverridden);
    }
    function setColorOverride(cssVariable, color) {
        themeOverrides[cssVariable] = color;
        updateThemeOverrides();
        setInputOverrideClass(cssVariable, true);
    }
    function removeColorOverride(cssVariable) {
        delete themeOverrides[cssVariable];
        document.documentElement.style.removeProperty(cssVariable);
        updateThemeOverrides();
        setInputOverrideClass(cssVariable, false);

        // If there was a note shown for overrides, we can also attempt to hide it now.
        attemptToggleChangedThemeNote();
    }
    function handleColorChange(color, cssVariable) {
        if (color !== '') {
            setColorOverride(cssVariable, color);
        } else {
            removeColorOverride(cssVariable);
        }
    }

    function hesk_resetThemeColor(cssVariable) {
        const input = getCachedDomRef(`input[name="<?php echo $php_css_var_prefix ?>${cssVariable}"]`);
        input.jscolor.fromString('');
        removeColorOverride(cssVariable);
        // While we could just recalculate for the specific variable here, it's best to just do all,
        // as if this color was part of calculations ofr other colors, others still need ot be recalculated as well
        recalculateInputColors();
        return false;
    }

    function hesk_resetAllThemeOverrides() {
        for (let cssVariable in themeOverrides) {
            hesk_resetThemeColor(cssVariable);
        }
    }

    // Updated preview color fro mGPT: It's similar, just with some extras
    function hesk_preview_color(jscolorOrString, cssVariable, idSelectorPrefix = 'theme_var', previewElementSuffix = '_preview') {
        let input, color;
        if (typeof jscolorOrString === 'object' && jscolorOrString.valueElement) {
            // It's a jscolor object
            input = jscolorOrString.valueElement;
            color = jscolorOrString.toHEXString();
        } else {
            // It's a string
            input = getCachedDomRef(`input[name="${idSelectorPrefix}${cssVariable}"]`);
            color = jscolorOrString.startsWith('#') ? jscolorOrString : "#" + jscolorOrString;
        }

        const previewElement = getCachedDomRef('#' + idSelectorPrefix + cssVariable + previewElementSuffix);
        previewElement.style.color = color;

        // set the :root value of the css variable
        document.documentElement.style.setProperty(cssVariable, color);

        handleColorChange(color, cssVariable);
        recalculateInputColors();
    }

    document.addEventListener('DOMContentLoaded', function() {
        // The jscolor fields are not automatically updated on render (as PHP does NOT have css color calculations yet), so on page load, we have to check for color values and update the jscolor fields with the computed values
        recalculateInputColors();

        <?php
        foreach($hesk_settings['theme_overrides'] as $css_variable => $color) {
            ?>
            var input = getCachedDomRef(`input[name="<?php echo $php_css_var_prefix . $css_variable?>"]`)
            hesk_preview_color('<?php echo $color; ?>', '<?php echo $css_variable; ?>');

        <?php }
        ?>
    });
    function recalculateInputColors() {
        var colorInputs = getCachedDomRef('.form-control.jscolor', false, true);

        colorInputs.forEach(function(input) {
            var cssVariable = input.name.replace('<?php echo $php_css_var_prefix; ?>', '');
            var computedColor = getComputedStyle(document.documentElement).getPropertyValue(cssVariable).trim();

            if (computedColor) {
                // Remove the leading '#' if present
                computedColor = computedColor.charAt(0) === '#' ? computedColor.slice(1) : computedColor;

                // Set the jscolor value
                var jscolor = input.jscolor;
                jscolor.fromString(computedColor);

                // Update the preview span
                var previewSpan = getCachedDomRef('#' + input.name + '_preview');
                if (previewSpan) {
                    previewSpan.style.color = '#' + computedColor;
                }
            }
        });
    }
</script>
<script src="<?php echo HESK_PATH; ?>js/jquery-ui.js?<?php echo $hesk_settings['hesk_version']; ?>"></script>
<?php
require_once(HESK_PATH . 'inc/footer.inc.php');
exit();


function hesk_displayCustomerThemes() {
    global $hesk_settings, $hesklang;

    $path = HESK_PATH . 'theme/' . $hesk_settings['site_theme'] . '/customer/css/themes/';

    $html = '';
    $html .= '<option value="" ' . (!isset($hesk_settings['customer_theme']) || $hesk_settings['customer_theme'] === ''  ? 'selected' : '') . '>' . $hesklang['theme_name_default'] . '</option>';

    $themeCssFiles = glob($path.'*.css');
    if (is_array($themeCssFiles)) {
        foreach ($themeCssFiles as $file) {
            $filename = pathinfo($file, PATHINFO_FILENAME); // Get filename without extension
            $formattedText = ucwords(str_replace('_', ' ', $filename)); // Replace underscores & capitalize words
            if (!empty($hesklang['theme_name_' . $filename])) {
                $formattedText = $hesklang['theme_name_' . $filename];
            }
            $html .= '<option value="' . $filename . '" ' . ($hesk_settings['customer_theme'] === $filename ? 'selected' : '') . '>' . $formattedText . '</option>';
        }
    }

    return $html;
}


function hesk_validate_color_hex($hex, $def = '#000000')
{
    $hex = strtolower($hex);
    return preg_match('/^\#[a-f0-9]{6}$/', $hex) ? $hex : $def;
} // END hesk_validate_color_hex()


function hesk_get_text_color($bg_color)
{
    // Get RGB values
    list($r, $g, $b) = sscanf($bg_color, "#%02x%02x%02x");

    // Is Black a good text color?
    if (hesk_color_diff($r, $g, $b, 0, 0, 0) >= 500)
    {
        return '#000000';
    }

    // Use white instead
    return '#ffffff';
} // END hesk_get_text_color()


function hesk_color_diff($R1,$G1,$B1,$R2,$G2,$B2)
{
    return max($R1,$R2) - min($R1,$R2) +
        max($G1,$G2) - min($G1,$G2) +
        max($B1,$B2) - min($B1,$B2);
} // END hesk_color_diff()
