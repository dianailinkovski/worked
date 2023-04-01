<?php
class JobCv extends CActiveRecord
{
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

    public function tableName()
    {
        return 'job_cv';
    }

    public function rules()
    {
        return array(
            array('cv, date', 'required'),
            array('cv', 'length', 'max'=>255),
			array('cv', 'file', 'types'=>'pdf, doc, docx', 'allowEmpty'=>true),
            array('id, cv, date', 'safe', 'on'=>'search'),
        );
    }

    public function relations()
    {
        return array(
            'jobs' => array(self::MANY_MANY, 'Job', 'job_job_cv(job_cv_id, job_id)'),
        );
    }

    public function attributeLabels()
    {
        return array(
           'id' => '#',
			'cv' => 'Curriculum vitae',
			'date' => 'Date d’envoi',
        	'file'=>Yii::t('jobModule.common', 'Votre curriculum vitae (en format Word ou PDF)'),	
        );
    }

    public function search()
    {
        $criteria=new CDbCriteria;

        $criteria->compare('id',$this->id,true);
        $criteria->compare('cv',$this->cv,true);
        $criteria->compare('date',$this->date,true);
		//$criteria->compare('job.title', $this->job_title_search, true );

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
			/*
			'sort'=>array(
				'defaultOrder'=>'date DESC',
		        'attributes'=>array(
		            'job_title_search'=>array(
		                'asc'=>'job.title',
		                'desc'=>'job.title DESC',
		            ),
		            '*',
		        ),
		    ),
			*/
        ));
    }

	public function behaviors() 
	{
	    return array(
	        'cvHandler' => array(
	        	'class' => 'application.components.behaviors.UploadingBehavior.ActiveRecordUploadingBehavior',
	        	'attribute' => 'cv',
	        	'uploadcare' => null,
				'dir' => 'files/_user/jobcv',
				'tempDir' => 'files/_user/jobcv/_temp',
				'cacheTime' => 10 * 24 * 60 * 60, // 10 days
				'allowDelete' => false,
	        )
	    );
	}
}