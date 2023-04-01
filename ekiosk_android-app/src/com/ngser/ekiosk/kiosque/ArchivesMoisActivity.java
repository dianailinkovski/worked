package com.ngser.ekiosk.kiosque;

import java.text.ParseException;
import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Calendar;
import java.util.Date;

import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

import android.content.Context;
import android.content.Intent;
import android.content.SharedPreferences;
import android.graphics.Bitmap;
import android.graphics.BitmapFactory;
import android.graphics.Color;
import android.graphics.Point;
import android.graphics.drawable.BitmapDrawable;
import android.os.Bundle;
import android.support.v4.app.Fragment;
import android.support.v4.app.FragmentManager;
import android.support.v4.app.FragmentStatePagerAdapter;
import android.support.v4.app.NavUtils;
import android.support.v4.view.ViewPager;
import android.util.Log;
import android.util.TypedValue;
import android.view.Display;
import android.view.Gravity;
import android.view.View;
import android.view.WindowManager;
import android.widget.AdapterView;
import android.widget.AdapterView.OnItemClickListener;
import android.widget.LinearLayout;
import android.widget.LinearLayout.LayoutParams;
import android.widget.RelativeLayout;
import android.widget.TextView;

import com.actionbarsherlock.app.ActionBar;
import com.actionbarsherlock.app.SherlockFragmentActivity;
import com.actionbarsherlock.internal.widget.IcsLinearLayout;
import com.ngser.ekiosk.R;
import com.ngser.ekiosk.Model.EditionModelClass;

public class ArchivesMoisActivity extends SherlockFragmentActivity {
	
	EditionModelClass edition;
	static int id_journal;
	
	DemoCollectionPagerAdapter mDemoCollectionPagerAdapter;
    ViewPager mViewPager;
    
    Bitmap bitmapBackGround;
    
    final static String PREFS_NAME = "eKioskPrefSetting";

	@Override
	protected void onCreate(Bundle savedInstanceState) {
		// TODO Auto-generated method stub
		super.onCreate(savedInstanceState);
		
		Bundle b = this.getIntent().getExtras();
		id_journal = b.getInt("id_journal");
		setContentView(R.layout.archives_main_mois);		
		
		mDemoCollectionPagerAdapter = new DemoCollectionPagerAdapter(getSupportFragmentManager());
        mViewPager = (ViewPager) findViewById(R.id.pager);
        mViewPager.setAdapter(mDemoCollectionPagerAdapter);
		mViewPager.setOffscreenPageLimit(0);
        
        
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
    	LinearLayout ll = (LinearLayout) findViewById(R.id.linearLayout);
    	ll.setBackgroundColor(Color.TRANSPARENT);
    	rl.bringChildToFront(ll);
		
    	
    	TextView creditTV = new TextView(getApplicationContext());
        creditTV.setTextSize(TypedValue.COMPLEX_UNIT_DIP, 20);
        Bitmap bm1 = BitmapFactory.decodeResource(getApplicationContext().getResources(), R.drawable.big_ekcredit);

	    BitmapDrawable myIcon = new BitmapDrawable(getApplicationContext().getResources(), bm1);
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
        listNavLayout.setGravity(Gravity.RIGHT); // <-- align the spinner to the
        										 // right
        listNavLayout.setPadding(0, 0, 10, 0);
        
        ActionBar actionBar = getSupportActionBar();
        actionBar.setCustomView(listNavLayout, new ActionBar.LayoutParams(Gravity.RIGHT));
        actionBar.setDisplayShowCustomEnabled(true);        
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
        	
        	//NavUtils.navigateUpFromSameTask(this);
        	final Intent intent = NavUtils.getParentActivityIntent(this);
        	intent.addFlags(Intent.FLAG_ACTIVITY_NO_ANIMATION);
        	NavUtils.navigateUpTo(this, intent);
        	
            return true;
        }
        
        return super.onOptionsItemSelected(item);
    }
	
	@Override
	protected void onDestroy() {
		// TODO Auto-generated method stub
		super.onDestroy();
		if (bitmapBackGround != null) {
        	bitmapBackGround.recycle();
        	bitmapBackGround = null;
		}
	}
	
	public class DemoCollectionPagerAdapter extends FragmentStatePagerAdapter {
	    public DemoCollectionPagerAdapter(FragmentManager fm) {
	        super(fm);
	    }

	    @Override
	    public Fragment getItem(int i) {  
	        
	        SharedPreferences settings = getApplicationContext().getSharedPreferences(PREFS_NAME, 0);
            String username = settings.getString("username", "");
            String password = settings.getString("password", "");
	    	
	    	StringBuilder strBuilder = new StringBuilder("http://api.ngser.gnetix.com/v1.1/getPublicationsArchive.php?id=");
	        strBuilder.append(String.valueOf(id_journal));
			
	        strBuilder.append("&month=");
	        strBuilder.append(String.valueOf(i));
	        
	        strBuilder.append("&username=");
	        strBuilder.append(username);
	        
	        strBuilder.append("&password=");
	        strBuilder.append(password);
	        
	        String url = strBuilder.toString();
	    	
	        Log.e("url = ", url);
	        
	        ArchivesKioskGridView rootView = (ArchivesKioskGridView) ArchivesKioskGridView.newInstance(url);
	    	
	        return rootView;
	    }

	    @Override
	    public int getCount() {
	        return 12;
	    }

	    @Override
	    public CharSequence getPageTitle(int position) {
	    	
	    	Date dt = new Date();
	    	Calendar c = Calendar.getInstance(); 
	    	c.setTime(dt); 
	    	c.add(Calendar.MONTH, -position);
	    	dt = c.getTime();	    	
	    	
	    	StringBuilder dateString = new StringBuilder("");
	        SimpleDateFormat sdf;
	        
	        
	        sdf = new SimpleDateFormat("MM");
	        dateString.append(getMonthString(sdf.format(dt)));
	        sdf = new SimpleDateFormat("yyyy");
	        dateString.append(" "+sdf.format(dt));
	    	
	        return dateString.toString();
	    }
	}

	public static class ArchivesKioskGridView extends KioskGridView {
		
		public static ArchivesKioskGridView newInstance(String urlString) {
			ArchivesKioskGridView fragment = new ArchivesKioskGridView();
	    	Bundle b = new Bundle();
			b.putString(ARG_URLSTRING, urlString);
			fragment.setArguments(b);
	    	return fragment;
	    }
		
		public void dataReceived() {
	    	try {
	        	
	    		topAds.setVisibility(View.GONE);
	    		bottomAds.setVisibility(View.GONE);
	    		
	        	String resultat = json.getString("resultat");
	        	JSONArray data = json.getJSONArray("data");	        	

	        	templist = new ArrayList<EditionModelClass>();
	            for (int i = 0; i < data.length(); i++) {
	            	
	                JSONObject c = data.getJSONObject(i);
	                
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
	    				temp.datePublication = 0;
	    			}
	                
	                temp.downloadPath = c.getString("downloadPath");
	                temp.coverPath = c.getString("coverPath");
	                temp.prix = c.getString("prix");
	                temp.bought = "0";
	                temp.isSubscription = c.getInt("isSubscription");
	                
	                templist.add(temp);
	            }	        	
	        	
	        } catch (JSONException e) {
	          e.printStackTrace();
	        }
	        
	        KioskArrayAdapter adapter = new KioskArrayAdapter(gridView.getContext().getApplicationContext(), android.R.layout.simple_list_item_1, templist, true, true);
		    
	        gridView.setAdapter(adapter);
	    	
	        gridView.setOnItemClickListener(new OnItemClickListener() {

				@Override
				public void onItemClick(AdapterView<?> parent, View view, int position, long id) {
					
					Intent intent = new Intent(gridView.getContext().getApplicationContext(), KioskEditionDetailActivity.class)
					.setFlags(Intent.FLAG_ACTIVITY_NEW_TASK);
					
					EditionModelClass selectedEdition = templist.get(position);
					Bundle b = new Bundle();
					b.putInt("id_edition", selectedEdition.id);
					intent.putExtras(b); //Put your id to your next Intent
					gridView.getContext().getApplicationContext().startActivity (intent);					
				}
			});
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
			returnString = "août";
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
