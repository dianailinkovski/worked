<?php

/**
 * Description of Repricing History
 *
 * @author  - kashif
 */
class ProductsTrendsMongo
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
    return 'products_trends';
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
   * function saveOffer
   *
   * @param <array> $offer_data    data to be saved
   * @param <string> $offer_id     id of the item offer
   *
   */
  public static function saveProductTrend(array $offer_data, $offer_id = null)
  {
    $mongo = self::connect();

    // update or add (upsert true)
    if($offer_id)
      return $mongo->update(array('_id' => $mongo->autoId($offer_id)), $offer_data, true);
    else
	    $mongo->insert($offer_data);
	
	
	   return $offer_data;
	
  }
  
   public static function updateMultipleRecord($crieteria,$updatedFields)
  {
	/*print_r($crieteria);
	print_r($updatedFields);*/
	 $mongo = self::connect();
	 $mongo->updateMulti($crieteria,$updatedFields,false,true); 
	
	  
  }
  
 
  /**
   * function get
   *
   * @param <array>  $offer_ids    id of the items offer
   *
   * return <array>
   *
   */
  public static function get($offer_ids)
  {
    $data = array();

    if(!empty($offer_ids))
    {
      $mongo = self::connect();

      // make sure we have something to iterate through
      if(!is_array($offer_ids))
        $offer_ids = array($offer_ids);

      foreach($offer_ids as &$id)
      {
        $id = $mongo->autoId($id);
      }

      $data = $mongo->get(array('_id' => array('$in' => $offer_ids)));
    }

    return $data;
  }
  
  function getByReference($id,$reference='')
  {
	  $mongo = self::connect();
	  $id = $mongo->autoId($id);
	  
	  $criteria = array('_id'=>$id);
	  if($reference !='' && $reference!='both')
	  {
		  $criteria['api_reference'] = $reference;
	  }
	  

	  $data = $mongo->get($criteria);

		return $data;
	  
  }

  /**
   * function save
   *
   * @param <string> $offer_id        id of the item offer
   * @param <array>  $data            data to be saved
   *
   */
  public static function save($offer_id, $data)
  {
    if(!empty($data))
    {
      $update = array(
        '$set' => $data
      );

      // save data
      self::saveOffer($update, $offer_id);
    }
  }

  /**
   * function saveItem
   *
   * @offer_id        id of the offer
   * @data            data to be added
   *
   */
  public static function saveItem($offer_id, $data)
  {
    $mongo = self::connect();

    if(!empty($data))
    {
      // update or add (upsert true)
      $update = array(
        '$addToSet' => array(
          'offers' => $data
        )
      );

      self::saveOffer($update, $offer_id);
    }
  }
  
  /**
   * function DeleteOffer
   *
   * @param <array>     $criteria
   * @$one              Bolean True/False if true than just delete one record against criteria.
   *
   */
  public static function DeleteOffer($criteria,$one = FALSE)
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
    //return $mongo->getWithCriteria('offers', $criteria);
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
  
  /**
   * function getDistinct
   *
   * @param <array>    $criteria
   *
   * return list of data found
   */
  public static function getDistinct($criteria)
  {
    $mongo = self::connect();

    return $mongo->getDistinct($criteria);    
  }
}