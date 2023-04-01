<?php

/**
 * cnMongo adds a simple wrapper to the PHP mongo library
 *
 * Usage:  cnMongo::connect()->database()->collection()->get();
 *
 * @package    symfony
 * @subpackage cnClasses
 * @author     Justin Vencel
 */
class plMongo
{
  public $connection;
  public $db;
  public $collection;
  public $cursor;


  static private $instances = array();
  static $sort_column;

  private function __construct($server)
  {
    $this->connection = new Mongo($server);
    return;
  }

  private static function checkInstance($server)
  {
    if(!isset(self::$instances[$server]))
    {
      self::$instances[$server] = new plMongo($server);
    }

    return self::$instances[$server];
  }

  protected function __clone()
  {
  }

  public static function connect($server = false)
  {
    if(!$server)
    {
      $CI =& get_instance();
      $server = $CI->config->item('mongo_server');
    }

    return self::checkInstance($server);
  }

  public function database($db = false)
  {
    if(!$db)
    {
      $CI =& get_instance();
      $db = $CI->config->item('mongo_db');
    }

    $this->db = $this->connection->selectDB($db);

    return $this;
  }

  public function collection($collection)
  {
    $this->collection = $this->db->selectCollection($collection);
    return $this;
  }

  public function get($f = array())
  {
    $cursor = $this->collection->find($f);

    $k = array();
    $i = 0;

    while($cursor->hasNext())
    {
      $k[$i] = $cursor->getNext();
      $i++;
    }

    return $k;
  }

  public function getOne($f)
  {
    return $this->collection->findOne($f);
  }

  public function getAll()
  {
    $this->cursor = $this->collection->find();

    return $this;
  }

  public function sort($order = 'asc', $field = 'id')
  {
    $order = (strtolower($order) == 'asc') ? 1 : -1;

    return $this->cursor->sort(array($field => $order));
  }

  /**
   * Inserting in safe mode so we can see errors
   *
   * @param <type> $f
   * @return <type>
   */
  public function insert($f)
  {
    return $this->collection->insert($f, array('safe' => true));
  }

  /**
   * Updating in safe mode so we can see errors
   *
   * @param <type> $criteria
   * @param <type> $newobj
   * @param <type> $upsert
   */
  public function update($criteria, $newobj, $upsert = false)
  {
    $this->collection->update($criteria, $newobj, array('upsert' => $upsert, 'safe' => true));
  }
  
  /**
   * Updating in specific fields and multiple record
   *
   * @param <type> $criteria
   * @param <type> $newobj Fields to update
   * @param <type> $upsert insert if not exist
   * @param <type> $multi  update multiple records 
   */
  public function updateMulti($criteria, $newobj, $upsert=false,$multi=true)
  {
    $this->collection->update($criteria, $newobj, array('upsert' => $upsert, 'safe' => true,'multiple'=>true));
	
  }

  public function delete($f, $one = FALSE)
  {
    $c = $this->collection->remove($f, array('justOne' => $one, 'safe' => true));
    return $c;
  }

  public function ensureIndex($args)
  {
    return $this->collection->ensureIndex($args);
  }

  public function autoId($value)
  {
    return new MongoId($value);
  }

  public static function _autoId($value)
  {
    return new MongoId($value);
  }

  /**
   * Replace dots with nothing because mongo keys cannot handle them.
   * (There is a small change that there could be conflicting keys, but we are going to live with this)
   * @param <type> $key
   * @return string
   */
  public static function escapeKey($key)
  {
    return str_replace('.', '', $key);
  }

  public function getWithCriteria($f = array())
  {
    if(!isset($f['where']))
    {
      $f['where'] = array();
    }
	
	$cursor = $this->collection->find($f['where']);

	if(isset($f['sort_column']))
	{
		if(is_array($f['sort_column']))
		{
			 $cursor->sort($f['sort_column']);
		}
		else
		{
	  $cursor->sort(array($f['sort_column'] => (isset($f['sort_order']) ? $f['sort_order'] : -1)));
		}
	}

	if(isset($f['limit']))
	{
	  $cursor->limit($f['limit']);
	}
	
	/*
		USED TO SKIP RECORDS LIKE STARTINDEX in simple MYSQL $start,$limit
	*/
	if(isset($f['skip']))
	{
	  $cursor->skip($f['skip']);
	}
	
	
	$k = array();

	while($cursor->hasNext())
	{
	  $k[] = (object)$cursor->getNext();
	  //$this->filterRow($index, $f, $cursor, $k);
	}	

    return $k;
  }
  
  public function getWithCriteriaSelect($f = array(),$sel=array())
  {
    if(!isset($f['where']))
    {
      $f['where'] = array();
    }
	
	$cursor = $this->collection->find($f['where'],$sel);

	if(isset($f['sort_column']))
	{
	  $cursor->sort(array($f['sort_column'] => (isset($f['sort_order']) ? $f['sort_order'] : -1)));
	}

	if(isset($f['limit']))
	{
	  $cursor->limit($f['limit']);
	}
	
	/*
		USED TO SKIP RECORDS LIKE STARTINDEX in simple MYSQL $start,$limit
	*/
	if(isset($f['skip']))
	{
	  $cursor->skip($f['skip']);
	}
	$k = array();

	while($cursor->hasNext())
	{
	  $k[] = (object)$cursor->getNext();
	  //$this->filterRow($index, $f, $cursor, $k);
	}	

    return $k;
  }
  
  public function getByGroup($f = array())
  {    
	$k = array();
	if(isset($f['group']))
    {		
		$cursor = $this->collection->group($f['group']['keys'], $f['group']['initial'], $f['group']['reduce'], $f['where']);
		$k = (isset($cursor['retval'])) ? $cursor['retval'] : array();
		
		if(isset($f['sort_column']))
		{
		  plMongo::sortMultiArray($k, $f['sort_column'], (isset($f['sort_order']) ? $f['sort_order'] : 'desc'));
		}
	
		if(isset($f['limit']))
		{
			if(isset($f['skip']))
			{
				$offset = $f['skip'];
			}
			else
			{
				$offset = 0;	
			}
			
		  $k = array_slice($k, $offset, $f['limit']);
		}		
		foreach($k as $key=>$val)	
		{
			$k[$key] = (object)$val;
		}		
    }	

    return $k;
  }
  
  public function getDistinct($command)
  {    
  
/******  	PARAMETER ARRAY EXAMPLE  *************
array(
			"distinct" => "violations", "key" => "merchant_id",'query'=>array('is_emailsend'=>'0')	
		                 );

*************************************************/
	$k = array();
	if(is_array($command))
    {
		$k = $this->db->command($command);
		/*		$map = new MongoCode("function() { emit(this.merchant_id,1); }");
		$reduce = new MongoCode("function(k, vals) { ".
			"return 1; }");
		
		$k = $this->db->command(array(
			"mapreduce" => "violations", 
			"map" => $map,
			"reduce" => $reduce,
			"query" => array("is_emailsend" => "0"),
			"out" => array("merge" => "violationsCounts")
		));
		
		$users = $this->db->selectCollection($k['result'])->find();
    $i=0;
	
	
	$final = iterator_to_array($users);*/
	
		//$k = $this->db->selectCollection('item_competing_offers')->find($f['where']);
		//debug("k array", $k, 2);
		//exit;		
    }	
		return $k;
  }
  
 
  private function filterRow($index, $f, $cursor, &$k)
  {
    $row = $cursor->getNext();

    if(!isset($row[$index]))
      return false;

    if(isset($f['filters']))
    {
      // make sure that array is used for iteration
      if(!is_array($f['filters']))
        $f['filters'] = array($f['filters']);

      foreach($row[$index] as $value)
      {
        if($this->applyFilter($value, $f['filters']))
        {
          $k[] = (object)$value;
        }
      }
    }
    else
    {
      $k = array_merge($k, $row[$index]);
    }
  }

  private function applyFilter($item, $filters)
  {
    $valid = true;

    foreach($filters as $key => $filter)
    {
      if($item[$key] != $filter)
        $valid = false;
    }

    return $valid;
  }

  public static function sortMultiArray(array &$toSort, $sort_column, $sort_order = 'desc')
  {
    self::$sort_column = $sort_column;
    usort($toSort, array("plMongo", "sort_numeric_" . $sort_order));
  }

  private static function sort_numeric_desc($x, $y)
  {
    $sort_column = self::$sort_column;

    if(is_object($x))
	{
		if($x->$sort_column == $y->$sort_column)
			return 0;
			
		return ($x->$sort_column > $y->$sort_column) ? -1 : 1;
	}
	
	if($x[$sort_column] == $y[$sort_column])
      return 0;
	 

    return ($x[$sort_column] > $y[$sort_column]) ? -1 : 1;
  }

  private static function sort_numeric_asc($x, $y)
  {
    $sort_column = self::$sort_column;

    if(is_object($x))
	{
		if($x->$sort_column == $y->$sort_column)
			return 0;
			
		return ($x->$sort_column < $y->$sort_column) ? -1 : 1;
	}
	
	if($x[$sort_column] == $y[$sort_column])
      return 0;
	 

    return ($x[$sort_column] < $y[$sort_column]) ? -1 : 1;
  }
}
