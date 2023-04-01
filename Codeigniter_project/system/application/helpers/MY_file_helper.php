<?php
/**
 * MY_file_helper
 *
 * Add additional functions to CI's file_helper
 */

/**
 * Get the file extension from a filename
 *
 * @param String $path
 * @return String
 */
function file_extension($path)
{
	return substr(strrchr($path, '.'), 1);
}