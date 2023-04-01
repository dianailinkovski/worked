package com.ngser.ekiosk;

import java.io.File;
import java.util.ArrayList;

import net.hockeyapp.android.CrashManager;

import android.app.DownloadManager;
import android.app.DownloadManager.Query;
import android.app.DownloadManager.Request;
import android.content.BroadcastReceiver;
import android.content.Context;
import android.content.Intent;
import android.content.IntentFilter;
import android.content.SharedPreferences;
import android.content.res.Configuration;
import android.database.Cursor;
import android.net.Uri;
import android.os.AsyncTask;
import android.os.Bundle;
import android.os.Environment;
import android.support.v4.app.ActionBarDrawerToggle;
import android.support.v4.app.FragmentActivity;
import android.support.v4.view.GravityCompat;
import android.support.v4.view.PagerAdapter;
import android.support.v4.view.ViewPager;
import android.support.v4.widget.DrawerLayout;
import android.util.Log;
import android.view.Menu;
import android.view.MenuItem;
import android.view.View;
import android.view.ViewGroup;

import com.ngser.ekiosk.Model.DatabaseHandler;
import com.ngser.ekiosk.Model.EditionModelClass;
import com.ngser.ekiosk.googleslidingtabs.SlidingTabLayout;
import com.ngser.ekiosk.kiosque.KiosqueActivity;
import com.ngser.ekiosk.menu.MenuListener;

public class MainActivity extends FragmentActivity {
	
	ViewPager mViewPager;
    SlidingTabLayout mSlidingTabLayout;
    private long enqueue;
    private DownloadManager dm;
    BroadcastReceiver receiver;
    
    private DrawerLayout mDrawerLayout;
    private ActionBarDrawerToggle mDrawerToggle;
    MenuListener menuListener;
    final String PREFS_NAME = "eKioskPrefSetting";
    
    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        
        setContentView(R.layout.bibliotheque_main_pager);
        
        View tempViewMenu = findViewById(R.id.drawer_layout);
        
        menuListener = new MenuListener(tempViewMenu, this);
        
        mDrawerLayout = (DrawerLayout) tempViewMenu;
        mDrawerToggle = new ActionBarDrawerToggle(
                this,                  /* host Activity */
                mDrawerLayout,         /* DrawerLayout object */
                R.drawable.ic_drawer,  /* nav drawer icon to replace 'Up' caret */
                R.string.drawerTitle,  /* "open drawer" description */
                R.string.app_name      /* "close drawer" description */
                ) {

            /** Called when a drawer has settled in a completely closed state. */
            public void onDrawerClosed(View view) {
                super.onDrawerClosed(view);
            }

            /** Called when a drawer has settled in a completely open state. */
            public void onDrawerOpened(View drawerView) {
                super.onDrawerOpened(drawerView);
            }
        };
        
        // set a custom shadow that overlays the main content when the drawer opens
        mDrawerLayout.setDrawerShadow(R.drawable.drawer_shadow, GravityCompat.START);
        
        // Set the drawer toggle as the DrawerListener
        mDrawerLayout.setDrawerListener(mDrawerToggle);

        getActionBar().setDisplayHomeAsUpEnabled(true);
        getActionBar().setHomeButtonEnabled(true);
        
        
        final String PREFS_NAME = "eKioskPrefSetting";

        SharedPreferences settings = getSharedPreferences(PREFS_NAME, 0);
        if (settings.getBoolean("showNoIssue", true)) {
        	SharedPreferences.Editor editor = settings.edit();
        	
            //the app is being launched for first time, do something        
            Log.d("Comments", "First time");

            // first time task
            
            editor.putInt("nbMaximum", 0);
            editor.putInt("tousAfter", 0);
            editor.putInt("deleteAfter", 1);
            
            editor.putBoolean("excluFavoris", true);
            editor.putBoolean("showNoIssue", false);
            // TODO
            //editor.putBoolean("showNoIssue", true); // le bon par defaut
            editor.putBoolean("showTutoriel", true);
            
            editor.putInt("ekcredit", 0);
            
            editor.putString("username", "");
            editor.putString("password", "");
            
            editor.commit();            
        }
        
        dm = (DownloadManager) getSystemService(DOWNLOAD_SERVICE);
        receiver = new BroadcastReceiver() {
            @Override
            public void onReceive(Context context, Intent intent) {
                String action = intent.getAction();
                if (DownloadManager.ACTION_DOWNLOAD_COMPLETE.equals(action)) {
                   
                    Query query = new Query();
                    query.setFilterById(enqueue);
                    Cursor c = dm.query(query);
                    if (c.moveToFirst()) {
                        int columnIndex = c.getColumnIndex(DownloadManager.COLUMN_STATUS);
                        if (DownloadManager.STATUS_SUCCESSFUL == c.getInt(columnIndex)) {
                        	Log.e("download complete", "ca marcher");
                        	
                            String uriString = c.getString(c.getColumnIndex(DownloadManager.COLUMN_LOCAL_URI));
                            String descriptionString = c.getString(c.getColumnIndex(DownloadManager.COLUMN_DESCRIPTION));
                        	Log.e("path", uriString);
                        	
                        	UnzipDownloadASync unzipTash = new UnzipDownloadASync();
                        	unzipTash.idString = descriptionString;
                        	unzipTash.uriString = uriString;
                        	unzipTash.execute();
                        	
                  	    }
                    }
                }
            }
        };
        registerReceiver(receiver, new IntentFilter(DownloadManager.ACTION_DOWNLOAD_COMPLETE));        
    }
    
    @Override
    protected void onResume() {
      super.onResume();
      checkForCrashes();
    }

    private void checkForCrashes() {
      CrashManager.register(this, "2efb83ded7cb63b064527c40023f1d51");
    }
    
    @Override
    protected void onPostCreate(Bundle savedInstanceState) {
        super.onPostCreate(savedInstanceState);
        // Sync the toggle state after onRestoreInstanceState has occurred.
        mDrawerToggle.syncState();
    }

    @Override
    public void onConfigurationChanged(Configuration newConfig) {
        super.onConfigurationChanged(newConfig);
        mDrawerToggle.onConfigurationChanged(newConfig);
    }
    
    @Override
    public boolean onCreateOptionsMenu(Menu menu) {
        
        // Inflate the menu; this adds items to the action bar if it is present.
        getMenuInflater().inflate(R.menu.main, menu);
        return true;
    }

    @Override
    public boolean onOptionsItemSelected(MenuItem item) {
        // Handle action bar item clicks here. The action bar will
        // automatically handle clicks on the Home/Up button, so long
        // as you specify a parent activity in AndroidManifest.xml.
        int id = item.getItemId();
        if (id == R.id.action_kiosque) {
        	
        	Intent intent = new Intent(getApplicationContext(), KiosqueActivity.class);
        	//Intent intent = new Intent(getApplicationContext(), MainActivity.class)
        	//.addFlags(Intent.FLAG_ACTIVITY_CLEAR_TOP | Intent.FLAG_ACTIVITY_NEW_TASK);
			startActivity(intent);
			
            return true;
        }
        else if (mDrawerToggle.onOptionsItemSelected(item)) {
        	return true;
        }
        
        return super.onOptionsItemSelected(item);
    }
    
    @Override
    protected void onStart() {
    	// TODO Auto-generated method stub
    	super.onStart();
    	
        menuListener.refresh();
        
        new supprimerApresASync().execute();
        
    }
    
    @Override
    protected void onDestroy() {
    	super.onDestroy();
    	
    	unregisterReceiver(receiver);
    	menuListener.onDestroy();    	
    }
    
    public void completedSupprimerApresASync() {
    	new supprimerMaximumASync().execute();
    }
    
    public void completedSupprimerMaximumASync() {
    	
    	mViewPager = (ViewPager) findViewById(R.id.viewpager);
        mViewPager.setAdapter(new BibliothequePagerAdapter());
        
        mSlidingTabLayout = (SlidingTabLayout) findViewById(R.id.sliding_tabs);
        mSlidingTabLayout.setViewPager(mViewPager);
        
        new StartDownloadASync().execute();        
    }
        
    class BibliothequePagerAdapter extends PagerAdapter {
    	
    	public int getItemPosition(Object object) {
    	    return POSITION_NONE;
    	}
    	
        @Override
        public int getCount() {
            return 4;
        }

        /**
         * @return true if the value returned from {@link #instantiateItem(ViewGroup, int)} is the
         * same object as the {@link View} added to the {@link ViewPager}.
         */
        @Override
        public boolean isViewFromObject(View view, Object o) {
        	return o == view;            
        }

        // BEGIN_INCLUDE (pageradapter_getpagetitle)
        /**
         * Return the title of the item at {@code position}. This is important as what this method
         * returns is what is displayed in the {@link SlidingTabLayout}.
         * <p>
         * Here we construct one using the position value, but for real application the title should
         * refer to the item's contents.
         */
        @Override
        public CharSequence getPageTitle(int position) {
        	String title = "";
        	switch (position) {
			case 0:
				title = "Récents";
				break;
			case 1:
				title = "Tous";
				break;
			case 2:
				title = "Favoris";
				break;
			case 3:
				title = "Abonnement";
				break;
			default:
				break;
			}
            return title;
        }
        // END_INCLUDE (pageradapter_getpagetitle)

        /**
         * Instantiate the {@link View} which should be displayed at {@code position}. Here we
         * inflate a layout from the apps resources and then change the text view to signify the position.
         */
        @Override
        public Object instantiateItem(ViewGroup container, int position) {
            // Inflate a new layout from our resources
        	
        	BookshelfGridView view = new BookshelfGridView(getBaseContext(), position);
    		
    		container.addView(view);
            return view;
        }

        /**
         * Destroy the item from the {@link ViewPager}. In our case this is simply removing the
         * {@link View}.
         */
        @Override
        public void destroyItem(ViewGroup container, int position, Object object) {
        	BookshelfGridView test = (BookshelfGridView) object;
        	test.onRemoveFromView();
            container.removeView((View) object);            
        }
    }
    
    private class StartDownloadASync extends AsyncTask<String, Void, String> {

        @Override
        protected String doInBackground(String... params) {
            
        	DatabaseHandler dbHandler = new DatabaseHandler(getApplicationContext());
        	ArrayList<EditionModelClass> editions_array_from_db = dbHandler.Get_Editions();
        	dbHandler.close();
        	dbHandler = null;
        	
        	for (int i = 0; i < editions_array_from_db.size(); i++) {
        		EditionModelClass tempEdition = editions_array_from_db.get(i); 
        		
            	if (tempEdition.localpath == null) {
            		boolean downloading = false;
            		Cursor c = dm.query( new DownloadManager.Query() );
            		while(c.moveToNext()) {
            		    if (c.getString(c.getColumnIndex(DownloadManager.COLUMN_DESCRIPTION)) == String.valueOf(tempEdition.id)) {
							downloading = true;
						}
            		}
            		if (!downloading) {
						
            			SharedPreferences settings = getSharedPreferences(PREFS_NAME, 0);
                        String username = settings.getString("username", "");
                        String password = settings.getString("password", "");
            			
						StringBuilder strBuilder = new StringBuilder("http://api.ngser.gnetix.com/v1.1/getEditionDownload.php?editionid=");
						  
						strBuilder.append(tempEdition.id);
						strBuilder.append("&username=");
						strBuilder.append(username);
						strBuilder.append("&password=");
						strBuilder.append(password);
						
						if (tempEdition.isSubscription == 1) {
							strBuilder.append("&subscription=1");
						}
						
						String url = strBuilder.toString();
						
						Request request = new Request(Uri.parse(url));
						File extStore = Environment.getExternalStorageDirectory();
						StringBuilder strBuilderFileName = new StringBuilder();
						strBuilderFileName.append(String.valueOf(tempEdition.id));
						strBuilderFileName.append(".zip");
						String fileString = strBuilderFileName.toString();
						request.setDestinationInExternalFilesDir(getApplicationContext(), extStore.getAbsolutePath(), fileString);
						
						request.setDescription(String.valueOf(tempEdition.id));
						enqueue = dm.enqueue(request);						
            		}
        		}
            }
        	return "";
        }
    }
    
    private class UnzipDownloadASync extends AsyncTask<String, Void, String> {
    	
    	public String uriString;
    	public String idString;
    	
        @Override
        protected String doInBackground(String... params) {
            
        	File mFile = new File(Uri.parse(uriString).getPath());
        	Log.e("test", mFile.getPath().toString());
        	
        		
			String[] array = mFile.getPath().toString().split("/");
			
			StringBuilder strBuilder = new StringBuilder();
			
			for (int i = 0; i < array.length-1; i++) {
				strBuilder.append(array[i]);
				strBuilder.append("/");
			}
			strBuilder.append(String.valueOf(idString));
      	    strBuilder.append("/");
      	    String path = strBuilder.toString();
      	    
          	Log.e("path", path);
            Decompress d = new Decompress(mFile.getPath().toString(), path); 
          	d.unzip(); 
          	Log.i("unzip", "---- done ----");
          	mFile.delete();
          	
          	DatabaseHandler dbHandler = new DatabaseHandler(getApplicationContext());
          	EditionModelClass edition = dbHandler.Get_Edition(Integer.parseInt(idString));
        	dbHandler.close();
          	
        	if (edition != null) {
        		Log.e("path", path);
        		DatabaseHandler dbHandler2 = new DatabaseHandler(getApplicationContext());
        		edition.setLocalPath(path);
        		edition.setDownloadDate(System.currentTimeMillis());
        		int test = dbHandler2.Update_Edition(edition);
        		dbHandler2.close();
        		Log.e("nb row modify", String.valueOf(test));
			}
            
        	return "";
        }
        
        @Override
        protected void onPostExecute(String result) {
        	mViewPager = (ViewPager) findViewById(R.id.viewpager);
            mViewPager.setAdapter(new BibliothequePagerAdapter());
            
            mSlidingTabLayout = (SlidingTabLayout) findViewById(R.id.sliding_tabs);
            mSlidingTabLayout.setViewPager(mViewPager);
        }
        
    }
    
    private class supprimerApresASync extends AsyncTask<String, Void, String> {

        @Override
        protected String doInBackground(String... params) {
        	SharedPreferences settings = getBaseContext().getApplicationContext().getSharedPreferences(PREFS_NAME, 0);
    		long tousAfter = 0;
    		
    		switch (settings.getInt("deleteAfter", 0)) {
			case 0:
				tousAfter = 15;
				break;
			case 1:
				tousAfter = 30;
				break;
			case 2:
				tousAfter = 60;
				break;
			case 3:
				tousAfter = 90;
				break;

			default:
				break;
			}
    		Boolean excludeFavoris = settings.getBoolean("excluFavoris", true);
    		if (tousAfter == 0) {
    			return "";
    		}
    		else {
    			Log.e("test", "test");
    			DatabaseHandler dbHandler = new DatabaseHandler(getBaseContext().getApplicationContext());
        		ArrayList<EditionModelClass> test = dbHandler.Get_Editions_Supprimer_Apres(tousAfter, excludeFavoris);
        		dbHandler.close();
        		
        		for (int i = 0; i < test.size(); i++) {
					EditionModelClass temp = test.get(i);
					Log.v("downloadDate = ", String.valueOf(temp.downloadDate));
					Log.v("localpath = ", String.valueOf(temp.localpath));
					
					
					if(temp.localpath != null) {
                		File pathToDelete = new File(temp.localpath);
                    	DeleteRecursive(pathToDelete);
                	}
                	
                	DatabaseHandler dbHandlerDelete = new DatabaseHandler(getApplicationContext());
                	dbHandlerDelete.Delete_Edition(temp.id);
                	dbHandlerDelete.close();					
				}
        		
    		}
        	return "";
        }
        
        @Override
        protected void onPostExecute(String result) {
        	completedSupprimerApresASync();
        }
    }
    
    private class supprimerMaximumASync extends AsyncTask<String, Void, String> {

        @Override
        protected String doInBackground(String... params) {
        	
        	SharedPreferences settings = getBaseContext().getApplicationContext().getSharedPreferences(PREFS_NAME, 0);
    		int nbMaximum = 0;
    		switch (settings.getInt("nbMaximum", 0)) {
			case 0:
				//nbMaximum = 1;
				nbMaximum = 30;
				break;
			case 1:
				//nbMaximum = 2;
				nbMaximum = 60;
				break;
			case 2:
				//nbMaximum = 3;
				nbMaximum = 90;
				break;
			case 3:
				//nbMaximum = 4;
				nbMaximum = 120;
				break;

			default:
				break;
			}
    		
    		Boolean excludeFavoris = settings.getBoolean("excluFavoris", true);
    		if (nbMaximum == 0) {
    			return "";
    		}
    		else {
    			
    			DatabaseHandler dbHandlerCountVerif = new DatabaseHandler(getBaseContext().getApplicationContext());
            	int currentCount = dbHandlerCountVerif.Get_Total_Contacts();
            	
            	Log.v("currentCount = ", String.valueOf(currentCount));
            	
            	if (currentCount > nbMaximum) {
					
            		DatabaseHandler dbHandler = new DatabaseHandler(getBaseContext().getApplicationContext());
            		ArrayList<EditionModelClass> test = dbHandler.Get_Editions_Last_X(currentCount - nbMaximum, excludeFavoris);
            		dbHandler.close();
            		
            		for (int i = 0; i < test.size(); i++) {
    					EditionModelClass temp = test.get(i);
    					Log.v("downloadDate = ", String.valueOf(temp.downloadDate));
    					Log.v("localpath = ", String.valueOf(temp.localpath));
    					
    					
    					if(temp.localpath != null) {
                    		File pathToDelete = new File(temp.localpath);
                        	DeleteRecursive(pathToDelete);
                    	}
                    	
                    	DatabaseHandler dbHandlerDelete = new DatabaseHandler(getApplicationContext());
                    	dbHandlerDelete.Delete_Edition(temp.id);
                    	dbHandlerDelete.close();    					
    				}            		
				}    			
    		}
        	
        	return "";
        }
        
        @Override
        protected void onPostExecute(String result) {
        	completedSupprimerMaximumASync();
        }
    }
    
    public void DeleteRecursive(File fileOrDirectory) {
	    if (fileOrDirectory.isDirectory())
	        for (File child : fileOrDirectory.listFiles())
	            DeleteRecursive(child);

	    fileOrDirectory.delete();
	}
}