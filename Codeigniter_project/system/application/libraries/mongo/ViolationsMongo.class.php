<?php

/**
 * Description of Repricing History
 *
 * @author  - kashif
 */
class ViolationsMongo
{
  /**
   * function database
   *
   * @return <type>
   */
  public static function database()
  {
    return 'marketvision';
  }

  /**
   * function collection
   *
   * @return <type>
   */
  public static function collection()
  {
    return 'violations';
  }

  /**
   * function collection
   *
   * @return <type>
   */
  public static function connect()
  {
    return plMongo::connect('localhost')->database(self::database())->collection(self::collection());
  }

  /**
   * function saveHistory
   *
   * @param <array> $history_data    data to be saved
   * @param <string> $history_id     id of the item history
   *
   */
  public static function saveViolation(array $history_data, $history_id = null)
  {
    $mongo = self::connect();

    // update or add (upsert true)
    if($history_id)
      return $mongo->update(array('_id' => $mongo->autoId($history_id)), $history_data, true);
    else
       $mongo->insert($history_data);
	   
	   return $history_data;
  }
  
   public static function getDistinct($command)
  {
    $mongo = self::connect();

    // update or add (upsert true)
	$history_data = $mongo->getDistinct($command);
	   
	   return $history_data;
  }

/*
	*$crieteria array of conditions  Like
		$crieteriaUpdate = array(
					  'merchant_id'=>$violations_data[$i]->merchant_id						                        );
					  
	*$updatedFields array of fields to update with $set  Like
	array('$set'=>array('is_emailsend'=>'0')
*/
 public static function updateMultipleRecord($crieteria,$updatedFields)
  {
	
	 $mongo = self::connect();
	 $mongo->updateMulti($crieteria,$updatedFields,false,true); 
	
	  
  }
  
  

  /**
   * function get
   *
   * @param <array>  $history_ids    id of the items history
   *
   * return <array>
   *
   */
  public static function get($history_ids)
  {
    $data = array();

    if(!empty($history_ids))
    {
      $mongo = self::connect();

      // make sure we have something to iterate through
      if(!is_array($history_ids))
        $history_ids = array($history_ids);

      foreach($history_ids as &$id)
      {
        $id = $mongo->autoId($id);
      }

      $data = $mongo->get(array('_id' => array('$in' => $history_ids)));
    }

    return $data;
  }

  /**
   * function save
   *
   * @param <string> $history_id        id of the item history
   * @param <array>  $data              data to be saved
   *
   */
  public static function save($history_id, $data)
  {
    if(!empty($data))
    {
      $update = array(
        '$set' => $data
      );

      // save data
      self::saveHistory($update, $history_id);
    }
  }

  /**
   * function saveItem
   *
   * @history_id        id of the history
   * @data              data to be added
   *
   */
  public static function saveItem($history_id, $data)
  {
    $mongo = self::connect();

    if(!empty($data))
    {
      // update or add (upsert true)
      $update = array(
        '$addToSet' => array(
          'items' => $data
        )
      );

      self::saveHistory($update, $history_id);
    }
  }
  
  /**
   * function DeleteItem
   *
   * @param <array>     $criteria
   * @$one              Bolean True/False if true than just delete one record against criteria.
   *
   */
  public static function DeleteItem($criteria,$one = FALSE)
  {
    $mongo = self::connect();
	
    return $mongo->delete($criteria,$one);
  }

  /**
   * function getWithCriteria
   *
   * @param <array>    $criteria
   *
   * return list of data found
   */
  public static function getWithCriteria($criteria)
  {
    $mongo = self::connect();

    return $mongo->getWithCriteria($criteria);
    //return $mongo->getWithCriteria('items', $criteria);
  }
  /**
   * function getWithCriteriaSelect
   *
   * @param <array>    $criteria Condition or empty if you want to select from all records
   *@param $selected Array of attributes(columns). you want to select
   * return list of data found
   */
  
   public static function getWithCriteriaSelect($criteria,$selected)
  {
    $mongo = self::connect();

    return $mongo->getWithCriteriaSelect($criteria,$selected);
    //return $mongo->getWithCriteria('offers', $criteria);
  }
  
  /**
   * function getByGroup
   *
   * @param <array>    $criteria
   *
   * return list of data found
   */
  public static function getByGroup($criteria)
  {
    $mongo = self::connect();

    return $mongo->getByGroup($criteria);    
  }
}