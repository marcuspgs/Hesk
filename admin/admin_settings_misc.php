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

// Rebuild prod assets function
if (isset($_GET['rebuild_prod_assets'])) {
    if (defined('HESK_DEMO') || ! $hesk_settings['debug_mode']) {
        hesk_exitDemo();
    } else {
        hesk_rebuildProdAssets();
    }
}

// Purge cache function
if (isset($_GET['purge_cache'])) {
    if (defined('HESK_DEMO')) {
        hesk_exitDemo();
    } else {
        hesk_purgeCache();
    }
}

$help_folder = '../language/' . $hesk_settings['languages'][$hesk_settings['language']]['folder'] . '/help_files/';

$enable_save_settings   = 0;
$enable_use_attachments = 0;

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
<div class="main__content settings">

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

        function hesk_rebuildProdAssets()
        {
            window.open('admin_settings_misc.php?rebuild_prod_assets',"Hesk_window","height=400,width=500,menubar=0,location=0,toolbar=0,status=0,resizable=1,scrollbars=1");
            return false;
        }

        function hesk_purgeCache()
        {
            window.open('admin_settings_misc.php?purge_cache',"Hesk_window","height=400,width=500,menubar=0,location=0,toolbar=0,status=0,resizable=1,scrollbars=1");
            return false;
        }
        //-->
    </script>
    <form method="post" action="admin_settings_save.php" name="form1" onsubmit="return hesk_checkFields()">
        <div class="settings__form form">
            <section class="settings__form_block">
                <h3><?php echo $hesklang['dat']; ?></h3>
                <div class="form-group timezone">
                    <label>
                        <span><?php echo $hesklang['TZ']; ?></span>
                        <a onclick="hesk_window('<?php echo $help_folder; ?>misc.html#63','400','500')">
                            <div class="tooltype right">
                                <svg class="icon icon-info">
                                    <use xlink:href="<?php echo HESK_PATH; ?>img/sprite.svg#icon-info"></use>
                                </svg>
                            </div>
                        </a>
                    </label>
                    <?php
                    // Get list of supported timezones
                    $timezone_list = hesk_generate_timezone_list();

                    // Do we need to localize month names?
                    if ($hesk_settings['language'] != 'English')
                    {
                        $timezone_list = hesk_translate_timezone_list($timezone_list);
                    }
                    ?>
                    <select name="s_timezone" id="timezone-select">
                        <?php
                        foreach ($timezone_list as $timezone => $description)
                        {
                            echo '<option value="' . $timezone . '"' . ($hesk_settings['timezone'] == $timezone ? ' selected' : '') . '>' . $description . '</option>';
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group flex-row">
                    <label>
                        <span><?php echo $hesklang['tfor']; ?></span>
                        <a onclick="hesk_window('<?php echo $help_folder; ?>misc.html#65','400','500')">
                            <div class="tooltype right">
                                <svg class="icon icon-info">
                                    <use xlink:href="<?php echo HESK_PATH; ?>img/sprite.svg#icon-info"></use>
                                </svg>
                            </div>
                        </a>
                    </label>
                    <input type="text" class="form-control" style="max-width: 200px; margin-right: 5px;" id="s_format_time" name="s_format_time" maxlength="255" value="<?php echo $hesk_settings['format_time']; ?>">
                    <select name="ex-time" id="ex-time">
                        <?php
                        $examples = array(
                            'H:i',
                            'H:i:s',
                            'g:i a',
                        );

                        $is_custom = true;
                        foreach ($examples as $example) {
                            if ($example == $hesk_settings['format_time']) {
                                $is_custom = false;
                                $selected = 'selected';
                            } else {
                                $selected = '';
                            }
                            echo '<option value="'.$example.'" '.$selected.'>'.hesk_date('now', false, true, true, $example).'</option>';
                        }
                        ?>
                        <option value="custom" <?php echo $is_custom ? 'selected' : ''; ?>><?php echo $hesklang['custom']; ?></option>
                    </select>
                    <script>
                        $('#ex-time').selectize();
                        $('#ex-time').on('change', function() {
                            if (this.value != 'custom') {
                                $('#s_format_time').val(this.value);
                            }
                        });
                    </script>
                </div>
                <div class="form-group flex-row">
                    <label>
                        <span><?php echo $hesklang['dfor']; ?></span>
                        <a onclick="hesk_window('<?php echo $help_folder; ?>misc.html#66','400','500')">
                            <div class="tooltype right">
                                <svg class="icon icon-info">
                                    <use xlink:href="<?php echo HESK_PATH; ?>img/sprite.svg#icon-info"></use>
                                </svg>
                            </div>
                        </a>
                    </label>
                    <input type="text" class="form-control" style="max-width: 200px; margin-right: 5px;" id="s_format_date" name="s_format_date" maxlength="255" value="<?php echo $hesk_settings['format_date']; ?>">
                    <select name="ex-date" id="ex-date">
                        <?php
                        $examples = array(
                            'm/d/Y',
                            'd/m/Y',
                            'm-d-Y',
                            'd-m-Y',
                            'Y-m-d',
                            'Y-d-m',
                            'd.m.Y',
                            'M j Y',
                            'j M Y',
                            'j M y',
                            'F j, Y',
                        );

                        $is_custom = true;
                        foreach ($examples as $example) {
                            if ($example == $hesk_settings['format_date']) {
                                $is_custom = false;
                                $selected = 'selected';
                            } else {
                                $selected = '';
                            }
                            echo '<option value="'.$example.'" '.$selected.'>'.hesk_date('now', false, true, true, $example).'</option>';
                        }
                        ?>
                        <option value="custom" <?php echo $is_custom ? 'selected' : ''; ?>><?php echo $hesklang['custom']; ?></option>
                    </select>
                    <script>
                        $('#ex-date').selectize();
                        $('#ex-date').on('change', function() {
                            if (this.value != 'custom') {
                                $('#s_format_date').val(this.value);
                            }
                        });
                    </script>
                </div>
                <div class="form-group flex-row">
                    <label>
                        <span><?php echo $hesklang['dtfor']; ?></span>
                        <a onclick="hesk_window('<?php echo $help_folder; ?>misc.html#67','400','500')">
                            <div class="tooltype right">
                                <svg class="icon icon-info">
                                    <use xlink:href="<?php echo HESK_PATH; ?>img/sprite.svg#icon-info"></use>
                                </svg>
                            </div>
                        </a>
                    </label>
                    <input type="text" class="form-control" style="max-width: 200px; margin-right: 5px;" id="s_format_timestamp" name="s_format_timestamp" maxlength="255" value="<?php echo $hesk_settings['format_timestamp']; ?>">
                    <select name="ex-timestamp" id="ex-timestamp">
                        <?php
                        $examples = array(
                            'm/d/Y g:i a',
                            'd/m/Y H:i:s',
                            'm-d-Y H:i:s',
                            'd-m-Y H:i:s',
                            'Y-m-d H:i:s',
                            'Y-d-m H:i:s',
                            'd.m.Y H:i:s',
                            'd M Y H:i',
                            'F jS, Y, g:i a',
                        );

                        $is_custom = true;
                        foreach ($examples as $example) {
                            if ($example == $hesk_settings['format_timestamp']) {
                                $is_custom = false;
                                $selected = 'selected';
                            } else {
                                $selected = '';
                            }
                            echo '<option value="'.$example.'" '.$selected.'>'.hesk_date('now', false, true, true, $example).'</option>';
                        }
                        ?>
                        <option value="custom" <?php echo $is_custom ? 'selected' : ''; ?>><?php echo $hesklang['custom']; ?></option>
                    </select>
                    <script>
                        $('#ex-timestamp').selectize();
                        $('#ex-timestamp').on('change', function() {
                            if (this.value != 'custom') {
                                $('#s_format_timestamp').val(this.value);
                            }
                        });
                    </script>
                </div>
                <div class="radio-group">
                    <h5>
                        <span><?php echo $hesklang['tdis']; ?></span>
                        <a onclick="hesk_window('<?php echo $help_folder; ?>misc.html#64','400','500')">
                            <div class="tooltype right">
                                <svg class="icon icon-info">
                                    <use xlink:href="<?php echo HESK_PATH; ?>img/sprite.svg#icon-info"></use>
                                </svg>
                            </div>
                        </a>
                    </h5>
                    <?php
                        $on = $hesk_settings['time_display'] ? 'checked="checked"' : '';
                        $off = $hesk_settings['time_display'] ? '' : 'checked="checked"';
                    ?>
                    <div class="radio-list">
                        <div class="radio-custom">
                            <input type="radio" id="s_time_display0" name="s_time_display" value="0" <?php echo $off; ?>>
                            <label for="s_time_display0"><?php echo $hesklang['tdisd']; ?></label>
                        </div>
                        <div class="radio-custom">
                            <input type="radio" id="s_time_display1" name="s_time_display" value="1" <?php echo $on; ?>>
                            <label for="s_time_display1"><?php echo $hesklang['tdisa']; ?></label>
                        </div>
                    </div>
                </div>
                <p>&nbsp;</p>
                <?php hesk_show_info( sprintf($hesklang['jsc_notice'], '<svg class="icon icon-info"><use xlink:href="'.HESK_PATH.'img/sprite.svg#icon-info"></use></svg>') ); ?>
                <div class="form-group flex-row">
                    <label>
                        <span><?php echo $hesklang['cdfor']; ?></span>
                        <a onclick="hesk_window('<?php echo $help_folder; ?>misc.html#68','400','500')">
                            <div class="tooltype right">
                                <svg class="icon icon-info">
                                    <use xlink:href="<?php echo HESK_PATH; ?>img/sprite.svg#icon-info"></use>
                                </svg>
                            </div>
                        </a>
                    </label>
                    <input type="text" class="form-control" style="max-width: 200px; margin-right: 5px;" id="s_format_datepicker_js" name="s_format_datepicker_js" maxlength="255" value="<?php echo $hesk_settings['format_datepicker_js']; ?>">
                    <select name="ex-js" id="ex-js">
                        <?php
                        $examples = array(
                            'mm/dd/yyyy',
                            'dd/mm/yyyy',
                            'mm-dd-yyyy',
                            'dd-mm-yyyy',
                            'd M yy',
                            'd M yyyy',
                            'D, d M yyyy'
                        );

                        $is_custom = true;
                        foreach ($examples as $example) {
                            if ($example == $hesk_settings['format_datepicker_js']) {
                                $is_custom = false;
                                $selected = 'selected';
                            } else {
                                $selected = '';
                            }
                            echo '<option value="'.$example.'" '.$selected.'>'.hesk_date('now', false, true, true, hesk_map_datepicker_date_format_to_php($example)).'</option>';
                        }
                        ?>
                        <option value="custom" <?php echo $is_custom ? 'selected' : ''; ?>><?php echo $hesklang['custom']; ?></option>
                    </select>
                    <script>
                        $('#ex-js').selectize();
                        $('#ex-js').on('change', function() {
                            if (this.value != 'custom') {
                                $('#s_format_datepicker_js').val(this.value);
                            }
                        });
                    </script>
                </div>
            </section>
            <section class="settings__form_block">
                <h3><?php echo $hesklang['other']; ?></h3>
                <div class="form-group">
                    <label>
                        <span><?php echo $hesklang['ip_whois']; ?></span>
                        <a onclick="hesk_window('<?php echo $help_folder; ?>misc.html#61','400','500')">
                            <div class="tooltype right">
                                <svg class="icon icon-info">
                                    <use xlink:href="<?php echo HESK_PATH; ?>img/sprite.svg#icon-info"></use>
                                </svg>
                            </div>
                        </a>
                    </label>
                    <input type="text" class="form-control" name="s_ip_whois_url" maxlength="255" value="<?php echo $hesk_settings['ip_whois']; ?>">
                </div>
                <tr>
                    <td><label> </label></td>
                </tr>
                <div class="checkbox-group">
                    <h5>
                        <span><?php echo $hesklang['mms']; ?></span>
                        <a onclick="hesk_window('<?php echo $help_folder; ?>misc.html#62','400','500')">
                            <div class="tooltype right">
                                <svg class="icon icon-info">
                                    <use xlink:href="<?php echo HESK_PATH; ?>img/sprite.svg#icon-info"></use>
                                </svg>
                            </div>
                        </a>
                    </h5>
                    <div class="checkbox-custom">
                        <input type="checkbox" id="s_maintenance_mode1" name="s_maintenance_mode" value="1" <?php if ($hesk_settings['maintenance_mode']) {echo 'checked';} ?>>
                        <label for="s_maintenance_mode1"><?php echo $hesklang['mmd']; ?></label>
                    </div>
                </div>
                <div class="checkbox-group">
                    <h5>
                        <span><?php echo $hesklang['al']; ?></span>
                        <a onclick="hesk_window('<?php echo $help_folder; ?>misc.html#21','400','500')">
                            <div class="tooltype right">
                                <svg class="icon icon-info">
                                    <use xlink:href="<?php echo HESK_PATH; ?>img/sprite.svg#icon-info"></use>
                                </svg>
                            </div>
                        </a>
                    </h5>
                    <div class="checkbox-custom">
                        <input type="checkbox" id="s_alink1" name="s_alink" value="1" <?php if ($hesk_settings['alink']) {echo 'checked';} ?>/>
                        <label for="s_alink1"><?php echo $hesklang['dap']; ?></label>
                    </div>
                </div>
                <div class="checkbox-group">
                    <h5>
                        <span><?php echo $hesklang['subnot']; ?></span>
                        <a onclick="hesk_window('<?php echo $help_folder; ?>misc.html#48','400','500')">
                            <div class="tooltype right">
                                <svg class="icon icon-info">
                                    <use xlink:href="<?php echo HESK_PATH; ?>img/sprite.svg#icon-info"></use>
                                </svg>
                            </div>
                        </a>
                    </h5>
                    <div class="checkbox-custom">
                        <input type="checkbox" id="s_submit_notice1" name="s_submit_notice" value="1" <?php if ($hesk_settings['submit_notice']) {echo 'checked';} ?>/>
                        <label for="s_submit_notice1"><?php echo $hesklang['subnot2']; ?></label>
                    </div>
                </div>
                <div class="checkbox-group multiple-emails">
                    <h5>
                        <span><?php echo $hesklang['sonline']; ?></span>
                        <a onclick="hesk_window('<?php echo $help_folder; ?>misc.html#56','400','500')">
                            <div class="tooltype right">
                                <svg class="icon icon-info">
                                    <use xlink:href="<?php echo HESK_PATH; ?>img/sprite.svg#icon-info"></use>
                                </svg>
                            </div>
                        </a>
                    </h5>
                    <div class="checkbox-custom">
                        <input type="checkbox" id="s_online1" name="s_online" value="1" <?php if ($hesk_settings['online']) {echo 'checked';} ?>>
                        <label for="s_online1"><?php echo $hesklang['sonline2']; ?></label>
                        <div class="form-group">
                            <input type="text" name="s_online_min" class="form-control" maxlength="4" value="<?php echo $hesk_settings['online_min']; ?>">
                        </div>
                    </div>
                </div>
                <div class="checkbox-group">
                    <h5>
                        <span><?php echo $hesklang['updates']; ?></span>
                        <a onclick="hesk_window('<?php echo $help_folder; ?>misc.html#59','400','500')">
                            <div class="tooltype right">
                                <svg class="icon icon-info">
                                    <use xlink:href="<?php echo HESK_PATH; ?>img/sprite.svg#icon-info"></use>
                                </svg>
                            </div>
                        </a>
                    </h5>
                    <div class="checkbox-custom">
                        <input type="checkbox" id="s_check_updates1" name="s_check_updates" value="1" <?php if ($hesk_settings['check_updates']) {echo 'checked';} ?>>
                        <label for="s_check_updates1"><?php echo $hesklang['updates2']; ?></label>
                    </div>
                </div>
            </section>
            <section class="settings__form_block">
                <h3><?php echo $hesklang['tools']; ?></h3>
                <div class="form-group row flex-row">
                    <label>
                        <span><?php echo $hesklang['purge_cache']; ?></span>
                        <a onclick="hesk_window('<?php echo $help_folder; ?>370.html#4','400','500')">
                            <div class="tooltype right">
                                <svg class="icon icon-info">
                                    <use xlink:href="<?php echo HESK_PATH; ?>img/sprite.svg#icon-info"></use>
                                </svg>
                            </div>
                        </a>
                    </label>
                    <button type="button" class="btn btn--blue-border" style="margin-left: 20px" ripple="ripple"
                            onclick="return hesk_purgeCache()">
                        <?php echo $hesklang['purge_cache_btn']; ?>
                    </button>
                </div>
                <?php if ($hesk_settings['debug_mode']): ?>
                <div class="form-group row flex-row">
                    <label>
                        <span><?php echo $hesklang['rebuild_assets']; ?></span>
                        <a onclick="hesk_window('<?php echo $help_folder; ?>370.html#5','400','500')">
                            <div class="tooltype right">
                                <svg class="icon icon-info">
                                    <use xlink:href="<?php echo HESK_PATH; ?>img/sprite.svg#icon-info"></use>
                                </svg>
                            </div>
                        </a>
                    </label>
                    <button type="button" class="btn btn--blue-border" style="margin-left: 20px" ripple="ripple"
                            onclick="return hesk_rebuildProdAssets()">
                        <?php echo $hesklang['rebuild_assets_btn']; ?>
                    </button>
                </div>
                <?php endif; ?>
            </section>
            <div class="settings__form_submit">
                <input type="hidden" name="token" value="<?php hesk_token_echo(); ?>">
                <input type="hidden" name="section" value="MISC">
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
<?php
require_once(HESK_PATH . 'inc/footer.inc.php');
exit();


// START hesk_rebuildProdAssets()
function hesk_rebuildProdAssetsAtPath($path, $matchFilesPath, $distPath = '', $ignoreNonMinifiedWrite = false) {
    global $hesklang;
    $coreCssPath = $path . $matchFilesPath;
    try {
        require_once __DIR__ . '/..'.'/vendor/autoload.php'; // Make sure autoloader is included

        if ( ! class_exists('\MatthiasMullie\Minify\CSS')) {
            return 'The <b>MatthiasMullie\Minify\CSS</b> class is not installed. 
                Please install it using Composer: <b>"composer require matthiasmullie/minify"</b>.
                ';
        }

        $cssMinifier = new \MatthiasMullie\Minify\CSS();

        $coreCssFiles = glob($coreCssPath);
        // 1. Get all .css files in the core folder, alphabetically
        if (is_array($coreCssFiles)) {

            // Remove the file if its name contains "default_theme_vars"
            // Note: unfortunately have to load that first separately (NOT bundled), so it can then get overwritten by any theme vars, BEFORE any color calculations are done
            // TODO alternatively, could simply print those contents into a style tag directly, to avoid loading the extra css file?
            // Remove any file containing "default_theme_vars" right away
            $coreCssFiles = array_filter($coreCssFiles, function ($file) {
                return strpos(basename($file), 'default_theme_vars') === false;
            });

            sort($coreCssFiles); // sort alphabetically
        }

        // Make sure dist folder for bundled assets is available and writable
        $distPath = $path . $distPath;
        if ( ! is_dir($distPath)) {
            // -> Try to create it
            //mkdir($distPath, 0755, true);

            // -> Is the folder now there?
            if ( ! is_dir($distPath) )
            {
                return '
                    Folder <b>' . $distPath . '</b> does not exist, and an attempt to create it failed.<br /><br />
                    &raquo;Make sure PHP has permission to write in folder <b>' . $distPath . '</b><br />
                    &raquo; contact your hosting company for help with setting up folder permissions.
                    ';
            }

            // -> Is it writable?
            if ( ! is__writable($distPath) )
            {
                // -> try to CHMOD it
                @chmod($distPath, 0777);

                // -> test again
                if ( ! is__writable($distPath) )
                {
                    // Note: Secondary check if folder is writable
                    $rebuildResult = '
                        Folder <b>' . $distPath . '</b> is not writable by PHP.<br /><br />
                        Make sure PHP has permission to write to folder <b>' . $distPath . '</b><br /><br />
                        &raquo; on <b>Linux</b> servers <a href="https://www.phpjunkyard.com/tutorials/ftp-chmod-tutorial.php">CHMOD</a> this folder to 777 (rwxrwxrwx)<br />
                        &raquo; on <b>Windows</b> servers allow Internet Guest Account to modify the folder<br />
                        &raquo; contact your hosting company for help with setting up folder permissions.
                        ';
                    return $rebuildResult;
                }
            }
        }

        // Combine into one big non-minified string
        $combinedCss = '';
        foreach ($coreCssFiles as $file) {
            $cssContent = file_get_contents($file);
            // TODO note: Minified version does this automatically, but this is hardcoded to just replace ../../ with ../ -> works for now,
            // TODO note -> but if paths were to be changed it might need ot be adjusted.
            $cssContent = preg_replace('/\.\.\/\.\.\//', '../', $cssContent);
            $combinedCss .= $cssContent . "\n";
            $cssMinifier->add($file); // for minified version, don't jsut add content, as it otherwise won't properly rewrite paths relatively to output folder.
        }

        if (!$ignoreNonMinifiedWrite) {
            // 3. Write non-minified version (optional, i.e. not necessary for admin currently)
            file_put_contents($distPath . '/app.css', $combinedCss);
        }

        // 4. Minify and write minified version
        $cssMinifier->minify($distPath . '/app.min.css');

        $rebuildResult = '✅ Combined and minified all CSS files from ' . $coreCssPath;
        $rebuildResult .= "\n Into: " . $distPath . '/app.min.css';

        $rebuildResult .= '<p><i>' . $hesklang['rebuilt_assets'] . '</i></p>';
        $rebuildResult .= '<pre>' . htmlspecialchars(implode("\n", $coreCssFiles)) . '</pre>';
    } catch (Exception $e) {
        error_log("hesk_rebuildProdAssets - Error bundling assets, error: " . $e->getMessage());
        $rebuildResult = '❌ Issue combining and minifying all CSS files from ' . $coreCssPath;
        $rebuildResult .= "\n Error details: " . $e->getMessage();
    }
    return $rebuildResult;
}


function hesk_rebuildProdAssets() {
    global $hesk_settings, $hesklang;

    // Note: We need to rebuild both Customer AND admin assets
    $path = HESK_PATH . 'theme/' . $hesk_settings['site_theme'] . '/customer';
    $matchFilesPath = '/css/core/*.css';
    $customerRebuildResult = hesk_rebuildProdAssetsAtPath($path, $matchFilesPath, '/dist');

    $path = HESK_PATH;
    $matchFilesPath = 'css/app.css'; // For admin, we currently just minify the main app.css one as it's not decoupled yet
    $adminRebuildResult = hesk_rebuildProdAssetsAtPath($path, $matchFilesPath, 'css', true);

    $rebuildResult = '<h5>Rebuilding Customer assets...</h5><p>' . $customerRebuildResult . '</p>';
    $rebuildResult .= '<h5>Rebuilding Admin assets...</h5><p>' . $adminRebuildResult . '</p>';

    // TODO consider reworking the various requests popup to a more modular/reusable approach/template, as currently it's a mess with this long code etc.
    ?>
    <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML; 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
    <html xmlns="http://www.w3.org/1999/xhtml" lang="en">
    <head>
        <title><?php echo $hesklang['s_inl']; ?></title>
        <meta http-equiv="Content-Type" content="text/html;charset=<?php echo $hesklang['ENCODING']; ?>" />
        <style type="text/css">
            body
            {
                margin:5px 5px;
                padding:0;
                background:#fff;
                color: black;
                font : 68.8%/1.5 Verdana, Geneva, Arial, Helvetica, sans-serif;
                text-align:left;
            }

            p
            {
                color : black;
                font-family : Verdana, Geneva, Arial, Helvetica, sans-serif;
                font-size: 1.0em;
            }
            h3
            {
                color : #AF0000;
                font-family : Verdana, Geneva, Arial, Helvetica, sans-serif;
                font-weight: bold;
                font-size: 1.0em;
                text-align:center;
            }
            .title
            {
                color : black;
                font-family : Verdana, Geneva, Arial, Helvetica, sans-serif;
                font-weight: bold;
                font-size: 1.0em;
            }
            .wrong   {color : red;}
            .correct {color : green;}
            pre {font-size:1.2em;}
        </style>
    </head>
    <body>
    <h3><?php echo $hesklang['rebuild_assets_btn']; ?></h3>
    <?php echo $rebuildResult; ?>
    <p>&nbsp;</p>
    <p align="center"><a href="admin_settings_misc.php?rebuild_prod_assets&amp;<?php echo rand(10000,99999); ?>"><?php echo $hesklang['rebuild_again']; ?></a> | <a href="#" onclick="Javascript:window.close()"><?php echo $hesklang['cwin']; ?></a></p>
    <p>&nbsp;</p>
    </body>
    </html>
    <?php
    exit();
}
// END hesk_rebuildProdAssets()


function hesk_purgeCache()
{
    global $hesk_settings, $hesklang;

    hesk_purge_cache();
    ?>
    <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML; 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
    <html xmlns="http://www.w3.org/1999/xhtml" lang="en">
    <head>
        <title><?php echo $hesklang['s_inl']; ?></title>
        <meta http-equiv="Content-Type" content="text/html;charset=<?php echo $hesklang['ENCODING']; ?>" />
        <style type="text/css">
            body
            {
                margin:5px 5px;
                padding:0;
                background:#fff;
                color: black;
                font : 68.8%/1.5 Verdana, Geneva, Arial, Helvetica, sans-serif;
                text-align:left;
            }

            p
            {
                color : black;
                font-family : Verdana, Geneva, Arial, Helvetica, sans-serif;
                font-size: 1.0em;
            }
            h3
            {
                color : #AF0000;
                font-family : Verdana, Geneva, Arial, Helvetica, sans-serif;
                font-weight: bold;
                font-size: 1.0em;
                text-align:center;
            }
            .title
            {
                color : black;
                font-family : Verdana, Geneva, Arial, Helvetica, sans-serif;
                font-weight: bold;
                font-size: 1.0em;
            }
            .wrong   {color : red;}
            .correct {color : green;}
            pre {font-size:1.2em;}
        </style>
    </head>
    <body>
    <h3><?php echo $hesklang['purge_cache']; ?></h3>
    <p align="center"><?php echo $hesklang['purge_cache_done']; ?></p>
    <p>&nbsp;</p>
    <p align="center"><a href="#" onclick="Javascript:window.close()"><?php echo $hesklang['cwin']; ?></a></p>
    <p>&nbsp;</p>
    </body>
    </html>
    <?php
    exit();
} // END hesk_purgeCache()


function hesk_exitDemo($message = null)
{
    global $hesk_settings, $hesklang;

    if ( ! $message) {
        $message = $hesklang['ddemo'];
    }
    ?>
    <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML; 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
    <html xmlns="http://www.w3.org/1999/xhtml" lang="en">
    <head>
        <title><?php echo $hesklang['s_inl']; ?></title>
        <meta http-equiv="Content-Type" content="text/html;charset=<?php echo $hesklang['ENCODING']; ?>" />
        <style type="text/css">
            body
            {
                margin:5px 5px;
                padding:0;
                background:#fff;
                color: black;
                font : 68.8%/1.5 Verdana, Geneva, Arial, Helvetica, sans-serif;
                text-align:left;
            }

            p
            {
                color : black;
                font-family : Verdana, Geneva, Arial, Helvetica, sans-serif;
                font-size: 1.0em;
            }
            h3
            {
                color : #AF0000;
                font-family : Verdana, Geneva, Arial, Helvetica, sans-serif;
                font-weight: bold;
                font-size: 1.0em;
                text-align:center;
            }
            .title
            {
                color : black;
                font-family : Verdana, Geneva, Arial, Helvetica, sans-serif;
                font-weight: bold;
                font-size: 1.0em;
            }
            .wrong   {color : red;}
            .correct {color : green;}
            pre {font-size:1.2em;}
        </style>
    </head>
    <body>
    <p align="center"><?php echo $message; ?></p>
    <p>&nbsp;</p>
    <p align="center"><a href="#" onclick="Javascript:window.close()"><?php echo $hesklang['cwin']; ?></a></p>
    <p>&nbsp;</p>
    </body>
    </html>
    <?php
    exit();
} // END hesk_exitDemo()
