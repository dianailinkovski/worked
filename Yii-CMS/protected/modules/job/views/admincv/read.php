<?php
$this->breadcrumbs=array(
	$this->sectionParentLabel=>array('admin/admin'),
	$this->sectionLabel=>array('admincv/admin'),
	"Détails de la postulation",
);

$this->title='Détails de la postulation # '.$modelCv->id;
?>

<div class="section-bloc bloc-editor">

	<p><strong>Date/heure de postulation :</strong> <?PHP echo $modelCv->date; ?></p>
	<p><strong>Nom du fichier (C.V.) :</strong> <?PHP echo $modelCv->cv; ?> [<a href="/<?PHP echo $modelCv->cvHandler->dir."/".$modelCv->cv; ?>" target="_blank" title="Télécharger le CV">télécharger</a>]</p>
	<p><strong>Liste des emplois postulés :</strong></p>
	<ul>
		<?PHP foreach ($modelCv->jobs as $job): ?>
		<li><?PHP echo $job->title; ?> (<?PHP echo $job->category->name; ?>)</li>
		<?PHP endforeach; ?>
	</ul>

</div>

<a href="<?php echo $this->createUrl('admincv/admin') ?>">Retour à la liste</a>