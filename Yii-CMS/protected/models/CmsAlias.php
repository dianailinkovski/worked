<?php

/**
 * This is the model class for table "cms_alias".
 *
 * The followings are the available columns in table 'cms_alias':
 * @property string $id
 * @property string $alias_fr
 * @property string $alias_en
 * @property string $route
 * @property string $parent_id
 * @property string $section_id
 *
 * The followings are the available model relations:
 * @property CmsSection $section
 * @property CmsAlias $parent
 * @property CmsAlias[] $cmsAlias
 */
class CmsAlias extends CActiveRecord
{
	public $parentId;
	
	public $location; // for AliasLocationWidget;
	
	public $locationWidget; // for AliasLocationWidget;

	/**
	 * Returns the static model of the specified AR class.
	 * @return CmsAlias the static model class
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
		return 'cms_alias';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('title, allow_children, location, keyword', 'required'), // todo: alias can be empty because of scenarios
			array('alias, keyword', 'length', 'max'=>100),
			array('keyword', 'unique'),
			array('title', 'length', 'max'=>255),
			array('section_id', 'length', 'max'=>10),
			array('allow_children', 'boolean'),
			array('section_id', 'default', 'setOnEmpty'=>true, 'value'=>null),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, alias, title, section_id', 'safe', 'on'=>'search'),
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
			'section' => array(self::BELONGS_TO, 'CmsSection', 'section_id'),
			'routes' => array(self::HAS_MANY, 'CmsAliasRoute', 'alias_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return AdminHelper::multilangLabels($this, array(
			'id' => '#',
			'alias' => 'Alias',
			'title' => 'Titre',
			'keyword' => 'Mot clé (pour utiliser dans des liens)',
			'section_id' => 'Section',
			'allow_children' => 'Peut avoir des enfants',
			'location' => 'Emplacement',
		));
	}
	
	public function search()
	{
		$criteria=new CDbCriteria;
		
		$criteria->order = $this->tree->hasManyRoots
                            ? $this->tree->rootAttribute.','.$this->tree->leftAttribute
                            : $this->tree->leftAttribute;

		foreach ($this->attributes as $attribute => $value) 
		{
			if (!empty($value))
				$criteria->compare($attribute, $this->{$attribute}, true);
		}

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
			'pagination' => false,
		));
	}
	
	// Generate keyword if not set
	public function beforeValidate() 
	{
		if ($this->isNewRecord && $this->keyword === null)
			$this->keyword = AdminHelper::generateUrlStr($this->alias, $this, 'keyword');

		return parent::beforeValidate();
	}

	public function beforeSave() 
	{
		if (!$this->isNewRecord)
			$this->clearCache();

		return parent::beforeSave();
	}

	public function beforeDelete() 
	{
		$this->clearCache();

		return parent::beforeDelete();
	}
    
	public function behaviors() 
	{
	    return array(
	        'ml' => array(
	            'class' => 'application.components.behaviors.MultilingualBehavior',
	            'localizedAttributes' => array('alias', 'title'), //attributes of the model to be translated
	            'languages' => Yii::app()->languageManager->languages, // array of your translated languages. Example : array('fr' => 'Français', 'en' => 'English')
	            'defaultLanguage' => Yii::app()->sourceLanguage, //your main language. Example : 'fr'
	    		'forceOverwrite' => true,
	        ),
	        'tree'=>array(
	            'class'=>'ext.nested-set-behavior.NestedSetBehavior',
	            'leftAttribute'=>'lft',
	            'rightAttribute'=>'rgt',
	            'levelAttribute'=>'level',
		    ),
	    );
	}
	
	public function defaultScope()
	{
	    return $this->ml->localizedCriteria();
	}

	private function clearCache()
	{
		foreach (Yii::app()->languageManager->languages as $language => $languageFull)
		{
			$recordBeforeSave = $this::model()->localized($language)->findByPk($this->id);
			$ancestors = $recordBeforeSave->ancestors()->localized($language)->findAll();
			$cacheId = 'parseUrl_/'.$language;

			if (!empty($ancestors))
			{
				for ($i = 1; $i < count($ancestors); $i++)
				{
					$cacheId .= '/'.$ancestors[$i]->alias;
					Yii::app()->cache->delete($cacheId); // Must delete all ancestors as well from cache otherwise it creates problems
				}
			}
			$cacheId .= '/'.$this->alias;
			Yii::app()->cache->delete($cacheId);

			Yii::app()->cache->delete('parseUrl-content_'.$recordBeforeSave->id);
			Yii::app()->cache->delete('createUrl-keyword-alias_'.$recordBeforeSave->keyword);
			Yii::app()->cache->delete('createUrl-keyword-route_'.$recordBeforeSave->keyword);
			Yii::app()->cache->delete('aliasBreadcrumb_'.$recordBeforeSave->id);

			foreach ($recordBeforeSave->routes as $route)
			{
				Yii::app()->cache->delete('createUrl_'.$route->route.'_na');
				Yii::app()->cache->delete('createUrl_'.$route->route.'_'.$recordBeforeSave->section_id);
			}

			// Must delete all descendants as well
			$descendants = $recordBeforeSave->descendants()->localized($language)->findAll();
			if (!empty($descendants))
			{
				for ($i = 0; $i < count($descendants); $i++)
				{
					if (isset($descendants[$i-1]) && ($levelDifference = $descendants[$i-1]->level - $descendants[$i]->level) >= 0)
					{
						for ($k = 0; $k < $levelDifference+1; $k++)
						{
							$cacheId = substr($cacheId, 0, strrpos($cacheId, '/'));
						}
					}
					$cacheId = $cacheId.'/'.$descendants[$i]->alias;
					Yii::app()->cache->delete($cacheId);

					Yii::app()->cache->delete('parseUrl-content_'.$descendants[$i]->id);
					Yii::app()->cache->delete('createUrl-keyword-alias_'.$descendants[$i]->keyword);
					Yii::app()->cache->delete('createUrl-keyword-route_'.$descendants[$i]->keyword);
					Yii::app()->cache->delete('aliasBreadcrumb_'.$descendants[$i]->id);

					foreach ($descendants[$i]->routes as $route)
					{
						Yii::app()->cache->delete('createUrl_'.$route->route.'_na');
						Yii::app()->cache->delete('createUrl_'.$route->route.'_'.$descendants[$i]->section_id);
					}
				}
			}
		}
	}
}