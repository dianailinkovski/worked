<?php
/**
 * User Identity (for admin users)
 * 
 * @author Bruno LL. <https://github.com/brunoll>
 * @link https://github.com/todo
 * @copyright Copyright © 2015 Societe G-NeTiX Inc. - All Rights Reserved
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 * @package Other
 */

class UserIdentity extends CUserIdentity
{
    private $_id;
    
    public function authenticate()
    {
        $record=User::model()->findByAttributes(array('username'=>$this->username));
        if($record===null)
            $this->errorCode=self::ERROR_USERNAME_INVALID;
        else if($record->password!==md5($this->password))
            $this->errorCode=self::ERROR_PASSWORD_INVALID;
        else
        {
            $this->_id='Admin-'.$record->id;
            $this->errorCode=self::ERROR_NONE;
        }
        return !$this->errorCode;
    }
 
    /**
     * id is prefixed by Admin- because the id can come from multiple tables and this id is in the User table.
     * @return string the unique user id;
     */
    public function getId()
    {
        return $this->_id;
    }
}