<?php
$this->pageTitle=Yii::app()->name . ' - '.Yii::t('common', 'Erreur sur la page');
$this->breadcrumbs=array(
	Yii::t('common', 'Erreur').' '.$code,
);
?>

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
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css" integrity="sha512-dTfge/zgoMYpP7QbHy4gWMEGsbsdZeCXz7irItjcC3sPUFtf0kuFbDz/ixG7ArTxmDjLXDmezHubeNikyKGVyQ==" crossorigin="anonymous">

    <!-- Custom styling -->
    <link href="<?php echo Yii::app()->request->baseUrl; ?>/css/error.css" rel="stylesheet">

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
        <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>

<body>

<div class="container">

    <div class="col-xs-12">
        <div class="col-middle">
            <div class="text-center text-center">
                <h1 class="error-number"><?php echo $code; ?></h1>
                <h2>
                	<?php 
                	echo Yii::t('common', 'Erreur').' '.$code; 
                	if ($code == "404")
                		echo ' - '.Yii::t('common', 'La page demandée nexiste pas ou a été supprimée'); 
                	elseif($code == "500")
                		echo ' - '.Yii::t('common', 'Une erreur interne s’est produite'); 
                	?>
                </h2>
                <p><?php echo CHtml::encode($message); ?></p>
            </div>
        </div>
    </div>

</div>

</body>

</html>