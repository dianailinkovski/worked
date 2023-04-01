<?php
class Message extends CActiveRecord
{
	private $_wasNewRecord;

	public function tableName()
	{
		return 'message';
	}

	public function rules()
	{
		$purifier = new CHtmlPurifier();
		$purifier->options = Yii::app()->params['htmlPurifierOptions'];

		return array(
			array('message, datetime', 'required'),
			array('datetime', 'date', 'format'=>'yyyy-MM-dd HH:mm:ss'),
			array('message','filter','filter'=>array($purifier,'purify')),
			array('id, message, datetime', 'safe', 'on'=>'search'),
		);
	}

	public function relations()
	{
		return array(
			'members' => array(self::MANY_MANY, 'Member', 'message_assoc(message_id, member_id)'),
		);
	}

	public function attributeLabels()
	{
		return array(
			'id' => '#',
			'message' => 'Message',
			'datetime' => 'Date / heure d’envoi',
		);
	}

	public function search()
	{
		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id,true);
		$criteria->compare('message',$this->message,true);
		$criteria->compare('datetime',$this->datetime,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	
	public function beforeSave()
	{
		if ($this->isNewRecord)
			$this->_wasNewRecord = true;
		
		return parent::beforeSave();
	}
	
	public function afterSave()
	{
		if ($this->_wasNewRecord)
		{
			$ids = Yii::app()->db->createCommand("SELECT id FROM member")->queryColumn();
			
			foreach ($ids as $id)
			{
				Yii::app()->db->createCommand("INSERT INTO message_assoc (member_id, message_id, seen) VALUES (".$id.", ".$this->id.", 0)")->execute();
			}
			$this->_wasNewRecord = false;
		}
		return parent::afterSave();
	}

	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
