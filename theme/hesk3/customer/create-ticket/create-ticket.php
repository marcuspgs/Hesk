<?php
global $hesk_settings, $hesklang;
/**
 * @var string $categoryName
 * @var int $categoryId
 * @var array $visibleCustomFieldsBeforeMessage
 * @var array $visibleCustomFieldsAfterMessage
 * @var array $customFieldsBeforeMessage
 * @var array $customFieldsAfterMessage
 * @var bool $customerLoggedIn - `true` if a customer is logged in, `false` otherwise
 * @var array $customerUserContext - User info for a customer if logged in.  `null` if a customer is not logged in.
 */

// This guard is used to ensure that users can't hit this outside of actual HESK code
if (!defined('IN_SCRIPT')) {
    die();
}
define('EXTRA_PAGE_CLASSES','page-create-ticket');

define('ALERTS',1);
define('CUSTOM_FIELDS',1);
define('ATTACHMENTS',1);
define('PRIORITIES',1);

define('LOAD_CSS_DROPZONE',1);
define('RENDER_COMMON_ELEMENTS',1);

define('TMP_TITLE',1); // TODO absolutelyRework

define('LOAD_JS_DATEPICKER',1);
define('LOAD_JS_DROPZONE',1);

define('FOOTER_DONT_CLOSE_HTML',1);

global $BREADCRUMBS;
$BREADCRUMBS = array(
    array('url' => $hesk_settings['site_url'], 'title' => $hesk_settings['site_title']),
    array('url' => "index.php", 'title' => $hesk_settings['hesk_title']),
    array('url' => "index.php?a=add", 'title' => $hesklang['submit_ticket']),
    array('title' => $categoryName)
);

/* Print header */
require_once(TEMPLATE_PATH . 'customer/inc/header.inc.php');
?>
        <div class="main__content">
            <div class="contr">
                <div style="margin-bottom: 20px;">
                    <?php hesk3_show_messages($serviceMessages); ?>
                    <?php hesk3_show_messages($messages); ?>
                </div>
                <h1 class="article__heading article__heading--form">
                    <span class="icon-in-circle" aria-hidden="true">
                        <svg class="icon icon-submit-ticket">
                            <use xlink:href="<?php echo TEMPLATE_PATH; ?>customer/img/sprite.svg#icon-submit-ticket"></use>
                        </svg>
                    </span>
                    <span class="ml-1"><?php echo $hesklang['submit_a_support_request']; ?></span>
                </h1>
                <div class="article-heading-tip">
                    <span><?php echo $hesklang['req_marked_with']; ?></span>
                    <span class="label required"></span>
                </div>
                <form class="form form-submit-ticket ticket-create <?php echo count($_SESSION['iserror']) ? 'invalid' : ''; ?>" method="post" action="submit_ticket.php?submit=1" aria-label="<?php echo $hesklang['create_a_ticket']; ?>" name="form1" id="form1" enctype="multipart/form-data" onsubmit="<?php if ($hesk_settings['submitting_wait']): ?>hesk_showLoadingMessage('recaptcha-submit');<?php endif; ?>" <?php echo $hesk_settings['disable_autofill_customer'] ? 'autocomplete="off" aria-autocomplete="none"' : ''; ?>>
                    <?php if (!$customerLoggedIn) { ?>
                    <div class="form-group">
                        <label class="label required" for="name"><?php echo $hesklang['name']; ?>:</label>
                        <?php
                        $input_css = 'form-control';
                        if (in_array('name', $_SESSION['iserror'])) {
                            $input_css .= ' isError';
                        }
                        ?>
                        <input type="text" id="name" name="name"
                               class="<?php echo $input_css; ?>"
                               maxlength="50"
                               value="<?php
                               if (isset($_SESSION['c_name'])) {
                                   echo stripslashes(hesk_input($_SESSION['c_name']));
                               } ?>"
                               <?php echo $hesk_settings['disable_autofill_customer'] ? 'autocomplete="off" aria-autocomplete="none"' : ''; ?>
                               required>
                    </div>
                    <div class="form-group">
                        <label class="label <?php if ($hesk_settings['require_email']) { ?>required<?php } ?>" for="email"><?php echo $hesklang['email']; ?>:</label>
                        <?php
                        $input_css = 'form-control';
                        if (in_array('email', $_SESSION['iserror'])) {
                            $input_css .= ' isError';
                        }
                        if (in_array('email', $_SESSION['isnotice'])) {
                            $input_css .= ' isNotice';
                        }
                        ?>
                        <input type="email"
                               class="<?php echo $input_css; ?>"
                               name="email" id="email" maxlength="1000"
                               value="<?php
                               if (isset($_SESSION['c_email'])) {
                                   echo stripslashes(hesk_input($_SESSION['c_email']));
                               } ?>"
                               <?php echo $hesk_settings['disable_autofill_customer'] ? 'autocomplete="off" aria-autocomplete="none"' : ''; ?>
                               <?php if($hesk_settings['detect_typos']) { echo ' onblur="HESK_FUNCTIONS.suggestEmail(\'email\', \'email_suggestions\', 0)"'; } ?>
                               <?php if ($hesk_settings['require_email']) { ?>required<?php } ?>>
                        <div id="email_suggestions" class="email-suggestion"></div>
                    </div>
                    <?php
                    if ($hesk_settings['confirm_email']):
                        ?>
                        <?php
                        $input_css = 'form-control';
                        if (in_array('email2', $_SESSION['iserror'])) {
                            $input_css .= ' isError';
                        }
                        if (in_array('email2', $_SESSION['isnotice'])) {
                            $input_css .= ' isNotice';
                        }
                        if ($customerLoggedIn) {
                            $input_css .= ' as-text';
                        }
                        ?>
                        <div class="form-group">
                            <label class="label <?php if ($hesk_settings['require_email']) { ?>required<?php } ?>" for="email2"><?php echo $hesklang['confemail']; ?>:</label>
                            <input type="<?php echo $hesk_settings['multi_eml'] ? 'text' : 'email'; ?>"
                                   class="<?php echo $input_css; ?>"
                                   name="email2" id="email2" maxlength="1000"
                                   <?php echo $hesk_settings['disable_autofill_customer'] ? 'autocomplete="off" aria-autocomplete="none"' : ''; ?>
                                   <?php if ($customerLoggedIn) { echo 'readonly'; } ?>
                                   value="<?php if (isset($_SESSION['c_email2'])) {echo stripslashes(hesk_input($_SESSION['c_email2']));} ?>"
                                   <?php if ($hesk_settings['require_email']) { ?>required<?php } ?>>
                        </div>
                    <?php endif;
                    }
                    if ($hesk_settings['multi_eml'] && !isset($_SESSION['c_followers'])): ?>
                    <div class="form-group" id="cc-link">
                        <a href="#" onclick="HESK_FUNCTIONS.toggleLayerDisplay('cc-div');HESK_FUNCTIONS.toggleLayerDisplay('cc-link')">
                            <?php echo $hesklang['add_cc']; ?>
                        </a>
                    </div>
                    <?php endif;
                    if ($hesk_settings['multi_eml']):
                        $display = isset($_SESSION['c_followers']) ? 'block' : 'none';
                    ?>
                    <div class="form-group" id="cc-div" style="display: <?php echo $display; ?>">
                        <label class="label" for="follower_email"><?php echo $hesklang['cc']; ?>:</label>
                        <?php
                        $input_css = 'form-control';
                        if (in_array('followers', $_SESSION['iserror'])) {
                            $input_css .= ' isError';
                        }
                        if (in_array('followers', $_SESSION['isnotice'])) {
                            $input_css .= ' isNotice';
                        }
                        ?>
                        <input type="text"
                               class="<?php echo $input_css; ?>"
                               <?php echo $hesk_settings['disable_autofill_customer'] ? 'autocomplete="off" aria-autocomplete="none"' : ''; ?>
                               name="follower_email" id="follower_email" maxlength="1000"
                               value="<?php
                               if (isset($_SESSION['c_followers'])) {
                                   echo stripslashes(hesk_input($_SESSION['c_followers']));
                               } ?>" <?php if($hesk_settings['detect_typos']) { echo ' onblur="HESK_FUNCTIONS.suggestEmail(\'follower_email\', \'follower_email_suggestions\', 0)"'; } ?>>
                        <div id="follower_email_suggestions" class="email-suggestion"></div>
                        <p><?php if ($hesk_settings['customer_accounts'] && $hesk_settings['customer_accounts_required']) echo $hesklang['only_verified_cc'] . ' '; echo $hesklang['cc_help']; ?></p>
                    </div>
                    <?php
                    endif;
                    if ($hesk_settings['cust_urgency']): ?>
                        <section class="param">
                            <span class="label required <?php if (in_array('priority',$_SESSION['iserror'])) echo 'isErrorStr'; ?>"><?php echo $hesklang['priority']; ?>:</span>
                            <div class="dropdown-select center out-close priority select-priority">
                                <select name="priority" aria-label="<?php echo $hesklang['priority']; ?>">
                                    <?php if ($hesk_settings['select_pri']): ?>
                                        <option value=""><?php echo $hesklang['select']; ?></option>
                                    <?php endif; ?>
                                    <?php
                                        //Get User access priority
                                        echo hesk_get_priority_select('', 0, $_SESSION['c_priority']);
                                    ?>
                                </select>
                            </div>
                        </section>
                    <?php
                    endif;
                    if (count($visibleCustomFieldsBeforeMessage) > 0):
                    ?>
                    <div class="divider"></div>
                    <?php
                    endif;
                    hesk3_output_custom_fields($customFieldsBeforeMessage);

                    if ($hesk_settings['require_subject'] != -1 || $hesk_settings['require_message'] != -1): ?>
                        <div class="divider"></div>
                        <?php if ($hesk_settings['require_subject'] != -1): ?>
                            <div class="form-group">
                                <label class="label <?php if ($hesk_settings['require_subject']) { ?>required<?php } ?>" for="subject">
                                    <?php echo $hesklang['subject']; ?>:
                                </label>
                                <input type="text" id="subject" class="form-control <?php if (in_array('subject',$_SESSION['iserror'])) {echo 'isError';} ?>"
                                       name="subject" maxlength="70"
                                       value="<?php if (isset($_SESSION['c_subject'])) {echo stripslashes(hesk_input($_SESSION['c_subject']));} ?>"
                                       <?php echo $hesk_settings['disable_autofill_customer'] ? 'autocomplete="off" aria-autocomplete="none"' : ''; ?>
                                       <?php if ($hesk_settings['require_subject']) { ?>required<?php } ?>>
                            </div>
                            <?php
                        endif;
                        if ($hesk_settings['require_message'] != -1): ?>
                            <div class="form-group">
                                <label class="label <?php if ($hesk_settings['require_message']) { ?>required<?php } ?>" for="message">
                                    <?php echo $hesklang['message']; ?>:
                                </label>
                                <textarea class="form-control <?php if (in_array('message',$_SESSION['iserror'])) {echo 'isError';} ?>"
                                          id="message" name="message" rows="12" cols="60"
                                          <?php if ($hesk_settings['require_message']) { ?>required<?php } ?>><?php if (isset($_SESSION['c_message'])) {echo stripslashes(hesk_input($_SESSION['c_message']));} ?></textarea>
                                <?php if (has_public_kb() && $hesk_settings['kb_recommendanswers'] && ! isset($_REQUEST['do_not_suggest'])): ?>
                                    <div class="kb-suggestions">
                                        <h2><?php echo $hesklang['sc']; ?>:</h2>
                                        <ul id="kb-suggestion-list" class="type--list">
                                        </ul>
                                        <div id="suggested-article-hidden-inputs" style="display: none">
                                            <?php // Will be populated with the list sent to the create ticket logic ?>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <?php
                        endif;
                    endif;

                    if (count($visibleCustomFieldsAfterMessage) > 0): ?>
                    <div class="divider"></div>
                    <?php
                    endif;

                    hesk3_output_custom_fields($customFieldsAfterMessage);

                    if ($hesk_settings['attachments']['use']):
                    ?>
                        <div class="divider"></div>
                        <section class="param param--attach">
                            <span class="label"><?php echo $hesklang['attachments']; ?>:</span>
                            <div class="attach">
                                <div>
                                    <?php hesk3_output_drag_and_drop_attachment_holder(); ?>
                                </div>
                                <div class="attach-tooltype">
                                    <a class="link" href="file_limits.php" onclick="HESK_FUNCTIONS.openWindow('file_limits.php',250,500);return false;">
                                        <?php echo $hesklang['ful']; ?>
                                    </a>
                                </div>
                            </div>
                        </section>
                        <div class="divider"></div>
                        <?php
                    endif;

                    if ($hesk_settings['question_use'] || ($hesk_settings['secimg_use'] && $hesk_settings['recaptcha_use'] !== 1)):
                    ?>
                    <div class="captcha-block">
                        <h2><?php echo $hesklang['verify_header']; ?></h2>

                        <?php if ($hesk_settings['question_use']): ?>
                        <div class="form-group">
                            <label class="required" for="question"><?php echo $hesk_settings['question_ask']; ?></label>
                            <?php
                            $value = '';
                            if (isset($_SESSION['c_question']))
                            {
                                $value = stripslashes(hesk_input($_SESSION['c_question']));
                            }
                            ?>
                            <input type="text" id="question" class="form-control <?php echo in_array('question',$_SESSION['iserror']) ? 'isError' : ''; ?>"
                                   name="question" size="20" value="<?php echo $value; ?>"
                                   <?php echo $hesk_settings['disable_autofill_customer'] ? 'autocomplete="off" aria-autocomplete="none"' : ''; ?>>
                        </div>
                        <?php
                            endif;

                            if ($hesk_settings['secimg_use'] && $hesk_settings['recaptcha_use'] != 1)
                            {
                                ?>
                                <div class="form-group">
                                    <?php
                                    // SPAM prevention verified for this session
                                    if (isset($_SESSION['img_verified']))
                                    {
                                        echo $hesklang['vrfy'];
                                    }
                                    // Use reCAPTCHA V2?
                                    elseif ($hesk_settings['recaptcha_use'] == 2)
                                    {
                                        ?>
                                        <div class="g-recaptcha" data-sitekey="<?php echo $hesk_settings['recaptcha_public_key']; ?>"></div>
                                        <?php
                                    }
                                    // At least use some basic PHP generated image (better than nothing)
                                    else
                                    {
                                        $cls = in_array('mysecnum',$_SESSION['iserror']) ? 'isError' : '';
                                        ?>
                                        <img name="secimg" src="print_sec_img.php?<?php echo rand(10000,99999); ?>" width="150" height="40" alt="<?php echo $hesklang['sec_img']; ?>" title="<?php echo $hesklang['sec_img']; ?>" style="vertical-align:text-bottom">
                                        <a class="btn btn-refresh" href="javascript:void(0)" onclick="javascript:document.form1.secimg.src='print_sec_img.php?'+ ( Math.floor((90000)*Math.random()) + 10000);">
                                            <svg class="icon icon-refresh">
                                                <use xlink:href="<?php echo TEMPLATE_PATH; ?>customer/img/sprite.svg#icon-refresh"></use>
                                            </svg>
                                        </a>
                                        <label class="required" for="mysecnum"><?php echo $hesklang['sec_enter']; ?></label>
                                        <input type="text" id="mysecnum" name="mysecnum" size="20" maxlength="5" autocomplete="off" aria-autocomplete="none" class="form-control <?php echo $cls; ?>">
                                    <?php
                                    }
                                    ?>
                                </div>
                                <?php
                            }
                            ?>
                    </div>
                    <div class="divider"></div>
                        <?php
                    endif;

                    if ($hesk_settings['submit_notice']):
                    ?>
                    <div class="alert browser-default">
                        <div class="alert__inner">
                            <b class="font-weight-bold"><?php echo $hesklang['before_submit']; ?>:</b>
                            <ul>
                                <li><?php echo $hesklang['all_info_in']; ?>.</li>
                                <li><?php echo $hesklang['all_error_free']; ?>.</li>
                            </ul>
                            <br>
                            <b class="font-weight-bold"><?php echo $hesklang['we_have']; ?>:</b>
                            <ul>
                                <li><?php echo hesk_htmlspecialchars(hesk_getClientIP()).' '.$hesklang['recorded_ip']; ?></li>
                                <li><?php echo $hesklang['recorded_time']; ?></li>
                            </ul>
                        </div>
                    </div>
                    <?php endif; ?>


                    <div class="form-footer">
                        <input type="hidden" name="token" value="<?php hesk_token_echo(); ?>">
                        <input type="hidden" name="category" value="<?php echo $categoryId; ?>">
                        <button type="submit" class="btn btn-full" ripple="ripple" id="recaptcha-submit">
                            <?php echo $hesklang['sub_ticket']; ?>
                        </button>
                        <!-- Do not delete or modify the code below, it is used to detect simple SPAM bots -->
                        <input type="hidden" name="hx" value="3" /><input type="hidden" name="hy" value="">
                        <!-- >
                        <input type="text" name="phone" value="3">
                        < -->
                    </div>
                    <?php
                    // Use Invisible reCAPTCHA?
                    if ($hesk_settings['secimg_use'] && $hesk_settings['recaptcha_use'] == 1 && ! isset($_SESSION['img_verified']))
                    {
                        ?>
                        <div class="g-recaptcha" data-sitekey="<?php echo $hesk_settings['recaptcha_public_key']; ?>" data-bind="recaptcha-submit" data-callback="recaptcha_submitForm"></div>
                        <?php
                    }
                    ?>
                </form>
            </div>
        </div>
        <div id="loading-overlay" class="loading-overlay">
            <div id="loading-message" class="loading-message">
                <div class="spinner"></div>
                <p><?php echo $hesklang['sending_wait']; ?></p>
            </div>
        </div>
<?php
    function hesk_jsString($str)
    {
        $str  = addslashes($str);
        $str  = str_replace('<br />' , '' , $str);
        $from = array("/\r\n|\n|\r/", '/\<a href="mailto\:([^"]*)"\>([^\<]*)\<\/a\>/i', '/\<a href="([^"]*)" target="_blank"\>([^\<]*)\<\/a\>/i');
        $to   = array("\\r\\n' + \r\n'", "$1", "$1");
        return preg_replace($from,$to,$str);
    } // END hesk_jsString()
?>
<script type="text/javascript">

    document.addEventListener("DOMContentLoaded", function() {

        $('#select_category').selectize();
        hesk_loadNoResultsSelectizePlugin('<?php echo hesk_jsString($hesklang['no_results_found']); ?>');
        <?php

        foreach ($customFieldsBeforeMessage as $customField)
        {
            if ($customField['type'] == 'select')
            {
                if ($customField['value']['is_searchable'] == 1) {
                    echo "$('#{$customField['name']}').addClass('read-write').attr('placeholder', '".$hesklang["search_by_pattern"]."').selectize({
                        delimiter: ',',
                        valueField: 'id',
                        labelField: 'displayName',
                        searchField: ['displayName'],
                        create: false,
                        copyClassesToDropdown: true,
                        plugins: ['no_results'],
                    });";
                } else {
                    echo "$('#{$customField['name']}').selectize();";
                }
            }
        }
        foreach ($customFieldsAfterMessage as $customField)
        {
            if ($customField['type'] == 'select')
            {
                if ($customField['value']['is_searchable'] == 1) {
                    echo "$('#{$customField['name']}').addClass('read-write').attr('placeholder', '".$hesklang["search_by_pattern"]."').selectize({
                        delimiter: ',',
                        valueField: 'id',
                        labelField: 'displayName',
                        searchField: ['displayName'],
                        create: false,
                        copyClassesToDropdown: true,
                        plugins: ['no_results'],
                    });";
                } else {
                    echo "$('#{$customField['name']}').selectize();";
                }
            }
        }
        ?>
    });
</script>

<?php if (has_public_kb() && $hesk_settings['kb_recommendanswers']): ?>
    <script type="text/javascript">
        var noArticlesFoundText = <?php echo json_encode($hesklang['nsfo']); ?>;

        document.addEventListener("DOMContentLoaded", function() {
            HESK_FUNCTIONS.getKbTicketSuggestions($('input[name="subject"]'),
                $('textarea[name="message"]'),
                function(data) {
                    $('.kb-suggestions').show();
                    var $suggestionList = $('#kb-suggestion-list');
                    var $suggestedArticlesHiddenInputsList = $('#suggested-article-hidden-inputs');
                    $suggestionList.html('');
                    $suggestedArticlesHiddenInputsList.html('');
                    var format = '<a href="knowledgebase.php?article={0}" class="suggest-preview" target="_blank">' +
                        '<span class="icon-in-circle" aria-hidden="true">' +
                        '<svg class="icon icon-knowledge">' +
                        '<use xlink:href="./theme/hesk3/customer/img/sprite.svg#icon-knowledge"></use>' +
                        '</svg>' +
                        '</span>' +
                        '<div class="suggest-preview__text">' +
                        '<p class="suggest-preview__title">{1}</p>' +
                        '<p>{2}</p>' +
                        '</div>' +
                        '</a>';
                    var hiddenInputFormat = '<input type="hidden" name="suggested[]" value="{0}">';
                    var results = false;
                    $.each(data, function() {
                        results = true;
                        $suggestionList.append(format.replace('{0}', this.id).replace('{1}', this.subject).replace('{2}', this.contentPreview));
                        $suggestedArticlesHiddenInputsList.append(hiddenInputFormat.replace('{0}', this.hiddenInputValue));
                    });

                    if (!results) {
                        $suggestionList.append('<li class="no-articles-found">' + noArticlesFoundText + '</li>');
                    }
                }
            );
        });
    </script>
<?php endif;

// Any adjustments to datepicker?
if (isset($hesk_settings['datepicker'])):
    ?>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const myDP = {};
            <?php
            foreach ($hesk_settings['datepicker'] as $selector => $data) {
                echo "
                myDP['{$selector}'] = $('{$selector}').datepicker(".((isset($data['position']) && is_string($data['position'])) ? "{position: '{$data['position']}'}" : "").");
            ";
                if (isset($data['timestamp']) && ($ts = intval($data['timestamp']))) {
                    echo "
                    myDP['{$selector}'].data('datepicker').selectDate(new Date({$ts} * 1000));
                ";
                }
            }
            ?>
        });
    </script>
<?php
endif;
?>
<?php
/* Print Footer */
require_once(TEMPLATE_PATH . 'customer/inc/footer.inc.php');

/*
 * Note: In this case, we have to make sure we load all footer scripts first, as otherwise it breaks some of the custom JS page code
 */
?>
<?php hesk3_output_drag_and_drop_script('c_attachments'); ?>
    </body>
</html>
