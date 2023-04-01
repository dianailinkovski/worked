package com.ngser.ekiosk.menu;

import android.app.Activity;
import android.content.SharedPreferences;
import android.content.res.Resources;
import android.graphics.Color;
import android.os.Bundle;
import android.util.TypedValue;
import android.view.MenuItem;
import android.view.View;
import android.view.View.OnClickListener;
import android.widget.Button;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.LinearLayout.LayoutParams;
import android.widget.RelativeLayout;
import android.widget.TextView;

import com.ngser.ekiosk.R;

public class SelectOptionActivity extends Activity {
	
	LinearLayout mLinearLayout;
	TextView mTextView;
	
	int currentSection;
	int selected;
	
	final String PREFS_NAME = "eKioskPrefSetting";
	
	
	String[] recentPendantString = new String[] {
			"Aprés ce delai, les éditions ne s'afficheront plus dans la section récents mais seront disponible dans la section tous de votre bibliothéque.", 
			"7 jours", 
			"15 jours", 
			"30 jours", 
			"Toujours" 
			};
	String[] supprimerApresString = new String[] { 
			"Aprés ce délai, les éditions seront supprimées automatiquement de votre appareil.\n\nVous pourrez toujours les télécharger é nouveaux via le Kiosque.", 
			"15 jours", 
			"30 jours", 
			"60 jours", 
			"90 jours", 
			"illimités" 
			};
	String[] maximumString = new String[] { 
			"Une fois ce nombre de publication atteint, les anciennes publication seront supprimées de votre appareil.\n\nnVous pourrez toujours les télécharger é nouveaux via le Kiosque.", 
			"30 publications", 
			"60 publications", 
			"90 publications", 
			"120 publications", 
			"illimitées" 
			};
	
	String[] currentStringArray;
	
	@Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.select_option_main);
        
        getActionBar().setDisplayHomeAsUpEnabled(true);
        getActionBar().setHomeButtonEnabled(true);
        
        mLinearLayout = (LinearLayout) findViewById(R.id.ll);
        mTextView = (TextView) findViewById(R.id.bottomTextView);
        
        Bundle b = this.getIntent().getExtras();
		int tempsection = b.getInt("section");
		setupForSection(tempsection);
		
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
	
	public void setupForSection(int section) {
		currentSection = section;
		SharedPreferences settings = getSharedPreferences(PREFS_NAME, 0);
		
		switch (currentSection) {
			case 0:
				getActionBar().setTitle("Récents pendant");
				currentStringArray = recentPendantString;
				selected = settings.getInt("tousAfter", 0)+1;
				break;
			case 1:
				getActionBar().setTitle("Supprimer aprés");
				currentStringArray = supprimerApresString;
				selected = settings.getInt("deleteAfter", 0)+1;
				break;
			case 2:
				getActionBar().setTitle("Maximum");
				currentStringArray = maximumString;
				selected = settings.getInt("nbMaximum", 0)+1;
				break;
	
			default:
				break;
		}
		
		View mView = new View(getApplicationContext());
		mView.setBackgroundColor(Color.GRAY);
		mLinearLayout.addView(mView, new LayoutParams(LayoutParams.MATCH_PARENT, 1));
        
		/***************************************/
        setupView();
        /***************************************/
        
        mView = new View(getApplicationContext());
		mView.setBackgroundColor(Color.GRAY);
		mLinearLayout.addView(mView, new LayoutParams(LayoutParams.MATCH_PARENT, 1));
	}
	
	public void setupView() {
		
		
		
		mTextView.setText(currentStringArray[0]);
		
		for (int i = 1; i < currentStringArray.length; i++) {
			
			RelativeLayout child = (RelativeLayout) getLayoutInflater().inflate(R.layout.custom_checkmark_row, null);
			
			Resources r = getResources();
			int right = (int) TypedValue.applyDimension(TypedValue.COMPLEX_UNIT_DIP, 20, r.getDisplayMetrics());
			int left = (int) TypedValue.applyDimension(TypedValue.COMPLEX_UNIT_DIP, 20, r.getDisplayMetrics());
			int top = (int) TypedValue.applyDimension(TypedValue.COMPLEX_UNIT_DIP, 3, r.getDisplayMetrics());
			//int bottom = (int) TypedValue.applyDimension(TypedValue.COMPLEX_UNIT_DIP, 5, r.getDisplayMetrics());
			
			LinearLayout.LayoutParams lllp = new LayoutParams(LayoutParams.MATCH_PARENT, LayoutParams.WRAP_CONTENT);
			lllp.setMargins(left, top, right, top);
			
			Button mButton = (Button) child.findViewById(R.id.button1);
			mButton.setOnClickListener(new OnClickListener() {
				
				@Override
				public void onClick(View v) {
					v.setBackgroundColor(getResources().getColor(R.color.blueLinktransparent));
					onTouchedButton(v);
	            }
				
			});
			mButton.setTag(String.valueOf(i));
			
			ImageView mImageView = (ImageView) child.findViewById(R.id.imageView1);
			mImageView.setVisibility(View.GONE);
			
			if (selected == i) {
				mImageView.setVisibility(View.VISIBLE);
			}
			
			mButton.setText(currentStringArray[i]);
			
			mLinearLayout.addView(child, lllp);
			
			if (i<currentStringArray.length-1) {
				View mView = new View(getApplicationContext());
				mView.setBackgroundColor(Color.GRAY);
				
				LinearLayout.LayoutParams lllp2 = new LayoutParams(LayoutParams.MATCH_PARENT, 1);
				
				lllp2.setMargins(left, 0, 0, 0);
				mLinearLayout.addView(mView, lllp2);
			}
			
		}
		
	}
	
	public void onTouchedButton(View v) {

		
		//StringBuilder s = new StringBuilder();
		//s.append(getActionBar().getTitle());
		//s.append(" : ");
		//s.append(currentStringArray[Integer.valueOf(String.valueOf(v.getTag()))]);
		
		SharedPreferences settings = getSharedPreferences(PREFS_NAME, 0);
		SharedPreferences.Editor editor = settings.edit();
		
		if (currentSection == 0) {
			editor.putInt("tousAfter", Integer.valueOf(String.valueOf(v.getTag()))-1);
		}
		else if (currentSection == 1) {
			editor.putInt("deleteAfter", Integer.valueOf(String.valueOf(v.getTag()))-1);
		}
		else if (currentSection == 2) {
			editor.putInt("nbMaximum", Integer.valueOf(String.valueOf(v.getTag()))-1);
		}
		
		editor.commit();
		
		//Toast.makeText(getApplicationContext(), s.toString(), Toast.LENGTH_LONG).show();
		finish();
		
	}
	
}
