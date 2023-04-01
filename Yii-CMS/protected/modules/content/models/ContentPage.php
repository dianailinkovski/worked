<?php
class ContentPage extends CActiveRecord
{
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	public function tableName()
	{
		return 'content_page';
	}

	public function rules()
	{
		return array(
			array('title, layout', 'required'),
			array('title', 'length', 'max'=>255),
			array('id, title', 'safe', 'on'=>'search'),
		);
	}

	public function relations()
	{
		return array(
			'alias' => array(self::BELONGS_TO, 'CmsAlias', 'alias_id'),
			'blocs' => array(self::HAS_MANY, 'Bloc', 'parent_id', 'condition'=>"(blocs.unique_id='content' OR blocs.id IS NULL)"), // blocs.id is null is to include pages that don't have blocs
		);
	}

	public function attributeLabels()
	{
		return AdminHelper::multilangLabels($this, array(
			'id' => '#',
			'title' => 'Titre de la page',
			'alias_id' => 'Alias',
			'layout' => 'Disposition de la page',
		));
	}

	public function search()
	{
		$criteria=new CDbCriteria;

		$criteria->compare('alias.title',$this->title,true);
		$criteria->with = 'alias';

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	public function afterFind() 
	{
		foreach (Yii::app()->languageManager->languages as $language => $fullLanguage) 
		{
			if($language === Yii::app()->sourceLanguage) $suffix = '';
    			else $suffix = '_'.$language;
    		
    		$alias = CmsAlias::model()->multilang()->findByPk($this->alias->id);
    		
    		$this->{'title'.$suffix} = $alias->{'title'.$suffix};
    		
    		if ($language == Yii::app()->language)
    			$this->title = $alias->{'title'.$suffix};
		}

		return parent::afterFind();
	}

	public function beforeDelete()
	{
		if (!$this->alias->isLeaf())
		{
			echo '<div class="flash-error">'.Yii::t('contentModule.admin', 'Vous devez relocaliser les sous-pages avant de supprimer celle-ci.').'</div>';
			return false;
		}
		else
		{
			foreach ($this->blocs as $bloc)
			{
				$bloc->delete();
			}
			$this->alias->deleteNode();
		}
		
		return parent::beforeDelete();
	}

	public function behaviors()
	{
		return array(
			'multilangVirtualAttributes'=>array(
				'class'=>'application.components.behaviors.MultilangVirtualAttributesBehavior',
				'attributes'=>array('title'),
			),
		);
	}
}
