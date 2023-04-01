<?php $this->beginContent('//adminLayouts/main'); ?>

<?php if(isset($this->breadcrumbs)):?>
    <?php $this->widget('zii.widgets.CBreadcrumbs', array(
        'links'=>$this->breadcrumbs,
    )); ?>
<?php endif ?>
<div class="page-title">
    <div class="title_right col-sm-4 col-lg-3 col-xs-12 pull-right">
        <div>
            <?php
            $this->beginWidget('zii.widgets.CPortlet', array(
                'title'=>'Operations',
            ));
            $this->widget('zii.widgets.CMenu', array(
                'items'=>$this->menu,
                'htmlOptions'=>array('class'=>'operations'),
            ));
            $this->endWidget();
            ?>
        </div>
    </div>
    <div class="title_left col-sm-8 col-lg-9 col-xs-12">
        <h3><?php echo $this->title; ?></h3>
    </div>
</div>
<div class="row">
    <div class="col-md-12 col-sm-12 col-xs-12">
        <?php echo $content; ?>
    </div>
</div>

<?php $this->endContent(); ?>