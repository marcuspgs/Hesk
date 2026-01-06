<?php
global $hesk_settings, $hesklang;
/**
 * @var array $articles List of search results
 * @var bool $customerLoggedIn
 * @var bool $customerLoggedIn - `true` if a customer is logged in, `false` otherwise
 * @var array $customerUserContext - User info for a customer if logged in.  `null` if a customer is not logged in.
 */

// This guard is used to ensure that users can't hit this outside of actual HESK code
if (!defined('IN_SCRIPT')) {
    die();
}
define('EXTRA_PAGE_CLASSES','page-search-results');

define('KBSEARCH',1);
define('RATING',1);

define('OUTPUT_SEARCH_STYLING',1);
define('RENDER_COMMON_ELEMENTS',1);

define('TMP_TITLE',1); // TODO absolutelyRework

define('OUTPUT_SEARCH_JAVASCRIPT',1);

global $BREADCRUMBS;
$BREADCRUMBS = array(
    array('url' => $hesk_settings['site_url'], 'title' => $hesk_settings['site_title']),
    array('url' => $hesk_settings['hesk_url'], 'title' => $hesk_settings['hesk_title']),
    array('url' => "knowledgebase.php", 'title' => $hesklang['kb_text']),
    array('title' => $hesklang['sr'])
);

/* Print header */
require_once(TEMPLATE_PATH . 'customer/inc/header.inc.php');
?>
        <div class="main__content">
            <div class="contr">
                <div class="help-search">
                    <?php displayKbSearch(); ?>
                </div>
                <article class="article">
                    <div class="block__head">
                        <span class="icon-in-circle" aria-hidden="true">
                            <svg class="icon icon-knowledge">
                                <use xlink:href="<?php echo TEMPLATE_PATH; ?>customer/img/sprite.svg#icon-knowledge"></use>
                            </svg>
                        </span>
                        <h1 class="h-3 ml-1 text-center"><?php echo $hesklang['sr']; ?> (<?php echo count($articles); ?>)</h1>
                    </div>
                    <?php foreach ($articles as $article): ?>
                        <a href="knowledgebase.php?article=<?php echo $article['id']; ?>" class="preview">
                            <span class="icon-in-circle" aria-hidden="true">
                                <svg class="icon icon-knowledge">
                                    <use xlink:href="<?php echo TEMPLATE_PATH; ?>customer/img/sprite.svg#icon-knowledge"></use>
                                </svg>
                            </span>
                            <div class="preview__text">
                                <h2 class="preview__title"><?php echo $article['subject']; ?></h2>
                                <p class="navlink__descr"><?php echo $article['content_preview']; ?></p>
                            </div>
                            <?php if ($hesk_settings['kb_views'] || $hesk_settings['kb_rating']): ?>
                                <div class="rate">
                                    <?php if ($hesk_settings['kb_views']): ?>
                                        <div style="margin-right: 10px; display: -ms-flexbox; display: flex;">
                                            <svg class="icon icon-eye-close">
                                                <use xlink:href="<?php echo TEMPLATE_PATH; ?>customer/img/sprite.svg#icon-eye-close"></use>
                                            </svg>
                                            <span class="lightgrey"><?php echo $article['views_formatted']; ?></span>
                                        </div>
                                    <?php
                                    endif;
                                    if ($hesk_settings['kb_rating']): ?>
                                        <?php echo hesk3_get_customer_rating($article['rating']); ?>
                                        <?php if ($hesk_settings['kb_views']) echo '<span class="lightgrey">('.$article['votes_formatted'].')</span>'; ?>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </a>
                        <!--[if IE]>
                            <p>&nbsp;</p>
                        <![endif]-->
                    <?php endforeach; ?>
                </article>
            </div>
        </div>
<?php
/* Print Footer */
require_once(TEMPLATE_PATH . 'customer/inc/footer.inc.php');
?>
