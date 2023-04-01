package com.ngser.ekiosk;

import pdftron.Common.PDFNetException;
import pdftron.PDF.PDFDoc;
import pdftron.PDF.PDFNet;
import pdftron.PDF.PDFViewCtrl;
import android.app.Activity;
import android.content.res.Configuration;
import android.os.Bundle;
import android.util.Log;

public class PdfReaderActivity extends Activity {
	
	private PDFViewCtrl mPDFViewCtrl;
	private String pdfPath;
	
	@Override
	protected void onCreate(Bundle savedInstanceState) { 
		super.onCreate(savedInstanceState);
	    // Initialize the library
		
		Bundle b = this.getIntent().getExtras();
		pdfPath = b.getString("path");
	
		try {
			PDFNet.initialize(this, R.raw.pdfnet);
		} catch (PDFNetException e) { 
			// Do something... e.printStackTrace();
		}
		// Inflate the view and get a reference to PDFViewCtrl
		setContentView(R.layout.bibliotheque_pdfreader);
		
		mPDFViewCtrl = (PDFViewCtrl) findViewById(R.id.pdfviewctrl);
		
		mPDFViewCtrl.setPageViewMode(PDFViewCtrl.PAGE_VIEW_FIT_PAGE);
		
		if (getResources().getConfiguration().orientation == Configuration.ORIENTATION_LANDSCAPE) {
			mPDFViewCtrl.setPagePresentationMode(PDFViewCtrl.PAGE_PRESENTATION_FACING_COVER);
			
		}
		else {
			mPDFViewCtrl.setPagePresentationMode(PDFViewCtrl.PAGE_PRESENTATION_SINGLE);
		}
		
		mPDFViewCtrl.setZoomLimits(PDFViewCtrl.ZOOM_LIMIT_RELATIVE, 1, 100);
		
		
		// Load a document
		PDFDoc doc = null;
		//Resources res = getResources();
		//InputStream is = res.openRawResource(R.raw.sample_doc); 
		try {
			doc = new PDFDoc(pdfPath);
			mPDFViewCtrl.setDoc(doc);
			
			if (savedInstanceState != null) {
		    	int gotopage = savedInstanceState.getInt("currentPage", 0);
		    	mPDFViewCtrl.setCurrentPage(gotopage);
			}
		    
			
			// Or you can use the full path instead
			//doc = new PDFDoc("/mnt/sdcard/sample_doc.pdf");
		} catch (PDFNetException e) { 
			doc = null;
			e.printStackTrace(); 
		}
		
	}
	
	@Override
	protected void onPause() {
	// This method simply stops the current ongoing rendering thread, text // search thread, and tool
	super.onPause();
	if (mPDFViewCtrl != null) {
	        mPDFViewCtrl.pause();
	    }
	}
	@Override
	protected void onResume() {
	// This method simply starts the rendering thread to ensure the PDF // content is available for viewing.
	super.onResume();
	if (mPDFViewCtrl != null) {
	        mPDFViewCtrl.resume();
	    }
	}
	@Override
	protected void onDestroy() {
	// Destroy PDFViewCtrl and clean up memory and used resources. 
	super.onDestroy();
	if (mPDFViewCtrl != null) {
	        mPDFViewCtrl.destroy();
	    }
	}
	@Override
	public void onLowMemory() {
	// Call this method to lower PDFViewCtrl's memory consumption. super.onLowMemory();
	if (mPDFViewCtrl != null) {
	        mPDFViewCtrl.purgeMemory();
	    }
	}
	
	protected void onSaveInstanceState(Bundle outState) {
		if (mPDFViewCtrl != null) {
			outState.putInt("currentPage", mPDFViewCtrl.getCurrentPage());
			
		}
		super.onSaveInstanceState(outState);
	};
	
}
