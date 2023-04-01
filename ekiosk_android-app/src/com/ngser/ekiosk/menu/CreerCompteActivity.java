package com.ngser.ekiosk.menu;

import java.lang.ref.WeakReference;

import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

import android.app.Activity;
import android.app.AlertDialog;
import android.app.ProgressDialog;
import android.content.Context;
import android.content.DialogInterface;
import android.content.Intent;
import android.content.SharedPreferences;
import android.os.Bundle;
import android.support.v4.content.LocalBroadcastManager;
import android.util.Log;
import android.view.MenuItem;
import android.webkit.JsResult;
import android.webkit.WebChromeClient;
import android.webkit.WebView;
import android.webkit.WebViewClient;

import com.ngser.ekiosk.R;
import com.ngser.ekiosk.kiosque.ActivationActivity;

public class CreerCompteActivity extends Activity {
	
	WebView webView;
	ProgressDialog progress;
	
	final String PREFS_NAME = "eKioskPrefSetting";
	
	class MyJavaScriptInterface {

        private Context ctx;

        MyJavaScriptInterface(Context ctx) {
            this.ctx = ctx;
        }

        public void showHTML(String html) {
            new AlertDialog.Builder(ctx).setTitle("HTML").setMessage(html)
                    .setPositiveButton(android.R.string.ok, null).setCancelable(false).create().show();
        }

    }
	
	@Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.creer_compte_activity);
        
        getActionBar().setDisplayHomeAsUpEnabled(true);
        getActionBar().setHomeButtonEnabled(true);
        
        /*
        final ProgressDialog pd = ProgressDialog.show(this, "", "Loading...",true);

        mWebView = (WebView) findViewById(R.id.webkitWebView1);
        mWebView.getSettings().setJavaScriptEnabled(true);
        mWebView.getSettings().setSupportZoom(true);  
        mWebView.getSettings().setBuiltInZoomControls(true);
        mWebView.setWebViewClient(new WebViewClient() {
            @Override
            public void onPageFinished(WebView view, String url) {
                if(pd.isShowing()&&pd!=null)
                {
                    pd.dismiss();
                }
            }
        });
        mWebView.loadUrl("http://www.yahoo.co.in");
        setTitle("Yahoo!");
        */
        
        webView = (WebView) findViewById(R.id.webView1);
        webView.getSettings().setJavaScriptEnabled(true);
        //webView.addJavascriptInterface(new MyJavaScriptInterface(this), "Android");
        /*webView.setWebViewClient(new WebViewClient() {
            @Override
            public void onPageFinished(WebView view, String url) {
            	webView.loadUrl("javascript:window.HtmlViewer.showHTML" +
                        "('<html>'+document.getElementById('info').value+'</html>');");
            }
        });*/
        
        webView.setWebChromeClient(new ChromeClient(this));
        webView.setWebViewClient(new webViewClient());
        progress = ProgressDialog.show(this, "", "Chargement ...");
        
        
        SharedPreferences settings = getSharedPreferences(PREFS_NAME, 0);
        int ekiosk = settings.getInt("ekcredit", 0);
        StringBuilder strBuilder = new StringBuilder("http://ngser.gnetix.com/site/memberform?ekcredit=");
        strBuilder.append(String.valueOf(ekiosk));
        webView.loadUrl(strBuilder.toString());
        
        //webView.loadUrl("http://gnetix.com/iphone/android/demo.html");
	}
	
	public boolean onOptionsItemSelected(MenuItem item) {
        
    	int id = item.getItemId();
        if (id == android.R.id.home) {
        	
        	this.finish();
        	
            return true;
        }
        
        return super.onOptionsItemSelected(item);
    }
	/*
	private void parsingError() {
		AlertDialog.Builder bld = new AlertDialog.Builder(getApplicationContext());
        bld.setMessage("Erreur. Votre compte a été cré mais il y a eu une erreur de détection automatique. Essayez de connecter votre compte dans le menu.");
        bld.setNeutralButton("Retour", null);
        bld.create().show();
	}
	*/
	
	private class ChromeClient extends WebChromeClient {
		
		private WeakReference<CreerCompteActivity> creerCompteActivityWeakRef;
		
		public ChromeClient(CreerCompteActivity weakActivity) {
			// TODO Auto-generated constructor stub
			this.creerCompteActivityWeakRef = new WeakReference<CreerCompteActivity>(weakActivity);
		}
		
	    @Override
	    public void onProgressChanged(WebView view, int newProgress) {
	        if(newProgress >= 85) {
	            progress.dismiss();
	        }
	        super.onProgressChanged(view, newProgress);
	    }
	    @Override
        public boolean onJsAlert(WebView view, String url, String message, JsResult result) {
            Log.d("LogTag", message);
            
            try {
				JSONObject data = new JSONObject(message);
				//JSONArray data = new JSONArray(message);
				Log.d("data", data.toString());
				
				String usernameString = data.getString("email");
	            String passwordString = data.getString("password");
	            //String prenomString = data.getString("first_name");
	            //String nomString = data.getString("last_name");
	            //String mobileString = data.getString("mobile");
	            String ekcreditString = data.getString("ek_credit");
	        	
	        	
	        	SharedPreferences settings = getSharedPreferences(PREFS_NAME, 0);
	            SharedPreferences.Editor editor = settings.edit();
            	
            	editor.putInt("ekcredit", Integer.valueOf(ekcreditString));
                editor.putString("username", usernameString);
                editor.putString("password", passwordString);
            	
                editor.commit();
                
                Intent intent = new Intent("SharedPreferencesReceiver");
                LocalBroadcastManager.getInstance(getApplicationContext()).sendBroadcast(intent);
                
                finish();
				
                Intent intent2 = new Intent(getApplicationContext(), ActivationActivity.class);
        		intent2.addFlags(Intent.FLAG_ACTIVITY_NEW_TASK);
        		getApplicationContext().startActivity(intent2);
				
			} catch (JSONException e) {
				e.printStackTrace();
				
				if (creerCompteActivityWeakRef.get() != null && !creerCompteActivityWeakRef.get().isFinishing()) {
			        AlertDialog.Builder builder = new AlertDialog.Builder(CreerCompteActivity.this);
			        builder.setCancelable(true);
			        builder.setTitle("Erreur");
			        builder.setMessage("Votre compte a été créé mais il y a eu une erreur de détection automatique.\n\nEssayez de connecter votre compte manuellement dans le menu.");
			        builder.setInverseBackgroundForced(true);

			        builder.setNeutralButton("Ok",new DialogInterface.OnClickListener() {
			          public void onClick(DialogInterface dialog, int whichButton){
			            dialog.dismiss();
			          }
			        });

			        builder.show();
			      }
		        
			}
            
            return true;
        }
	}
	
	private class webViewClient extends WebViewClient {
		@Override
	    public void onPageFinished (WebView webView, String url) {
			Log.e("testestestestest", "testsetsetseteset");
			//webView.loadUrl("javascript:document.getElementById('info').value");
			
			//webView.loadUrl("javascript:window.HTMLOUT.showHTML('document.getElementById('info').value')");
			
			webView.loadUrl("javascript:alert(getInfo())");
			
			//String varSendText = null; 
			//webView.loadUrl("javascript:(function() { " + " var varSendText = document.getElementById('info').value; " + "})()" + "return" + "varSendText"); 
			//Toast.makeText(getApplicationContext(), varSendText, Toast.LENGTH_LONG).show();
			
	    }
	}
	
}
