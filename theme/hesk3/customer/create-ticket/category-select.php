<?php
global $hesk_settings, $hesklang;
/**
 * @var bool $customerLoggedIn - `true` if a customer is logged in, `false` otherwise
 * @var array $customerUserContext - User info for a customer if logged in.  `null` if a customer is not logged in.
 */

// This guard is used to ensure that users can't hit this outside of actual HESK code
if (!defined('IN_SCRIPT')) {
    die();
}
define('EXTRA_PAGE_CLASSES','page-category-select');

define('ALERTS',1);

define('RENDER_COMMON_ELEMENTS',1);

global $BREADCRUMBS;
$BREADCRUMBS = array(
    array('url' => $hesk_settings['site_url'], 'title' => $hesk_settings['site_title']),
    array('url' => $hesk_settings['hesk_url'], 'title' => $hesk_settings['hesk_title']),
    array('title' => $hesklang['submit_ticket'])
);

/* Print header */
require_once(TEMPLATE_PATH . 'customer/inc/header.inc.php');
?>
        <div class="main__content">
            <div class="contr">
                <div style="margin-bottom: 20px;">
                    <?php
                    hesk3_show_messages($messages);
                    ?>
                </div>
                <?php hesk3_show_messages($serviceMessages); ?>
                <h1 class="select__title"><?php echo $hesklang['select_category_text']; ?></h1>
                <?php
                // Show dropdown or list, depending on number of categories
                if (($category_count = count($hesk_settings['categories'])) > $hesk_settings['cat_show_select']):
                ?>
                    <form action="index.php" method="get">
                        <div style="display: table; margin: 40px auto;">
                            <select class="form-control cat-select" name="category" id="select_category" aria-label="<?php echo $hesklang['select_category']; ?>">
                                <?php
                                if ($hesk_settings['select_cat'])
                                {
                                    echo '<option value="">'.$hesklang['select'].'</option>';
                                }
                                foreach ($hesk_settings['categories'] as $k=>$v)
                                {
                                    echo '<option value="'.$k.'">'.$v['name'].'</option>';
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-footer">
                            <button class="btn btn-full" type="submit"><?php echo $hesklang['c2c']; ?></button>
                            <input type="hidden" name="a" value="add">
                        </div>
                    </form>
                <?php else: ?>
                    <div class="nav">
                        <?php foreach ($hesk_settings['categories'] as $k => $v): ?>
                        <a href="index.php?a=add&amp;category=<?php echo $k; ?>" class="navlink <?php if ($category_count > 8) echo "navlink-condensed"; ?>">
                            <span class="icon-in-circle" aria-hidden="true">
                                <svg class="icon icon-chevron-right">
                                    <use xlink:href="<?php echo TEMPLATE_PATH; ?>customer/img/sprite.svg#icon-chevron-right"></use>
                                </svg>
                            </span>
                            <div>
                                <h2 class="navlink__title"><!--[if IE]> &raquo; <![endif]--><?php echo $v['name']; ?></h2>
                            </div>
                        </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        $('#select_category').selectize();
    });
</script>
<?php
/* Print Footer */
require_once(TEMPLATE_PATH . 'customer/inc/footer.inc.php');
?>
