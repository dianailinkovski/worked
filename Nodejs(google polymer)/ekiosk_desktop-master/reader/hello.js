/* -*- Mode: Java; tab-width: 2; indent-tabs-mode: nil; c-basic-offset: 2 -*- */
/* vim: set shiftwidth=2 tabstop=2 autoindent cindent expandtab: */

//
// See README for overview
//

'use strict';

//
// Fetch the PDF document from the URL using promises
//
/*
PDFJS.getDocument('helloworld.pdf').then(function(pdf) {
	
  var numPages = pdf.numPages;
  // Using promise to fetch the page

  
  var wrapperpdf = document.getElementById('wrapper-pdf');
  
  for (var i = 1; i <= numPages; i++) {
	  
	var scale = 1.5;
    var viewport = page.getViewport(scale);

    var pageDisplayWidth = viewport.width;
    var pageDisplayHeight = viewport.height;

    var pageDivHolder = document.createElement('div');
    pageDivHolder.className = 'pdfpage';
    pageDivHolder.style.width = pageDisplayWidth + 'px';
    pageDivHolder.style.height = pageDisplayHeight + 'px';
    div.appendChild(pageDivHolder);

    // Prepare canvas using PDF page dimensions
    var canvas = document.createElement('canvas');
    var context = canvas.getContext('2d');
    canvas.width = pageDisplayWidth;
    canvas.height = pageDisplayHeight;
    wrapperpdf.appendChild(canvas);

    // Render PDF page into canvas context
    var renderContext = {
      canvasContext: context,
      viewport: viewport
    };
    page.render(renderContext).promise.then(callback);
*/
	  
	  
	  /*
	  var canvas = document.createElement("CANVAS");
	  var divIdName =  'page-'+i;
	  canvas.setAttribute('id',divIdName);
	  
	  wrapperpdf.appendChild(canvas);
	  
	  pdf.getPage(i).then(function(page) {
		  
		var scale = 1.0;
		var viewport = page.getViewport(scale);
	
		//
		// Prepare canvas using PDF page dimensions
		//
		//var canvas = document.getElementById('the-canvas');
		var context = canvas.getContext('2d');
		canvas.height = viewport.height;
		canvas.width = viewport.width;
	
		//
		// Render PDF page into canvas context
		//
		var renderContext = {
		  canvasContext: context,
		  viewport: viewport
		};
		page.render(renderContext);
		
	  });
	  */
//  }
  
  
  // Using promise to fetch the page
  /*
  pdf.getPage(1).then(function(page) {
    var scale = 1.5;
    var viewport = page.getViewport(scale);

    //
    // Prepare canvas using PDF page dimensions
    //
    var canvas = document.getElementById('the-canvas');
    var context = canvas.getContext('2d');
    canvas.height = viewport.height;
    canvas.width = viewport.width;

    //
    // Render PDF page into canvas context
    //
    var renderContext = {
      canvasContext: context,
      viewport: viewport
    };
    page.render(renderContext);
	
  });
  */
//});

function renderPage(div, pdf, pageNumber, callback) {
  pdf.getPage(pageNumber).then(function(page) {
    var scale = 1.5;
    var viewport = page.getViewport(scale);

    var pageDisplayWidth = viewport.width;
    var pageDisplayHeight = viewport.height;

    var pageDivHolder = document.createElement('div');
    pageDivHolder.className = 'pdfpage';
    pageDivHolder.style.width = pageDisplayWidth + 'px';
    pageDivHolder.style.height = pageDisplayHeight + 'px';
    div.appendChild(pageDivHolder);

    // Prepare canvas using PDF page dimensions
    var canvas = document.createElement('canvas');
    var context = canvas.getContext('2d');
    canvas.width = pageDisplayWidth;
    canvas.height = pageDisplayHeight;
    pageDivHolder.appendChild(canvas);

    // Render PDF page into canvas context
    var renderContext = {
      canvasContext: context,
      viewport: viewport
    };
    page.render(renderContext).promise.then(callback);

    // Prepare and populate form elements layer
    var formDiv = document.createElement('div');
    pageDivHolder.appendChild(formDiv);

    setupForm(formDiv, page, viewport);
  });
}

// Fetch the PDF document from the URL using promices
PDFJS.getDocument("../issue-for-reader.pdf").then(function getPdfForm(pdf) {
  // Rendering all pages starting from first
  //var viewer = document.getElementById('viewer');
  var viewer = document.getElementById('wrapper-pdf');
  var pageNumber = 1;
  renderPage(viewer, pdf, pageNumber++, function pageRenderingComplete() {
    if (pageNumber > pdf.numPages) {
      return; // All pages rendered
    }
    // Continue rendering of the next page
    renderPage(viewer, pdf, pageNumber++, pageRenderingComplete);
  });
});