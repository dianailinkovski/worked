<?php
class EntryForm extends CFormModel
{
	public $attributeLabels=array();
	
	public $behaviors=array();
  
	
	private $data;

	
  public function __get($varName)
  {
      if (!array_key_exists($varName,$this->data))
          throw new CHttpException(500, 'Attribute not defined!');
      else 
          return $this->data[$varName];
  }

  public function __set($varName, $value)
  {
      $this->data[$varName] = $value;
  }
    
	public function attributeLabels()
  {
    	return $this->attributeLabels;
  }
    
	public function behaviors()
  {
    	return $this->behaviors;
  }
}
