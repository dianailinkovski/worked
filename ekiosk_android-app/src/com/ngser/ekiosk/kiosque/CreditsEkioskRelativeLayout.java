package com.ngser.ekiosk.kiosque;

import java.util.ArrayList;

import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

import android.content.Context;
import android.content.SharedPreferences;
import android.content.res.Configuration;
import android.content.res.Resources;
import android.os.AsyncTask;
import android.os.Bundle;
import android.util.AttributeSet;
import android.util.Log;
import android.util.TypedValue;
import android.view.View;
import android.view.ViewGroup;
import android.widget.GridView;
import android.widget.LinearLayout;
import android.widget.ProgressBar;
import android.widget.ScrollView;

import com.devspark.progressfragment.ProgressFragment;
import com.ngser.ekiosk.R;
import com.ngser.ekiosk.AsyncTask.JSONParser;
import com.ngser.ekiosk.Model.PackagesModelClass;

public class CreditsEkioskRelativeLayout extends ProgressFragment {
	
	int AccountActivated = -1;
	
	ExpandableHeightGridView gridView;
	ScrollView scrollView;
	LinearLayout linearLayout;
	ProgressBar mainProgressBar;
	
	GetDataPackages getDataPackages;
	GetAccountValidated getAccountValidated;
	ArrayList<PackagesModelClass> templist;
	
	JSONObject json;
	
	final String PREFS_NAME = "eKioskPrefSetting";
	
	public static CreditsEkioskRelativeLayout newInstance() {
		CreditsEkioskRelativeLayout fragment = new CreditsEkioskRelativeLayout();
    	return fragment;
    }
	
	@Override
    public void onCreate(Bundle savedInstanceState) {
    	// TODO Auto-generated method stub
    	Log.v("onCreate", "onCreate");
    	super.onCreate(savedInstanceState);
    	
    }
    
	@Override
    public void onDestroyView() {
    	// TODO Auto-generated method stub
    	super.onDestroyView();
    	getDataPackages.cancel(true);
    	if (getAccountValidated != null) {
    		getAccountValidated.cancel(true);
		}
    	
    }
    
    @Override
    public void onViewCreated(View view, Bundle savedInstanceState) {
        super.onViewCreated(view, savedInstanceState);
        //LayoutInflater inflater = (LayoutInflater) getContext().getSystemService(Context.LAYOUT_INFLATER_SERVICE);
		//View myFragmentView = inflater.inflate(R.layout.credit_ekiosk, null, false);
        setContentView(R.layout.credit_ekiosk);
        
        this.scrollView = (ScrollView) getView().findViewById(R.id.scrollView);
		this.linearLayout = (LinearLayout) getView().findViewById(R.id.mainLinearLayout);
		this.mainProgressBar = (ProgressBar) getView().findViewById(R.id.mainProgressBar);
		
		//addView(myFragmentView);
		
		gridView = new ExpandableHeightGridView(getActivity().getApplicationContext());
		float px;
        Resources r = getResources();
        if ((getResources().getConfiguration().screenLayout & Configuration.SCREENLAYOUT_SIZE_MASK) == Configuration.SCREENLAYOUT_SIZE_XLARGE || 
        		(getResources().getConfiguration().screenLayout & Configuration.SCREENLAYOUT_SIZE_MASK) == Configuration.SCREENLAYOUT_SIZE_LARGE) {
        	px = TypedValue.applyDimension(TypedValue.COMPLEX_UNIT_DIP, 200, r.getDisplayMetrics());
        	
        }
        else {
        	px = TypedValue.applyDimension(TypedValue.COMPLEX_UNIT_DIP, 150, r.getDisplayMetrics());
        }
        
        gridView.setColumnWidth((int) px);
        gridView.setNumColumns(GridView.AUTO_FIT);
		gridView.setExpanded(true);
       // linearLayout.removeAllViews();
        linearLayout.addView(gridView);
        
        //scrollView.setVisibility(View.GONE);
        
    }
	
    @Override
	public void onStart() {
		super.onStart();
		
		getDataPackages = new GetDataPackages();
        getDataPackages.execute();
        
        
        SharedPreferences settings = getActivity().getSharedPreferences(PREFS_NAME, 0);
        String username = settings.getString("username", "");
        String password = settings.getString("password", "");
        
        if (username.equals("") && password.equals("")) {
			AccountActivated = 1;
		}
        else {
        	getAccountValidated = new GetAccountValidated();
            getAccountValidated.execute();
        }
		
	}
	
    @Override
    public void onStop() {
    	super.onStop();
    	
    	gridView.setAdapter(null);
    	setContentShown(false);
    	
    }
    
	public void dataReceived() {
    	try {
    		if (AccountActivated == -1) {
				return;
			}
    		
    		setContentShown(true);
        	//String resultat = json.getString("resultat");
        	JSONArray data = json.getJSONArray("data");
        	//Log.d("resultat", resultat);
        	//Log.d("data", json.getString("data"));
        	
        	templist = new ArrayList<PackagesModelClass>();
        	
    	    for (int i = 0; i < data.length(); i++) {
            	
                JSONObject c = data.getJSONObject(i);
                
                PackagesModelClass temp = new PackagesModelClass();
                
                temp.id = c.getString("id");
                temp.nom = c.getString("nom");
                temp.google = c.getString("google");
                temp.quantite = c.getString("quantite");
                temp.prix_usd = c.getString("prix_usd");
                temp.equivalent = c.getString("equivalent");
                temp.bonis = c.getString("bonis");
                
                templist.add(temp);
        	}
        	
    		
        	
        } catch (JSONException e) {
          e.printStackTrace();
        }
        
        CreditEkioskArrayAdapter adapter = new CreditEkioskArrayAdapter(getActivity().getApplicationContext(), android.R.layout.simple_list_item_1, templist, AccountActivated);
	    
        gridView.setAdapter(adapter);
        
        mainProgressBar.setVisibility(View.GONE);
        
    }
	
	private class GetDataPackages extends AsyncTask<String, String, JSONObject> {
		
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
	        
	        StringBuilder strBuilder = new StringBuilder("http://api.ngser.gnetix.com/v1.1/getVCBundle.php");
	        
	        String url = strBuilder.toString();
	        
	        
	        JSONObject json = jParser.getJSONFromUrl(url);
	        if (isCancelled()) {
				return null;
			}
	        return json;
	    }
	    
	    @Override
	    protected void onPostExecute(JSONObject _json) {
	        
	    	if (isCancelled()) {
				return;
			}
	    	
	    	if (_json == null) {
				return;
			}
	    	
        	json = _json;
	    	dataReceived();
	    	
	    }
	}
	
	private class GetAccountValidated extends AsyncTask<String, String, JSONObject> {
		
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
	        
	        SharedPreferences settings = getActivity().getSharedPreferences(PREFS_NAME, 0);
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
	    protected void onPostExecute(JSONObject _json) {
	        
	    	if (isCancelled()) {
				return;
			}
	    	
	    	if (_json == null) {
				return;
			}
	    	
	    	try {
	    		
	    		JSONObject data = _json.getJSONObject("data");
	    		
				AccountActivated = Integer.parseInt(data.getString("activated"));
				dataReceived();
				
				//getDataPackages = new GetDataPackages();
		        //getDataPackages.execute();
		        
			} catch (NumberFormatException e) {
				// TODO Auto-generated catch block
				e.printStackTrace();
			} catch (JSONException e) {
				// TODO Auto-generated catch block
				e.printStackTrace();
			}
	    	
	    	
	    	
        	//json = _json;
	    	//dataReceived();
	    	
	    }
	}
	
	public class ExpandableHeightGridView extends GridView
	{
		
		boolean expanded = false;
		
		public ExpandableHeightGridView(Context context)
		{
			super(context);
		}
		
		public ExpandableHeightGridView(Context context, AttributeSet attrs)
		{
			super(context, attrs);
		}
		
		public ExpandableHeightGridView(Context context, AttributeSet attrs, int defStyle)
		{
			super(context, attrs, defStyle);
		}
		
		public boolean isExpanded()
		{
			return expanded;
		}
		
		@Override
		public void onMeasure(int widthMeasureSpec, int heightMeasureSpec)
		{
			// HACK! TAKE THAT ANDROID!
			if (isExpanded())
			{
				// Calculate entire height by providing a very large height hint.
				// But do not use the highest 2 bits of this integer; those are
				// reserved for the MeasureSpec mode.
				int expandSpec = MeasureSpec.makeMeasureSpec(
					Integer.MAX_VALUE >> 2, MeasureSpec.AT_MOST);
				super.onMeasure(widthMeasureSpec, expandSpec);
				
				ViewGroup.LayoutParams params = getLayoutParams();
				params.height = getMeasuredHeight();
			}
			else
			{
				super.onMeasure(widthMeasureSpec, heightMeasureSpec);
			}
		}
		
		public void setExpanded(boolean expanded)
		{
			this.expanded = expanded;
		}
	}
	
}
