<?php
class BlocPeoplePeople extends CActiveRecord
{
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	public function tableName()
	{
		return 'bloc_people_people';
	}

	public function rules()
	{
		return array(
			array('bloc_people_id, name, rank', 'required'),
			array('bloc_people_id, rank', 'length', 'max'=>10),
			array('name, telephone, telephone2, fax, email, title, department, image', 'length', 'max'=>255),
			array('description', 'length', 'max'=>500),
			array('image', 'file', 'types'=>'jpg, gif, png', 'allowEmpty'=>true),
			array('id, bloc_people_id, name, telephone, telephone2, fax, email, title, department, description, image, rank', 'safe', 'on'=>'search'),
		);
	}

	public function relations()
	{
		return array(
			'bloc' => array(self::BELONGS_TO, 'BlocPeople', 'bloc_people_id'),
			'blocPeoplePeopleLangs' => array(self::HAS_MANY, 'BlocPeoplePeopleLang', 'bloc_people_people_id'),
		);
	}

	public function attributeLabels()
	{
		return AdminHelper::multilangLabels($this, array(
			'id' => '#',
			'bloc_people_id' => 'Bloc People',
			'name' => 'Nom',
			'telephone' => '# Téléphone',
			'telephone2' => '# Téléphone 2',
			'fax' => '# Télécopieur',
			'email' => 'Adresse courriel',
			'image' => 'Photo',
			'title' => 'Titre',
			'department' => 'Département',
			'description' => 'Description ou biographie (max. 500 caractères)',
			'rank' => 'Rank',		
		));
	}

	public function search()
	{
		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id,true);
		$criteria->compare('bloc_people_id',$this->bloc_people_id,true);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('telephone',$this->telephone,true);
		$criteria->compare('telephone2',$this->telephone2,true);
		$criteria->compare('fax',$this->fax,true);
		$criteria->compare('email',$this->email,true);
		$criteria->compare('title',$this->title,true);
		$criteria->compare('department',$this->department,true);
		$criteria->compare('description',$this->description,true);
		$criteria->compare('rank',$this->rank,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	
	public function behaviors() 
	{
	    return array(
	        'ml' => array(
	            'class' => 'application.components.behaviors.MultilingualBehavior',
	            'localizedAttributes' => array('title', 'department', 'description'),
	            'languages' => Yii::app()->languageManager->languages,
	            'defaultLanguage' => Yii::app()->sourceLanguage,
	    		'forceOverwrite' => true,
	        ),
	        'imageHandler' => array(
	        	'class' => 'application.components.behaviors.UploadingBehavior.ActiveRecordUploadingBehavior',
	        	'attribute' => 'image',
				'dir' => 'files/_user/bloc_people',
				'tempDir' => 'files/_user/bloc_people/_temp',
				'cacheTime' => 10 * 24 * 60 * 60, // 10 days
				'formats' => array(
					'm'=>array(135, 180),
				)
	        )
	    );
	}
	
	public function defaultScope()
	{
	    return array_merge(array(
			'order'=>'rank ASC',
		), $this->ml->localizedCriteria());
	}
}
