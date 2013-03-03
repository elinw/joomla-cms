<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_weblinks
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

?>
<div class="categories-list<?php echo $this->pageclass_sfx;?>">
<?php if ($displayData->params->get('show_page_heading')) : ?>
<h1>
	<?php echo $displayData->escape($displayData->params->get('page_heading')); ?>
</h1>
<?php endif; ?>

<?php if ($displayData->params->get('show_base_description')) : ?>
	<?php 	//If there is a description in the menu parameters use that; ?>
		<?php if($displayData->params->get('categories_description')) : ?>
			<div class="category-desc base-desc">
			<?php echo JHtml::_('content.prepare', $displayData->params->get('categories_description'), '',  $displayData->extension . '.categories'); ?>
			</div>
		<?php  else: ?>
			<?php //Otherwise get one from the database if it exists. ?>
			<?php  if ($displayData->parent->description) : ?>
				<div class="category-desc base-desc">
					<?php echo JHtml::_('content.prepare', $displayData->parent->description, '', $displayData->extension . '.categories'); ?>
				</div>
			<?php  endif; ?>
		<?php  endif; ?>
	<?php endif; ?>

