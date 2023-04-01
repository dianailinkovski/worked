<?php

class ContestEntry extends CActiveRecord
{
	public $fields;
	
	// Adding a bunch of fields for the results page.
	public $field_0; public $field_1; public $field_2; public $field_3; public $field_4; public $field_5; public $field_6; public $field_7; public $field_8; public $field_9; public $field_10;
	public $field_11; public $field_12; public $field_13; public $field_14; public $field_15; public $field_16; public $field_17; public $field_18; public $field_19; public $field_20;
	public $field_21; public $field_22; public $field_23; public $field_24; public $field_25; public $field_26; public $field_27; public $field_28; public $field_29; public $field_30;
	public $field_31; public $field_32; public $field_33; public $field_34; public $field_35; public $field_36; public $field_37; public $field_38; public $field_39; public $field_40;
	public $field_41; public $field_42; public $field_43; public $field_44; public $field_45; public $field_46; public $field_47; public $field_48; public $field_49; public $field_50;
	public $field_51; public $field_52; public $field_53; public $field_54; public $field_55; public $field_56; public $field_57; public $field_58; public $field_59; public $field_60;
	public $field_61; public $field_62; public $field_63; public $field_64; public $field_65; public $field_66; public $field_67; public $field_68; public $field_69; public $field_70;
	public $field_71; public $field_72; public $field_73; public $field_74; public $field_75; public $field_76; public $field_77; public $field_78; public $field_79; public $field_80;
	public $field_81; public $field_82; public $field_83; public $field_84; public $field_85; public $field_86; public $field_87; public $field_88; public $field_89; public $field_90;
	public $field_91; public $field_92; public $field_93; public $field_94; public $field_95; public $field_96; public $field_97; public $field_98; public $field_99; public $field_100;

	public function tableName()
	{
		return 'contest_entry';
	}

	public function rules()
	{
		return array(
			array('contest_id, ip', 'required'),
			array('contest_id', 'length', 'max'=>10),
			array('ip', 'length', 'max'=>255),
			array('id, contest_id, created_at', 'safe', 'on'=>'search'),
		);
	}

	public function relations()
	{
		return array(
			'contest' => array(self::BELONGS_TO, 'Contest', 'contest_id'),
			'items' => array(self::HAS_MANY, 'ContestEntryItem', 'contest_entry_id'),
		);
	}

	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'contest_id' => 'Contest',
			'created_at' => 'Created At',
		);
	}

	public function search()
	{
		$criteria = new CDbCriteria;
		
		$criteria->select = array('id', 'created_at');
		
		$criteria->compare('id',$this->id,true);
		$criteria->compare('created_at',$this->created_at,true);
		
		$i = 0;
		$sortAttributes=array();
		foreach ($this->fields as $field)
		{
			if ($field['type'] == 'radio')
				$columnSql = "
				(SELECT contest_field_multi.title 
				FROM contest_entry_item 
				LEFT JOIN contest_field_multi ON contest_entry_item.content = contest_field_multi.id 
				WHERE contest_entry_item.contest_entry_id = t.id AND contest_entry_item.contest_field_id = ".$field['id']." 
				LIMIT 1)";
			elseif ($field['type'] == 'checkbox')
				$columnSql = "
				(SELECT GROUP_CONCAT(contest_field_multi.title, ', ') AS results 
				FROM contest_entry_item LEFT JOIN contest_field_multi ON contest_entry_item.content = contest_field_multi.id 
				WHERE contest_entry_item.contest_entry_id = t.id AND contest_entry_item.contest_field_id = ".$field['id'].")";
			else
				$columnSql = "
				(SELECT content 
				FROM contest_entry_item 
				WHERE contest_entry_id = t.id AND contest_field_id = ".$field['id']." 
				LIMIT 1)";
		
			$criteria->select[] = $columnSql.' AS field_'.$i;
			
			$criteria->compare($columnSql, $this->{'field_'.$i},true);
			
			$sortAttributes['field_'.$i] = array(
                'asc'=>'field_'.$i,
                'desc'=>'field_'.$i.' DESC',
			);

			$i++;
		}
		$sortAttributes[] = '*';
		$criteria->addCondition('contest_id='.$this->contest_id);
		
		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
			'sort'=>array(
			    'attributes'=>$sortAttributes,
		    ),
		));
	}
	
	public function beforeDelete()
	{
		foreach ($this->items as $item)
		{
			if ($item->field->type == 'file')
			{
				if (file_exists('files/_user/contest_entries/'.$item->content))
					unlink('files/_user/contest_entries/'.$item->content);
			}
		}
		return parent::beforeDelete();	
	}

	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
