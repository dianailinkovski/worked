<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * html_helper.php
 *
 * An extension of CI's HTML Helper
 */
function link_js($file) {
	//$version = '';
	//$filepath = get_root_folder() . 'js/' . $file;
	//if (strpos($filepath,'?')===false && file_exists($filepath)) $version = '?v='.filemtime($filepath);
	//$file .= $version;
	return '<script type="text/javascript" src="' . base_url() . 'js/' . $file . '"></script>';
}

function script_tag($src = '', $type = 'text/javascript', $index_page = FALSE) {
	$CI =& get_instance();
	$script = '<script';
	if (is_array($src)) {
		foreach ($src as $k => $v) {
			if ($k == 'src' AND strpos($v, '://') === FALSE) {
				if ($index_page === TRUE) {
					$script .= ' src="' . $CI->config->site_url($v) . '"';
				} else {
					$script .= ' src="' . $CI->config->slash_item('base_url') . $v . '"';
				}
			} else {
				$script .= "$k=\"$v\"";
			}
		}

		$script .= "></script>\n";
	} else {
		if (strpos($src, '://') !== FALSE) {
			$script .= ' src="' . $src . '" ';
		} elseif ($index_page === TRUE) {
			$script .= ' src="' . $CI->config->site_url($src) . '" ';
		} else {
			$script .= ' src="' . $CI->config->slash_item('base_url') . $src . '" ';
		}

		$script .= ' type="' . $type . '"';
		$script .= '></script>' . "\n";
	}

	return $script;
}

/* End of file html_helper.php */
/* Location: ./system/application/helpers/html_helper.php */