<?php

/**
 * This is the model class for table "content_blocs".
 *
 * The followings are the available columns in table 'content_bloc':
 * @property string $id
 * @property string $bloc_id
 * @property string $bloc_type
 * @property string $page_id
 *
 * The followings are the available model relations:
 * @property ContentPage $page
 */
class Bloc extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return ContentBlocs the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'bloc';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('bloc_id, bloc_type, rank', 'required'),
			array('bloc_id, parent_id, rank', 'length', 'max'=>10),
			array('bloc_type, title', 'length', 'max'=>255),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, bloc_id, bloc_type, parent_id, rank', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
		);
	}

	public function beforeDelete() 
	{
		$modelsSamePage = self::model()->multilang()->findAllByAttributes(array('parent_id'=>$this->parent_id));

		foreach ($modelsSamePage as $model) 
		{
			if ($model->rank > $this->rank) 
			{
				$model->rank--;
				$model->save();
			}
		}
		$className = 'Bloc'.ucfirst($this->bloc_type);
		$bloc = $className::model()->findByPk($this->bloc_id);

		foreach ($bloc->behaviors() as $behaviorName => $behavior)
		{
			if ($behavior['class'] == 'application.components.behaviors.BlocBehavior')
				$bloc->$behaviorName->deleting = true;
		}
		$bloc->delete();

		return parent::beforeDelete();
	}
	
	public function behaviors() 
	{
	    return array(
	        'ml' => array(
	            'class' => 'application.components.behaviors.MultilingualBehavior',
	            'localizedAttributes' => array('title', 'title_url'), //attributes of the model to be translated
	            'languages' => Yii::app()->languageManager->languages, // array of your translated languages. Example : array('fr' => 'Français', 'en' => 'English')
	            'defaultLanguage' => Yii::app()->sourceLanguage, //your main language. Example : 'fr'
	    		'forceOverwrite' => true,
	        ),
	    );
	}
	
	public function defaultScope()
	{
	    return array_merge(array(
			'order'=>'rank ASC',
		), $this->ml->localizedCriteria());
	}
}