<?PHP
$jobTitle = CHtml::encode($job->title);

$this->breadcrumbs = Helper::breadcrumbsFromAlias(true);
$this->breadcrumbs[] = $job->title;
$this->pageTitle = Helper::titleFromBreadcrumbs();

$jobPublicationDate = Helper::formatDate($job->publication_date, "reg");
$jobStartDate = (isset($job->start_date) and $job->start_date != '0000-00-00') ? Helper::formatDate($job->start_date, "reg") : Yii::t('jobModule.common', 'Indéterminée');
$jobPostulationEndDate = (isset($job->postulation_end_date)) ? substr(Helper::formatDate($job->postulation_end_date, "reg+time"), 0, -3) : Yii::t('jobModule.common', 'Indéterminée');

switch ($job->type):
	case 1:
		$jobType = Yii::t('jobModule.common', 'Permanent');
		break;
	case 2:
		$jobType = Yii::t('jobModule.common', 'Temps partiel');
		break;
	case 3:
		$jobType = Yii::t('jobModule.common', 'Saisonnier');
		break;
	case 4:
		$jobType = $job->type;
		break;
	default:
		$jobType = Yii::t('jobModule.common', 'Indéterminé');
endswitch;

// facebook OG Meta
Yii::app()->facebook->ogTags['og:site_name'] = Yii::app()->name;
Yii::app()->facebook->ogTags['og:title'] = $job->title;
Yii::app()->facebook->ogTags['og:type'] = "object";
//Yii::app()->facebook->ogTags['og:image'] = "http://".Yii::app()->request->serverName.Yii::app()->request->baseUrl."/images/ville-st-felicien-logo-facebook.jpg";
?>

<article class="mod-job mod-type-detail">

	<header>
		<h1 class="page-title"><?php echo $jobTitle; ?></h1>
	</header>
	
	<?PHP if ($jobType != 4): ?>
		
		<?PHP if (Yii::app()->user->getState('siteVersion') == 'desktop'): ?>
		<div class="fb-like" data-send="false" data-show-faces="false" data-share="true"></div>
		<?PHP endif; ?>
			
		<section id="<?PHP echo Yii::t('jobModule.common', 'secteur'); ?>" class="section-bloc bloc-editor">
		
			<header>
				<h1 class="job-caract-title"><?PHP echo Yii::t('jobModule.common', 'Caractéristiques de l\'emploi'); ?></h1>
			</header>
			
			<table id="job-caract" class="table">
				<tbody>
					<tr>
						<th><?PHP echo Yii::t('jobModule.common', 'Secteur d\'emploi'); ?></th>
						<td><?PHP echo CHtml::encode($job->category->name); ?></td>
					</tr>
					<tr>
						<th><?PHP echo Yii::t('jobModule.common', 'Type d\'emploi'); ?></th>
						<td><?PHP echo $jobType; ?></td>
					</tr>
					<tr>
						<th><?PHP echo Yii::t('jobModule.common', 'Date de publication de l\'offre'); ?></th>
						<td><?PHP echo $jobPublicationDate; ?></td>
					</tr>
					<tr>
						<th><?PHP echo Yii::t('jobModule.common', 'Nombre de postes à combler'); ?></th>
						<td><?PHP echo ($job->nb_available > 0) ? $job->nb_available : Yii::t('jobModule.common', 'Indéterminée'); ?></td>
					</tr>
					<tr>
						<th><?PHP echo Yii::t('jobModule.common', 'Date de début de l\'emploi'); ?></th>
						<td><?PHP echo $jobStartDate; ?></td>
					</tr>
					<tr>
						<th><?PHP echo Yii::t('jobModule.common', 'Date et heure limite pour postuler'); ?></th>
						<td><?PHP echo $jobPostulationEndDate; ?></td>
					</tr>
				</tbody>
			</table>
			
		</section>
		
		<?PHP
		// Blocs
		$this->widget('application.components.widgets.Blocs.BlocsWidget', array(
			'parentId'=>$job->id,
			'uniqueId'=>'job',
		));
		?>
		
		<a href="<?PHP echo $this->createUrl('index'); ?>" class="btn btn-lg btn-primary"><?PHP echo Yii::t('jobModule.common', 'Postuler'); ?></a>

	<?PHP else: ?>
		
		<p><?PHP echo Yii::t('jobModule.common', 'Pour faire partie de notre banque de candidatures dans le secteur'); ?> <strong><?PHP echo CHtml::encode($job->category->name); ?></strong><?PHP echo Yii::t('jobModule.common', ', vous pouvez nous transmettre votre CV en utilisant le formulaire de la section'); ?> <a href="<?PHP echo $this->createUrl('index'); ?>"><?PHP echo Yii::t('jobModule.common', 'Emplois'); ?></a>.</p>
		
	<?php endif;?>

</article>

<a href="<?PHP echo $this->createUrl('index'); ?>" class="back"><?PHP echo Yii::t('jobModule.common', 'Retour à la liste des emplois'); ?></a>