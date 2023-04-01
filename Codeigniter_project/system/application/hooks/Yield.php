<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Yield_hook {

	public function do_yield() {
		$CI =& get_instance();
		$CI->_pre_display();

		$layout = $CI->_get_layout();
		$view = $CI->_get_view();

		$output = file_exists(APPPATH . 'views/' . $view . '.php') ?
			$CI->load->view($view, $CI->data, TRUE)
			:
			$CI->output->get_output();

		if ( ! empty($layout)) {
			$vars = $CI->get_tpl_vars();
			$vars['content'] = $output;
			$vars['javascript_files'] = $CI->_get_javascript_files();

			$output = $CI->load->view('layouts/' . $layout, $vars, TRUE);
		}

		$CI->output->_display($output);
	}

}
