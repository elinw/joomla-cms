<?php

$columns = $this->params->get('compact_columns', 1);
$bsspans = floor(12 / $columns );
if ($bsspans < 1):
	$bsspans = 1;
endif;
$bscolumns = floor( 12 / $bsspans);

$maximum = $this->params->get('compact_maximum', 200);
$maxrows = ceil( $maximum / $bsspans);
$n = count($this->items);
?>
<?php if ($this->items == false || $n == 0) : ?>
	<p> <?php echo JText::_('COM_TAGS_NO_ITEMS'); ?></p>
<?php else : ?>
	<?php $j = 0; ?>
	<?php foreach ($this->items as $i => $item) : ?>
		<?php if ($item->parent_id == 0): ?>
			<?php $items = array_splice($this->items, $i, 1);?>
		<?php endif; ?>
		<?php $j++ ;?>
	<?php endforeach; ?>

	<?php foreach ($this->items as $i=>$item) : ?>
		<?php if ($n == 1 || $i == 0 || $bscolumns == 1 || ($i != 1 && ($i) % $bscolumns   == 0)) : ?>
			<?php if ($i == 0) :?>
				<div class="row-fluid cat-list-row0">
			<?php else : ?>
				<div class="row-fluid cat-list-row<?php echo  ($bscolumns / $i) % 2 ; ?>">
			<?php endif; ?>
		<?php endif; ?>
				<div class="span<?php echo $bsspans;?>">
					<?php  echo '<h3> <a href="' . JRoute::_(TagsHelperRoute::getTagRoute($item->id))  .'">'
						. $this->escape($item->title) . '</a> </h3>';  ?>
				</div>
			<?php  if  ( ($i == 0 && $n == 1) || $i == $n-2 || $bscolumns == 1 || $i + 1 == $bscolumns || ( ($i+1) % $bscolumns  == 0))  :  ?>
				</div>
			<?php endif; ?>

		<?php $i++ ?>
	<?php endforeach;?>

	<?php endif; ?>

	<div class="clearfix"></div>