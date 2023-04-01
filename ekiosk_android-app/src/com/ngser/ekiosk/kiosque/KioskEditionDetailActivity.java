package com.ngser.ekiosk.kiosque;

import java.text.ParseException;
import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Date;

import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

import android.content.Context;
import android.content.Intent;
import android.content.SharedPreferences;
import android.content.res.Configuration;
import android.content.res.Resources;
import android.graphics.Bitmap;
import android.graphics.BitmapFactory;
import android.graphics.Color;
import android.graphics.Point;
import android.graphics.drawable.BitmapDrawable;
import android.os.AsyncTask;
import android.os.Bundle;
import android.support.v4.app.NavUtils;
import android.util.AttributeSet;
import android.util.Log;
import android.util.TypedValue;
import android.view.Display;
import android.view.Gravity;
import android.view.View;
import android.view.View.OnClickListener;
import android.view.ViewGroup;
import android.view.WindowManager;
import android.widget.AdapterView;
import android.widget.AdapterView.OnItemClickListener;
import android.widget.Button;
import android.widget.GridView;
import android.widget.LinearLayout;
import android.widget.LinearLayout.LayoutParams;
import android.widget.ProgressBar;
import android.widget.RelativeLayout;
import android.widget.ScrollView;
import android.widget.TextView;
import android.widget.Toast;

import com.actionbarsherlock.app.ActionBar;
import com.actionbarsherlock.app.SherlockActivity;
import com.actionbarsherlock.internal.widget.IcsLinearLayout;
import com.ngser.ekiosk.MainActivity;
import com.ngser.ekiosk.PdfReaderActivity;
import com.ngser.ekiosk.R;
import com.ngser.ekiosk.AsyncTask.JSONParser;
import com.ngser.ekiosk.Model.DatabaseHandler;
import com.ngser.ekiosk.Model.EditionModelClass;
import com.ngser.ekiosk.kiosque.KioskArrayAdapter.ImageLoadedListener;

public class KioskEditionDetailActivity extends SherlockActivity {
	
	int AccountActivated = -1;
	
	ArrayList<EditionModelClass> templist;
    private JSONParse jsonAsync;
    private GetDataEdition getDataEdition;
    LinearLayout mainLinearLayout;
    Bitmap bitmapBackGround;
    Bitmap big_ekcredit;
    Button buyButton;
    
	EditionModelClass edition;
	int id_edition;
	ExpandableHeightGridView gridView;
	
	AdsImageView topAds;
	AdsImageView bottomAds;
	Bitmap topAdsBitmap = null;
	Bitmap bottomAdsBitmap = null;
	
	ScrollView mainScrollView;
	ProgressBar mainProgressBar;
	
	Context mContext;
	
	DatabaseHandler dbHandler = new DatabaseHandler(this);
	
	final String PREFS_NAME = "eKioskPrefSetting";
	
	public KioskEditionDetailActivity() {
		
	}
	
	@Override
	protected void onSaveInstanceState(Bundle outState) {
	    if (topAds.getVisibility() == View.VISIBLE) {
			if (topAds.adsBitmap != null) {
				topAdsBitmap = Bitmap.createBitmap(topAds.adsBitmap);
			}
			else {
				topAdsBitmap = null;
			}
			outState.putParcelable("topAdsBitmap", topAdsBitmap);
		}
	    
	    if (bottomAds.getVisibility() == View.VISIBLE) {
			if (bottomAds.adsBitmap != null) {
				bottomAdsBitmap = Bitmap.createBitmap(bottomAds.adsBitmap);
			}
			else {
				bottomAdsBitmap = null;
			}
			outState.putParcelable("bottomAdsBitmap", bottomAdsBitmap);
		}
		
	    Log.d("STATE-SAVE", "onSaveInstanceState()");
	    super.onSaveInstanceState(outState);
	}
	
	@Override
	protected void onCreate(Bundle savedInstanceState) {
		// TODO Auto-generated method stub
		super.onCreate(savedInstanceState);
		if (savedInstanceState != null) {
			topAdsBitmap = savedInstanceState.getParcelable("topAdsBitmap");
			bottomAdsBitmap = savedInstanceState.getParcelable("bottomAdsBitmap");
		}
		
		mContext = getApplicationContext();
		
		Bundle b = this.getIntent().getExtras();
		id_edition = b.getInt("id_edition");
		
		
		setContentView(R.layout.kiosque_edition_detail);
		mainLinearLayout = (LinearLayout) findViewById(R.id.mainLinearLayout);
		mainScrollView = (ScrollView) findViewById(R.id.scrollView);
		mainProgressBar = (ProgressBar) findViewById(R.id.mainProgressBar);
		
		
		BitMapBGView bgImageView = new BitMapBGView(getBaseContext());
		bitmapBackGround = BitmapFactory.decodeResource(getResources(), R.drawable.bg_test);
    	
    	WindowManager wm = (WindowManager) getApplicationContext().getSystemService(Context.WINDOW_SERVICE);
    	Display display = wm.getDefaultDisplay();
    	Point size = new Point();
    	display.getSize(size);
    	int width = size.x;
    	int height = size.y;
    	
    	
    	float scalingFactor = 0;
    	int scaleHeight = 0;
    	int scaleWidth = 0;
    	
    	scalingFactor = (float) width / (float) bitmapBackGround.getWidth();
    	scaleHeight = (int) (bitmapBackGround.getHeight() * scalingFactor);
        scaleWidth = (int) (bitmapBackGround.getWidth() * scalingFactor);
        
        if (scaleHeight < height) {
        	scalingFactor = (float) height / (float) bitmapBackGround.getHeight();
        	scaleHeight = (int) (bitmapBackGround.getHeight() * scalingFactor);
            scaleWidth = (int) (bitmapBackGround.getWidth() * scalingFactor);
		}
    	
        
    	bgImageView.imageBitmap = Bitmap.createScaledBitmap(bitmapBackGround,scaleWidth,scaleHeight,true);
        
    	RelativeLayout rl = (RelativeLayout)findViewById(R.id.relativeLayout);
    	RelativeLayout.LayoutParams params = new RelativeLayout.LayoutParams(RelativeLayout.LayoutParams.MATCH_PARENT, RelativeLayout.LayoutParams.MATCH_PARENT);
    	params.addRule(RelativeLayout.ALIGN_PARENT_LEFT, RelativeLayout.TRUE);
    	params.addRule(RelativeLayout.ALIGN_PARENT_TOP, RelativeLayout.TRUE);
    	
    	rl.addView(bgImageView, params);
    	mainScrollView.setBackgroundColor(Color.TRANSPARENT);
    	rl.bringChildToFront(mainScrollView);
		
    	TextView creditTV = new TextView(getApplicationContext());
        creditTV.setTextSize(TypedValue.COMPLEX_UNIT_DIP, 20);
        big_ekcredit = BitmapFactory.decodeResource(getApplicationContext().getResources(), R.drawable.big_ekcredit);

	    BitmapDrawable myIcon = new BitmapDrawable(getApplicationContext().getResources(), big_ekcredit);
	    creditTV.setCompoundDrawablesWithIntrinsicBounds(null, null, myIcon, null ); 
	    creditTV.setCompoundDrawablePadding(10);
	    creditTV.setGravity(Gravity.CENTER);
	    SharedPreferences settings = getSharedPreferences(PREFS_NAME, 0);
		int ekcredit = settings.getInt("ekcredit", 0);
    	creditTV.setText(String.valueOf(ekcredit));
    	creditTV.setTextColor(Color.BLACK);
    	
	    // configure custom view
	    IcsLinearLayout listNavLayout = (IcsLinearLayout) getLayoutInflater().inflate(R.layout.abs__action_bar_tab_bar_view, null);
        LinearLayout.LayoutParams paramsll = new LinearLayout.LayoutParams(LayoutParams.WRAP_CONTENT, LayoutParams.MATCH_PARENT);
        paramsll.gravity = Gravity.CENTER;
        listNavLayout.addView(creditTV, paramsll);
        listNavLayout.setGravity(Gravity.RIGHT);
        listNavLayout.setPadding(0, 0, 10, 0);
        
        ActionBar actionBar = getSupportActionBar();
        actionBar.setCustomView(listNavLayout, new ActionBar.LayoutParams(Gravity.RIGHT));
        actionBar.setDisplayShowCustomEnabled(true);    	
    	
		buyButton = (Button) findViewById(R.id.button1);
		
		buyButton.setOnClickListener(new OnClickListener() {
			
			@Override
			public void onClick(View v) {                	
                	
                	if (buyButton.getText().toString().equals("Ouvrir")) {
						
                		DatabaseHandler dbHandler = new DatabaseHandler(getApplicationContext());
                      	EditionModelClass editionBD = dbHandler.Get_Edition(edition.id);
                    	dbHandler.close();
                    	
                    	if (edition != null && editionBD.openDate == 0) {
        	        		DatabaseHandler dbHandler2 = new DatabaseHandler(getApplicationContext());
        	        		editionBD.setOpenDate(System.currentTimeMillis());
        	        		int test = dbHandler2.Update_Edition(editionBD);
        	        		dbHandler2.close();
        				}
        				
        				Intent intent = new Intent(getApplicationContext(), PdfReaderActivity.class)
        				.setFlags(Intent.FLAG_ACTIVITY_NEW_TASK);
        				String path = editionBD.getLocalPath();
        				path += "/issue.pdf";
        				Log.i("path", path);
        			    intent.putExtra("path", path);
        			    getApplicationContext().startActivity(intent);
                		return;
					}
                	
                	if (AccountActivated == -1) {
						return;
					}
                	else if (AccountActivated == 0) {
						
                		Intent intent2 = new Intent(getApplicationContext(), ActivationActivity.class);
                		intent2.addFlags(Intent.FLAG_ACTIVITY_NEW_TASK);
                		startActivity(intent2);
                		
                		return;
					}
                	
                	if (buyButton.getText().toString().equals("Télécharger")) {
						
                		dbHandler.Add_Edition(edition);
        				String Toast_msg = "Publication ajouté";
    				    Show_Toast(Toast_msg);
    				    
    				    Intent intent2 = new Intent(getApplicationContext(), MainActivity.class);
    				    intent2.addFlags(Intent.FLAG_ACTIVITY_NEW_TASK | Intent.FLAG_ACTIVITY_CLEAR_TOP); 
    				    startActivity(intent2);                		
					}
                	else {
	                	
	                	JSONObject jsonObj = new JSONObject();
	    				
	    				try {	    					
	    					jsonObj.put("nom", edition.nom);
	    					jsonObj.put("type", edition.type);
	    					jsonObj.put("categorie", edition.categorie);
	    			        
	    					jsonObj.put("id", edition.id);
	    					jsonObj.put("id_journal", edition.id_journal);
	    					jsonObj.put("datePublication", edition.datePublication);
	    					jsonObj.put("downloadPath", edition.downloadPath);
	    					jsonObj.put("coverPath", edition.coverPath);
	    					jsonObj.put("prix", edition.prix);
	    					jsonObj.put("isSubscription", edition.isSubscription);
	    					jsonObj.put("telechargementRestant", edition.telechargementRestant);
	    					
	    					
	    				} catch (JSONException e) {
	    					e.printStackTrace();
	    				}
	                	
	                	Intent intent2 = new Intent(getApplicationContext(), BuyWithCreditActivity.class);
					    intent2.addFlags(Intent.FLAG_ACTIVITY_NEW_TASK);
					    
					    Bundle b = new Bundle();
						b.putString("id_prix_publication", jsonObj.toString());
						intent2.putExtras(b); //Put your id to your next Intent
						
					    startActivity(intent2);
                	}                	
            }
        });
		
		
		
		gridView = new ExpandableHeightGridView(this);
		float px;
        Resources r = getResources();
        if ((getResources().getConfiguration().screenLayout & Configuration.SCREENLAYOUT_SIZE_MASK) == Configuration.SCREENLAYOUT_SIZE_XLARGE || 
        		(getResources().getConfiguration().screenLayout & Configuration.SCREENLAYOUT_SIZE_MASK) == Configuration.SCREENLAYOUT_SIZE_LARGE) {
        	px = TypedValue.applyDimension(TypedValue.COMPLEX_UNIT_DIP, 140, r.getDisplayMetrics());
        	
        }
        else {
        	px = TypedValue.applyDimension(TypedValue.COMPLEX_UNIT_DIP, 100, r.getDisplayMetrics());
        }
        
        gridView.setColumnWidth((int) px);
        gridView.setNumColumns(GridView.AUTO_FIT);
		gridView.setExpanded(true);
        
		float height2 = width * 0.1388f;
		
		topAds = new AdsImageView(getApplicationContext());
		LinearLayout.LayoutParams tempLayout = new LinearLayout.LayoutParams(LinearLayout.LayoutParams.WRAP_CONTENT, Math.round(height2));
		mainLinearLayout.addView(topAds, 0, tempLayout);
		mainLinearLayout.addView(gridView);
        
		bottomAds = new AdsImageView(getApplicationContext());
		
		LinearLayout.LayoutParams tempLayout2 = new LinearLayout.LayoutParams(LinearLayout.LayoutParams.WRAP_CONTENT, Math.round(height2));
		mainLinearLayout.addView(bottomAds, tempLayout2);
		
		mainProgressBar.bringToFront();
        
	}
	
	@Override
    public boolean onCreateOptionsMenu(com.actionbarsherlock.view.Menu menu) {
    	getActionBar().setDisplayHomeAsUpEnabled(true);
        return true;
    }

    @Override
    public boolean onOptionsItemSelected(com.actionbarsherlock.view.MenuItem item) {
        
    	int id = item.getItemId();
        if (id == android.R.id.home) {        	
        	final Intent intent = NavUtils.getParentActivityIntent(this);
        	intent.addFlags(Intent.FLAG_ACTIVITY_NO_ANIMATION);
        	NavUtils.navigateUpTo(this, intent);
        	
            return true;
        }
        
        return super.onOptionsItemSelected(item);
    }
	
    @Override
    protected void onStart() {
    	// TODO Auto-generated method stub
    	super.onStart();
    	AccountActivated = -1;
    	
    	mainLinearLayout.setVisibility(View.GONE);
    	
    	getDataEdition = new GetDataEdition();
        getDataEdition.execute();
        
        
        SharedPreferences settings = getSharedPreferences(PREFS_NAME, 0);
        String username = settings.getString("username", "");
        String password = settings.getString("password", "");
        
        if (username.equals("") && password.equals("")) {
        	AccountActivated = 1;
		}
    }
    
    @Override
    protected void onStop() {
    	super.onStop();
    	
    	TextView titleTextView = (TextView) findViewById(R.id.titleTextView);
        TextView categorieTextView = (TextView) findViewById(R.id.categorieTextView);
        TextView dateTextView = (TextView) findViewById(R.id.dateTextView);
        TextView prixTextView = (TextView) findViewById(R.id.prixTextView);
        TextView notifTextView = (TextView) findViewById(R.id.notifTextView);
        
        NetworkedCacheableImageView imageView = (NetworkedCacheableImageView) findViewById(R.id.imageView1);
        ProgressBar progressBar = (ProgressBar) findViewById(R.id.progressBar1);
        Button button = (Button) findViewById(R.id.button1);
        TextView buttonDetail = (TextView) findViewById(R.id.button1Detail);
        
        
        titleTextView.setText("");
        categorieTextView.setText("");
        dateTextView.setText("");
        
        prixTextView.setText("");
        notifTextView.setText("");
        
        imageView.setImageBitmap(null);
        progressBar.setVisibility(View.VISIBLE);
        
        button.setText("");
        buttonDetail.setText("");
        
        gridView.setAdapter(null);
        
        mainProgressBar.setVisibility(View.VISIBLE);
        
        
        if (topAds.getVisibility() == View.VISIBLE) {
			if (topAds.adsBitmap != null) {
				topAdsBitmap = Bitmap.createBitmap(topAds.adsBitmap);
			}
			else {
				topAdsBitmap = null;
			}
		}
	    
	    if (bottomAds.getVisibility() == View.VISIBLE) {
			if (bottomAds.adsBitmap != null) {
				bottomAdsBitmap = Bitmap.createBitmap(bottomAds.adsBitmap);
			}
			else {
				bottomAdsBitmap = null;
			}
		}
	    
	    topAds.adsImage.setImageBitmap(null);
	    bottomAds.adsImage.setImageBitmap(null);
	    
    }
    
	@Override
	protected void onDestroy() {
		// TODO Auto-generated method stub
		super.onDestroy();
		
		if (bitmapBackGround != null) {
        	bitmapBackGround.recycle();
        	bitmapBackGround = null;
		}
		if (big_ekcredit != null) {
        	big_ekcredit.recycle();
        	big_ekcredit = null;
		}
		if (getDataEdition != null) {
			getDataEdition.cancel(true);			
		}
		
		if (jsonAsync != null) {
			jsonAsync.cancel(true);
		}
		
	}
		
	public void Show_Toast(String msg) {
		Toast.makeText(getApplicationContext(), msg, Toast.LENGTH_LONG).show();
    }
	
	private class GetDataEdition extends AsyncTask<String, String, JSONObject> {
		
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
            String username = settings.getString("username", "");
            String password = settings.getString("password", "");
	        
	        StringBuilder strBuilder = new StringBuilder("http://api.ngser.gnetix.com/v1.1/getEditionRefreshed.php?editionId=");
	        
	    	strBuilder.append(id_edition);
	    	
	        strBuilder.append("&username=");
	        strBuilder.append(username);
	        strBuilder.append("&password=");
	        strBuilder.append(password);
	        String url = strBuilder.toString();
	        
	        Log.v("url", url);
	        
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
	        	Log.d("resultat", resultat);
	        	Log.d("data", data.toString());
	        	
	        	edition = new EditionModelClass();
                
	        	edition.nom = data.getString("nom");
	        	edition.type = data.getString("type");
	        	edition.categorie = data.getString("categorie");
                
	        	edition.id = Integer.parseInt(data.getString("id"));
	        	edition.id_journal = data.getString("id_journal");
	        	try {
    				SimpleDateFormat format = new SimpleDateFormat("yyyy-MM-dd");
    		    	Date date = format.parse(data.getString("datePublication"));
    		    	edition.datePublication = date.getTime();
    			} catch (ParseException e) {
    				//e.printStackTrace();
    				edition.datePublication = 0;
    			}
                //edition.datePublication = Long.parseLong(data.getString("datePublication"));
	        	edition.downloadPath = data.getString("downloadPath");
	        	edition.coverPath = data.getString("coverPath");
	        	edition.prix = data.getString("prix");
	        	edition.isSubscription = data.getInt("isSubscription");
	        	edition.telechargementRestant = data.getString("telechargementRestant");
	        	
	        	AccountActivated = Integer.parseInt(data.getString("activatedAccount"));
	        	
	        } catch (JSONException e) {
	          e.printStackTrace();
	        }
	        
	        if (isCancelled()) {
				return;
			}
	        
	        setDataInView();
	        mainLinearLayout.setVisibility(View.VISIBLE);
	        
	        
	        jsonAsync = new JSONParse();
	        jsonAsync.execute();
            
	    }
	}
		
	private void setDataInView() {
		TextView titleTextView = (TextView) findViewById(R.id.titleTextView);
        TextView categorieTextView = (TextView) findViewById(R.id.categorieTextView);
        TextView dateTextView = (TextView) findViewById(R.id.dateTextView);
        TextView prixTextView = (TextView) findViewById(R.id.prixTextView);
        TextView notifTextView = (TextView) findViewById(R.id.notifTextView);
        
        NetworkedCacheableImageView imageView = (NetworkedCacheableImageView) findViewById(R.id.imageView1);
        ProgressBar progressBar = (ProgressBar) findViewById(R.id.progressBar1);
        Button button = (Button) findViewById(R.id.button1);
        TextView buttonDetail = (TextView) findViewById(R.id.button1Detail);
        
        final boolean fromCache = imageView.loadImage(edition.coverPath, false, new ImageLoadedListener(progressBar));
        
        if (fromCache) {
        	progressBar.setVisibility(View.GONE);
        } else {
        	progressBar.setVisibility(View.VISIBLE);
        }
        
        StringBuilder dateString = new StringBuilder("édition du\n");
        SimpleDateFormat sdf;
        
        Date netDate = (new Date(edition.datePublication));
        
        sdf = new SimpleDateFormat("dd");
        dateString.append(sdf.format(netDate)+" ");
        sdf = new SimpleDateFormat("MM");
        dateString.append(getMonthString(sdf.format(netDate)));
        sdf = new SimpleDateFormat("yyyy");
        dateString.append(" "+sdf.format(netDate));        
        
        titleTextView.setText(edition.nom);
        categorieTextView.setText(edition.categorie);
        dateTextView.setText(dateString);        
        
        Bitmap bm1 = BitmapFactory.decodeResource(mContext.getResources(), R.drawable.big_ekcredit);

	    BitmapDrawable myIcon = new BitmapDrawable(mContext.getResources(), bm1);
	    prixTextView.setCompoundDrawablesWithIntrinsicBounds(null, null, myIcon, null ); 
	    prixTextView.setCompoundDrawablePadding(10);
	    
        prixTextView.setText(edition.prix);        
        
        DatabaseHandler dbHandler = new DatabaseHandler(getApplicationContext());
      	EditionModelClass editionBD = dbHandler.Get_Edition(edition.id);
    	dbHandler.close();
    	
        
        if (editionBD != null) {
        	button.setText("Ouvrir");
        	prixTextView.setVisibility(View.GONE);
        	notifTextView.setVisibility(View.GONE);
        	
        	if (edition.telechargementRestant.equals("-1")) {
        		buttonDetail.setText("3 téléchargements par achat");
        	}
        	else if (edition.telechargementRestant.equals("0")) {
            	buttonDetail.setText("Téléchargement par achat épuisé");
            }
            else if (edition.telechargementRestant.equals("1")) {
            	buttonDetail.setText("1 téléchargement restant");
            }
            else {
            	buttonDetail.setText(edition.telechargementRestant+" téléchargements restant");
            }
		}
        else if (edition.telechargementRestant.equals("-1")) {
        	button.setText("Acheter");
        	buttonDetail.setText("3 téléchargements par achat");
        	
        	prixTextView.setVisibility(View.VISIBLE);
        	notifTextView.setVisibility(View.VISIBLE);        	
		}
        else if (edition.telechargementRestant.equals("0")) {
        	button.setText("Acheter é nouveau");
        	buttonDetail.setText("Téléchargement par achat épuisé");
        	
        	prixTextView.setVisibility(View.GONE);
        	notifTextView.setVisibility(View.GONE);
        }
        else if (edition.telechargementRestant.equals("1")) {
        	button.setText("Télécharger");
        	buttonDetail.setText("1 téléchargement restant");
        	
        	prixTextView.setVisibility(View.GONE);
        	notifTextView.setVisibility(View.GONE);
        }
        else {
        	button.setText("Télécharger");
        	buttonDetail.setText(edition.telechargementRestant+" téléchargements restant");
        	prixTextView.setVisibility(View.GONE);
        	notifTextView.setVisibility(View.GONE);
        }
        
        SharedPreferences settings = getSharedPreferences(PREFS_NAME, 0);
        String username = settings.getString("username", "");
        String password = settings.getString("password", "");
        
        if (username.equals("") || password.equals("")) {
        	buttonDetail.setText("Aucun retéléchargement sans compte ekiosk");
		}
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
	        
	        SharedPreferences settings = getSharedPreferences(PREFS_NAME, 0);
            String username = settings.getString("username", "");
            String password = settings.getString("password", "");
	        
	        StringBuilder strBuilder = new StringBuilder("http://api.ngser.gnetix.com/v1.1/getMemeEditeurs.php?id=");
	        
			strBuilder.append(edition.id_journal);
			
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
	        	JSONObject data = json.getJSONObject("data");
	        	Log.d("resultat", resultat);
	        	Log.d("data", json.getString("data"));
	        	
	        	JSONArray publicationsArray = data.getJSONArray("publications");
	        	JSONObject topAdsObject = data.getJSONObject("topPub");
	        	JSONObject bottomAdsObject = data.getJSONObject("bottomPub");

	        	templist = new ArrayList<EditionModelClass>();
                for (int i = 0; i < publicationsArray.length(); i++) {
                	if (isCancelled()) {
        				return;
        			}
                	
                    JSONObject c = publicationsArray.getJSONObject(i);
                    
                    EditionModelClass temp = new EditionModelClass();
                    
                    temp.nom = c.getString("nom");
                    temp.type = c.getString("type");
                    temp.categorie = c.getString("categorie");
                    
                    temp.id = Integer.parseInt(c.getString("id"));
                    temp.id_journal = c.getString("id_journal");
                    try {
        				SimpleDateFormat format = new SimpleDateFormat("yyyy-MM-dd");
        		    	Date date = format.parse(c.getString("datePublication"));
        		    	temp.datePublication = date.getTime();
        			} catch (ParseException e) {
        				//e.printStackTrace();
        				temp.datePublication = 0;
        			}
                    
                    temp.downloadPath = c.getString("downloadPath");
                    temp.coverPath = c.getString("coverPath");
                    temp.prix = c.getString("prix");
                    temp.isSubscription = c.getInt("isSubscription");
                    
                    temp.telechargementRestant = c.getString("telechargementRestant");
                    
                    templist.add(temp);
                }
	        	
                if (topAdsObject.getString("image").equals("")) {
					//mainLinearLayout.removeView(topAds);
                	topAds.setVisibility(View.GONE);
				}
                else {
                	if (topAdsBitmap != null) {
						topAds.adsBitmap = topAdsBitmap;
					}
                	
                	if (topAdsObject.getString("url").equals("")) {
                		topAds.setUrlString(topAdsObject.getString("image"), "");
                	}
                	else {
                		topAds.setUrlString(topAdsObject.getString("image"), topAdsObject.getString("url"));
                	}
                }
                
                if (bottomAdsObject.getString("image").equals("")) {
					//mainLinearLayout.removeView(bottomAds);
                	bottomAds.setVisibility(View.GONE);
				}
                else {
                	if (bottomAdsBitmap != null) {
						bottomAds.adsBitmap = bottomAdsBitmap;
					}
                	
                	if (bottomAdsObject.getString("url").equals("")) {
                		bottomAds.setUrlString(bottomAdsObject.getString("image"), "");
                	}
                	else {
                		bottomAds.setUrlString(bottomAdsObject.getString("image"), bottomAdsObject.getString("url"));
                	}
                }
                
                //topAds.setUrlString("http://ngser.gnetix.com/files/_user/ads/Festival_des_grillades_2014_copie_m.jpg", "");
                //bottomAds.setUrlString("http://ngser.gnetix.com/files/_user/ads/Festival_des_grillades_2014_copie_m.jpg", "");
	        	
	        } catch (JSONException e) {
	          e.printStackTrace();
	        }
	        
	        if (isCancelled()) {
				return;
			}
	        
	        
	        EmptyMarkKioskArrayAdapter adapter = new EmptyMarkKioskArrayAdapter(getApplicationContext(), android.R.layout.simple_list_item_1, templist, true, false);
    	    
	        gridView.setAdapter(adapter);
        	
	        gridView.setOnItemClickListener(new OnItemClickListener() {

				@Override
				public void onItemClick(AdapterView<?> parent, View view, int position, long id) {
					// TODO Auto-generated method stub
					
					edition = (EditionModelClass) gridView.getItemAtPosition(position);
					
					setDataInView();
					mainScrollView.smoothScrollTo(0, 0);
					
				}
			});
        	
	        mainScrollView.smoothScrollTo(0, 0);
	        
	        mainProgressBar.setVisibility(View.GONE);
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
	
	public String getMonthString(String monthString) {
		String returnString = "";
		
		if (monthString.equals("01")) {
			returnString = "janvier";
		}
		else if (monthString.equals("02")) {
			returnString = "février";
		}
		else if (monthString.equals("03")) {
			returnString = "mars";
		}
		else if (monthString.equals("04")) {
			returnString = "avril";
		}
		else if (monthString.equals("05")) {
			returnString = "mai";
		}
		else if (monthString.equals("06")) {
			returnString = "juin";
		}
		else if (monthString.equals("07")) {
			returnString = "juillet";
		}
		else if (monthString.equals("08")) {
			returnString = "aoét";
		}
		else if (monthString.equals("09")) {
			returnString = "septembre";
		}
		else if (monthString.equals("10")) {
			returnString = "octobre";
		}
		else if (monthString.equals("11")) {
			returnString = "novembre";
		}
		else if (monthString.equals("12")) {
			returnString = "décembre";
		}
		
		
		
		return returnString;
	}
	
}
