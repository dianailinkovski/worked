package com.ngser.ekiosk.kiosque;

import java.util.ArrayList;

import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

import android.content.Intent;
import android.content.SharedPreferences;
import android.content.res.Resources;
import android.graphics.Color;
import android.os.AsyncTask;
import android.os.Bundle;
import android.util.Log;
import android.util.TypedValue;
import android.view.View;
import android.widget.AdapterView;
import android.widget.AdapterView.OnItemClickListener;
import android.widget.GridView;

import com.devspark.progressfragment.ProgressGridFragment;
import com.ngser.ekiosk.AsyncTask.JSONParser;
import com.ngser.ekiosk.Model.EditionModelClass;

public class KioskGridViewFragment2 extends ProgressGridFragment {
	private static final String ARG_POSITION = "position";
	private int position;
    //private Handler mHandler;
    ArrayList<EditionModelClass> templist;
    private JSONParse jsonAsync;
    
    final String PREFS_NAME = "eKioskPrefSetting";
    
    //private LruCache<String, Bitmap> mMemoryCache;
    
    public static KioskGridViewFragment2 newInstance(int position) {
    	KioskGridViewFragment2 fragment = new KioskGridViewFragment2();
    	Bundle b = new Bundle();
		b.putInt(ARG_POSITION, position);
		fragment.setArguments(b);
        return fragment;
    }

    @Override
    public void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        position = getArguments().getInt(ARG_POSITION);
        
        
        setHasOptionsMenu(true);
    }

    @Override
    public void onViewCreated(View view, Bundle savedInstanceState) {
        super.onViewCreated(view, savedInstanceState);
        //getGridView().setNumColumns(2);
        Resources r = getResources();
        float px = TypedValue.applyDimension(TypedValue.COMPLEX_UNIT_DIP, 100, r.getDisplayMetrics());
        
        getGridView().setColumnWidth((int) px);
        
        getGridView().setNumColumns(GridView.AUTO_FIT);
    }

    @Override
    public void onActivityCreated(Bundle savedInstanceState) {
        super.onActivityCreated(savedInstanceState);
        // Setup text for empty content
        setEmptyText("Aucune publication trouvé");
        //getGridView().setBackgroundResource(R.drawable.bg_test);
        getView().setBackgroundColor(Color.TRANSPARENT);
        obtainData();
    }

    @Override
    public void onDestroyView() {
        super.onDestroyView();
        //mHandler.removeCallbacks(mShowContentRunnable);
        jsonAsync.cancel(true);
    }

    private void obtainData() {
        // Show indeterminate progress
        setGridShown(false);
        
        jsonAsync = new JSONParse();
        jsonAsync.execute();
        
        //mHandler = new Handler();
        //mHandler.postDelayed(mShowContentRunnable, 3000);
        
        //new JSONParse().execute();
    }
    
    
    private class JSONParse extends AsyncTask<String, String, JSONObject> {
		
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
	        
	        StringBuilder strBuilder = new StringBuilder("http://api.ngser.gnetix.com/v1.1/getRecentsParCategorie.php?categorie=");
	        switch (position) {
			case 0:
				strBuilder.append("Tous");
				break;
			case 1:
				strBuilder.append("Quotidien");
				break;
			case 2:
				strBuilder.append("Hebdomadaire");
				break;
			case 3:
				strBuilder.append("Mensuel");
				break;
			case 4:
				strBuilder.append("Annuel");
				break;
			case 5:
				strBuilder.append("Livres");
				break;

			default:
				break;
			}
	        
	        SharedPreferences settings = getActivity().getApplicationContext().getSharedPreferences(PREFS_NAME, 0);
            String username = settings.getString("username", "");
            String password = settings.getString("password", "");
	        
	        strBuilder.append("&username=");
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
	        	JSONArray data = json.getJSONArray("data");
	        	Log.d("resultat", resultat);
	        	Log.d("data", json.getString("data"));
	        	
	        	

	        	templist = new ArrayList<EditionModelClass>();
                for (int i = 0; i < data.length(); i++) {
                	if (isCancelled()) {
        				return;
        			}
                	
                    JSONObject c = data.getJSONObject(i);
                    
                    EditionModelClass temp = new EditionModelClass();
                    
                    temp.nom = c.getString("nom");
                    temp.type = c.getString("type");
                    temp.categorie = c.getString("categorie");
                    
                    temp.id = Integer.parseInt(c.getString("id"));
                    temp.id_journal = c.getString("id_journal");
                    temp.datePublication = Long.parseLong(c.getString("datePublication"));
                    temp.downloadPath = c.getString("downloadPath");
                    temp.coverPath = c.getString("coverPath");
                    temp.prix = c.getString("prix");
                    temp.bought = c.getString("bought");
                    
                    templist.add(temp);
                }
	        	
	    		
	        	
	        } catch (JSONException e) {
	          e.printStackTrace();
	        }
	        
	        if (isCancelled()) {
				return;
			}
	        KioskArrayAdapter adapter = new KioskArrayAdapter(getActivity().getApplicationContext(), android.R.layout.simple_list_item_1, templist, false, false);
    	    
        	setGridAdapter(adapter);
        	
        	getGridView().setOnItemClickListener(new OnItemClickListener() {

				@Override
				public void onItemClick(AdapterView<?> parent, View view, int position, long id) {
					// TODO Auto-generated method stub
					Intent intent = new Intent(getActivity(), KioskEditionDetailActivity.class);
					
					EditionModelClass selectedEdition = templist.get(position);
					Bundle b = new Bundle();
					b.putInt("id_edition", selectedEdition.id);
					intent.putExtras(b); //Put your id to your next Intent
			        startActivity (intent);
					
				}
			});
        	
            setGridShown(true);
            
	    }
	}
    
}
