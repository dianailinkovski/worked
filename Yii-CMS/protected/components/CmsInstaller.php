<?php
/**
 * Cms installation component.
 * 
 * @author Bruno LL. <https://github.com/brunoll>
 * @link https://github.com/todo
 * @copyright Copyright © 2015 Societe G-NeTiX Inc. - All Rights Reserved
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 * @package Core
 */

class CmsInstaller extends CApplicationComponent
{
	/**
	 * @var boolean is cms installed.
	 */
	private $_installed;
	
	/**
	 * Check if the cms is installed or not.
	 * @return boolean is installed
	 */
	public function getInstalled() 
	{
		if ($this->_installed === null) 
		{
			$app = Yii::app();
			
			$schema = array(
	            "SELECT COUNT(*) FROM cms_alias",
	            "SELECT COUNT(*) FROM cms_section",
	        );
	
			// if any error happenned then it's not installed
			try {
	            foreach($schema as $sql) 
	            {
	                $command = $app->db->createCommand($sql);
	                $command->queryScalar();
	            }
				$installed = true;
			} 
			catch( CDbException $e ) { $installed = false; }	
			
			$this->_installed = $installed;	
		}
		
		return $this->_installed;
	}
	
	/**
	 * Install the cms from the sql in data/schema.sql.
	 * @throws CHttpException on sql error
	 */
	public function install()
	{
		if (!$this->installed)
		{
			$app = Yii::app();
			
			$schema = file_get_contents(dirname(__FILE__).'/../data/schema.sql');
		
	        $schema = preg_split("/(?<!\\\);\s*/", trim($schema, ';'));

	        $txn = $app->db->beginTransaction();

	        try {
	            // Execute each query in the schema.
	            foreach($schema as $sql) 
	            {
	            	$lines = preg_split("/((?<!\\\|\r)\n)|((?<!\\\)\r\n)/", $sql);
	            	$sqlCommand = '';
	            	foreach ($lines as $line) 
	            	{
	            		$sqlCommand .= $line."\n";
	            	}
	            	if ($sql != '')
	            	{
	                	$command = $app->db->createCommand($sql);
	                	$command->execute();
	            	}
	            }
	            // All commands executed successfully, commit.
	            $txn->commit();
	        } 
	        catch( CDbException $e ) 
	        {
	            // Something went wrong, rollback.
	            $txn->rollback();

	            throw new CHttpException(500, $e->errorInfo[2]);
	        }
		}
	}
}