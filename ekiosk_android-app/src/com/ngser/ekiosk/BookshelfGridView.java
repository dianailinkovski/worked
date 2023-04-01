package com.ngser.ekiosk;

import java.util.ArrayList;

import android.content.Context;
import android.content.Intent;
import android.content.SharedPreferences;
import android.content.res.Configuration;
import android.content.res.Resources;
import android.graphics.Bitmap;
import android.graphics.BitmapFactory;
import android.graphics.Canvas;
import android.os.Bundle;
import android.util.Log;
import android.util.TypedValue;
import android.view.View;
import android.widget.AdapterView;
import android.widget.GridView;

import com.ngser.ekiosk.Model.DatabaseHandler;
import com.ngser.ekiosk.Model.EditionModelClass;

import fi.harism.curl.CurlActivity;

public class BookshelfGridView extends GridView {
	
	private Bitmap mShelfBackground;
    private int mShelfWidth;
    private int mShelfHeight;
    
    final String PREFS_NAME = "eKioskPrefSetting";
    
    ArrayList<EditionModelClass> editions_data = new ArrayList<EditionModelClass>();
    public BibliothequeArrayAdapter cAdapter = null;
    DatabaseHandler dbHandler;
    
    Bitmap shelfBackground;
    
    public void onRemoveFromView() {
    	shelfBackground.recycle();
    }
    
	public BookshelfGridView(Context context, int position) {
        super(context);
        
        float px;
        
        Resources r = getResources();
        if ((getResources().getConfiguration().screenLayout & Configuration.SCREENLAYOUT_SIZE_MASK) == Configuration.SCREENLAYOUT_SIZE_XLARGE || 
        		(getResources().getConfiguration().screenLayout & Configuration.SCREENLAYOUT_SIZE_MASK) == Configuration.SCREENLAYOUT_SIZE_LARGE) {
        	px = TypedValue.applyDimension(TypedValue.COMPLEX_UNIT_DIP, 140, r.getDisplayMetrics());
        }
        else {
        	px = TypedValue.applyDimension(TypedValue.COMPLEX_UNIT_DIP, 105, r.getDisplayMetrics());
        }
                
        setColumnWidth((int) px);
        
        setNumColumns(GridView.AUTO_FIT);
        setVerticalSpacing(0);
        
        load();
        
        dbHandler = new DatabaseHandler(getContext().getApplicationContext());
    	ArrayList<EditionModelClass> editions_array_from_db;
    	
    	if (position == 0) {    		
    		SharedPreferences settings = context.getSharedPreferences(PREFS_NAME, 0);
    		int tousAfter = settings.getInt("tousAfter", 0);
    		if (tousAfter == 3) {
    			editions_array_from_db = dbHandler.Get_Editions();				
			}
    		else {
    			editions_array_from_db = dbHandler.Get_Editions_Recents((tousAfter+1)*7);
    		}
		}
    	else if (position == 2) {
    		editions_array_from_db = dbHandler.Get_Editions_Favoris();
		}
    	else if (position == 1) {
    		editions_array_from_db = dbHandler.Get_Editions();
		}
    	else {
    		editions_array_from_db = dbHandler.Get_Editions_Abonnement();
    	}
    	
    	
    	for (int i = 0; i < editions_array_from_db.size(); i++) {
    		
    		int temp_id = editions_array_from_db.get(i).getId();
    		String idJournal = editions_array_from_db.get(i).getId_journal(); 
    		String nom = editions_array_from_db.get(i).getNom();
    		String type = editions_array_from_db.get(i).getType();
    		String categorie = editions_array_from_db.get(i).getCategorie();
    		long datePublication = editions_array_from_db.get(i).getDatePublication();
    		String downloadPath = editions_array_from_db.get(i).getDownloadPath();
    		String coverPath = editions_array_from_db.get(i).getCoverPath();
    		String prix = editions_array_from_db.get(i).getPrix();
    		String bought = editions_array_from_db.get(i).getBought();
    		String localpath = editions_array_from_db.get(i).getLocalPath();
    		long downloadDate = editions_array_from_db.get(i).getDownloadDate();
    		long openDate = editions_array_from_db.get(i).getOpenDate();
    		String favoris = editions_array_from_db.get(i).getFavoris();    		
            
    		EditionModelClass tempEdition = new EditionModelClass();
    		tempEdition.setId(temp_id);
    		tempEdition.setId_journal(idJournal);
    		tempEdition.setNom(nom);
    		tempEdition.setType(type);
    		tempEdition.setCategorie(categorie);
    		tempEdition.setDatePublication(datePublication);
    		tempEdition.setDownloadPath(downloadPath);
    		tempEdition.setCoverPath(coverPath);
    		tempEdition.setPrix(prix);
    		tempEdition.setBought(bought);
    		tempEdition.setLocalPath(localpath);
    		tempEdition.setDownloadDate(downloadDate);
    		tempEdition.setOpenDate(openDate);
    		tempEdition.setFavoris(favoris);
    		
    	    editions_data.add(tempEdition);    	    
    	}
    	
    	dbHandler.close();
        
    	cAdapter = new BibliothequeArrayAdapter(getContext().getApplicationContext(), android.R.layout.simple_list_item_1, editions_data);
    	cAdapter.setTabPosition(position);
    	
    	setAdapter(cAdapter);
    	
    	setOnItemClickListener(new OnItemClickListener() {

			@Override
			public void onItemClick(AdapterView<?> parent, View view, int position, long id) {
				// TODO Auto-generated method stub
				
				Log.e("idtouched", String.valueOf(editions_data.get(position).getId()));
				
				if (editions_data.get(position).localpath == null) {
					return;
				}
				
				DatabaseHandler dbHandler = new DatabaseHandler(getContext().getApplicationContext());
	          	EditionModelClass edition = dbHandler.Get_Edition(editions_data.get(position).id);
	        	dbHandler.close();
	          	
	        	if (edition != null && edition.openDate == 0) {
	        		DatabaseHandler dbHandler2 = new DatabaseHandler(getContext().getApplicationContext());
	        		edition.setOpenDate(System.currentTimeMillis());
	        		int test = dbHandler2.Update_Edition(edition);
	        		dbHandler2.close();
				}
				
				Intent intent = new Intent(getContext().getApplicationContext(), CurlActivity.class).setFlags(Intent.FLAG_ACTIVITY_NEW_TASK);
				String path = editions_data.get(position).getLocalPath();
				Log.i("path", path);
			    intent.putExtra("path", path);
			    getContext().getApplicationContext().startActivity(intent);
			}
		});
    	
    	setOnItemLongClickListener(new OnItemLongClickListener() {
            @Override
            public boolean onItemLongClick(AdapterView<?> parent, View view, int position, long id) {
                // TODO Auto-generated method stub
            	
            	Intent intent = new Intent(getContext().getApplicationContext(), BibliothequeEditionDetail.class)
				.setFlags(Intent.FLAG_ACTIVITY_NEW_TASK);
            	Bundle b = new Bundle();
				b.putInt("id_edition", editions_data.get(position).getId());
				intent.putExtras(b); //Put your id to your next Intent
				getContext().getApplicationContext().startActivity(intent);
				
                return true;
            }
        });    	
    }
	
	private void load() {    
        if ((getResources().getConfiguration().screenLayout & Configuration.SCREENLAYOUT_SIZE_MASK) == Configuration.SCREENLAYOUT_SIZE_XLARGE || 
        		(getResources().getConfiguration().screenLayout & Configuration.SCREENLAYOUT_SIZE_MASK) == Configuration.SCREENLAYOUT_SIZE_LARGE) {
        	shelfBackground = BitmapFactory.decodeResource(getResources(), R.drawable.tablette_simple_2_2);
        }
        else {
        	shelfBackground = BitmapFactory.decodeResource(getResources(), R.drawable.tablette_simple_2);
        }
        
        if (shelfBackground != null) {
            mShelfWidth = shelfBackground.getWidth();
            mShelfHeight = shelfBackground.getHeight();
            mShelfBackground = shelfBackground;
        }        
    }
	
	@Override
    protected void dispatchDraw(Canvas canvas) {
        final int count = getChildCount();
        final int top = count > 0 ? getChildAt(0).getTop() : 0;
        final int shelfWidth = mShelfWidth;
        final int shelfHeight = mShelfHeight;
        final int width = getWidth();
        final int height = getHeight();
        final Bitmap background = mShelfBackground;

        for (int x = 0; x < width; x += shelfWidth) {
            for (int y = top; y < height; y += shelfHeight) {
                canvas.drawBitmap(background, x, y, null);
            }
        }

        if (count == 0) {
            
        }

        super.dispatchDraw(canvas);
    }
}
