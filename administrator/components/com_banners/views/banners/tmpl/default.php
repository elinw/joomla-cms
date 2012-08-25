<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_banners
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');
JHtml::_('jquerybehavior.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('dropdown.init');

$user		= JFactory::getUser();
$userId		= $user->get('id');
$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));
$canOrder	= $user->authorise('core.edit.state', 'com_banners.category');
$archived	= $this->state->get('filter.published') == 2 ? true : false;
$trashed	= $this->state->get('filter.published') == -2 ? true : false;
$params		= (isset($this->state->params)) ? $this->state->params : new JObject;
$saveOrder	= $listOrder == 'ordering';
if ($saveOrder)
{
	$saveOrderingUrl = 'index.php?option=com_banners&task=banners.saveOrderAjax&tmpl=component';
	JHtml::_('sortablelist.sortable', 'articleList', 'adminForm', strtolower($listDirn), $saveOrderingUrl);
}
$sortFields = $this->getSortFields();
?>
<script type="text/javascript">
	Joomla.orderTable = function() {
		table = document.getElementById("sortTable");
		direction = document.getElementById("directionTable");
		order = table.options[table.selectedIndex].value;
		if (order != '<?php echo $listOrder; ?>') {
			dirn = 'asc';
		} else {
			dirn = direction.options[direction.selectedIndex].value;
		}
		Joomla.tableOrdering(order, dirn, '');
	}
</script>
<form action="<?php echo JRoute::_('index.php?option=com_banners&view=banners'); ?>" method="post" name="adminForm" id="adminForm">
	<div id="filter-bar" class="btn-toolbar">
		<div class="filter-search btn-group pull-left">
			<label for="filter_search" class="element-invisible"><?php echo JText::_('COM_BANNERS_SEARCH_IN_TITLE');?></label>
			<input type="text" name="filter_search" id="filter_search" placeholder="<?php echo JText::_('COM_BANNERS_SEARCH_IN_TITLE'); ?>" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" title="<?php echo JText::_('COM_BANNERS_SEARCH_IN_TITLE'); ?>" />
		</div>
		<div class="btn-group pull-left">
			<button type="submit" class="btn" rel="tooltip" title="<?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?>"><i class="icon-search"></i></button>
			<button type="button" class="btn" rel="tooltip" title="<?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?>" onclick="document.id('filter_search').value='';this.form.submit();"><i class="icon-remove"></i></button>
		</div>
		<div class="btn-group pull-right hidden-phone">
			<label for="limit" class="element-invisible"><?php echo JText::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC');?></label>
			<?php echo $this->pagination->getLimitBox(); ?>
		</div>
		<div class="btn-group pull-right hidden-phone">
			<label for="directionTable" class="element-invisible"><?php echo JText::_('JFIELD_ORDERING_DESC');?></label>
			<select name="directionTable" id="directionTable" class="input-small" onchange="Joomla.orderTable()">
				<option value=""><?php echo JText::_('JFIELD_ORDERING_DESC');?></option>
				<option value="asc" <?php if ($listDirn == 'asc') echo 'selected="selected"'; ?>><?php echo JText::_('JGLOBAL_ORDER_ASCENDING');?></option>
				<option value="desc" <?php if ($listDirn == 'desc') echo 'selected="selected"'; ?>><?php echo JText::_('JGLOBAL_ORDER_DESCENDING');?></option>
			</select>
		</div>
		<div class="btn-group pull-right">
			<label for="sortTable" class="element-invisible"><?php echo JText::_('JGLOBAL_SORT_BY');?></label>
			<select name="sortTable" id="sortTable" class="input-medium" onchange="Joomla.orderTable()">
				<option value=""><?php echo JText::_('JGLOBAL_SORT_BY');?></option>
				<?php echo JHtml::_('select.options', $sortFields, 'value', 'text', $listOrder);?>
			</select>
		</div>
	</div>
	<div class="clearfix"> </div>
	<table class="table table-striped" id="articleList">
		<thead>
			<tr>
				<th width="1%" class="center hidden-phone" nowrap="nowrap">
					<i class="icon-menu-2 hasTip" title="<?php echo JText::_('JGRID_HEADING_ORDERING'); ?>"></i>
				</th>
				<th width="1%" class="hidden-phone">
					<input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
				</th>
				<th width="5%" class="center">
					<?php echo JText::_('JSTATUS'); ?>
				</th>
				<th>
					<?php echo JText::_('COM_BANNERS_HEADING_NAME'); ?>
				</th>
				<th width="1%" class="hidden-phone">
					<?php echo JText::_('COM_BANNERS_HEADING_STICKY'); ?>
				</th>
				<th width="10%" class="hidden-phone">
					<?php echo JText::_('COM_BANNERS_HEADING_CLIENT'); ?>
				</th>
				<th width="10%" class="hidden-phone">
					<?php echo JText::_('COM_BANNERS_HEADING_IMPRESSIONS'); ?>
				</th>
				<th width="10%" class="hidden-phone">
					<?php echo JText::_('COM_BANNERS_HEADING_CLICKS'); ?>
				</th>
				<th width="10%" class="hidden-phone">
					<?php echo JText::_('JGRID_HEADING_LANGUAGE'); ?>
				</th>
				<th width="1%" class="hidden-phone">
					<?php echo JText::_('JGRID_HEADING_ID'); ?>
				</th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="13">
					<?php echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
		<tbody>
		<?php foreach ($this->items as $i => $item) :
			$ordering  = ($listOrder == 'ordering');
			$item->cat_link = JRoute::_('index.php?option=com_categories&extension=com_banners&task=edit&type=other&cid[]='. $item->catid);
			$canCreate  = $user->authorise('core.create',     'com_banners.category.' . $item->catid);
			$canEdit    = $user->authorise('core.edit',       'com_banners.category.' . $item->catid);
			$canCheckin = $user->authorise('core.manage',     'com_checkin') || $item->checked_out == $userId || $item->checked_out == 0;
			$canChange  = $user->authorise('core.edit.state', 'com_banners.category.' . $item->catid) && $canCheckin;
			?>
			<tr class="row<?php echo $i % 2; ?>" sortable-group-id="<?php echo $item->catid?>">
				<td class="order nowrap center hidden-phone">
				<?php if ($canChange) :
					$disableClassName = '';
					$disabledLabel	  = '';
					if (!$saveOrder) :
						$disabledLabel    = JText::_('JORDERINGDISABLED');
						$disableClassName = 'inactive tip-top';
					endif; ?>
					<span class="sortable-handler <?php echo $disableClassName?>" title="<?php echo $disabledLabel?>" rel="tooltip">
						<i class="icon-menu"></i>
					</span>
					<input type="text" style="display:none"  name="order[]" size="5"
						value="<?php echo $item->ordering;?>" class="width-20 text-area-order " />
				<?php else : ?>
					<span class="sortable-handler inactive" >
						<i class="icon-menu"></i>
					</span>
				<?php endif; ?>
				</td>
				<td class="center hidden-phone">
					<?php echo JHtml::_('grid.id', $i, $item->id); ?>
				</td>
				<td class="center">
					<?php echo JHtml::_('jgrid.published', $item->state, $i, 'banners.', $canChange, 'cb', $item->publish_up, $item->publish_down); ?>
				</td>
				<td class="nowrap has-context">
					<div class="pull-left">
						<?php if ($item->checked_out) : ?>
							<?php echo JHtml::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time, 'banners.', $canCheckin); ?>
						<?php endif; ?>
						<?php if ($canEdit) : ?>
							<a href="<?php echo JRoute::_('index.php?option=com_banners&task=banner.edit&id='.(int) $item->id); ?>">
								<?php echo $this->escape($item->name); ?></a>
						<?php else : ?>
							<?php echo $this->escape($item->name); ?>
						<?php endif; ?>
						<div class="small">
							<?php echo $this->escape($item->category_title); ?>
						</div>
					</div>
					<div class="pull-left">
						<?php
							// Create dropdown items
							JHtml::_('dropdown.edit', $item->id, 'banner.');
							JHtml::_('dropdown.divider');
							if ($item->state) :
								JHtml::_('dropdown.unpublish', 'cb' . $i, 'banners.');
							else :
								JHtml::_('dropdown.publish', 'cb' . $i, 'banners.');
							endif;

							JHtml::_('dropdown.divider');

							if ($archived) :
								JHtml::_('dropdown.unarchive', 'cb' . $i, 'banners.');
							else :
								JHtml::_('dropdown.archive', 'cb' . $i, 'banners.');
							endif;

							if ($item->checked_out) :
								JHtml::_('dropdown.checkin', 'cb' . $i, 'banners.');
							endif;

							if ($trashed) :
								JHtml::_('dropdown.untrash', 'cb' . $i, 'banners.');
							else :
								JHtml::_('dropdown.trash', 'cb' . $i, 'banners.');
							endif;

							// render dropdown list
							echo JHtml::_('dropdown.render');
							?>
					</div>
				</td>
				<td class="center hidden-phone">
					<?php echo JHtml::_('banner.pinned', $item->sticky, $i, $canChange); ?>
				</td>
				<td class="small hidden-phone">
					<?php echo $item->client_name;?>
				</td>
				<td class="small hidden-phone">
					<?php echo JText::sprintf('COM_BANNERS_IMPRESSIONS', $item->impmade, $item->imptotal ? $item->imptotal : JText::_('COM_BANNERS_UNLIMITED'));?>
				</td>
				<td class="center small hidden-phone">
					<?php echo $item->clicks;?> -
					<?php echo sprintf('%.2f%%', $item->impmade ? 100 * $item->clicks / $item->impmade : 0);?>
				</td>

				<td class="small nowrap hidden-phone">
					<?php if ($item->language == '*'):?>
						<?php echo JText::alt('JALL', 'language'); ?>
					<?php else:?>
						<?php echo $item->language_title ? $this->escape($item->language_title) : JText::_('JUNDEFINED'); ?>
					<?php endif;?>
				</td>
				<td class="center hidden-phone">
					<?php echo $item->id; ?>
				</td>
			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
	<?php echo $this->pagination->getListFooter(); ?>
	<?php //Load the batch processing form. ?>
	<?php echo $this->loadTemplate('batch'); ?>

	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
	<?php echo JHtml::_('form.token'); ?>
</form>
