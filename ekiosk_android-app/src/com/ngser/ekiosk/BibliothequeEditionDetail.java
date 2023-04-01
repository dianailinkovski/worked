package com.ngser.ekiosk;

import java.io.File;
import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Date;

import android.app.Activity;
import android.app.AlertDialog;
import android.content.Context;
import android.content.DialogInterface;
import android.content.Intent;
import android.content.res.Configuration;
import android.content.res.Resources;
import android.graphics.Bitmap;
import android.graphics.BitmapFactory;
import android.graphics.Color;
import android.os.Bundle;
import android.support.v4.app.NavUtils;
import android.util.AttributeSet;
import android.util.Log;
import android.util.TypedValue;
import android.view.Gravity;
import android.view.Menu;
import android.view.MenuItem;
import android.view.View;
import android.view.View.OnClickListener;
import android.view.ViewGroup;
import android.view.animation.AnimationUtils;
import android.widget.AdapterView;
import android.widget.AdapterView.OnItemClickListener;
import android.widget.Button;
import android.widget.GridView;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.ProgressBar;
import android.widget.RelativeLayout;
import android.widget.ScrollView;
import android.widget.TextView;
import android.widget.Toast;

import com.ngser.ekiosk.BibliothequeArrayAdapter.ImageLoadedListener;
import com.ngser.ekiosk.Model.DatabaseHandler;
import com.ngser.ekiosk.Model.EditionModelClass;
import com.ngser.ekiosk.kiosque.BitMapBGView;
import com.ngser.ekiosk.kiosque.KioskArrayAdapter;
import com.ngser.ekiosk.kiosque.NetworkedCacheableImageView;

public class BibliothequeEditionDetail extends Activity {
	
	ArrayList<EditionModelClass> templist;
    
	EditionModelClass edition;
	int id_edition;
	ExpandableHeightGridView gridView;
	
	ScrollView mainScrollView;
	ProgressBar mainProgressBar;
	
	DatabaseHandler dbHandler = new DatabaseHandler(this);
	@Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        
        Bundle b = this.getIntent().getExtras();
		id_edition = b.getInt("id_edition");
        
		
        setContentView(R.layout.bibliotheque_edition_detail);
        
        LinearLayout linearLayout = (LinearLayout) findViewById(R.id.mainLinearLayout);
		mainScrollView = (ScrollView) findViewById(R.id.scrollView);
		mainProgressBar = (ProgressBar) findViewById(R.id.mainProgressBar);
		
		
		BitMapBGView bgImageView = new BitMapBGView(getBaseContext());
    	Bitmap bm = BitmapFactory.decodeResource(getResources(), R.drawable.fond_fingerprint);
    	
    	bgImageView.imageBitmap = bm;
    	bgImageView.setAlpha((float) 0.1);
    	
    	RelativeLayout rl = (RelativeLayout)findViewById(R.id.relativeLayout);
    	RelativeLayout.LayoutParams params = new RelativeLayout.LayoutParams(bm.getWidth(), bm.getHeight());
    	params.addRule(RelativeLayout.ALIGN_PARENT_RIGHT, RelativeLayout.TRUE);
    	params.addRule(RelativeLayout.ALIGN_PARENT_BOTTOM, RelativeLayout.TRUE);
    	
    	rl.addView(bgImageView, params);
    	mainScrollView.setBackgroundColor(Color.TRANSPARENT);
    	rl.bringChildToFront(mainScrollView);
    	
    	
		
		Button buyButton = (Button) findViewById(R.id.button1);
		
		buyButton.setOnClickListener(new OnClickListener() {
			
			@Override
			public void onClick(View v) {
                	
                	
                	if (edition != null) {
                		Log.e("tet2222", edition.favoris);
                		if (edition.favoris.equals("1")) {
                			
                			
                			
                			DatabaseHandler dbHandler2 = new DatabaseHandler(getApplicationContext());
		                  	EditionModelClass edition = dbHandler2.Get_Edition(id_edition);
		                  	dbHandler2.close();
		                  	
		                	if (edition != null) {
		                		DatabaseHandler dbHandler3 = new DatabaseHandler(getApplicationContext());
		                		edition.setFavoris("0");
		                		int test = dbHandler3.Update_Edition(edition);
		                		dbHandler3.close();
		                		Log.e("nb row modify", String.valueOf(test));
							}
		                	
		                	
		                	
		                	
                			/*
                			DatabaseHandler dbHandler2 = new DatabaseHandler(getApplicationContext());
                    		edition.setFavoris("0");
                    		int test = dbHandler2.Update_Edition(edition);
                    		dbHandler2.close();
                    		*/
                    		//Log.e("nb row modify", String.valueOf(test));	
                		}
                		else {
                			DatabaseHandler dbHandler2 = new DatabaseHandler(getApplicationContext());
		                  	EditionModelClass edition = dbHandler2.Get_Edition(id_edition);
		                  	dbHandler2.close();
		                  	
		                	if (edition != null) {
		                		DatabaseHandler dbHandler3 = new DatabaseHandler(getApplicationContext());
		                		edition.setFavoris("1");
		                		int test = dbHandler3.Update_Edition(edition);
		                		dbHandler3.close();
		                		Log.e("nb row modify", String.valueOf(test));
							}
                			/*
                			DatabaseHandler dbHandler2 = new DatabaseHandler(getApplicationContext());
                    		edition.setFavoris("1");
                    		int test = dbHandler2.Update_Edition(edition);
                    		dbHandler2.close();
                    		*/
                    		//Log.e("nb row modify", String.valueOf(test));							
						}
                		
                		setup();
            		}
                	
                
            }
        });
		
		Button deleteButton = (Button) findViewById(R.id.button2);
		deleteButton.setOnClickListener(new OnClickListener() {
			
			
			
			void DeleteRecursive(File fileOrDirectory) {
			    if (fileOrDirectory.isDirectory())
			        for (File child : fileOrDirectory.listFiles())
			            DeleteRecursive(child);

			    fileOrDirectory.delete();
			}
			
			@Override
			public void onClick(View v) {
                
                	
                	
                	
                	
                	AlertDialog.Builder builder = new AlertDialog.Builder(BibliothequeEditionDetail.this);
                	builder.setTitle("Avertissement");
                	builder.setMessage("Voulez-vous vraiment supprimer cette publication de votre appareil ?\n\nVous pourrez la télécharger é nouveau dans le Kiosque.");
                	builder.setPositiveButton(android.R.string.yes, new DialogInterface.OnClickListener() {
                        public void onClick(DialogInterface dialog, int which) { 
                        	if(edition.localpath != null) {
                        		File pathToDelete = new File(edition.localpath);
                            	DeleteRecursive(pathToDelete);                        		
                        	}
                        	
                        	DatabaseHandler dbHandler2 = new DatabaseHandler(getApplicationContext());
                    		dbHandler2.Delete_Edition(edition.id);
                    		dbHandler2.close();
                        	
            				String Toast_msg = "édition supprimée";
        				    Show_Toast(Toast_msg);
        				    finish();
                        }
                     });
                	builder.setNegativeButton(android.R.string.no, new DialogInterface.OnClickListener() {
                        public void onClick(DialogInterface dialog, int which) { 
                            
                        }
                     });
                	AlertDialog dialog = builder.show();
                	TextView messageText = (TextView)dialog.findViewById(android.R.id.message);
                	messageText.setGravity(Gravity.CENTER);
                	dialog.show();
                	
				    
                
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
        
        linearLayout.addView(gridView);
        
        gridView.setOnItemClickListener(new OnItemClickListener() {

			@Override
			public void onItemClick(AdapterView<?> parent, View view, int position, long id) {
				// TODO Auto-generated method stub
				
				edition = (EditionModelClass) gridView.getItemAtPosition(position);
				id_edition = edition.id;
				
				setup();
				//setDataInView();
				mainScrollView.smoothScrollTo(0, 0);
				
			}
		});
        
        setup();
        
	}
	
	public void setup() {
		
		DatabaseHandler dbHandler = new DatabaseHandler(getApplicationContext());
      	edition = dbHandler.Get_Edition(id_edition);
    	dbHandler.close();
		
    	Log.e("setup", edition.favoris);
    	
		setDataInView();
		
		mainScrollView.setVisibility(View.GONE);
		
		dbHandler = new DatabaseHandler(getApplicationContext());
    	ArrayList<EditionModelClass> editions_array_from_db = dbHandler.Get_EditionsWithJournal(edition.id_journal);
    	
    	templist = new ArrayList<EditionModelClass>();
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
    		
    		templist.add(tempEdition);
    	}
    	dbHandler.close();
    	
        KioskArrayAdapter adapter = new KioskArrayAdapter(getApplicationContext(), android.R.layout.simple_list_item_1, templist, true, false);
	    
        gridView.setAdapter(adapter);
        
        show();
	}
	
	@Override
    public boolean onCreateOptionsMenu(Menu menu) {
    	getActionBar().setDisplayHomeAsUpEnabled(true);

        // Inflate the menu; this adds items to the action bar if it is present.
        //getMenuInflater().inflate(R.menu.main, menu);
        return true;
    }

    @Override
    public boolean onOptionsItemSelected(MenuItem item) {
        // Handle action bar item clicks here. The action bar will
        // automatically handle clicks on the Home/Up button, so long
        // as you specify a parent activity in AndroidManifest.xml.
        
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
		
	}
	
	
	public void Show_Toast(String msg) {
		Toast.makeText(getApplicationContext(), msg, Toast.LENGTH_LONG).show();
    }
	
	private void show() {
		
		mainProgressBar.startAnimation(AnimationUtils.loadAnimation(getApplicationContext(), android.R.anim.fade_out));
		mainScrollView.startAnimation(AnimationUtils.loadAnimation(getApplicationContext(), android.R.anim.fade_in));
		mainProgressBar.setVisibility(View.GONE);
		mainScrollView.setVisibility(View.VISIBLE);
        
	}
	
	private void setDataInView() {
		TextView titleTextView = (TextView) findViewById(R.id.titleTextView);
        TextView categorieTextView = (TextView) findViewById(R.id.categorieTextView);
        TextView dateTextView = (TextView) findViewById(R.id.dateTextView);
        
        NetworkedCacheableImageView imageView = (NetworkedCacheableImageView) findViewById(R.id.imageView1);
        ProgressBar progressBar = (ProgressBar) findViewById(R.id.progressBar1);
        Button button = (Button) findViewById(R.id.button1);
        Button button2 = (Button) findViewById(R.id.button2);
        
        ImageView imageView2 = (ImageView) findViewById(R.id.imageView2);
        
        final boolean fromCache = imageView.loadImage(edition.coverPath, false, new ImageLoadedListener(progressBar));
        
        if (fromCache) {
        	progressBar.setVisibility(View.GONE);
        } else {
        	progressBar.setVisibility(View.VISIBLE);
        }
        
        StringBuilder dateString = new StringBuilder("édition du\n");
        //dateString.append(edition.datePublication);
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
        
        
        button.setText("Favoris");
        button2.setText("Supprimer");
        
        Log.e("setDataInView", edition.favoris);
        if (edition.favoris.equals("1")) {
        	imageView2.setVisibility(View.VISIBLE);
		}
        else {
        	imageView2.setVisibility(View.GONE);
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
