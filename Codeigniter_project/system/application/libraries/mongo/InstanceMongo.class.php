<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Instance
 *
 * @author purelogics
 */
class InstanceMongo
{
  private static $searchType = array(
      'keyword' => 'phrase',
      'blacklist' => 'not',
      'twitter_name' => 'twitter',
      'facebook_name' => 'facebook',
  );

  public static function filtersList()
  {
    return array(
        'nospam',
        'nodups',
        'lang',
        'noprofanity',
        'nooffers',
        'nolinks',
        'topicid',
        'topiclevel',
        'polarity'
    );
  }

  public static function defaultText()
  {
    return array(
        'keyword' => 'Add keyword',
        'blacklist' => 'Keyword to avoid',
        'twitter_name' => 'Add Twitter name',
        'facebook_name' => 'Add Facebook name'
    );
  }

  /**
   * Don't change this, add a different location to the app.yml
   * @return <type>
   */
  public static function database()
  {
    $config = sfConfig::get('app_mongo_instance');

    return ($config['db'] ? $config['db'] : 'instance');
  }

  /**
   * Don't change this, add a different location to the app.yml
   * @return <type>
   */
  public static function collection()
  {
    $config = sfConfig::get('app_mongo_instance');

    return ($config['collection'] ? $config['collection'] : 'instance');
  }

  /**
   * function getInstance
   *
   * @instance_id        id of the instance
   *
   */
  public static function getInstances($projInstances)
  {
    $data = array();
    if(!empty($projInstances))
    {
      $mongo = cnMongo::connect()->database(self::database())->collection(self::collection());
      $ids = array();

      // make sure we have something to iterate through
      if(!is_array($projInstances))
        $projInstances = array($projInstances);

      foreach($projInstances as $key => $inst)
      {
        $ids[] = $mongo->autoId($key);
      }

      $data = $mongo->get(array('_id' => array('$in' => $ids)));
    }

    return $data;
  }

  /**
   * function getInstance
   *
   * @instance_id        id of the instance
   *
   */
  public static function getInstance($instance_id)
  {
    $mongo = cnMongo::connect()->database(self::database())->collection(self::collection());
    $instance = $mongo->get(array('_id' => $mongo->autoId($instance_id)));

    return ($instance) ? $instance[0] : null;
  }

  /**
   * function saveInstance
   *
   * @instance_id        id of the instance
   * @config             instance configurations
   * @keywords           keywords
   *
   */
  public static function saveInstance(array $instanceData, MongoId $instance_id = null)
  {
    $mongo = cnMongo::connect()->database(self::database())->collection(self::collection());

    // update or add (upsert true)
    if($instance_id)
      return $mongo->update(array('_id' => $instance_id), $instanceData, true);
    else
      return $mongo->insert($instanceData);
  }

  /**
   * function saveUser
   *
   * @instance_id        id of the instance
   * @screen_name        name used for matching in criteria
   * @type               type of the user
   * @new_name           new name of the user
   *    -- if have to delete a user, new name is false
   *
   */
  public static function updateUser($instance_id, $screen_name, $type, $new_name = false)
  {
    $mongo = cnMongo::connect()->database(self::database())->collection(self::collection());

    $criteria = array(
        '_id' => $mongo->autoId($instance_id),
        'instanceusers.screenname' => $new_name,
        'instanceusers.service' => self::$searchType[$type]
    );

    // if update or delete request, delete previous record
    if($screen_name != $new_name)
    {
      $user = self::userData($screen_name, array(), self::$searchType[$type]);

      $update = array(
        '$pull' => array(
          'instanceusers' => $user
        )
      );

      self::saveInstance($update, $instance_id);
    }

    // add or update
    if($new_name && !$mongo->getOne($criteria))
    {
      $user = self::userData($new_name, array(), self::$searchType[$type]);

      // update or add (upsert true)
      $update = array(
        '$addToSet' => array(
          'instanceusers' => $user
        )
      );

      self::saveInstance($update, $instance_id);
    }
  }

  /**
   * function saveKeywords
   *
   * @instance_id        id of the instance
   * @data               data
   *
   */
  public static function saveKeywordSettings($instance_id, $data)
  {
    $instance = self::getInstance($instance_id);

    $keywords = array();
    $users = array();

    // filter keyword
    foreach($data as $key => $val)
    {
      if(in_array($key, array('twitter_name', 'facebook_name')))
      {
        self::updateUser($instance_id, $val, $key, $val);
      }
      else
        $keywords = array_merge($keywords, self::filterKeyword($val, $keywords, self::$searchType[$key]));
    }

    if(!empty($keywords))
    {
      $update = array(
        '$set' => $keywords,
      );

      // save data
      self::saveInstance($update, $instance_id);
    }

    return $data;
  }

  /**
   * function user row
   *
   * @user        user to add
   * @users       previously saved users
   * @service
   *
   */
  public static function userData($user_name, $users, $service)
  {
    $data = array();

    if($users)
    {
      foreach($users as $user)
      {
        if($user['service'] == $service && $user['screenname'] == $user_name)
          return $data;
      }
    }

    if($user_name)
    {
      $data = array(
          'lowerscreenname' => strtolower($user_name),
          'service' => $service,
          'screenname' => $user_name
      );
    }

    return $data;
  }

  /**
   * function filterKeyword
   *
   * @keyword        keyword to add
   * @items          previously saved keywords
   * @search_type
   *
   */
  public static function filterKeyword($keyword, $items, $search_type = 'phrase')
  {
    $data = array();

    $keyword = Utility::convert_smart_quotes($keyword);

    if($search_type != 'not')
    {
      $search_type = self::searchWordType($keyword);
    }

    if(substr($keyword, 0, 1) == '"' && substr($keyword, strlen($keyword) - 1, 1) == '"')
    {
      $keyword = trim($keyword, '"');
    }

    $keyword = trim($keyword);

    if($items)
    {
      foreach($items as $item)
      {
        if($item['searchtype'] == $search_type && $item['name'] == $keyword)
          return $data;
      }
    }

    if($keyword)
    {
      $key = strtolower($keyword);
      $key = cnMongo::escapeKey($key);
      $data['keywords.' . $key] = array(
          'name'       => $keyword,
          'searchtype' => $search_type,
      );
    }

    return $data;
  }

  /**
   * function searchWordType
   *
   * @keyword
   *
   */
  public static function searchWordType($keyword)
  {
    $doubleQuote = substr_count($keyword, '"');
    //$singleQuote = substr_count($keyword, "'");

    if($doubleQuote > 1)
    {
      return 'phrase';
    }
    else
    {
      return 'and';
    }
  }

  /**
   * function deleteKeyword
   *
   * @instance_id        id of the instance
   * @item               item to be deleted
   * @type               type of the item to be deleted
   *
   */
  public static function deleteKeyword($instance_id, $item, $type)
  {
    $instance = self::getInstance($instance_id);

    if($instance)
    {
      // twitter and facebook
      if(in_array($type, array('twitter_name', 'facebook_name')))
      {
        self::updateUser($instance_id, $item, $type);
      }
      else
      {
        $row = self::filterKeyword($item, array(), self::$searchType[$type]);
        $update = array(
            '$unset' => $row
        );

        // save data
        self::saveInstance($update, $instance_id);
      }

      return true;
    }
  }

  /**
   * function updateKeyword
   *
   * @instance_id        id of the instance
   * @orig_keyword       original keyword
   * @item               edited value
   * @type               type of the item
   *
   */
  public static function updateKeyword($instance_id, $orig_keyword, $item, $type)
  {
    $instance = self::getInstance($instance_id);

    if($instance && trim($item))
    {
      // validate users
      if(in_array($type, array('twitter_name', 'facebook_name')))
      {
        if(!($item = cnValidate::socialUser($item, $type)))
          return -1;
      }

      // twitter and facebook
      if(in_array($type, array('twitter_name', 'facebook_name')))
      {
        self::updateUser($instance_id, $orig_keyword, $type, $item);
      }
      else
      {
        $row = self::filterKeyword($item, array(), self::$searchType[$type]);
        $delete = self::filterKeyword($orig_keyword, array(), self::$searchType[$type]);

        $update = array(
            '$unset' => $delete,
            '$set' => $row
        );

        // save data
        self::saveInstance($update, $instance_id);
      }
    }

    return $item;
  }

  /**
   * function updateKeyword
   *
   * @instance_id        id of the instance
   * @orig_keyword       original keyword
   * @item               edited value
   * @type               type of the item
   *
   */
  public static function isSearchTypeValid($type, $keyType)
  {
    return (
    ($type == 'blacklist' && $keyType == 'not') ||
    ($type == 'keyword' && $keyType != 'not')
    );
  }

  /**
   * function saveFilters
   *
   * @instance_id        id of the instance
   * @data               key-value pair of filters
   *
   */
  public static function saveFilters($instanceId, $data)
  {
    // topicid is required
    if(isset($data['topicid']) && !trim($data['topicid']))
      $data['topiclevel'] = 0;

    if(isset($data['lang']) && $data['lang'] == 1)
      $data['lang'] = 'en';
    else
      $data['lang'] = false;

    $config = array();

    // iterate over data
    foreach($data as $key => $value)
    {
      if(is_numeric($value))
        $value = (int) $value;

      $config[cnMongo::escapeKey($key)] = $value;
    }

    $update = array(
        '$set' => $config
    );

    // save data
    self::saveInstance($update, $instanceId);

    return true;
  }

  /**
   * function deleteInstances
   *
   * @param MongoId $instanceId
   */
  public static function deleteInstance(MongoId $instanceId)
  {
    $mongo = cnMongo::connect()->database(self::database())->collection(self::collection());
    $mongo->delete(array('_id' => $instanceId));
  }

  /**
   * function saveConfigJson
   *
   * @instance_id        id of the instance
   * @data               key-value pair data
   *
   */
  public static function saveConfigJson($instance_id, $data)
  {
    $instance = self::getInstance($instance_id);

    $update = array(
      '$set' => array(
        'config_json' => $data
      )
    );
    print_r($update);

    // save data
    //self::saveInstance($instanceId, $update);

    return true;
  }
}