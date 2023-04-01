<?php $this->beginContent('//adminLayouts/main'); ?>

<?php if(isset($this->breadcrumbs)):?>
    <?php $this->widget('zii.widgets.CBreadcrumbs', array(
        'links'=>$this->breadcrumbs,
    )); ?>
<?php endif ?>
<div class="page-title">
    <h3><?php echo $this->title; ?></h3>
</div>
<div>
    <?php echo $content; ?>
</div>

<?php $this->endContent(); ?>