<?PHP
/*
Utilisation dans la vu parente : $this->widget('application.modules.job.components.Recents');
*/

$index = 1;
$entry_type = 1;
foreach ($jobs as $job) :

	$jobLink = $this->controller->createUrl('/job/default/detail', array('t'=>$job->title_url));
	$jodStartDate = substr($job->start_date, 0, 10);
	$jodStartYear = substr($job->start_date, 0, 4);
	$jodStartMonth = substr($job->start_date, 5, 2);
	$jodStartDay = substr($job->start_date, 8, 2);
	$xtraClass = ($index == $this->maxNbrEntries) ? " last" : "";
	?>

	<article class="entry_type<?PHP echo $entry_type.$xtraClass; ?>">
		<header>
			<p class="date" title="Emploi débutant le <?PHP echo $jodStartDate; ?>"><?PHP echo $jodStartDay."-".$jodStartMonth; ?> <span><?PHP echo $jodStartYear; ?></span></p>
			<h1><a href="<?PHP echo $jobLink; ?>"><?PHP echo CHtml::encode($job->title); ?></a></h1>
		</header>
	</article>
		
	<?PHP
	if ($entry_type == 1):
		$entry_type = 2;
	elseif ($entry_type == 2):
		$entry_type = 3;
	elseif ($entry_type == 3):
		$entry_type = 1;
	endif;
	
	$index++;
endforeach;

if ($index == 1):
?>

	<p>Aucune offre d'emploi n'est disponible pour le moment. <a data-animate="true" href="#aAbonnementInfolettre">Abonnez-vous à notre infolettre</a> pour recevoir nos prochaines offres d'emplois par courriel.</p>

<?PHP
endif;
?>
