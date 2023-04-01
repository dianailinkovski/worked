package com.ngser.ekiosk.kiosque;

import org.json.JSONException;
import org.json.JSONObject;

import android.app.Activity;
import android.app.AlertDialog;
import android.content.DialogInterface;
import android.content.SharedPreferences;
import android.os.AsyncTask;
import android.os.Bundle;
import android.view.MenuItem;
import android.view.View;
import android.view.View.OnClickListener;
import android.widget.Button;
import android.widget.ProgressBar;

import com.ngser.ekiosk.R;
import com.ngser.ekiosk.AsyncTask.JSONParser;

public class ActivationActivity extends Activity {
	
	final String PREFS_NAME = "eKioskPrefSetting";
	
	GetVerifValidateAccount getVerifValidateAccount = null;
	GetResendActivationMail getResendActivationMail = null;
	ProgressBar progressBar;
	Button verifButton;
	Button sendButton;
	
	AlertDialog alertDialog = null;
	
	@Override
	protected void onDestroy() {
		// TODO Auto-generated method stub
		super.onDestroy();
		if (alertDialog != null) {
			if (alertDialog.isShowing()) {
				alertDialog.dismiss();
			}
		}
		
		
	}
	
	@Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activation_activity);
        
        getActionBar().setDisplayHomeAsUpEnabled(true);
        getActionBar().setHomeButtonEnabled(true);
        
        verifButton = (Button) findViewById(R.id.verifButton);
        sendButton = (Button) findViewById(R.id.sendButton);
        progressBar = (ProgressBar) findViewById(R.id.progressBar1);
        
        verifButton.setOnClickListener(new OnClickListener() {
			@Override
			public void onClick(View v) {
				if (getVerifValidateAccount == null) {
					progressBar.setVisibility(View.VISIBLE);
					verifButton.setVisibility(View.GONE);
					sendButton.setVisibility(View.GONE);
					verifValidateAccount();					
				}
				
			}
		});
		
        sendButton.setOnClickListener(new OnClickListener() {
			@Override
			public void onClick(View v) {
				if (getResendActivationMail == null) {
					progressBar.setVisibility(View.VISIBLE);
					verifButton.setVisibility(View.GONE);
					sendButton.setVisibility(View.GONE);
					sendMail();					
				}
			}
		});
        
	}
	
	@Override
    public boolean onOptionsItemSelected(MenuItem item) {
        
    	int id = item.getItemId();
        if (id == android.R.id.home) {
        	
        	this.finish();
        	
            return true;
        }
        
        return super.onOptionsItemSelected(item);
    }
	
	public void verifValidateAccount() {
		getVerifValidateAccount = new GetVerifValidateAccount();
		getVerifValidateAccount.execute();
	}
	
	public void sendMail() {
		getResendActivationMail = new GetResendActivationMail();
		getResendActivationMail.execute();
	}
	
	
	private class GetVerifValidateAccount extends AsyncTask<String, String, JSONObject> {
		
		@Override
	    protected JSONObject doInBackground(String... args) {
	        JSONParser jParser = new JSONParser();
	        // Getting JSON from URL
	        
	        SharedPreferences settings = getSharedPreferences(PREFS_NAME, 0);
            String username = settings.getString("username", "");
            String password = settings.getString("password", "");
	        
	        StringBuilder strBuilder = new StringBuilder("http://api.ngser.gnetix.com/v1.1/validateMemberActivation.php?");
	        
	        strBuilder.append("username=");
	        strBuilder.append(username);
	        strBuilder.append("&password=");
	        strBuilder.append(password);
	        String url = strBuilder.toString();
	        
	        
	        JSONObject json = jParser.getJSONFromUrl(url);
	        if (isCancelled()) {
				return null;
			}
	        return json;
	    }
	    
	    @Override
	    protected void onPostExecute(JSONObject json) {
	        
	        try {
	        	
	        	if (json == null) {
					return;
				}
	        	
	        	String resultat = json.getString("resultat");
	        	JSONObject data = json.getJSONObject("data");
	        	
	        	
	        	if (resultat.equals("false")) {
	        		showErreur(json.getString("data"));
				}
	        	else {
		        	if (data.getString("activated").equals("1")) {
						finish();
					}
		        	else {
		        		showMessage("Votre compte n'est toujours pas activé.");
		        	}
	        	}
	        	
	        } catch (JSONException e) {
	          e.printStackTrace();
	        }
	        
	        getVerifValidateAccount = null;
	        progressBar.setVisibility(View.GONE);
	        verifButton.setVisibility(View.VISIBLE);
			sendButton.setVisibility(View.VISIBLE);
	    }
	}
	
	private class GetResendActivationMail extends AsyncTask<String, String, JSONObject> {
		
		@Override
	    protected JSONObject doInBackground(String... args) {
	        JSONParser jParser = new JSONParser();
	        // Getting JSON from URL
	        
	        SharedPreferences settings = getSharedPreferences(PREFS_NAME, 0);
            String username = settings.getString("username", "");
            String password = settings.getString("password", "");
	        
	        StringBuilder strBuilder = new StringBuilder("http://api.ngser.gnetix.com/v1.1/resendActivationMail.php?");
	        
	        strBuilder.append("username=");
	        strBuilder.append(username);
	        strBuilder.append("&password=");
	        strBuilder.append(password);
	        String url = strBuilder.toString();
	        
	        
	        JSONObject json = jParser.getJSONFromUrl(url);
	        if (isCancelled()) {
				return null;
			}
	        return json;
	    }
	    
	    @Override
	    protected void onPostExecute(JSONObject json) {
	        
	        try {
	        	
	        	if (json == null) {
					return;
				}
	        	
	        	String resultat = json.getString("resultat");
	        	//JSONObject data = json.getJSONObject("data");
	        	
	        	
	        	if (resultat.equals("false")) {
	        		showErreur(json.getString("data"));
				}
	        	else {
	        		showMessage(json.getString("data"));
	        	}
	        	
	        } catch (JSONException e) {
	          e.printStackTrace();
	        }
	        
	        getResendActivationMail = null;
	        progressBar.setVisibility(View.GONE);
	        verifButton.setVisibility(View.VISIBLE);
			sendButton.setVisibility(View.VISIBLE);
	    }
	}
	
	public void showErreur(String error) {
		AlertDialog.Builder builder = new AlertDialog.Builder(ActivationActivity.this);
    	builder.setTitle("Erreur");
    	builder.setMessage(error);
    	builder.setPositiveButton("Retour", new DialogInterface.OnClickListener() {
            public void onClick(DialogInterface dialog, int which) { 
            	dialog.dismiss();
            }
         });
    	
    	alertDialog = builder.create();
   	 	alertDialog.show();
   	 	
	}
	
	public void showMessage(String message) {
		AlertDialog.Builder builder = new AlertDialog.Builder(ActivationActivity.this);
    	builder.setTitle("Information");
    	builder.setMessage(message);
    	builder.setPositiveButton("Retour", new DialogInterface.OnClickListener() {
            public void onClick(DialogInterface dialog, int which) { 
            	dialog.dismiss();
            }
         });
    	
    	alertDialog = builder.create();
    	alertDialog.show();
		
	}
	
}
