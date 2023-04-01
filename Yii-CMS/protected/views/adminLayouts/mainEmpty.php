<?php Yii::app()->clientScript->registerCoreScript('jquery'); ?>
<?php Yii::app()->clientScript->registerCoreScript('jquery.ui'); ?>
<?php Yii::app()->clientScript->registerScriptFile(Yii::app()->request->baseUrl.'/javascript/admin/bootstrap.min.js', CClientScript::POS_END); ?>
<?php Yii::app()->clientScript->registerScriptFile(Yii::app()->request->baseUrl.'/javascript/admin/progressbar/bootstrap-progressbar.min.js', CClientScript::POS_END); ?>
<?php Yii::app()->clientScript->registerScriptFile(Yii::app()->request->baseUrl.'/javascript/admin/nicescroll/jquery.nicescroll.min.js', CClientScript::POS_END); ?>
<?php Yii::app()->clientScript->registerScriptFile(Yii::app()->request->baseUrl.'/javascript/admin/moment-with-locales.min.js', CClientScript::POS_END); ?>
<?php Yii::app()->clientScript->registerScriptFile(Yii::app()->request->baseUrl.'/javascript/admin/datetimepicker/bootstrap-datetimepicker.js', CClientScript::POS_END); ?>
<?php Yii::app()->clientScript->registerScriptFile(Yii::app()->request->baseUrl.'/javascript/admin/custom.js', CClientScript::POS_END); ?>
<?php Yii::app()->clientScript->registerScriptFile(Yii::app()->request->baseUrl.'/javascript/admin/screenfull/screenfull.min.js', CClientScript::POS_END); ?>
<?php Yii::app()->clientScript->registerScriptFile(Yii::app()->request->baseUrl.'/javascript/admin/jquery.blockUI.js', CClientScript::POS_END); ?>
<?php Yii::app()->clientScript->registerScriptFile(Yii::app()->request->baseUrl.'/javascript/admin/select/select2.full.js', CClientScript::POS_END); ?>
<?php Yii::app()->clientScript->registerScriptFile(Yii::app()->request->baseUrl.'/javascript/admin/icheck/icheck.min.js', CClientScript::POS_END); ?>

<!DOCTYPE html>
<html lang="<?php echo CHtml::encode(Yii::app()->language); ?>">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <!-- Meta, title, CSS, favicons, etc. -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title><?php echo CHtml::encode($this->pageTitle); ?></title>

    <link href="<?php echo Yii::app()->request->baseUrl; ?>/images/favicon.png" rel="shortcut icon">

    <!-- Bootstrap and core CSS -->
    <link href="<?php echo Yii::app()->request->baseUrl; ?>/css/admin/bootstrap.min.css" rel="stylesheet">
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css" rel="stylesheet">
    <link href="<?php echo Yii::app()->request->baseUrl; ?>/css/admin/animate.min.css" rel="stylesheet">

    <!-- Datetime picker -->
    <link rel="stylesheet" href="<?php echo Yii::app()->request->baseUrl; ?>/css/admin/datetimepicker/bootstrap-datetimepicker.css" />

    <!-- Custom styling -->
    <link href="<?php echo Yii::app()->request->baseUrl; ?>/css/admin/custom.css" rel="stylesheet">

    <!-- Checkboxes -->
    <link href="<?php echo Yii::app()->request->baseUrl; ?>/css/admin/icheck/flat/green.css" rel="stylesheet">

    <!-- select2 -->
    <link href="<?php echo Yii::app()->request->baseUrl; ?>/css/admin/select/select2.min.css" rel="stylesheet">

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
        <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>

<body class="nav-md" id="page-<?php echo preg_replace('/[^A-Za-z0-9_\-\.]/', '-', Yii::app()->controller->route); ?>">

<?php echo $content; ?>

</body>

</html>