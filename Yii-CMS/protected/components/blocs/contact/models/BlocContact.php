<?php
class BlocContact extends CActiveRecord
{
	public function tableName()
	{
		return 'bloc_contact';
	}

	public function rules()
	{
		return array(
			array('name', 'required'),
			array('name, address, city, province, postal_code, country, phone1, phone2, phone_toll_free, fax, email, image, image_title, comment', 'length', 'max'=>255),
			array('image', 'file', 'types'=>'jpg, gif, png', 'allowEmpty'=>true),
			array('display_contact_form', 'boolean'),
			array('google_maps', 'safe'),
			array('id, name, address, city, province, postal_code, country, phone1, phone2, phone_toll_free, fax, email, google_maps, image, image_title, comment', 'safe', 'on'=>'search'),
		);
	}

	public function relations()
	{
		return array(
			'blocContactLangs' => array(self::HAS_MANY, 'BlocContactLang', 'bloc_contact_id'),
		);
	}

	public function attributeLabels()
	{
		return AdminHelper::multilangLabels($this, array(
			'id' => '#',
			'title_anchor' => 'Inclure dans l’index de contenu',
			'title_page' => 'Afficher le titre dans la page',
			'title' => 'Titre du bloc',
			'name' => 'Nom de l’entreprise ou de l’organisme',
			'address' => 'Adresse (no civique et nom de rue)',
			'city' => 'Ville',
			'province' => 'Province',
			'postal_code' => 'Code postal',
			'country' => 'Pays',
			'phone1' => 'Numéro de téléphone 1',
			'phone2' => 'Numéro de téléphone 2',
			'phone_toll_free' => 'Numéro sans frais',
			'fax' => 'Numéro de télécopieur',
			'email' => 'Adresse courriel',
			'google_maps' => 'Code d’intégration Google Maps (débutant par "&lt;iframe")',
			'image' => 'Photo de l’entreprise ou de l’organisme',
			'image_title' => 'Titre de la photo',
			'comment' => 'Commentaire',
			'display_contact_form' => 'Afficher le formulaire de contact',
		));
	}

	public function search()
	{
		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id,true);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('address',$this->address,true);
		$criteria->compare('city',$this->city,true);
		$criteria->compare('province',$this->province,true);
		$criteria->compare('postal_code',$this->postal_code,true);
		$criteria->compare('country',$this->country,true);
		$criteria->compare('phone1',$this->phone1,true);
		$criteria->compare('phone2',$this->phone2,true);
		$criteria->compare('phone_toll_free',$this->phone_toll_free,true);
		$criteria->compare('fax',$this->fax,true);
		$criteria->compare('email',$this->email,true);
		$criteria->compare('google_maps',$this->google_maps,true);
		$criteria->compare('image',$this->image,true);
		$criteria->compare('image_title',$this->image_title,true);
		$criteria->compare('comment',$this->comment,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
	
	public function behaviors() 
	{
	    return array(
	        'ml' => array(
	            'class' => 'application.components.behaviors.MultilingualBehavior',
	            'localizedAttributes' => array('comment', 'image_title'),
	            'languages' => Yii::app()->languageManager->languages,
	            'defaultLanguage' => Yii::app()->sourceLanguage,
	        	'forceOverwrite' => true,
	        ),
	        'bloc' => array(
	            'class' => 'application.components.behaviors.BlocBehavior',
	        ),
			'multilangVirtualAttributes'=>array(
				'class'=>'application.components.behaviors.MultilangVirtualAttributesBehavior',
				'attributes'=>array('title'),
			),
	        'imageHandler' => array(
	        	'class' => 'application.components.behaviors.UploadingBehavior.ActiveRecordUploadingBehavior',
	        	'attribute' => 'image',
				'dir' => 'files/_user/bloc_contact',
				'tempDir' => 'files/_user/bloc_contact/_temp',
				'cacheTime' => 10 * 24 * 60 * 60, // 10 days
				'formats' => array(
					'm'=>array(400, 300),
				)
	        )
	    );
	}
	
	public function defaultScope()
	{
	    return $this->ml->localizedCriteria();
	}
}
