<?php

function process_sub_nav($nav_item)
{
	$sub_item_htm = "";

	if (isset($nav_item["sub"]) && $nav_item["sub"])
	{
		$sub_nav_item = $nav_item["sub"];

		$sub_item_htm = $this->process_sub_nav($sub_nav_item);
	}
	else
	{
		$sub_item_htm .= '<ul>';

		foreach ($nav_item as $key => $sub_item)
		{
			$url = isset($sub_item["url"]) ? $sub_item["url"] : "#";
			$url_target = isset($sub_item["url_target"]) ? 'target="'.$sub_item["url_target"].'"' : "";
			$icon = isset($sub_item["icon"]) ? '<i class="fa fa-lg fa-fw '.$sub_item["icon"].'"></i>' : "";
			$nav_title = isset($sub_item["title"]) ? $sub_item["title"] : "(No Name)";
			$label_htm = isset($sub_item["label_htm"]) ? $sub_item["label_htm"] : "";
				
			$sub_item_htm .=
			'<li '.(isset($sub_item["active"]) ? 'class = "active"' : '').'>' .
			'<a href="'.$url.'" '.$url_target.'>'.$icon.' '.$nav_title.$label_htm.'</a>' .
			(isset($sub_item["sub"]) ? process_sub_nav($sub_item["sub"]) : '') .
			'</li>';
		}

		$sub_item_htm .= '</ul>';
	}
		
	return $sub_item_htm;
}

/**
 * Gets an environment variable from available sources, and provides emulation
 * for unsupported or inconsistent environment variables (i.e. DOCUMENT_ROOT on
 * IIS, or SCRIPT_NAME in CGI mode).  Also exposes some additional custom
 * environment information.
 *
 * @param  string $key Environment variable name.
 * @return string Environment variable setting.
 * @link http://book.cakephp.org/view/701/env
 */
if (!function_exists('env'))
{

	function env($key)
	{
		if ($key == 'HTTPS')
		{
			if (isset($_SERVER['HTTPS']))
			{
				return (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
			}
			return (strpos(env('SCRIPT_URI'), 'https://') === 0);
		}

		if ($key == 'SCRIPT_NAME')
		{
			if (env('CGI_MODE') && isset($_ENV['SCRIPT_URL']))
			{
				$key = 'SCRIPT_URL';
			}
		}

		$val = null;
		if (isset($_SERVER[$key]))
		{
			$val = $_SERVER[$key];
		}
		elseif (isset($_ENV[$key]))
		{
			$val = $_ENV[$key];
		}
		elseif (getenv($key) !== false)
		{
			$val = getenv($key);
		}

		if ($key === 'REMOTE_ADDR' && $val === env('SERVER_ADDR'))
		{
			$addr = env('HTTP_PC_REMOTE_ADDR');
			if ($addr !== null)
			{
				$val = $addr;
			}
		}

		if ($val !== null)
		{
			return $val;
		}

		switch ($key)
		{
			case 'SCRIPT_FILENAME':
				if (defined('SERVER_IIS') && SERVER_IIS === true)
				{
					return str_replace('\\\\', '\\', env('PATH_TRANSLATED'));
				}
				break;
			case 'DOCUMENT_ROOT':
				$name = env('SCRIPT_NAME');
				$filename = env('SCRIPT_FILENAME');
				$offset = 0;
				if (!strpos($name, '.php'))
				{
					$offset = 4;
				}
				return substr($filename, 0, strlen($filename) - (strlen($name) + $offset));
				break;
			case 'PHP_SELF':
				return str_replace(env('DOCUMENT_ROOT'), '', env('SCRIPT_FILENAME'));
				break;
			case 'CGI_MODE':
				return (PHP_SAPI === 'cgi');
				break;
			case 'HTTP_BASE':
				$host = env('HTTP_HOST');
				if (substr_count($host, '.') !== 1)
				{
					return preg_replace('/^([^.])*/i', null, env('HTTP_HOST'));
				}
				return '.' . $host;
				break;
		}
		return null;
	}

}

if (!function_exists('get_hour_dropdown_array'))
{
    function get_hour_dropdown_array()
    {
        $times = array();
        
        $curTime = "1:00 AM";
        
        $times[1] = $curTime;
        
        for ($i = 0; $i < 22; $i++)
        {
            $strTime = strtotime($curTime);
             
            $ind = date('G', strtotime('+1 hours', $strTime));
             
            $times[$ind] = date('g:i A', strtotime('+1 hours', $strTime));
             
            $curTime = date('g:i a', strtotime('+1 hours', $strTime));
        }
        
        return $times;
    }
}

/**
 * Generate a random UUID
 *
 * @see http://www.ietf.org/rfc/rfc4122.txt
 * @return RFC 4122 UUID
 * @static
 */
if (!function_exists('uuid'))
{

	function uuid()
	{
		$node = env('SERVER_ADDR');
		$pid = null;

		if (strpos($node, ':') !== false)
		{
			if (substr_count($node, '::'))
			{
				$node = str_replace('::', str_repeat(':0000', 8 - substr_count($node, ':')) . ':', $node);
			}
			$node = explode(':', $node);
			$ipv6 = '';

			foreach ($node as $id)
			{
				$ipv6 .= str_pad(base_convert($id, 16, 2), 16, 0, STR_PAD_LEFT);
			}
			$node = base_convert($ipv6, 2, 10);

			if (strlen($node) < 38)
			{
				$node = null;
			}
			else
			{
				$node = crc32($node);
			}
		}
		elseif (empty($node))
		{
			$host = env('HOSTNAME');

			if (empty($host))
			{
				$host = env('HOST');
			}

			if (!empty($host))
			{
				$ip = gethostbyname($host);

				if ($ip === $host)
				{
					$node = crc32($host);
				}
				else
				{
					$node = ip2long($ip);
				}
			}
		}
		elseif ($node !== '127.0.0.1')
		{
			$node = ip2long($node);
		}
		else
		{
			$node = null;
		}

		if (empty($node))
		{
			$CI = & get_instance();
			$node = crc32($CI->config->item('security_salt'));
		}

		if (function_exists('zend_thread_id'))
		{
			$pid = zend_thread_id();
		}
		else
		{
			$pid = getmypid();
		}

		if (!$pid || $pid > 65535)
		{
			$pid = mt_rand(0, 0xfff) | 0x4000;
		}

		list($timeMid, $timeLow) = explode(' ', microtime());
		
		$uuid = sprintf("%08x-%04x-%04x-%02x%02x-%04x%08x", (int) $timeLow, (int) substr($timeMid, 2) & 0xffff, mt_rand(0, 0xfff) | 0x4000, mt_rand(0, 0x3f) | 0x80, mt_rand(0, 0xff), $pid, $node);

		return $uuid;
	}

}