package com.ngser.ekiosk.menu;

import java.util.ArrayList;
import java.util.List;

import org.apache.http.message.BasicNameValuePair;
import org.json.JSONException;
import org.json.JSONObject;

import android.app.Activity;
import android.app.AlertDialog;
import android.content.Context;
import android.content.DialogInterface;
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

public class CodeActivationActivity extends Activity {
	
	private EditText codeEditText;
	private ProgressBar confirmProgressBar;
	private Button confirmButton;
	private SendCodeTask sendCodeTask;
	final String PREFS_NAME = "eKioskPrefSetting";
	
	private Context mContext;
	
	@Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        mContext = this;
        setContentView(R.layout.code_activation_activity);
        
        getActionBar().setDisplayHomeAsUpEnabled(true);
        getActionBar().setHomeButtonEnabled(true);
        
        codeEditText = (EditText) findViewById(R.id.codeEditText);
        
        confirmProgressBar = (ProgressBar) findViewById(R.id.confirmProgressBar);
        
        confirmButton = (Button) findViewById(R.id.confirmButton);
        
        confirmButton.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
            	sendFunction();
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
	
	public void sendFunction() {
		codeEditText.setBackgroundResource(R.drawable.edittext_border_no_error);
		
		if (codeEditText.getText().toString().equals("")) {
			codeEditText.setBackgroundResource(R.drawable.edittext_border_error);
		}
		else {
			confirmProgressBar.setVisibility(View.VISIBLE);
			confirmButton.setEnabled(false);
			confirmButton.setAlpha(0.5f);
			
			sendCodeTask = new SendCodeTask();
			sendCodeTask.execute();			
		}
		
	}
	
	public void connectionFailedFunction(String failedError) {
		
		confirmProgressBar.setVisibility(View.GONE);
		confirmButton.setEnabled(true);
		confirmButton.setAlpha(1);
		
		AlertDialog.Builder bld = new AlertDialog.Builder(this);
        bld.setMessage(failedError);
        bld.setNeutralButton("Retour", null);
        bld.create().show();
	}
	
	private class SendCodeTask extends AsyncTask<String, String, JSONObject> {
		
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
	        
            
	        String codeString = codeEditText.getText().toString();
            
            JSONObject jsonObj = new JSONObject();
			
			try {				
				SharedPreferences settings = getSharedPreferences(PREFS_NAME, 0);
	            String username = settings.getString("username", "");
	            String password = settings.getString("password", "");
				
				jsonObj.put("username", username);
				jsonObj.put("password", password);
				jsonObj.put("code", codeString);
				
			}
			catch (JSONException e) {
				e.printStackTrace();
			}
	     	
	        
	        List <BasicNameValuePair> nameValuePairs = new ArrayList<BasicNameValuePair>();
			// add an HTTP variable and value pair
	        nameValuePairs.add(new BasicNameValuePair("data", jsonObj.toString()));            
            
            StringBuilder strBuilder = new StringBuilder("http://api.ngser.gnetix.com/v1.1/AddCreditWithCode.php");
	        String url = strBuilder.toString();            
            
	        JSONObject json = jParser.getJSONFromUrlWithPostArray(url, nameValuePairs);	        
	        
	        if (isCancelled()) {
				return null;
			}
	        
	        return json;
	    }
	    
	    @Override
	    protected void onPostExecute(JSONObject json) {
	        
	    	if (json == null)
	    		return;
	    	
	    	try {
	    		String resultat = json.getString("resultat");
	        	Log.d("resultat", resultat);
	        	
	        	if (!resultat.equals("true")) {
	        		String data = json.getString("data");
		        	Log.d("data", data);
	        		connectionFailedFunction(data);
	        		
	        		return;	        		
	        	}   
	    	}catch (Exception e) {
	    		e.printStackTrace();
	    	}
	    	
	    	try {
	    		JSONObject jsonobj = json.getJSONObject("data");
	    		
	    		String data = "0";
	    		data = jsonobj.getString("total");
    			
    			SharedPreferences settings = getSharedPreferences(PREFS_NAME, 0);
                SharedPreferences.Editor editor = settings.edit();
                
                
                String username = settings.getString("username", "");
                String password = settings.getString("password", "");
        		
        		int total = 0;
        		if (username.equals("") || password.equals("")) {
                    int current = settings.getInt("ekcredit", 0);
                    int added = Integer.valueOf(data);
                    total = current + added;
                }
                else {
                    total = Integer.valueOf(data);
                }
                
        		editor.putInt("ekcredit", total);	        		
                editor.commit();
                
	    	}catch (Exception e) {
	    		e.printStackTrace();
	    	}
	    	
	    	AlertDialog.Builder bld = new AlertDialog.Builder(mContext);
            bld.setTitle("Informations");
            bld.setMessage("Votre abonnement EKIOSK a été pris en compte");
            bld.setNeutralButton("OK", new DialogInterface.OnClickListener() {
                public void onClick(DialogInterface dialog, int which) {
                	finish();
                	overridePendingTransition(0, 0);
                }
            });	                
            bld.create().show();
            
            Intent intent = new Intent("SharedPreferencesReceiver");
            LocalBroadcastManager.getInstance(getApplicationContext()).sendBroadcast(intent);
            	        
	        if (isCancelled()) {
				return;
			}	                   
	    }
	}
	
}
