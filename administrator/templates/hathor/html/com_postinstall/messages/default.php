<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_postinstall
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

$isComPostinstall   = (JFactory::getApplication()->input->getString('option') == 'com_postinstall');
$postInstallModules = JModuleHelper::getModules('postinstall');

$showSidebar = ($postInstallModules && $isComPostinstall);
?>

<div class="postinstall-messages">
	<div class="col <?php if ($showSidebar): ?>main-section<?php endif; ?>">
		<?php if (empty($this->items)): ?>
			<div class="hero-unit">
				<h2><?php echo JText::_('COM_POSTINSTALL_LBL_NOMESSAGES_TITLE') ?></h2>
				<p><?php echo JText::_('COM_POSTINSTALL_LBL_NOMESSAGES_DESC') ?></p>
				<a href="index.php?option=com_postinstall&view=messages&task=reset&eid=<?php echo $this->eid; ?>&<?php echo $this->token ?>=1" class="btn btn-warning btn-large">
					<span class="icon icon-eye-open"></span>
					<?php echo JText::_('COM_POSTINSTALL_BTN_RESET') ?>
				</a>
			</div>
		<?php else: ?>
			<h2><?php echo JText::_('COM_POSTINSTALL_LBL_MESSAGES') ?></h2>
			<?php foreach($this->items as $item): ?>
				<fieldset>
					<legend><?php echo JText::_($item->title_key) ?></legend>
					<p class="small">
						<?php echo JText::sprintf('COM_POSTINSTALL_LBL_SINCEVERSION', $item->version_introduced) ?>
					</p>
					<p><?php echo JText::_($item->description_key) ?></p>

					<div>
						<?php if ($item->type !== 'message'): ?>
						<a href="index.php?option=com_postinstall&view=messages&task=action&id=<?php echo $item->postinstall_message_id ?>&<?php echo $this->token ?>=1" class="btn btn-primary">
							<?php echo JText::_($item->action_key) ?>
						</a>
						<?php endif; ?>
						<a href="index.php?option=com_postinstall&view=message&task=unpublish&id=<?php echo $item->postinstall_message_id ?>&<?php echo $this->token ?>=1" class="btn btn-inverse btn-small">
							<?php echo JText::_('COM_POSTINSTALL_BTN_HIDE') ?>
						</a>
					</div>
				</fieldset>
			<?php endforeach; ?>
		<?php endif; ?>
	</div>
	<?php if ($showSidebar): ?>
		<div class="col options-section">
			<?php
			foreach ($postInstallModules as $new):
				echo JModuleHelper::renderModule($new, array('style' => 'xhtml'));
			endforeach;
			?>
		</div>
	<?php endif; ?>
</div>
