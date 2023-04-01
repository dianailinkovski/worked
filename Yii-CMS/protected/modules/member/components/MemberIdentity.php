<?php
/**
 * User Identity for member.
 * 
 * @author Bruno LL. <https://github.com/brunoll>
 * @link https://github.com/todo
 * @copyright Copyright © 2015 Societe G-NeTiX Inc. - All Rights Reserved
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 * @package Other
 */

class MemberIdentity extends CUserIdentity
{
    private $_id;
    
    public function authenticate($skipPwVerification=false)
    {
        $record=Member::model()->findByAttributes(array('email'=>$this->username), "activation_hash = ''");
        if($record===null)
            $this->errorCode=self::ERROR_USERNAME_INVALID;
        else if(!$skipPwVerification && $record->password!==md5($this->password))
            $this->errorCode=self::ERROR_PASSWORD_INVALID;
        else
        {
        	$this->_id='Member-'.$record->id;
        	$this->errorCode=self::ERROR_NONE;

        	$record->last_login_date = date('Y-m-d H:i:s');

        	if (!$record->save())
        		throw new CHttpException(500);
        }
        return !$this->errorCode;
    }
 
    public function getId()
    {
        return $this->_id;
    }
}