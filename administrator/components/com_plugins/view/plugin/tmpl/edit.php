<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_plugins
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

JHtml::_('behavior.formvalidation');
JHtml::_('formbehavior.chosen', 'select');

$script = "Joomla.submitbutton = function(task)
        {
                        if (task == 'j.cancel' || document.formvalidator.isValid(document.id('plugin-form'))) {";

$script .= "        Joomla.submitform(task, document.getElementById('plugin-form'));
                        }
        };";

JFactory::getDocument()->addScriptDeclaration($script);
?>
<form action="<?php echo JRoute::_('index.php?option=com_plugins&layout=edit&extension_id='.(int)  $this->form->getValue('extension_id')); ?>" method="post" name="adminForm" id="plugin-form" class="form-validate form-horizontal">

<fieldset>
		<?php echo JHtml::_('bootstrap.startTabSet', 'myTab', array('active' => 'details')); ?>
			<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'details', JText::_('JDETAILS', true)); ?>
				<div class="control-group">
					<div class="control-label">
						<?php echo $this->form->getLabel('name'); ?>
					</div>
					<div class="controls">
						<?php echo $this->form->getInput('name'); ?>

					</div>
				</div>
				<div class="control-group">
					<div class="control-label">
						<?php echo $this->form->getLabel('enabled'); ?>
					</div>
					<div class="controls">
						<?php echo $this->form->getInput('enabled'); ?>
					</div>
				</div>
				<div class="control-group">
					<div class="control-label">
						<?php echo $this->form->getLabel('access'); ?>
					</div>
					<div class="controls">
						<?php echo $this->form->getInput('access'); ?>
					</div>
				</div>
				<div class="control-group">
					<div class="control-label">
						<?php echo $this->form->getLabel('ordering'); ?>
					</div>
					<div class="controls">
						<?php echo $this->form->getInput('ordering'); ?>
					</div>
				</div>
				<div class="control-group">
					<div class="control-label">
						<?php echo $this->form->getLabel('folder'); ?>
					</div>
					<div class="controls">
						<?php echo $this->form->getInput('folder'); ?>
					</div>
				</div>
				<div class="control-group">
					<div class="control-label">
						<?php echo $this->form->getLabel('element'); ?>
					</div>
					<div class="controls">
						<?php echo $this->form->getInput('element'); ?>
					</div>
				</div>
				<?php if ($this->form->getValue('extension_id')) : ?>
					<div class="control-group">
						<div class="control-label">
							<?php echo $this->form->getLabel('extension_id'); ?>
						</div>
						<div class="controls">
							<?php echo $this->form->getInput('extension_id'); ?>
						</div>
					</div>
				<?php endif; ?>
				<!-- Plugin metadata -->
				<?php if ($this->item->get('xml')) : ?>
					<?php if ($text = trim($this->item->xml->description)) : ?>
						<div class="control-group">
							<label id="jform_extdescription-lbl" class="control-label">
								<?php echo JText::_('JGLOBAL_DESCRIPTION'); ?>
							</label>
							<div class="controls disabled">
								<?php echo JText::_($text); ?>
							</div>
						</div>
					<?php endif; ?>
				<?php else : ?>
					<div class="alert alert-error">
						<?php echo JText::_('COM_PLUGINS_XML_ERR'); ?>
					</div>
				<?php endif; ?>
			<?php echo JHtml::_('bootstrap.endTab'); ?>

			<?php echo $this->loadTemplate('options'); ?>

		<?php echo JHtml::_('bootstrap.endTabSet'); ?>
	</fieldset>

	<input type="hidden" name="task" value="" />
	<?php echo JHtml::_('form.token'); ?>
</form>
