<?php
/**
 * Variables available:
 * - $app_id: App ID
 * - $pager: pager html
 * 
 */

?>
<div id="text-container" class="cmtls-articles-container">
    
	<div class="toolbar">
	    <?php if(cmtls_group_can_create_content($cmtls['current_group'], $user)): print l('+ '.t('Add article'), 'cmtls/'.$cmtls['current_group']->nid.'/'.$cmtls['current_app']->nid.'/article/add', array('attributes' => array('class' => 'button modalframe-child', 'id' => 'add-article-button', 'modal_frame_width' => 820))) ?><?php endif; ?>

	    <?php /*
	    <div class="content-filter-toggle">
			<a href="#" class="button">v <?php print t('Latest posts'); ?></a>
	    </div>
	    */ ?>
	</div> <!-- toolbar -->
    
    <div id="cmtls-content-container-append">
        <?php print views_embed_view('cmtls_articles', 'default', $app_id); ?>
    </div>

    <?php print $pager; ?>
    
</div>