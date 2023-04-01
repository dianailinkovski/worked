<?php

/**
 * This is the model class for table "cms_section".
 *
 * The followings are the available columns in table 'cms_section':
 * @property string $id
 * @property string $name_fr
 * @property string $name_en
 * @property string $module
 *
 * The followings are the available model relations:
 * @property Alias[] $Alias
 */
class CmsSection extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return Section the static model class
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
		return 'cms_section';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('name, module', 'required'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, name, module', 'safe', 'on'=>'search'),
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
			'aliases' => array(self::HAS_MANY, 'Alias', 'section_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{		
		return AdminHelper::multilangLabels($this, array(
			'id' => '#',
			'name' => Yii::t('admin', 'Name'),
			'module' => 'Module',
		));
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id,true);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('module',$this->module,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	
	public function uri($language=false) 
	{	
		if ($this->id !== null) {
			if ($language === false)
				$language = Yii::app()->language;

			$parent_id = $this->parent_id;
			$uri = array($this->{'alias_'.$language});
			while ($parent_id != 0) {
				if (($parentModel = self::model()->findByPk($parent_id)) !== null) {
					$uri[] = $parentModel->{'alias_'.$language};
					$parent_id = $parentModel->parent_id;
				} else {
					return null;	
				}
			}
			$uriString = '';
			foreach (array_reverse($uri) as $uriOne) {
				$uriString .= $uriOne.'/';
			}
			
			return trim($uriString, '/');
		}
		return null;
	}
	
	public function afterValidate() 
	{
		if ($this->isNewRecord) {
			$auth=Yii::app()->authManager;

			$module = Yii::createComponent('application.modules.'.$this->module.'.'.ucfirst($this->module).'Module', null, null);
			
			if (!$module->instantiable && CmsSection::model()->findAllByAttributes(array('module'=>$this->module)) != null) 
			{
				$this->addError('module', Yii::t('admin', 'Ce module ne peut être instancié.'));
			}
		}

		return parent::afterValidate();
	}
	
	public function afterSave() 
	{
		if ($this->isNewRecord) {
			$auth=Yii::app()->authManager;
			
			$module = Yii::createComponent('application.modules.'.$this->module.'.'.ucfirst($this->module).'Module', null, null);
			
			if ($module->instantiable) {
				$auth->createOperation('adminSectionId-'.$this->id,'Section '.$this->name);
				$auth->addItemChild('Admin','adminSectionId-'.$this->id);
			} else {
				$auth->createOperation('adminModule-'.$this->module,'Module '.$this->name);
				$auth->addItemChild('Admin','adminModule-'.$this->module);
			}
		}

		return parent::afterSave();
	}
	
	public function beforeDelete() 
	{
		$auth=Yii::app()->authManager;
		
		$module = Yii::createComponent('application.modules.'.$this->module.'.'.ucfirst($this->module).'Module', null, null);
		
		if ($module->instantiable)
			$auth->removeAuthItem('adminSectionId-'.$this->id);
		else
			$auth->removeAuthItem('adminModule-'.$this->module);

		return parent::beforeDelete();
	}
	
	public function behaviors() 
	{
	    return array(
	        'ml' => array(
	            'class' => 'application.components.behaviors.MultilingualBehavior',
	            'localizedAttributes' => array('name'), //attributes of the model to be translated
	            'languages' => Yii::app()->languageManager->languages, // array of your translated languages. Example : array('fr' => 'Français', 'en' => 'English')
	            'defaultLanguage' => Yii::app()->sourceLanguage, //your main language. Example : 'fr'
	    		'forceOverwrite' => true,
	        ),
	    );
	}
	
	public function defaultScope()
	{
	    return $this->ml->localizedCriteria();
	}
}
