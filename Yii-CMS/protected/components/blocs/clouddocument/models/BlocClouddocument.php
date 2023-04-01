<?php
class BlocClouddocument extends CActiveRecord
{
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	public function tableName()
	{
		return 'bloc_clouddocument';
	}

	public function rules()
	{
		return array(
			array('id', 'safe', 'on'=>'search'),
			array('path', 'required'),
			array('path, previous_folder_hash', 'length', 'max'=>255),
			array('previous_folder', 'safe'),
		);
	}

	public function relations()
	{
		return array(
		);
	}

	public function attributeLabels()
	{
		return AdminHelper::multilangLabels($this, array(
			'id' => '#',
			'title_anchor' => 'Inclure dans l’index de contenu',
			'title_page' => 'Afficher le titre dans la page',
			'title' => 'Titre du bloc',
			'path' => 'Chemin',
		));
	}
	
	public function beforeSave()
	{
		if (!$this->isNewRecord)
		{
			$oldPath = self::model()->findByPk($this->id)->path;
			
			if ($this->path != $oldPath)
			{
				$this->previous_folder_hash = '';
				$this->previous_folder = '';
			}
		}

		return parent::beforeSave();
	}

	public function search()
	{
		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	public function behaviors() 
	{
	    return array(
	        'bloc' => array(
	            'class' => 'application.models.behaviors.BlocBehavior',
	        ),
			'multilangVirtualAttributes'=>array(
				'class'=>'application.models.behaviors.MultilangVirtualAttributesBehavior',
				'attributes'=>array('title'),
			),
	    );
	}
}