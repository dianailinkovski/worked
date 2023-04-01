<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * pdf_helper.php
 *
 *
 */

/**
 * Compress html
 *
 * @param String $buffer
 */
function compressHTML($buffer)
{
	$search = array(
		'/\>[^\S ]+/s',
		'/[^\S ]+\</s',
		'/(\s)+/s'
	);
	$replace = array(
		'>',
		'<',
		'\\1'
	);
	$buffer = preg_replace($search, $replace, $buffer);

	return $buffer;
}

/**
 * Write HTML to a pdf file
 *
 * @param String $html
 * @param String $fileName
 */

/*
// Controller::writePDF() as of 08/07/2012
function writePDF($html,$fileName)
{
	require_once(APPPATH.'3rdparty/tcpdf/tcpdf.php');
	// create new PDF document
	$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

	// set default header data
	$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH,PDF_HEADER_TITLE,PDF_HEADER_STRING);

	// set default monospaced font
	$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

	//set margins
	$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
	$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
	$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

	//set auto page breaks
	$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

	//set image scale factor
	$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

	//set some language-dependent strings
	//$pdf->setLanguageArray($l);

	// ---------------------------------------------------------

	// set default font subsetting mode
	$pdf->setFontSubsetting(true);

	// Set font
	// dejavusans is a UTF-8 Unicode font, if you only need to
	// print standard ASCII chars, you can use core fonts like
	// helvetica or times to reduce file size.
	$pdf->SetFont('helvetica', '', 8, '', true);

	// Add a page
	// This method has several options, check the source code documentation for more information.

	// Print text using writeHTML()
	if(is_array($html))
	{
		foreach($html as $htm_)
		{
			$pdf->AddPage();
			$pdf->writeHTML($this->compressHTML($htm_), true, false, true, false);
		}
	}
	else
	{
		$pdf->AddPage();
		$html = $this->compressHTML($html);
		$html = str_replace(array('<head>', '<body>', '</head>', '</body>', '</html>', '<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">', '<meta http-equiv="content-type" content="text/html; charset=utf-8">'), array('','','','','','',''), $html);

		$pdf->writeHTML($html, true, false, true, false);
	}


	// $filePath = dirname(BASEPATH)."/warehouse/$fileName";

	// Close and output PDF document
	$pdf->Output($fileName, 'I');
}

 // catalog::writePDF() as of 08/07/2012
 function writePDF($html, $fileName)
	{

		// load the tcpdf class
		if ( ! class_exists('TCPDF'))
			require(APPPATH . '3rdparty/tcpdf/tcpdf.php');

		// create new PDF document
		$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

		// set header data
		$pdf->SetHeaderData("", 0, "", "");

		// set default monospaced font
		$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

		//set margins
		$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);

		//set auto page breaks
		$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

		//set image scale factor
		$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

		//set some language-dependent strings
		//$pdf->setLanguageArray($l);
		// ---------------------------------------------------------
		// set default font subsetting mode
		$pdf->setFontSubsetting(true);

		// Set font
		// dejavusans is a UTF-8 Unicode font, if you only need to
		// print standard ASCII chars, you can use core fonts like
		// helvetica or times to reduce file size.
		$pdf->SetFont('helvetica', '', 10, '', true);

		// Add a page
		// This method has several options, check the source code documentation for more information.
		// Print text using writeHTML()
		if (is_array($html))
		{
			foreach ($html as $htm_)
			{
				$pdf->AddPage();
				$pdf->writeHTML($this->compressHTML($htm_), true, false, true, false);
			}
		} else
		{
			$pdf->AddPage();
			$html = $this->compressHTML($html);
			$html = str_replace(array('<head>', '<body>', '</head>', '</body>', '</html>', '<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">', '<meta http-equiv="content-type" content="text/html; charset=utf-8">'), array('', '', '', '', '', '', ''), $html);

			$pdf->writeHTML($html, true, false, true, false);
		}


		$filePath = dirname(BASEPATH) . "/warehouse/$fileName";

		// Close and output PDF document
		$pdf->Output($fileName, 'I');
	}

 // Reports::writePDF() as of 08/07/2012
 function writePDF($html, $fileName){
		// load the tcpdf class
		if ( ! class_exists('TCPDF'))
			require(APPPATH . '3rdparty/tcpdf/tcpdf.php');

		// create new PDF document
		$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
		$pdf->setPrintHeader(false);
		// set default header data
		$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);
		// set header and footer fonts
		//$pdf->setHeaderFont(array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
		//$pdf->setHeaderFont(array('Verdana,Geneva,sans-serif', '', '12px'));
		$pdf->setFooterFont(array(PDF_FONT_NAME_DATA, '', '7px'));
		// set default monospaced font
		$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
		if(isset($this->data->filename) && $this->data->filename == 'Price violations'){
			$this->data->title = str_replace(' pricing activity ', ' Price Violations ', $this->data->title);
		}
		$barcode = '&copy; ' . date('Y') . ', Sticky Business, LLC - www.JustSticky.com'; //. $this->data->headerDate
		$pdf->setBarcode($barcode);
		//set margins
		$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
		$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
		$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
		//$pdf->AliasNbPages('');
		//set auto page breaks
		$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
		//set image scale factor
		$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
		//set some language-dependent strings
		//$pdf->setLanguageArray($l);
		// ---------------------------------------------------------
		// set default font subsetting mode
		$pdf->setFontSubsetting(true);
		// Set font
		// dejavusans is a UTF-8 Unicode font, if you only need to
		// print standard ASCII chars, you can use core fonts like
		// helvetica or times to reduce file size.
		$pdf->SetFont('helvetica', '', 5, '', true);
		// Add a page
		// This method has several options, check the source code documentation for more information.
		// Print text using writeHTML()
		if(is_array($html)){
			foreach ($html as $htm_){
				$pdf->AddPage();
				$pdf->writeHTML($this->compressHTML($htm_), true, false, true, false);
			}
		}else{
			$pdf->AddPage();
			$html = $this->compressHTML($html);
			$html = str_replace(array('<head>', '<body>', '</head>', '</body>', '</html>', '<html>', '<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">', '<meta http-equiv="content-type" content="text/html; charset=utf-8" />'), array('', '', '', '', '', '', '', ''), $html);
			$pdf->writeHTML($html, true, false, true, false);
		}
		// Close and output PDF document
		$pdf->Output($fileName, 'I');
	}
 */

function tcpdf_write($html, $fileName, array $opts = array())
{
	$pdf = tcpdfTemplate($opts);

	if( ! is_array($html)){
		$html = array($html);
	}

	foreach ($html as $htm_){
		@tcpdf_addPage($pdf, $htm_);
	}

	// Close and output PDF document
	$pdf->Output($fileName, 'I');
}

/**
 * Return an instance of TCPDF that is ready to be written to
 *
 * @param array $opts { default : array }
 * @return TCPDF
 */
function tcpdfTemplate(array $opts = array())
{
	$default = array(
		'encoding' => 'UTF-8',
		'font' => 'helvetica',
		'font-size' => 5,
		'footer-font-size' => '7px',
		'barcode' => '&copy; ' . date('Y') . ', Sticky Business, LLC - www.JustSticky.com' //. $this->data->headerDate
	);

	foreach ($default as $key => $value)
		if ( ! isset($opts[$key]))
			$opts[$key] = $value;

	// create new PDF document

	// load the tcpdf class
	if ( ! class_exists('TCPDF'))
		require(APPPATH . '3rdparty/tcpdf/tcpdf.php');

	$pdf = new TCPDF(
		PDF_PAGE_ORIENTATION,
		PDF_UNIT,
		PDF_PAGE_FORMAT,
		true,
		$opts['encoding'],
		false
	);

	$pdf->setPrintHeader(false);

	// set default header data
	$pdf->SetHeaderData(
		PDF_HEADER_LOGO,
		PDF_HEADER_LOGO_WIDTH,
		PDF_HEADER_TITLE,
		PDF_HEADER_STRING
	);

	//set margins
	$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
	$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
	$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
	//$pdf->AliasNbPages('');

	//set auto page breaks
	$pdf->SetAutoPageBreak(true, PDF_MARGIN_BOTTOM);

	//set image scale factor
	$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

	//set some language-dependent strings
	//$pdf->setLanguageArray($l);

	// set header and footer fonts
	//$pdf->setHeaderFont(array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
	//$pdf->setHeaderFont(array('Verdana,Geneva,sans-serif', '', '12px'));
	$pdf->setFooterFont(
		array(
			PDF_FONT_NAME_DATA,
			'',
			$opts['footer-font-size']
		)
	);

	// set default monospaced font
	$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

	// set default font subsetting mode
	$pdf->setFontSubsetting(true);

	// Set font
	// dejavusans is a UTF-8 Unicode font, if you only need to
	// print standard ASCII chars, you can use core fonts like
	// helvetica or times to reduce file size.
	$pdf->SetFont($opts['font'], '', $opts['font-size'], '', true);

	// set barcode
	$pdf->setBarcode($opts['barcode']);

	return $pdf;
}

/**
 * Procedural way to add a page to a TCPDF object
 *
 * @param TCPDF $pdf
 * @param String $html
 */
function tcpdf_addPage(TCPDF &$pdf, $html)
{
	// Add a page
	// This method has several options, check the source code documentation for more information.
	// Print text using writeHTML()
	$pdf->AddPage();
	$html = compressHTML($html);
	$html = str_replace(
		array(
			'<head>',
			'<body>',
			'</head>',
			'</body>',
			'</html>',
			'<html>',
			'<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">',
			'<meta http-equiv="content-type" content="text/html; charset=utf-8" />'
		),
		'',
		$html
	);
	$pdf->writeHTML($html, true, false, true, false);
}

function tcpdf_options($type = 'default')
{
	switch (strtolower($type))
	{
		case 'catalog':
			$opts = array(
				'encoding' => 'UTF-8',
				'font' => 'helvetica',
				'font-size' => 10,
				'footer-font-size' => '7px',
				'barcode' => '&copy; ' . date('Y') . ', Sticky Business, LLC - www.JustSticky.com' //. $this->data->headerDate
			);
			break;
		case 'reports':
			$opts = array(
				'encoding' => 'UTF-8',
				'font' => 'helvetica',
				'font-size' => 5,
				'footer-font-size' => '7px',
				'barcode' => '&copy; ' . date('Y') . ', Sticky Business, LLC - www.JustSticky.com' //. $this->data->headerDate
			);
			break;
		case 'default':
		default:
			$opts = array(
				'encoding' => 'UTF-8',
				'font' => 'helvetica',
				'font-size' => 8,
				'footer-font-size' => '7px',
				'barcode' => '&copy; ' . date('Y') . ', Sticky Business, LLC - www.JustSticky.com' //. $this->data->headerDate
			);
	}

	return $opts;
}
