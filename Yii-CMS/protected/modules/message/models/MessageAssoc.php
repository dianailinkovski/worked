<?php
class MessageAssoc extends CActiveRecord
{
	public function tableName()
	{
		return 'message_assoc';
	}

	public function rules()
	{
		return array(
			array('message_id, member_id, seen', 'required'),
			array('seen', 'boolean'),
			array('message_id, member_id', 'length', 'max'=>10),
			array('message_id, member_id, seen', 'safe', 'on'=>'search'),
		);
	}

	public function relations()
	{
		return array(
			'member'=>array(self::BELONGS_TO, 'Member', 'member_id'),
			'message'=>array(self::BELONGS_TO, 'Message', 'message_id'),
		);
	}

	public function attributeLabels()
	{
		return array(
			'message_id' => 'Message',
			'member_id' => 'Member',
			'seen' => 'Seen',
		);
	}

	public function search()
	{
		$criteria=new CDbCriteria;

		$criteria->compare('message_id',$this->message_id,true);
		$criteria->compare('member_id',$this->member_id,true);
		$criteria->compare('seen',$this->seen);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
