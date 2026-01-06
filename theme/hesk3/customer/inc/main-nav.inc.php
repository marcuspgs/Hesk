<?php
global $hesk_settings, $hesklang;
/**
 * @var array $customerUserContext - User info for a customer if logged in.  `null` if a customer is not logged in.
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

require_once(TEMPLATE_PATH . 'customer/inc/login-navbar-elements.php');
?>

<header class="header">
    <div class="contr">
        <div class="header__inner">
            <a href="<?php echo $hesk_settings['hesk_url']; ?>" class="header__logo">
                <?php echo $hesk_settings['hesk_title']; ?>
            </a>
            <?php if (!defined('IGNORE_NAVBAR_RENDER')) {
                // in some cases, i.e. error.php, we want to ignore rendering this, but in all other cases we need/want to render it
                renderLoginNavbarElements($customerUserContext ?? null);
                renderNavbarLanguageSelect();
            }
            ?>
        </div>
    </div>
</header>
<?php
// Note: We're not using define() in this exception, as there's situations where breadcrumbs can't be set as a constant,
// due to some dynamic link generation in a few cases
global $BREADCRUMBS;
if (isset($BREADCRUMBS) && !empty($BREADCRUMBS)) {
?>
<div class="breadcrumbs">
    <div class="contr">
        <div class="breadcrumbs__inner">
            <?php foreach($BREADCRUMBS as $breadcrumb):
                if (!empty($breadcrumb['url'])) {
                    // If URL is defined, assume it's not the last breadcrumb, and there's another after it
                    ?>
                    <a href="<?php echo $breadcrumb['url']; ?>">
                        <span><?php echo $breadcrumb['title']; ?></span>
                    </a>
                    <svg class="icon icon-chevron-right">
                        <use xlink:href="<?php echo TEMPLATE_PATH; ?>customer/img/sprite.svg#icon-chevron-right"></use>
                    </svg>
                <?php
                } else {
                    // If URL NOT defined, assume this is the last one, and don't print another chevron or URL
                    ?>
                    <div class="last"><?php echo $breadcrumb['title']; ?></div>
                    <?php
                }
            endforeach; ?>
        </div>
    </div>
</div>
<?php
} ?>