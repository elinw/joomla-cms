<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_tags
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

JHtml::_('behavior.formvalidation');
JHtml::_('formbehavior.chosen', 'select');
$fieldset = $this->form->getFieldsets();
$params = $this->state->get('params');
$saveHistory = $params->get('save_history');

$script = "Joomla.submitbutton = function(task)
        {
                        if (task == 'j.cancel' || document.formvalidator.isValid(document.id('tag-form'))) {";

$script .= "        Joomla.submitform(task, document.getElementById('tag-form'));
                        }
        };";

JFactory::getDocument()->addScriptDeclaration($script);
?>
<form action="<?php echo JRoute::_('index.php?option=com_tags&layout=edit&id=' . (int) $this->id); ?>" method="post" name="adminForm" id="tag-form" class="form-validate">

	<?php echo JLayoutHelper::render('joomla.edit.title_alias', $this->model); ?>

	<div class="form-horizontal">
		<?php echo JHtml::_('bootstrap.startTabSet', 'myTab', array('active' => 'details')); ?>

		<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'details', JText::_('COM_TAGS_FIELDSET_DETAILS', true)); ?>
		<div class="row-fluid">
			<div class="span9">
				<div class="form-vertical">
					<?php echo $this->form->getControlGroup('description'); ?>
				</div>
			</div>
			<div class="span3">
			<?php  $fields  =  array(
				array('parent', 'parent_id'),
				array('published', 'state', 'enabled'),
				'access',
				'language',
				'note',
				'version_note');  ?>
				<?php //do this directly or solve problem with the data

			$hiddenFields = array();

			// Multilanguage check:
			/*if (!JLanguageMultilang::isEnabled())
			 {
				$hiddenFields[] = 'language';
			}*/
			if (!$saveHistory)
			{
				$hiddenFields[] = 'version_note';
			}

			$html = array();
			$html[] = '<fieldset class="form-vertical">';

			foreach ($fields as $field)
			{
				$field = is_array($field) ? $field : array($field);
				foreach ($field as $f)
				{
					if ($this->form->getField($f))
					{
						if (in_array($f, $hiddenFields))
						{
							$this->form->setFieldAttribute($f, 'type', 'hidden');
						}

						$html[] = $this->form->getControlGroup($f);
						break;
					}
				}
			}

			$html[] = '</fieldset>';
			echo implode('', $html); ?>
			</div>
		</div>
		<?php echo JHtml::_('bootstrap.endTab'); ?>

		<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'publishing', JText::_('JGLOBAL_FIELDSET_PUBLISHING', true)); ?>
		<div class="row-fluid form-horizontal-desktop">
			<div class="span6">
				<?php echo JLayoutHelper::render('joomla.edit.publishingdata', $this->model); ?>
			</div>
			<div class="span6">
				<?php echo JLayoutHelper::render('joomla.edit.metadata', $this->model); ?>
			</div>
		</div>
		<?php echo JHtml::_('bootstrap.endTab'); ?>

		<?php echo JLayoutHelper::render('joomla.edit.params', $this->model); ?>

	</div>
	<input type="hidden" name="task" value="" />
	<?php echo JHtml::_('form.token'); ?>
</form>
