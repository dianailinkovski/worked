<?php
/**
 * Override control filter to always redirect when access is denied.
 * 
 * @author Bruno LL. <https://github.com/brunoll>
 * @link https://github.com/todo
 * @copyright Copyright © 2015 Societe G-NeTiX Inc. - All Rights Reserved
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 * @package Filter
 */

class AccessControlAwaysRedirect extends CAccessControlFilter
{
	/**
	 * Default behavior goes to login page only if user is guest.
	 * This will make it so it will always redirect instead of throwing an error.
	 */
	protected function accessDenied($user,$message)
	{
		$user->loginRequired();
	}
}