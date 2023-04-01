<?php
class BlocGooglemap extends CActiveRecord
{
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	public function tableName()
	{
		return 'bloc_googlemap';
	}

	public function rules()
	{
		return array(
			array('iframe', 'required'),
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
			'iframe' => 'Code d’intégration Google Maps (débutant par "&lt;iframe")',
		));
	}

	public function search()
	{
		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id,true);
		$criteria->compare('iframe',$this->iframe,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
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