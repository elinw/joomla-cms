<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_tags
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT.'/helpers');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.framework');

// Get the user object.
$user = JFactory::getUser();

// Check if user is allowed to add/edit based on tags permissions.
$canEdit = $user->authorise('core.edit', 'com_tags');
$canCreate = $user->authorise('core.create', 'com_tags');
$canEditState = $user->authorise('core.edit.state', 'com_tags');

$n = count($this->items);
?>

<?php if ($this->items == false || $n == 0) : ?>
	<p> <?php echo JText::_('COM_TAGS_NO_TAGS'); ?></p>
<?php else : ?>
		<ul class="category list-striped list-condensed">
			<?php foreach ($this->items as $i => $item) : ?>
				<?php if ($item->parent_id != 0) : ?>
				<div>
					<?php if ((!empty($item->access)) && in_array($item->access, $this->user->getAuthorisedViewLevels())) : ?>
						<?php if ($item->published == 0) : ?>
							<li class="system-unpublished cat-list-row<?php echo $i % 2; ?>">
						<?php else: ?>
							<li class="cat-list-row<?php echo $i % 2; ?>" >
							<?php  echo '<h3> <a href="' . JRoute::_(TagsHelperRoute::getTagRoute($item->id))  .'">'
								. $this->escape($item->title) . '</a> </h3>';  ?>
						<?php endif; ?>
						<?php  if ($this->state->get('show_link_hits', 1)) : ?>
							<span class="list-hits badge badge-info pull-right">
								<?php echo JText::sprintf('JGLOBAL_HITS_COUNT', $item->hits); ?>
							</span>
						<?php endif; ?>
						<?php  if ($this->state->get('show_item_image', 1) && !empty($item->images)) : ?>
						<?php  $images  = json_decode($item->images); ?>
						<span class="tag-body">
							<?php $imgfloat = (empty($images->float_intro)) ? $this->state->get('float_intro') : $images->float_intro; ?>
							<div class="pull-<?php echo htmlspecialchars($imgfloat); ?> item-image"> <img
							<?php if ($images->image_intro_caption):
								echo 'class="caption"'.' title="' . htmlspecialchars($images->image_intro_caption) . '"';
							endif; ?>
							src="<?php echo htmlspecialchars($images->image_intro); ?>" alt="<?php echo htmlspecialchars($images->image_fulltext_alt); ?>"/> </div>
						</span>
						<?php endif; ?>
						<?php  if ($this->state->get('show_item_body', 1)) : ?>
							<span class="tag-body">
								<?php echo $item->description; ?>
							</span>
						<?php endif; ?>
							</li>
					<?php  endif;?></div>
				<?php  endif;?>
			<?php endforeach; ?>
		</ul>

	<?php  endif;?>