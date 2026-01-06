<?php
global $hesk_settings, $hesklang;
/**
 * footer.inc.php takes care of closing the DOM structure of what header.inc.php opens up:
 * Up to the <main> element.
 *
 * IMPORTANT: footer.inc.php closes off the <main>, <body> and <html> elements.
 *
 * If you need to add any custom JS to a page, just create a <script> tag before the customer/inc/footer.inc.php
 * is required - so still part within the <main> scope.
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
if (!defined('IN_SCRIPT')) {die('Invalid attempt');}

/*******************************************************************************
The code below handles HESK licensing and must be included in the template.

Removing this code is a direct violation of the HESK End User License Agreement,
will void all support and may result in unexpected behavior.

To purchase a HESK license and support future HESK development please visit:
https://www.hesk.com/buy.php
*******************************************************************************/
$hesk_settings['hesk_license']('GZvb3RlciBjbGFzcz0iZm9vdGVyIiBzdHlsZT0iZGlzcGxhe
TpibG9jayAhaW1wb3J0YW50OyI+PHAgY2xhc3M9InRleHQtY2VudGVyIiBzdHlsZT0iZGlzcGxheTpib
G9jayAhaW1wb3J0YW50OyI+UG93ZXJlZCBieSA8YSBocmVmPSJodHRwczovL3d3dy5oZXNrLmNvbSIgY
2xhc3M9ImxpbmsiIHN0eWxlPSJkaXNwbGF5OmlubGluZSAhaW1wb3J0YW50OyI+SGVscCBEZXNrIFNvZ
nR3YXJlPC9hPiA8c3BhbiBjbGFzcz0iZm9udC13ZWlnaHQtYm9sZCIgc3R5bGU9ImRpc3BsYXk6aW5sa
W5lICFpbXBvcnRhbnQ7Ij5IRVNLPC9zcGFuPjxicj4mbmJzcDs8L3A+PC9mb290ZXI+',"\120",
"2c6d33b92c7068eed222cfc904a0e6019a579a8d");
/*******************************************************************************
END LICENSE CODE
*******************************************************************************/
?>
            </main>  <!-- End main -->
        </div> <!-- End wrapper -->
<?php
// Start loading any additional commonly used scripts.
include(TEMPLATE_PATH . '../../footer.txt');

// Force extending sessions if specifically enabled
if ($hesk_settings['extend_customer'] && isset($_SESSION['customer']['id'])) : ?>
    <iframe src="<?php echo HESK_PATH . 'extend_session.php'; ?>" height="10" width="10" style="border:none;"></iframe>
<?php
endif;

// Note: for the most common ones, they are loaded unless ignored, for the rarer ones, they're only loaded if defined.
if (!defined('IGNORE_LOAD_JQUERY')) : ?>
    <script src="<?php echo TEMPLATE_PATH; ?>customer/js/jquery-3.5.1.min.js"></script>
<?php
endif;
if (!defined('IGNORE_LOAD_HESK_FUNC')) : ?>
    <script src="<?php echo TEMPLATE_PATH; ?>customer/js/hesk_functions.js?<?php echo $hesk_settings['hesk_version']; ?>"></script>
<?php
endif;
if (!defined('IGNORE_LOAD_SVG4')) : ?>
    <script src="<?php echo TEMPLATE_PATH; ?>customer/js/svg4everybody.min.js"></script>
<?php
endif;
if (!defined('IGNORE_LOAD_SELECTIZE')) : ?>
    <script src="<?php echo TEMPLATE_PATH; ?>customer/js/selectize.min.js?<?php echo $hesk_settings['hesk_version']; ?>"></script>
<?php
endif;
if (!defined('IGNORE_LOAD_APP')) : ?>
    <script src="<?php echo TEMPLATE_PATH; ?>customer/js/app<?php echo $hesk_settings['debug_mode'] ? '' : '.min'; ?>.js?<?php echo $hesk_settings['hesk_version']; ?>"></script>
<?php
endif;
if (defined('OUTPUT_SEARCH_JAVASCRIPT')) :
    // TODO CAN this BE simply be moved in the end here, or does it have to be in the middle as in 4/5 cases, or is it fine like in mytickets, where it's done in the end?
    outputSearchJavascript();
endif;

// Note: For less common ones, require to define them to load them.
if (defined('LOAD_JS_JQUERY_MODAL')) : ?>
    <script src="<?php echo TEMPLATE_PATH; ?>customer/js/jquery.modal.min.js"></script>
<?php
endif;
if (defined('LOAD_JS_DATEPICKER')) : ?>
    <script src="<?php echo TEMPLATE_PATH; ?>customer/js/datepicker.min.js"></script>
    <script type="text/javascript">
        (function ($) { $.fn.datepicker.language['en'] = {
            days: ['<?php echo $hesklang['d0']; ?>', '<?php echo $hesklang['d1']; ?>', '<?php echo $hesklang['d2']; ?>', '<?php echo $hesklang['d3']; ?>', '<?php echo $hesklang['d4']; ?>', '<?php echo $hesklang['d5']; ?>', '<?php echo $hesklang['d6']; ?>'],
            daysShort: ['<?php echo $hesklang['sun']; ?>', '<?php echo $hesklang['mon']; ?>', '<?php echo $hesklang['tue']; ?>', '<?php echo $hesklang['wed']; ?>', '<?php echo $hesklang['thu']; ?>', '<?php echo $hesklang['fri']; ?>', '<?php echo $hesklang['sat']; ?>'],
            daysMin: ['<?php echo $hesklang['su']; ?>', '<?php echo $hesklang['mo']; ?>', '<?php echo $hesklang['tu']; ?>', '<?php echo $hesklang['we']; ?>', '<?php echo $hesklang['th']; ?>', '<?php echo $hesklang['fr']; ?>', '<?php echo $hesklang['sa']; ?>'],
            months: ['<?php echo $hesklang['m1']; ?>','<?php echo $hesklang['m2']; ?>','<?php echo $hesklang['m3']; ?>','<?php echo $hesklang['m4']; ?>','<?php echo $hesklang['m5']; ?>','<?php echo $hesklang['m6']; ?>', '<?php echo $hesklang['m7']; ?>','<?php echo $hesklang['m8']; ?>','<?php echo $hesklang['m9']; ?>','<?php echo $hesklang['m10']; ?>','<?php echo $hesklang['m11']; ?>','<?php echo $hesklang['m12']; ?>'],
            monthsShort: ['<?php echo $hesklang['ms01']; ?>','<?php echo $hesklang['ms02']; ?>','<?php echo $hesklang['ms03']; ?>','<?php echo $hesklang['ms04']; ?>','<?php echo $hesklang['ms05']; ?>','<?php echo $hesklang['ms06']; ?>', '<?php echo $hesklang['ms07']; ?>','<?php echo $hesklang['ms08']; ?>','<?php echo $hesklang['ms09']; ?>','<?php echo $hesklang['ms10']; ?>','<?php echo $hesklang['ms11']; ?>','<?php echo $hesklang['ms12']; ?>'],
            today: '<?php echo hesk_slashJS($hesklang['r1']); ?>',
            clear: '<?php echo hesk_slashJS($hesklang['clear']); ?>',
            dateFormat: '<?php echo hesk_slashJS($hesk_settings['format_datepicker_js']); ?>',
            timeFormat: '<?php echo hesk_slashJS($hesk_settings['format_time']); ?>',
            firstDay: <?php echo $hesklang['first_day_of_week']; ?>
        }; })(jQuery);
    </script>
<?php
endif;
if (defined('LOAD_JS_DROPZONE')) :
    ?>
    <script src="<?php echo TEMPLATE_PATH; ?>customer/js/dropzone.min.js"></script>
<?php
endif;
if (defined('LOAD_JS_ZEBRA')) :
    ?>

    <script src="./js/zebra_tooltips.min.js?<?php echo $hesk_settings['hesk_version']; ?>"></script>
    <?php if (function_exists('hesk3_output_drag_and_drop_script')) hesk3_output_drag_and_drop_script('r_attachments'); ?>
    <script>
        $(document).ready(function() {
            new $.Zebra_Tooltips($('.tooltip'), {animation_offset: 0, animation_speed: 100, hide_delay: 0, show_delay: 0, vertical_alignment: 'above', vertical_offset: 5});
        });
    </script>
<?php
endif;
if (defined('RECAPTCHA')) : ?>
    <script src="https://www.google.com/recaptcha/api.js?hl=<?php echo $hesklang['RECAPTCHA']; ?>" async defer></script>
    <script>
        if (typeof recaptcha_submitForm === 'undefined') {
            // default catcha submit function - some pages might have a different handler defined already
            function recaptcha_submitForm() {
                document.getElementById("form1").submit();
            }
        }
    </script>
<?php endif; ?>

<?php
/* In some cases (like create-ticket.php) we have to close out html after footer is loaded, in order we can ensure to load some
HTML elements after jQuery & other scripts have been loaded first
*/
if (!defined('FOOTER_DONT_CLOSE_HTML')) : ?>
    </body> <!-- End body -->
</html> <!-- End html -->
<?php endif; ?>
