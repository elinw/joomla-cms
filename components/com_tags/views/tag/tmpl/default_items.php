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
	<p> <?php echo JText::_('COM_TAGS_NO_ITEMS'); ?></p>
<?php else : ?>

		<ul class="category list-striped list-condensed">
			<?php foreach ($this->items as $item) : ?>
				<?php
				if ((!empty($item->access)) && in_array($item->access, $this->user->getAuthorisedViewLevels())) : ?>
					<?php if ($item->published == 0) : ?>
						<li class="system-unpublished cat-list-row<?php echo $i % 2; ?>">
					<?php else: ?>
						<li class="cat-list-row<?php echo $i % 2; ?>" >
						<?php  echo '<a href="'. JRoute::_($item->urlprefix . $item->id) .'">'
							. $item->title . '</a>';  ?>
					<?php endif; ?>
					<?php  if ($this->state->get('show_link_hits', 1)) : ?>
						<span class="list-hits badge badge-info pull-right">
							<?php echo JText::sprintf('JGLOBAL_HITS_COUNT', $item->hits); ?>
						</span>
					<?php endif; ?>
					<?php  if ($this->state->get('show_item_body', 1)) : ?>
						<span class="tag-body">
							<?php echo $item->body; ?>
						</span>
					<?php endif; ?>

						</li>
				<?php  endif;?>
			<?php endforeach; ?>
		</ul>


		<?php if ($this->state->get('show_pagination')) : ?>
		 <div class="pagination">
			<?php if ($this->params->def('show_pagination_results', 1)) : ?>
				<p class="counter">
					<?php echo $this->pagination->getPagesCounter(); ?>
				</p>
			<?php endif; ?>
				<?php echo $this->pagination->getPagesLinks(); ?>
			</div>
		<?php endif; ?>

<?php endif; ?>
