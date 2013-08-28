<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  Template.hathor
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
?>
<<<<<<< HEAD:administrator/components/com_config_old/views/application/tmpl/default_ftp.php
<fieldset class="form-horizontal">
	<legend><?php echo JText::_('COM_CONFIG_FTP_SETTINGS'); ?></legend>
	<?php
	foreach ($this->form->getFieldset('ftp') as $field):
	?>
		<div class="control-group">
			<div class="control-label"><?php echo $field->label; ?></div>
			<div class="controls"><?php echo $field->input; ?></div>
		</div>
	<?php
	endforeach;
	?>
</fieldset>
=======

<li class="button" <?php echo $displayData['id']; ?>>
	<?php echo $displayData['action']; ?>
</li>
>>>>>>> 7901c01bf47563cf5c541b207d39e668ff997164:administrator/templates/hathor/html/layouts/joomla/toolbar/base.php
