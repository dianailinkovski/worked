package com.ngser.ekiosk.kiosque;

import java.io.InputStream;

import org.apache.http.HttpEntity;
import org.apache.http.HttpResponse;
import org.apache.http.HttpStatus;
import org.apache.http.client.methods.HttpGet;
import org.apache.http.impl.client.DefaultHttpClient;

import android.content.Context;
import android.content.Intent;
import android.graphics.Bitmap;
import android.graphics.BitmapFactory;
import android.net.Uri;
import android.os.AsyncTask;
import android.util.Log;
import android.view.View;
import android.widget.ImageView;
import android.widget.ProgressBar;
import android.widget.RelativeLayout;

public class AdsImageView extends RelativeLayout {
	
	public ImageView adsImage;
	public ProgressBar adsProgressBar;
	public Bitmap adsBitmap;
	public String urlImage;
	public String urlOnClick;
	public ImageDownloader imageDownloader;
	
	public AdsImageView(Context context) {
		super(context);
		adsBitmap = null;
		//float width = this.getWidth();
		//float height = width * 0.1388f;
		
		
		RelativeLayout.LayoutParams adsImageLP = new RelativeLayout.LayoutParams(RelativeLayout.LayoutParams.MATCH_PARENT, RelativeLayout.LayoutParams.MATCH_PARENT);
		RelativeLayout.LayoutParams adsProgressBarLP = new RelativeLayout.LayoutParams(50, 50);
		adsProgressBarLP.addRule(RelativeLayout.CENTER_HORIZONTAL, RelativeLayout.TRUE);
		adsProgressBarLP.addRule(RelativeLayout.CENTER_VERTICAL, RelativeLayout.TRUE);
		
		adsImage = new ImageView(context);
		//adsImage.setBackgroundColor(Color.RED);
		adsImage.setOnClickListener(new OnClickListener() {
		    public void onClick(View v) {
		        if (urlOnClick == null) {
					return;
				}
		        if (urlOnClick.equals("")) {
					return;
				}
		        
		        Intent i = new Intent(Intent.ACTION_VIEW).setFlags(Intent.FLAG_ACTIVITY_NEW_TASK);
		        i.setData(Uri.parse(urlOnClick));
		        getContext().getApplicationContext().startActivity(i);
		    }
		});
		
		adsProgressBar = new ProgressBar(context);
		adsProgressBar.setIndeterminate(true);
		
		addView(adsImage, adsImageLP);
		addView(adsProgressBar, adsProgressBarLP);
		
	}
	
	public void setUrlString(String tempImageUrl, String tempOnClickUrl) {
		urlImage = tempImageUrl;
		urlOnClick = tempOnClickUrl;
		
		if (adsBitmap == null) {
			imageDownloader = new ImageDownloader();
			imageDownloader.urlToLoad = urlImage;
			imageDownloader.execute();
		}
		else {
			imageDownloadCompleted(adsBitmap);
		}
		
		
	}
	
	public void imageDownloadCompleted(Bitmap result) {
		adsBitmap = result;
		adsImage.setImageBitmap(adsBitmap);
		adsProgressBar.setVisibility(View.GONE);
	}
	
	
	private class ImageDownloader extends AsyncTask<Void, Void, Bitmap> {
		public String urlToLoad;
		
		@Override
		protected Bitmap doInBackground(Void... params) {
			return downloadBitmap(urlToLoad);
		}
		
		@Override
		protected void onPostExecute(Bitmap result) {
			Log.i("Async-Example", "onPostExecute Called");
			//downloadedImg.setImageBitmap(result);
			imageDownloadCompleted(result);
			//simpleWaitDialog.dismiss();

		}

		private Bitmap downloadBitmap(String url) {
			// initilize the default HTTP client object
			final DefaultHttpClient client = new DefaultHttpClient();

			//forming a HttoGet request 
			final HttpGet getRequest = new HttpGet(url);
			try {

				HttpResponse response = client.execute(getRequest);

				//check 200 OK for success
				final int statusCode = response.getStatusLine().getStatusCode();

				if (statusCode != HttpStatus.SC_OK) {
					Log.w("ImageDownloader", "Error " + statusCode + 
							" while retrieving bitmap from " + url);
					return null;

				}

				final HttpEntity entity = response.getEntity();
				if (entity != null) {
					InputStream inputStream = null;
					try {
						// getting contents from the stream 
						inputStream = entity.getContent();

						// decoding stream data back into image Bitmap that android understands
						final Bitmap bitmap = BitmapFactory.decodeStream(inputStream);

						return bitmap;
					} finally {
						if (inputStream != null) {
							inputStream.close();
						}
						entity.consumeContent();
					}
				}
			} catch (Exception e) {
				// You Could provide a more explicit error message for IOException
				getRequest.abort();
				Log.e("ImageDownloader", "Something went wrong while" + " retrieving bitmap from " + url + e.toString());
			} 

			return null;
		}
	}
	
}
