<?php $delim = '';?>
<?php if(count($BREADCRUMBS)>1):?>
	<h4><?php foreach ($BREADCRUMBS as $row):?>
		<?php echo $delim;?> <a href="<?php echo $BASE_URL;?>category/show/catId=<?php echo $row['id'];?>"><?php echo $row['name'];?></a>
		<?php $delim = '>'; ?> 
	<?php endforeach; ?></h4>
<?php else: ?>
	<a href="<?php echo $BASE_URL;?>category/ulist">[Show All]</a>
<?php endif; ?>
