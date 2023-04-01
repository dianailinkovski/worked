package com.ngser.ekiosk.kiosque;

import java.util.ArrayList;
import java.util.List;

import org.apache.http.message.BasicNameValuePair;
import org.json.JSONException;
import org.json.JSONObject;

import android.app.Activity;
import android.content.Intent;
import android.content.SharedPreferences;
import android.os.AsyncTask;
import android.os.Bundle;
import android.support.v4.content.LocalBroadcastManager;
import android.util.Log;
import android.view.MotionEvent;
import android.view.View;
import android.view.View.OnClickListener;
import android.view.View.OnTouchListener;
import android.widget.Button;
import android.widget.LinearLayout;
import android.widget.ProgressBar;
import android.widget.TextView;
import android.widget.Toast;

import com.ngser.ekiosk.MainActivity;
import com.ngser.ekiosk.R;
import com.ngser.ekiosk.AsyncTask.JSONParser;
import com.ngser.ekiosk.Model.DatabaseHandler;
import com.ngser.ekiosk.Model.EditionModelClass;

public class BuyWithCreditActivity extends Activity {
	private TextView currentTV;
	private TextView costTV;
	private TextView newSoldeTV;
	
	private ProgressBar currentPB;
	private ProgressBar confirmPB;
	
	private Button cancelButton;
	private Button confirmButton;
	private Button openBundleButton;
	
	LinearLayout linearlayoutButton, bundleLinearLayout;
	
	//private String id_publication;
	//private String prix_publication;
	
	private EditionModelClass edition;
	
	DatabaseHandler dbHandler = new DatabaseHandler(this);
	
	final String PREFS_NAME = "eKioskPrefSetting";
	
	public BuyWithCreditActivity() {
		
	}
	
	@Override
	protected void onCreate(Bundle savedInstanceState) {
		// TODO Auto-generated method stub
		super.onCreate(savedInstanceState);
		
		
		
		Bundle b = this.getIntent().getExtras();
		String id_prix_publication = b.getString("id_prix_publication");
		
		edition = new EditionModelClass();
		
		try {
			JSONObject json = new JSONObject(id_prix_publication);
			//id_publication = json.getString("id");
			//prix_publication = json.getString("prix");
			
			edition.nom = json.getString("nom");
			edition.type = json.getString("type");
			edition.categorie = json.getString("categorie");
	        
			edition.id = json.getInt("id");
			edition.id_journal = json.getString("id_journal");
			edition.datePublication = json.getLong("datePublication");
			edition.downloadPath = json.getString("datePublication");
			edition.coverPath = json.getString("coverPath");
			edition.prix = json.getString("prix");
			//edition.bought = json.getString("bought");
			edition.telechargementRestant = json.getString("telechargementRestant");
			
		} catch (JSONException e) {
			e.printStackTrace();
		}
		
        
        
        
		
		
		setContentView(R.layout.buy_with_credit_main);
		
		this.linearlayoutButton = (LinearLayout) findViewById(R.id.linearlayoutButton);
		this.bundleLinearLayout = (LinearLayout) findViewById(R.id.bundleLinearLayout);
		
		this.currentTV = (TextView) findViewById(R.id.currentTV);
		this.costTV = (TextView) findViewById(R.id.costTV);
		this.newSoldeTV = (TextView) findViewById(R.id.newSoldeTV);
		
		this.currentPB = (ProgressBar) findViewById(R.id.currentPB);
		this.confirmPB = (ProgressBar) findViewById(R.id.confirmPB);
		
		this.cancelButton = (Button) findViewById(R.id.cancelButton);
		this.confirmButton = (Button) findViewById(R.id.confirmButton);
		this.openBundleButton = (Button) findViewById(R.id.openBundleButton);
		
		this.cancelButton.setOnClickListener(new OnClickListener() {
			
			@Override
			public void onClick(View v) {
		     finish();
		    }
		   });
		this.confirmButton.setOnClickListener(new OnClickListener() {
			
			@Override
			public void onClick(View v) {
			buyingPublication();
		    }
		   });
		
		this.openBundleButton.setOnClickListener(new OnClickListener() {
			
			@Override
			public void onClick(View v) {
				// TODO Auto-generated method stub
				Intent intent = new Intent(getApplicationContext(), KiosqueActivity.class)
		    	.addFlags(Intent.FLAG_ACTIVITY_CLEAR_TOP | Intent.FLAG_ACTIVITY_NEW_TASK);
		    	intent.putExtra("showBundle", true);
				startActivity(intent);
				
			}
		});
		
		this.currentTV.setText("");
		this.newSoldeTV.setText("");
		this.confirmButton.setEnabled(false);
		this.confirmButton.setAlpha(0.5f);
		
		this.confirmPB.setVisibility(View.GONE);
		
		this.costTV.setText("-"+edition.prix);
		
		
		
		
		
		validateCredit();
		
	}
	
	
	private void validateCredit() {
		
        SharedPreferences settings = getSharedPreferences(PREFS_NAME, 0);
        if (!settings.getString("username", "").equals("")) {
        	
        	new creditValidatorTask().execute();
        	
        }
        else {
        	
        	calculeCredit();
        	
        }
        
	}
	
	private void calculeCredit() {
		
		SharedPreferences settings = getSharedPreferences(PREFS_NAME, 0);
		
		int ekcredit = settings.getInt("ekcredit", 0);
    	int cost = Integer.valueOf(this.costTV.getText().toString());
    	int newSolde = ekcredit + cost;
    	
    	this.newSoldeTV.setText(String.valueOf(newSolde));
    	this.currentTV.setText(String.valueOf(ekcredit));
    	this.currentPB.setVisibility(View.GONE);
		
		if (newSolde < 0) {
			this.newSoldeTV.setTextColor(getResources().getColor(R.color.redButton));
			this.confirmButton.setEnabled(false);
			this.confirmButton.setAlpha(0.5f);
			
			this.linearlayoutButton.setVisibility(View.GONE);
			this.bundleLinearLayout.setVisibility(View.VISIBLE);
			
		}
    	else {
    		this.confirmButton.setEnabled(true);
    		this.confirmButton.setAlpha(1.0f);
    	}
		
	}
	
	private void buyingPublication() {
		this.cancelButton.setEnabled(false);
		this.cancelButton.setAlpha(0.5f);
		
		this.confirmButton.setEnabled(false);
		this.confirmButton.setAlpha(0.5f);
		
		this.confirmPB.setVisibility(View.VISIBLE);
		
		new buyingTask().execute();
		
	}
	
	private void buyingCompleted() {
		
		dbHandler.Add_Edition(edition);
		String Toast_msg = "Publication ajouté";
	    Toast.makeText(getApplicationContext(), Toast_msg, Toast.LENGTH_LONG).show();
	    
	    Intent intent2 = new Intent(getApplicationContext(), MainActivity.class);
	    intent2.addFlags(Intent.FLAG_ACTIVITY_NEW_TASK | Intent.FLAG_ACTIVITY_CLEAR_TOP); 
	    startActivity(intent2);
		
	}
	
	private class buyingTask extends AsyncTask<String, Void, JSONObject> {
		
        @Override
        protected JSONObject doInBackground(String... params) {
        	JSONParser jParser = new JSONParser();
	        // Getting JSON from URL
	        if (isCancelled()) {
				return null;
			}
	        
	        StringBuilder strBuilder = new StringBuilder("http://api.ngser.gnetix.com/v1.1/AddAchatIndividuel.php");
	        
	        JSONObject jsonObj = new JSONObject();
			
			try {
				//jsonObj.put("id", edition.id);
				//jsonObj.put("prix", edition.prix);
				
				SharedPreferences settings = getSharedPreferences(PREFS_NAME, 0);
	            String username = settings.getString("username", "");
	            String password = settings.getString("password", "");
				
				jsonObj.put("username", username);
				jsonObj.put("password", password);
				jsonObj.put("editionid", String.valueOf(edition.id));
				jsonObj.put("quantite", edition.prix);
				
			}
			catch (JSONException e) {
				e.printStackTrace();
			}
	     	
	        
	        List <BasicNameValuePair> nameValuePairs = new ArrayList<BasicNameValuePair>();
			// add an HTTP variable and value pair
	        nameValuePairs.add(new BasicNameValuePair("data", jsonObj.toString()));
	        
	        
	        String url = strBuilder.toString();
	        Log.v("url archives", url);
	        
	        JSONObject json = jParser.getJSONFromUrlWithPostArray(url, nameValuePairs);
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
	        	Log.e("resultat", resultat);
	        	JSONObject data = json.getJSONObject("data");
	        	
	        	if (resultat.equals("true")) {
					
	        		Log.e("test", "test");
	        		
	        		String newCountCredit = data.getString("total");
	        		
	                SharedPreferences settings = getSharedPreferences(PREFS_NAME, 0);
	                SharedPreferences.Editor editor = settings.edit();
	                
	                
	                String username = settings.getString("username", "");
	                String password = settings.getString("password", "");
	        		
	        		if (username.equals("") || password.equals("")) {
	        			
	        			int current = settings.getInt("ekcredit", 0);
	                    int total = current - Integer.valueOf(newCountCredit);
	                    editor.putInt("ekcredit", total);
	        			
					}
	        		else {
	        			
	        			editor.putInt("ekcredit", Integer.valueOf(newCountCredit));
	        			
	        		}
	        		
	        		
	                
	                
	                editor.commit();
	        		
	                Intent intent = new Intent("SharedPreferencesReceiver");
                    LocalBroadcastManager.getInstance(getApplicationContext()).sendBroadcast(intent);
	                
	        		buyingCompleted();
				}
	        	else {
					Log.e("else", "else");
				}
	        	
        	} catch (JSONException e) {
  	          e.printStackTrace();
  	        }
        }
        
    }
	
	private class creditValidatorTask extends AsyncTask<String, Void, JSONObject> {

        @Override
        protected JSONObject doInBackground(String... params) {
        	JSONParser jParser = new JSONParser();
	        // Getting JSON from URL
	        if (isCancelled()) {
				return null;
			}
	        
	        StringBuilder strBuilder = new StringBuilder("http://api.ngser.gnetix.com/v1.1/getCurrentCredit.php");
	        
	        String url = strBuilder.toString();
	        Log.v("url archives", url);
	        
	        List <BasicNameValuePair> nameValuePairs = new ArrayList<BasicNameValuePair>();
	        
	        SharedPreferences settings = getSharedPreferences(PREFS_NAME, 0);
            String username = settings.getString("username", "");
            String password = settings.getString("password", "");
	        
	        nameValuePairs.add(new BasicNameValuePair("username", username));
	        nameValuePairs.add(new BasicNameValuePair("password", password));
	        
	        JSONObject json = jParser.getJSONFromUrlWithPostArray(url, nameValuePairs);
	        
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
	        	Log.e("resultat", resultat);
	        	JSONObject data = json.getJSONObject("data");
	        	
	        	if (resultat.equals("true")) {
	        		
	        		final String PREFS_NAME = "eKioskPrefSetting";
	                SharedPreferences settings = getSharedPreferences(PREFS_NAME, 0);
	                SharedPreferences.Editor editor = settings.edit();
	                int total = Integer.valueOf(data.getString("quantite"));
	                editor.putInt("ekcredit", total);
                    editor.commit();
	                
                    Intent intent = new Intent("SharedPreferencesReceiver");
                    LocalBroadcastManager.getInstance(getApplicationContext()).sendBroadcast(intent);
                    
                    calculeCredit();
                    
	        	}
	        	
        	} catch (JSONException e) {
	          e.printStackTrace();
	        }
        }
        
    }
	
}
