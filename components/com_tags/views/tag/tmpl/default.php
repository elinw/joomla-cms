<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_tags
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
// Note that there are certain parts of this layout used only when there is exactly one tag.

JHtml::addIncludePath(JPATH_COMPONENT.'/helpers');

?>
<div class="tag-category<?php echo $this->pageclass_sfx; ?>">
<?php  if ($this->state->get('show_page_heading')) : ?>
<h1>
	<?php echo $this->escape($this->params->get('page_heading')); ?>
</h1>
<?php endif;  ?>
<?php if($this->state->get('show_tag_title', 1)) : ?>
<h2>
	<?php echo JHtml::_('content.prepare', $this->document->title, '', 'com_tag.tag'); ?>
</h2>
<?php endif; ?>
<?php // We only show a tag description if there is a single tag. ?>
<?php  if (count($this->item) == 1 && ($this->state->params->get('show_description', 1) || $this->state->params->get('show_description_image', 1))) : ?>
	<div class="category-desc"><?php $this->state->params->get('show_description_image', 1); ?>
	<?php  if ($this->state->params->get('show_description_image', 1)== 1 && $this->item[0]->images) :?>
		<img src="<?php echo $this->item[0]->get('images');?>">
	<?php endif; ?>
	<?php if ($this->state->params->get('show_description') == 1 && $this->item[0]->description) : ?>
		<?php echo JHtml::_('content.prepare', $this->item[0]->description, '', 'com_tags.tag'); ?>
	<?php endif; ?>
	<div class="clr"></div>
	</div>
<?php endif; ?>

<?php echo $this->loadTemplate('items'); ?>

</div>
