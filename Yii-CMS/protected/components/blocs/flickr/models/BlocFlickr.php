<?php
class BlocFlickr extends CActiveRecord
{
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	public function tableName()
	{
		return 'bloc_flickr';
	}

	public function rules()
	{
		return array(
			array('user_id, nbr_images', 'required'),
			array('nbr_images', 'numerical', 'min'=>0),
			array('show_as_carrousel', 'boolean'),
			array('set_id', 'safe'),
			array('id, iframe', 'safe', 'on'=>'search'),
		);
	}

	public function attributeLabels()
	{
		return AdminHelper::multilangLabels($this, array(
			'id' => '#',
			'title_anchor' => 'Inclure dans l’index de contenu',
			'title_page' => 'Afficher le titre dans la page',
			'title' => 'Titre du bloc',
			'user_id'=>'Utilisateur',
			'set_id'=>'Galerie',
			'nbr_images'=>'Nombre d’images',
			'show_as_carrousel'=>'Afficher en tant que carrousel',
		));
	}
	
	public function behaviors() 
	{
	    return array(
	        'bloc' => array(
	            'class' => 'application.components.behaviors.BlocBehavior',
	        ),
			'multilangVirtualAttributes'=>array(
				'class'=>'application.components.behaviors.MultilangVirtualAttributesBehavior',
				'attributes'=>array('title'),
			),
	    );
	}
}