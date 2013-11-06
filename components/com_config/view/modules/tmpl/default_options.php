<?php
/**
 * @package     Joomla.site
 * @subpackage  com_config
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
?>
<?php

	$fieldSets = $this->form->getFieldsets('params');

	echo JHtml::_('bootstrap.startAccordion', 'collapseTypes', array('active' => 'slide1'));
	$i = 0;
	
	foreach ($fieldSets as $name => $fieldSet) :
	
	$label = !empty($fieldSet->label) ? $fieldSet->label : 'COM_MODULES_'.$name.'_FIELDSET_LABEL';
	$class = isset($fieldSet->class) && !empty($fieldSet->class) ? $fieldSet->class : '';
	
	
	if (isset($fieldSet->description) && trim($fieldSet->description)) :
	echo '<p class="tip">'.$this->escape(JText::_($fieldSet->description)).'</p>';
	endif;
?>
<?php 

echo JHtml::_('bootstrap.addSlide', 'collapseTypes', ucfirst($name), 'collapse' . $i++);
?>

<ul class="nav nav-tabs nav-stacked">
<?php 	
	foreach ($this->form->getFieldset($name) as $field) :

?>
	
	<li>
	
	<div class="control-group">
		<div class="control-label">
			<?php echo $field->label; ?>
		</div>
		<div class="controls">
			<?php echo $field->input; ?>
		</div>
	</div>
	
	</li>
	
<?php endforeach; ?>

</ul>

<?php echo JHtml::_('bootstrap.endSlide'); ?>

<?php

endforeach;

echo JHtml::_('bootstrap.endAccordion');
