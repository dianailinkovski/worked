<?php

/**
 * Extends CI's Config library for interaction
 * with SV configuration values
 *
 * @author J$
 */
class MY_Config extends CI_Config
{

	/**
	* Generate the URL for a public facing page
	*
	* @param String $uri { defaul : '' }
	* @return String
	*/
	public function public_url($uri = '') {
		if (is_array($uri))
			$uri = implode('/', $uri);

		if ($uri == '')
		{
			return $this->slash_item('public_base_url') . $this->item('index_page');
		}
		else
		{
			$suffix = ($this->item('url_suffix') == FALSE) ? '' : $this->item('url_suffix');
			return $this->slash_item('public_base_url').$this->slash_item('index_page').trim($uri, '/').$suffix;
		}
	}

}
