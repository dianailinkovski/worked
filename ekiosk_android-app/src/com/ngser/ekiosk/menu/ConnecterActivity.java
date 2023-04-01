package com.ngser.ekiosk.menu;

import org.json.JSONException;
import org.json.JSONObject;

import android.app.Activity;
import android.app.AlertDialog;
import android.content.Intent;
import android.content.SharedPreferences;
import android.os.AsyncTask;
import android.os.Bundle;
import android.support.v4.content.LocalBroadcastManager;
import android.util.Log;
import android.view.MenuItem;
import android.view.View;
import android.widget.Button;
import android.widget.EditText;
import android.widget.ProgressBar;

import com.ngser.ekiosk.R;
import com.ngser.ekiosk.AsyncTask.JSONParser;

public class ConnecterActivity extends Activity {
	
	private EditText usernameEditText, passwordEditText;
	private ProgressBar confirmProgressBar;
	private Button confirmButton, cancelButton;
	private StartLoginTask startLoginTask;
	final String PREFS_NAME = "eKioskPrefSetting";
	
	@Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.login_activity);
        
        getActionBar().setDisplayHomeAsUpEnabled(true);
        getActionBar().setHomeButtonEnabled(true);
        
        usernameEditText = (EditText) findViewById(R.id.usernameEditText);
        passwordEditText = (EditText) findViewById(R.id.passwordEditText);
        
        confirmProgressBar = (ProgressBar) findViewById(R.id.confirmProgressBar);
        
        confirmButton = (Button) findViewById(R.id.confirmButton);
        cancelButton = (Button) findViewById(R.id.cancelButton);
        
        cancelButton.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
            	finish();
            }
        });
        
        confirmButton.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
            	connectFunction();
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
	
	public void connectFunction() {
		usernameEditText.setBackgroundResource(R.drawable.edittext_border_no_error);
		passwordEditText.setBackgroundResource(R.drawable.edittext_border_no_error);
		
		if (usernameEditText.getText().toString().equals("")) {
			usernameEditText.setBackgroundResource(R.drawable.edittext_border_error);
		}
		else if (passwordEditText.getText().toString().equals("")) {
			passwordEditText.setBackgroundResource(R.drawable.edittext_border_error);
		}
		else {
			confirmProgressBar.setVisibility(View.VISIBLE);
			confirmButton.setEnabled(false);
			cancelButton.setEnabled(false);
			confirmButton.setAlpha(0.5f);
			cancelButton.setAlpha(0.5f);
			
			startLoginTask = new StartLoginTask();
			startLoginTask.execute();			
		}
		
	}
	
	public void connectionFailedFunction(String failedError) {
		
		confirmProgressBar.setVisibility(View.GONE);
		confirmButton.setEnabled(true);
		cancelButton.setEnabled(true);
		confirmButton.setAlpha(1);
		cancelButton.setAlpha(1);
		
		AlertDialog.Builder bld = new AlertDialog.Builder(this);
        bld.setMessage(failedError);
        bld.setNeutralButton("Retour", null);
        bld.create().show();
	}
	
	private class StartLoginTask extends AsyncTask<String, String, JSONObject> {
		
	    @Override
	    protected void onPreExecute() {
	        super.onPreExecute();
	        
	    }
	    
	    @Override
	    protected JSONObject doInBackground(String... args) {
	        JSONParser jParser = new JSONParser();
	        // Getting JSON from URL
	        if (isCancelled()) {
				return null;
			}
	        
	        SharedPreferences settings = getSharedPreferences(PREFS_NAME, 0);
            int ekiosk = settings.getInt("ekcredit", 0);
	        
            String username = usernameEditText.getText().toString();
            String password = passwordEditText.getText().toString();
                    
            //username = "lacina.kone@ekioskmobile.net";
            //password = "ivoirekiosk99";
            
	        StringBuilder strBuilder = new StringBuilder("http://api.ngser.gnetix.com/v1.1/Login.php?");
	        
	        strBuilder.append("username=");
	        strBuilder.append(username.trim());
	        strBuilder.append("&password=");
	        strBuilder.append(password.trim());
	        strBuilder.append("&ekcredit=");
	        strBuilder.append(String.valueOf(ekiosk));
	        
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
	        	Log.d("resultat", resultat);
	        	
	        	
	        	
	        	if (resultat.equals("true")) {
	        		
	        		JSONObject data = json.getJSONObject("data");
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
	        	}
	        	else {
	        		String data = json.getString("data");
		        	Log.d("data", data);
	        		connectionFailedFunction(data);
	        	}
            	
	        	
	        } catch (JSONException e) {
	          e.printStackTrace();
	        }
	        
	        if (isCancelled()) {
				return;
			}
	        
            
	    }
	}
	
}
