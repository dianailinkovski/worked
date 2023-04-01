<?php

require_once APPPATH . 'libraries/simple_html_dom_new.php';

class mvexport
{
	private $dom;

	function __construct($contents)
	{
		$this->dom = str_get_html($contents);
	}

	/**
	 * Get the text from each search-result div
	 *
	 * @param String $selector
	 *
	 * @return array
	 */
	function getTitles($selector = '.search-result')
	{
		$titles = array();
		if(is_object($this->dom))
		{
			$containers = $this->dom->find($selector);
			foreach ($containers as $container)
			{
				$titles[] = $this->getTitle($container, true);
			}
		}

		return $titles;
	}

	/**
	 * Get the table elements by a selector
	 *
	 * @param String $selector
	 *
	 * @return array
	 */
	function getTables($selector = '.rptTable')
	{
		$tables = array();
		if(is_object($this->dom))
		{
			$contentTables = $this->dom->find($selector);
			foreach ($contentTables as $table)
			{
				$tables[] = $table->children();
			}
		}

		return $tables ? $tables : array();
	}

	/**
	 * Get the tables and corresponding titles from the dom
	 *
	 * @return array
	 */
	function getReport($table_selector = '.rptTable', $title_selector = '.search-result')
	{
		$report = array();
		$titles = $this->getTitles($title_selector);
		$tables = $this->getTables($table_selector);

		for ($i = 0, $n = count($tables); $i < $n; $i++)
		{
			$title = isset($titles[$i]) ? $titles[$i] : '';
			$report[] = array(
				'title' => $title,
				'table' => $tables[$i]
			);
		}

		return $report;
	}

	/**
	 *
	 * function getTitle
	 *
	 * @param <domnode>    $container
	 * @param <string>     $selector
	 *
	 */
	function getTitle($container, $text_only = false, $selector = '.prod-heading')
	{
		$heading = $container->find($selector, 0);

		return ($heading) ? ($text_only ? $heading->text() : $heading->innertext()) : '';
	}

	/**
	 *
	 * function getContentRow
	 *
	 * @param <domnode>    $node
	 *
	 */
	function getContentRow($node)
	{
		return $node->find('tr');
	}

	/**
	 *
	 * function getRowColumns
	 *
	 * @param <domnode>    $row
	 * @param <string>     $selector
	 *
	 */
	function getRowColumns($row, $selector)
	{
		return $row->find($selector);
	}

	/**
	 *
	 * function getContentTable
	 *
	 * @param <domnode>    $node
	 *
	 */
	function renderContentTable($node, $attributes = '')
	{
		$str = '';

		foreach($node->find('tr') as $key_ => $tr)
		{
			$bgColor = ($key_ % 2 == 0) ? 'bgcolor="#E7E7E8"' : '';
			$selector = ($key_ == 0) ? 'th' : 'td';

			$str .= '<tr '.$bgColor.'>';

			foreach($tr->find($selector) as $td)
			{
				$str .= '<'.$selector.' '.$attributes.'>'.$td->innertext().'</'.$selector.'>';
			}

			$str .= '</tr>';
		}

		return $str;
	}

	/**
	 *
	 * function calculateWidth
	 *
	 *
	 * @param <int>      $columns_count
	 *
	 */
	function calculateWidth($columns_count, $index)
	{
		if($columns_count == 10)
		{
			if($index == 9)
			{
				$width = 109;
			}
			elseif($index == 0 || $index == 1)
			{
				$width = 90;
			}
			else
			{
				$width = 53;
			}
		}
		else
		{
			if($index == 0)
			{
				$width = 160;
			}
			else
			{
				$width = ((660 - 160) / ($columns_count - 1));
			}
		}

		return $width;
	}

	/**
	 *
	 * function clear
	 *
	 */
	function clear()
	{
		if(is_object($this->dom))
		{
			$this->dom->clear();
		}
	}
}