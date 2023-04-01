<?php
class ContestEntryItem extends CActiveRecord
{
	public function tableName()
	{
		return 'contest_entry_item';
	}

	public function rules()
	{
		return array(
			array('contest_entry_id, contest_field_id, content', 'required'),
			array('contest_entry_id, contest_field_id', 'length', 'max'=>10),
			array('content', 'length', 'max'=>1000),
			array('id, contest_entry_id, contest_field_id, content', 'safe', 'on'=>'search'),
		);
	}

	public function relations()
	{
		return array(
			'field' => array(self::BELONGS_TO, 'ContestField', 'contest_field_id'),
			'entry' => array(self::BELONGS_TO, 'ContestEntry', 'contest_entry_id'),
		);
	}

	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'contest_entry_id' => 'Contest Entry',
			'contest_field_id' => 'Contest Field',
			'content' => 'Content',
		);
	}

	public function search()
	{
		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id,true);
		$criteria->compare('contest_entry_id',$this->contest_entry_id,true);
		$criteria->compare('contest_field_id',$this->contest_field_id,true);
		$criteria->compare('content',$this->content,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
