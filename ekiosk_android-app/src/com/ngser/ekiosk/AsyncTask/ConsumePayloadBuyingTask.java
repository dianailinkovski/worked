package com.ngser.ekiosk.AsyncTask;

import java.util.ArrayList;
import java.util.List;

import org.apache.http.message.BasicNameValuePair;
import org.json.JSONException;
import org.json.JSONObject;

import android.content.Context;
import android.content.SharedPreferences;
import android.os.AsyncTask;
import android.os.Process;
import android.util.Log;

public class ConsumePayloadBuyingTask extends AsyncTask<String, Process, String> {
	
	
	
	public interface ConsumePayloadBuyingTaskListener{
	  public void ConsumePayloadBuyingTaskFinish(String receivedTotalString);
	  public void ConsumePayloadBuyingTaskFinishWithError(String errorString);
	}
	
	ConsumePayloadBuyingTaskListener mListener;
	
	public String mPayload = "payload_test", mSKU = "sku_test";
	final String PREFS_NAME = "eKioskPrefSetting";
	public Context mContext;
	private JSONObject mJson;
	
	public void setListener(ConsumePayloadBuyingTaskListener listener){
		mListener = listener;
	}
   
	@Override
	protected String doInBackground(String... params) {
		
		
		StringBuilder strBuilder = new StringBuilder("http://api.ngser.gnetix.com/v1.1/android/ConsumePayload.php");
        
        String url = strBuilder.toString();
        Log.v("url archives", url);
        
        List <BasicNameValuePair> nameValuePairs = new ArrayList<BasicNameValuePair>();
        
        SharedPreferences settings = mContext.getSharedPreferences(PREFS_NAME, 0);
        String username = settings.getString("username", "");
        String password = settings.getString("password", "");
        
        nameValuePairs.add(new BasicNameValuePair("username", username));
        nameValuePairs.add(new BasicNameValuePair("password", password));
        nameValuePairs.add(new BasicNameValuePair("sku", mSKU));
        nameValuePairs.add(new BasicNameValuePair("payload", mPayload));
        
        JSONParser jParser = new JSONParser();
        mJson = jParser.getJSONFromUrlWithPostArray(url, nameValuePairs);
		
		
		return null;
	}
	
	@Override
	protected void onPostExecute(String result) {
		// TODO Auto-generated method stub
		
		try {
        	if (mJson == null) {
				return;
			}
        	
        	String resultat = mJson.getString("resultat");
        	Log.e("resultat", resultat);
        	
        	
        	if (resultat.equals("true")) {
        		String data = mJson.getString("data");
        		mListener.ConsumePayloadBuyingTaskFinish(data);
        	}
        	else {
        		String data = mJson.getString("data");
        		mListener.ConsumePayloadBuyingTaskFinishWithError(data);
        		
        	}
        	
    	} catch (JSONException e) {
          e.printStackTrace();
          mListener.ConsumePayloadBuyingTaskFinishWithError(e.toString());
        }
	}

}
