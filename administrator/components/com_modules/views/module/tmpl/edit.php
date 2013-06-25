<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_modules
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.combobox');
JHtml::_('formbehavior.chosen', 'select');

$hasContent = empty($this->item->module) || $this->item->module == 'custom' || $this->item->module == 'mod_custom';

// Get Params Fieldsets
$this->fieldsets = $this->form->getFieldsets('params');


$script = "Joomla.submitbutton = function(task)
	{
			if (task == 'module.cancel' || document.formvalidator.isValid(document.id('module-form'))) {";
if ($hasContent)
{
	$script .= $this->form->getField('content')->save();
}
$script .= "	Joomla.submitform(task, document.getElementById('module-form'));
				if (self != top)
				{
					window.top.setTimeout('window.parent.SqueezeBox.close()', 1000);
				}
			}
	}";

JFactory::getDocument()->addScriptDeclaration($script);
?>
<form action="<?php echo JRoute::_('index.php?option=com_modules&layout=edit&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="module-form" class="form-validate form-horizontal">
	<fieldset>
		<ul class="nav nav-tabs">
			<li class="active"><a href="#details" data-toggle="tab"><?php echo JText::_('JDETAILS'); ?></a></li>
			<li><a href="#options" data-toggle="tab"><?php echo JText::_('JOPTIONS'); ?></a></li>

			<?php if ($hasContent) : ?>
				<li><a href="#custom" data-toggle="tab"><?php echo JText::_('COM_MODULES_CUSTOM_OUTPUT'); ?></a></li>
			<?php endif; ?>

			<li><?php echo $this->form->getLabel('access'); ?>
			<?php echo $this->form->getInput('access'); ?></li>

			<?php if ($this->canDo->get('core.admin')): ?>
				<li><span class="faux-label"><?php echo JText::_('JGLOBAL_ACTION_PERMISSIONS_LABEL'); ?></span>
					<div class="button2-left"><div class="blank">
						<button type="button" onclick="document.location.href='#access-rules';">
							<?php echo JText::_('JGLOBAL_PERMISSIONS_ANCHOR'); ?>
						</button>
					</div></div>
				</li>
			<?php endif; ?>

			<li><?php echo $this->form->getLabel('ordering'); ?>
			<?php echo $this->form->getInput('ordering'); ?></li>

			<?php if ((string) $this->item->xml->name != 'Login Form'): ?>
			<li><?php echo $this->form->getLabel('publish_up'); ?>
			<?php echo $this->form->getInput('publish_up'); ?></li>

			<li><?php echo $this->form->getLabel('publish_down'); ?>
			<?php echo $this->form->getInput('publish_down'); ?></li>

			<?php endif; ?>
		</ul>

		<div class="tab-content">
			<div class="tab-pane active" id="details">
				<div class="row-fluid">
					<div class="span6">
						<div class="control-group">
							<div class="control-label">
								<?php echo $this->form->getLabel('title'); ?>
							</div>
							<div class="controls">
								<?php echo $this->form->getInput('title'); ?>
							</div>
						</div>
						<div class="control-group">
							<div class="control-label">
								<?php echo $this->form->getLabel('showtitle'); ?>
							</div>
							<div class="controls">
								<?php echo $this->form->getInput('showtitle'); ?>
							</div>
						</div>
						<div class="control-group">
							<div class="control-label">
								<?php echo $this->form->getLabel('position'); ?>
							</div>
							<div class="controls">
								<?php echo $this->loadTemplate('positions'); ?>
							</div>
						</div>
						<hr />
						<?php if ((string) $this->item->xml->name != 'Login Form') : ?>
							<div class="control-group">
								<div class="control-label">
									<?php echo $this->form->getLabel('published'); ?>
								</div>
								<div class="controls">
									<?php echo $this->form->getInput('published'); ?>
								</div>
							</div>
						<?php endif; ?>
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
						<?php if ((string) $this->item->xml->name != 'Login Form') : ?>
							<div class="control-group">
								<div class="control-label">
									<?php echo $this->form->getLabel('publish_up'); ?>
								</div>
								<div class="controls">
									<?php echo $this->form->getInput('publish_up'); ?>
								</div>
							</div>
							<div class="control-group">
								<div class="control-label">
									<?php echo $this->form->getLabel('publish_down'); ?>
								</div>
								<div class="controls">
									<?php echo $this->form->getInput('publish_down'); ?>
								</div>
							</div>
						<?php endif; ?>

						<div class="control-group">
							<div class="control-label">
								<?php echo $this->form->getLabel('language'); ?>
							</div>
							<div class="controls">
								<?php echo $this->form->getInput('language'); ?>
							</div>
						</div>
						<div class="control-group">
							<div class="control-label">
								<?php echo $this->form->getLabel('note'); ?>
							</div>
							<div class="controls">
								<?php echo $this->form->getInput('note'); ?>
							</div>
						</div>
					</div>
					<div class="span6">
						<?php if ($this->item->xml) : ?>
							<?php if ($text = trim($this->item->xml->description)) : ?>
								<blockquote>
									<h4>
										<?php echo JText::_('COM_MODULES_MODULE_DESCRIPTION'); ?>
										<?php if ($this->item->id) : ?>
											<span class="label label-info"><?php echo JText::_('JGRID_HEADING_ID'); ?> : <?php echo $this->item->id; ?></span>
										<?php endif; ?>
									</h4>
									<hr />
									<div>
										<?php echo JText::_($text); ?>
									</div>
									<hr />
									<div>
										<span class="label"><?php echo $this->item->client_id == 0 ? JText::_('JSITE') : JText::_('JADMINISTRATOR'); ?></span> / <span class="label"><?php if ($this->item->xml) echo ($text = (string) $this->item->xml->name) ? JText::_($text) : $this->item->module;else echo JText::_('COM_MODULES_ERR_XML');?></span>
									</div>
								</blockquote>
							<?php endif; ?>
						<?php else : ?>
							<div class="alert alert-error"><?php echo JText::_('COM_MODULES_ERR_XML'); ?></div>
						<?php endif; ?>
					</div>
				</div>
			</div>
			<div class="tab-pane" id="options">
				<?php echo $this->loadTemplate('options'); ?>
			</div>

			<?php if ($hasContent) : ?>
			<div class="tab-pane" id="custom">
				<?php echo $this->form->getInput('content'); ?>
			</div>
			<?php endif; ?>
			<?php if ($this->item->client_id == 0) : ?>
				<div class="tab-pane" id="assignment">
					<?php echo $this->loadTemplate('assignment'); ?>
				</div>
			<?php endif; ?>
		</div>

	<?php if ($this->canDo->get('core.admin')): ?>
		<div class="width-100 fltlft">
			<?php echo JHtml::_('sliders.start', 'permissions-sliders-'.$this->item->id, array('useCookie'=>1)); ?>

				<?php echo JHtml::_('sliders.panel', JText::_('COM_MODULES_FIELDSET_RULES'), 'access-rules'); ?>
				<fieldset class="panelform">
					<?php echo $this->form->getLabel('rules'); ?>
					<?php echo $this->form->getInput('rules'); ?>
				</fieldset>

			<?php echo JHtml::_('sliders.end'); ?>
		</div>
	<?php endif; ?>
	<div>

		<input type="hidden" name="task" value="" />
		<?php echo JHtml::_('form.token'); ?>
		<?php echo $this->form->getInput('module'); ?>
		<?php echo $this->form->getInput('client_id'); ?>
	</fieldset>
</form>
