/*
   Copyright 2012 Harri Smatt

   Licensed under the Apache License, Version 2.0 (the "License");
   you may not use this file except in compliance with the License.
   You may obtain a copy of the License at

       http://www.apache.org/licenses/LICENSE-2.0

   Unless required by applicable law or agreed to in writing, software
   distributed under the License is distributed on an "AS IS" BASIS,
   WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
   See the License for the specific language governing permissions and
   limitations under the License.
 */

package fi.harism.curl;

import java.io.ByteArrayOutputStream;
import java.io.File;
import java.io.FileOutputStream;
import java.io.IOException;
import java.io.InputStream;

import net.sf.andpdf.nio.ByteBuffer;
import net.sf.andpdf.refs.HardReference;
import pdftron.Common.PDFNetException;
import pdftron.PDF.PDFDoc;
import pdftron.PDF.PDFDraw;
import pdftron.PDF.PDFNet;
import pdftron.PDF.Page;
import android.app.ProgressDialog;
import android.content.pm.ActivityInfo;
import android.content.res.Configuration;
import android.graphics.Bitmap;
import android.graphics.Bitmap.Config;
import android.graphics.BitmapFactory;
import android.graphics.Canvas;
import android.graphics.Matrix;
import android.graphics.Paint;
import android.graphics.Point;
import android.os.AsyncTask;
import android.os.Bundle;
import android.util.DisplayMetrics;
import android.util.Log;
import android.view.Display;

import com.actionbarsherlock.app.SherlockActivity;
import com.actionbarsherlock.view.Window;
import com.ngser.ekiosk.R;
import com.sun.pdfview.PDFImage;
import com.sun.pdfview.PDFPaint;

/**
 * Simple Activity for curl testing.
 * 
 * @author harism
 */
public class CurlActivity extends SherlockActivity {

	private CurlView mCurlView;
	public Bitmap[] bitmaps;
	public int viewSize = 0;
	public int pageCount = 0;
	public int currentRenderingPage = 0;

	public float scale = 1.0f;

	String pdfPath;

	PDFDraw draw;
	PDFDoc tiger_doc;

	public int gotopage = 0;

	private float issueRatio = 0.5f;
	private VerifierImages imageTask;

	private int openglWidth = 0, openglHeight = 0;

	@Override
	public void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);

		try {
			PDFNet.initialize(this, R.raw.pdfnet);
		} catch (PDFNetException e) {
			// Do something... e.printStackTrace();
		}

		Bundle b = this.getIntent().getExtras();
		pdfPath = b.getString("path");

		requestWindowFeature(Window.FEATURE_INDETERMINATE_PROGRESS);

		setContentView(R.layout.pdf_reader_pagecurl);

		// StrictMode.setThreadPolicy((new
		// android.os.StrictMode.ThreadPolicy.Builder()).permitAll().build());
		// Settings
		PDFImage.sShowImages = true; // show images
		PDFPaint.s_doAntiAlias = true; // make text smooth
		HardReference.sKeepCaches = true; // save images in cache
		if (savedInstanceState != null) {
			gotopage = savedInstanceState.getInt("currentPage", 0);
		} else {
			gotopage = 0;
		}

		Log.e("gotopage", String.valueOf(gotopage));

		pdfLoadImages();

	}

	protected void onDestroy() {
		super.onDestroy();
		if (imageTask != null) {
			if (imageTask.progressDialog.isShowing()) {
				imageTask.progressDialog.dismiss();
			}
			imageTask.cancel(false);
			imageTask = null;
		}

		/*
		 * try { tiger_doc.close(); } catch (PDFNetException e) {
		 * e.printStackTrace(); }
		 */
	};

	protected void onSaveInstanceState(Bundle outState) {
		if (mCurlView != null) {
			outState.putInt("currentPage", mCurlView.getCurrentIndex());
			Log.e("currentPage", String.valueOf(mCurlView.getCurrentIndex()));
		}
		super.onSaveInstanceState(outState);
	};

	ByteBuffer readToByteBuffer(InputStream inputStream) throws IOException {
		ByteArrayOutputStream outputStream = new ByteArrayOutputStream();

		byte buf[] = new byte[1024];
		int len;
		try {
			while ((len = inputStream.read(buf)) != -1) {
				outputStream.write(buf, 0, len);
			}
			inputStream.close();
		} catch (IOException e) {

		}

		ByteBuffer byteData = ByteBuffer.wrap(outputStream.toByteArray());
		outputStream.close();
		return byteData;
	}

	boolean getOrientation() {
		if (getResources().getConfiguration().orientation == Configuration.ORIENTATION_LANDSCAPE)
			return false;
		return true;
	}

	void loadImageInArray() {

		setRequestedOrientation(ActivityInfo.SCREEN_ORIENTATION_SENSOR);

		imageTask = null;

		Bitmap current = BitmapFactory.decodeFile(pdfPath + String.valueOf(0) + "_small.png");
		if (current == null)
			return;
		float floatedWidth = current.getWidth();
		float floatedHeight = current.getHeight();
		float imageRatio = floatedWidth / floatedHeight;

		if (getResources().getConfiguration().orientation == 2) {
			imageRatio = imageRatio * 2;
		}

		issueRatio = imageRatio;
		// Log.e("test-asd", String.valueOf(issueRatio));
		mCurlView = new CurlView(this, issueRatio);
		setContentView(mCurlView);

		// mCurlView.setBackgroundColor(0xFF202830);
		mCurlView.setBackgroundColor(0xFF000000);

		mCurlView.setSizeChangedObserver(new SizeChangedObserver());

		mCurlView.setAllowLastPageCurl(false);

		mCurlView.setPageProvider(new PageProvider());

		// mCurlView.setRatio(issueRatio);

		if (gotopage % 2 != 0) {
			++gotopage;
		}
		if (gotopage == pageCount) {
			--gotopage;
		}

		mCurlView.setCurrentIndex(gotopage);

	}

	// Load Images:
	public void pdfLoadImages() {
		try {
			// run async

			imageTask = new VerifierImages();

			imageTask.execute();
			System.gc();// run GC
		} catch (Exception e) {
			Log.d("error", e.toString());
		}
	}

	@Override
	public void onPause() {
		super.onPause();
		if (mCurlView != null) {
			mCurlView.onPause();
		}

	}

	@Override
	public void onResume() {
		super.onResume();
		if (mCurlView != null) {
			mCurlView.onResume();
		}

	}

	/*
	 * @Override public Object onRetainNonConfigurationInstance() { return
	 * mCurlView.getCurrentIndex(); }
	 */
	/**
	 * Bitmap provider.
	 */
	private class PageProvider implements CurlView.PageProvider {
		@Override
		public int getPageCount() {
			return pageCount;
		}

		@Override
		public void updatePage(CurlPage page, int width, int height, int index) {

			// Log.e("updatePage-index = ", String.valueOf(index)+",
			// "+page.toString());

			Log.e("size : ", "width = " + String.valueOf(width) + ", height = " + String.valueOf(height));

			if (mCurlView.getViewMode() == CurlRenderer.SHOW_TWO_PAGES) {

				Bitmap left = BitmapFactory.decodeFile(pdfPath + String.valueOf(index) + "_small.png");
				Bitmap right = BitmapFactory.decodeFile(pdfPath + String.valueOf(index + 1) + "_small.png");

				if (right != null) {
					right = flipImage(right, 2);
					right = getTexture(right, width, height, true);
					page.setTexture(right, CurlPage.SIDE_BACK);
				}

				if (left != null) {
					left = getTexture(left, width, height, true);
					page.setTexture(left, CurlPage.SIDE_FRONT);
				}

			} else {

				Bitmap current = BitmapFactory.decodeFile(pdfPath + String.valueOf(index) + "_small.png");

				current = getTexture(current, width, height, true);

				page.setTexture(current, CurlPage.SIDE_FRONT);
				page.setTexture(makeTransparent(current, 50), CurlPage.SIDE_BACK);

			}

		}

		@Override
		public Bitmap updatePageFullSize(CurlPage page, int width, int height, int index, boolean fliped) {

			// Log.e("updatePage-index = ", String.valueOf(index)+",
			// "+page.toString());

			Log.e("size : ", "width = " + String.valueOf(width) + ", height = " + String.valueOf(height));

			Bitmap current = BitmapFactory.decodeFile(pdfPath + String.valueOf(index) + ".png");
			if (current == null) {
				return current;
			}
			if (fliped) {
				return getTexture(flipImage(current, 1), width, height, false);
			} else {
				return getTexture(current, width, height, false);
			}

		}

		@Override
		public Bitmap updatePageSmallSize(CurlPage page, int width, int height, int index, boolean fliped,
				boolean transparent) {

			Log.e("size : ", "width = " + String.valueOf(width) + ", height = " + String.valueOf(height));

			Bitmap current = BitmapFactory.decodeFile(pdfPath + String.valueOf(index) + "_small.png");

			if (current == null) {
				return current;
			}

			if (fliped) {
				current = flipImage(current, 1);
			}

			if (transparent) {
				current = makeTransparent(current, 50);
			}

			return getTexture(current, width, height, true);

		}

	}

	public Bitmap getTexture(Bitmap d, int width, int height, Boolean isSmall) {
		Log.v("toto", "toto");
		// width = width - 20;
		// height = height - 20;

		if (isSmall) {
			Log.e("small original : ", "width = " + String.valueOf(width) + ", height = " + String.valueOf(height));
			Log.e("small image : ",
					"width = " + String.valueOf(d.getWidth()) + ", height = " + String.valueOf(d.getHeight()));
			return d;
		}

		float floatedWidth, floatedHeight;

		/*****************************************/
		/** screen ratio calul **/
		/*****************************************/

		DisplayMetrics metrics = this.getResources().getDisplayMetrics();
		int screenWidth = metrics.widthPixels;
		int screenHeight = metrics.heightPixels;

		/*****************************************/
		/** image ratio calcule **/
		/*****************************************/

		floatedWidth = d.getWidth();
		floatedHeight = d.getHeight();
		int imageWidth = 0, imageHeight = 0;
		float imageRatio = 0;

		imageRatio = floatedWidth / floatedHeight;
		// imageRatio = issueRatio;
		imageHeight = openglHeight;
		imageWidth = Math.round(imageHeight * imageRatio);

		if (imageWidth > width) {
			imageRatio = floatedHeight / floatedWidth;
			imageWidth = openglWidth;
			imageHeight = Math.round(imageWidth * imageRatio);
		}

		Log.e("original : ", "width = " + String.valueOf(width) + ", height = " + String.valueOf(height));
		Log.e("screen : ", "width = " + String.valueOf(screenWidth) + ", height = " + String.valueOf(screenHeight));
		Log.e("image : ", "width = " + String.valueOf(imageWidth) + ", height = " + String.valueOf(imageHeight));

		Log.e("trueimage : ",
				"width = " + String.valueOf(d.getWidth()) + ", height = " + String.valueOf(d.getHeight()));

		return d;

	}

	public Bitmap flipImage(Bitmap src, int type) {
		// create new matrix for transformation
		Matrix matrix = new Matrix();
		// if vertical

		// if horizonal

		// x = x * -1
		matrix.preScale(-1.0f, 1.0f);
		// unknown type

		// return transformed image
		return Bitmap.createBitmap(src, 0, 0, src.getWidth(), src.getHeight(), matrix, true);
	}

	public Bitmap makeTransparent(Bitmap src, int value) {
		int width = src.getWidth();
		int height = src.getHeight();
		Bitmap transBitmap = Bitmap.createBitmap(width, height, Config.ARGB_8888);
		Canvas canvas = new Canvas(transBitmap);
		canvas.drawARGB(255, 255, 255, 255);
		// config paint
		final Paint paint = new Paint();
		paint.setAlpha(value);
		canvas.drawBitmap(src, 0, 0, paint);
		return transBitmap;
	}

	/**
	 * CurlView size changed observer.
	 */
	private class SizeChangedObserver implements CurlView.SizeChangedObserver {
		@Override
		public void onSizeChanged(int w, int h) {
			openglHeight = h;
			openglWidth = w;

			if (w > h) {
				mCurlView.setMargins(0.0f, 0.0f, 0.0f, 0.0f);
				mCurlView.setRenderLeftPage(true);
				mCurlView.setViewMode(CurlView.SHOW_TWO_PAGES);
			} else {
				mCurlView.setMargins(0.05f, 0.05f, 0.05f, 0.05f);
				mCurlView.setRenderLeftPage(false);
				mCurlView.setViewMode(CurlView.SHOW_ONE_PAGE);
			}

			/*
			 * if (w > h) { mCurlView.setViewMode(CurlView.SHOW_TWO_PAGES);
			 * mCurlView.setMargins(.1f, .05f, .1f, .05f); } else {
			 * mCurlView.setViewMode(CurlView.SHOW_ONE_PAGE);
			 * mCurlView.setMargins(.1f, .1f, .1f, .1f); }
			 */
		}
	}

	private class VerifierImages extends AsyncTask<Void, Void, Void> {
		// create and show a progress dialog
		public ProgressDialog progressDialog = ProgressDialog.show(CurlActivity.this, "", "Opening...");

		@Override
		protected void onPostExecute(Void result) {
			// after async close progress dialog
			progressDialog.dismiss();

			loadImageInArray();
		}

		@Override
		protected Void doInBackground(Void... params) {
			try {

				PDFDraw draw = new PDFDraw();

				try {

					// Common code for remaining samples.
					PDFDoc tiger_doc = new PDFDoc(pdfPath + "/issue.pdf");
					// Initialize the security handler, in case the PDF is
					// encrypted.
					tiger_doc.initSecurityHandler();

					pageCount = tiger_doc.getPageCount();

					for (int i = 1; i <= pageCount; i++) {

						currentRenderingPage = i;
						runOnUiThread(new Runnable() {
							@Override
							public void run() {
								progressDialog.setMessage("Conversion pour Android\n"
										+ String.valueOf(currentRenderingPage) + " de " + String.valueOf(pageCount));
							}
						});
						if (isCancelled()) {
							progressDialog.cancel();
							return null;
						}

						File file = new File(pdfPath + "/" + String.valueOf(i - 1) + ".png");
						if (!file.exists()) {
							setRequestedOrientation(ActivityInfo.SCREEN_ORIENTATION_PORTRAIT);

							Page page = (Page) (tiger_doc.getPageIterator(i).next());

							int reelwidth = (int) page.getPageWidth();
							int reelheight = (int) page.getPageHeight();
							int width = 0, height = 0;
							// if (width > 1500 || height > 1500) {

							// width = (width * 1500) / height;
							/*
							 * DisplayMetrics metrics =
							 * getResources().getDisplayMetrics(); int
							 * screenWidth = metrics.widthPixels; int
							 * screenHeight = metrics.heightPixels;
							 */

							Display display = getWindowManager().getDefaultDisplay();
							Point size = new Point();
							display.getSize(size);
							int screenWidth = size.x;
							int screenHeight = size.y;

							if (screenWidth < reelwidth || screenHeight < reelheight) {
								draw.setDPI(300); // Set the output resolution
													// is to 100 DPI.

								int imageHeight, imageWidth;
								float floatedHeight = reelheight, floatedWidth = reelwidth;

								float imageRatio = floatedHeight / floatedWidth;
								imageHeight = screenHeight;
								imageWidth = Math.round(imageHeight * imageRatio);

								if (imageWidth > screenWidth) {
									imageRatio = floatedHeight / floatedWidth;
									imageWidth = screenWidth;
									imageHeight = Math.round(imageWidth * imageRatio);
								}
								height = imageHeight * 2;
								width = imageWidth * 2;

							} else {
								draw.setDPI(300); // Set the output resolution
													// is to 100 DPI.
								height = (reelheight * 1500) / reelwidth;
								width = 1500;
							}
							// }

							// --------------------------------------------------------------------------------
							// Example 3) Convert the first page to raw bitmap.
							// Also, rotate the
							// page 90 degrees and save the result as RAW.

							// draw.setRotate(Page.e_90); // Rotate all pages 90
							// degrees clockwise.
							draw.setImageSize((int) width, (int) height);
							draw.setAntiAliasing(true);
							draw.setCaching(false);

							Bitmap image = Bitmap.createBitmap(draw.getBitmap(page));

							FileOutputStream out = null;
							try {
								out = new FileOutputStream(pdfPath + "/" + String.valueOf(i - 1) + ".png");
								image.compress(Bitmap.CompressFormat.PNG, 100, out);
							} catch (Exception e) {
								e.printStackTrace();
							} finally {
								try {
									out.close();
								} catch (Throwable ignore) {
								}
							}

							image.recycle();

							draw.setDPI(30); // Set the output resolution is to
												// 100 DPI.
							draw.setImageSize((int) width / 6, (int) height / 6);
							Bitmap imagesmall = Bitmap.createBitmap(draw.getBitmap(page));

							FileOutputStream outsmall = null;
							try {
								outsmall = new FileOutputStream(pdfPath + "/" + String.valueOf(i - 1) + "_small.png");
								imagesmall.compress(Bitmap.CompressFormat.PNG, 100, outsmall);
							} catch (Exception e) {
								e.printStackTrace();
							} finally {
								try {
									outsmall.close();
								} catch (Throwable ignore) {
								}
							}

							imagesmall.recycle();

						}

						// bitmaps[i-1] =
						// Bitmap.createBitmap(draw.getBitmap(page));

					}

					tiger_doc.close();
					tiger_doc = null;
				} catch (Exception e) {
					e.printStackTrace();
				}

			} catch (Exception e) {
				Log.d("error", e.toString());
			}
			return null;
		}
	}

}