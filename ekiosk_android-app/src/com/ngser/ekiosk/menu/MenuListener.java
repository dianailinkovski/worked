package com.ngser.ekiosk.menu;

import android.app.AlertDialog;
import android.content.BroadcastReceiver;
import android.content.Context;
import android.content.DialogInterface;
import android.content.Intent;
import android.content.IntentFilter;
import android.content.SharedPreferences;
import android.support.v4.content.LocalBroadcastManager;
import android.util.Log;
import android.view.View;
import android.view.View.OnClickListener;
import android.widget.Button;
import android.widget.CompoundButton;
import android.widget.Switch;
import android.widget.TextView;

import com.ngser.ekiosk.R;

public class MenuListener {
	public Context mContext;
	
	final String PREFS_NAME = "eKioskPrefSetting";
	
	Button connexionButton;
	Button creerButton;
	
	Button recentsButton;
	Button supprimerButton;
	Button maximumButton;
	Switch exclureSwitch;
	
	TextView favorisTV;
	
	SharedPreferences settings;
	
	public void onDestroy() {
		LocalBroadcastManager.getInstance(mContext).unregisterReceiver(mSharedPreferencesReceiver);
		settings = null;
		mContext = null;
	}
	
	public MenuListener(View menuView, Context tempcontext) {
		
		mContext = tempcontext;
		
		LocalBroadcastManager.getInstance(mContext).registerReceiver(mSharedPreferencesReceiver, new IntentFilter("SharedPreferencesReceiver"));
		
		connexionButton = (Button) menuView.findViewById(R.id.connecterButton);
		creerButton = (Button) menuView.findViewById(R.id.creerButton);
		
		recentsButton = (Button) menuView.findViewById(R.id.recentsButton);
		supprimerButton = (Button) menuView.findViewById(R.id.supprimerButton);
		maximumButton = (Button) menuView.findViewById(R.id.maximumButton);
		exclureSwitch = (Switch) menuView.findViewById(R.id.favorisSwitch);
		
		favorisTV = (TextView) menuView.findViewById(R.id.favorisTV);
		
		settings = mContext.getSharedPreferences(PREFS_NAME, 0);
		
		SharedPreferences.OnSharedPreferenceChangeListener prefListener = new SharedPreferences.OnSharedPreferenceChangeListener()
		{
		  @Override
		  public void onSharedPreferenceChanged( SharedPreferences sharedPreferences, String key )
		  {
			  setupButton();
		     Log.w( "settings", "Pref key: " + key );
		  }
		};
		settings.registerOnSharedPreferenceChangeListener( prefListener );
		
		setupButton();
        //connexionButton.setText(username);
        //creerButton.setText(password);
		
		
		recentsButton.setOnClickListener(new OnClickListener() {
			@Override
			public void onClick(View v) {
                	Intent intent = new Intent(mContext, SelectOptionActivity.class).addFlags(Intent.FLAG_ACTIVITY_NEW_TASK);
                	intent.putExtra("section", 0);
        			mContext.startActivity(intent);
            }
		});
		
		supprimerButton.setOnClickListener(new OnClickListener() {
			@Override
			public void onClick(View v) {
                	Intent intent = new Intent(mContext, SelectOptionActivity.class).addFlags(Intent.FLAG_ACTIVITY_NEW_TASK);
                	intent.putExtra("section", 1);
        			mContext.startActivity(intent);
            }
		});
		
		maximumButton.setOnClickListener(new OnClickListener() {
			@Override
			public void onClick(View v) {
                	Intent intent = new Intent(mContext, SelectOptionActivity.class).addFlags(Intent.FLAG_ACTIVITY_NEW_TASK);
                	intent.putExtra("section", 2);
        			mContext.startActivity(intent);
            }
		});
		
		exclureSwitch.setOnCheckedChangeListener(new CompoundButton.OnCheckedChangeListener() {
		    public void onCheckedChanged(CompoundButton buttonView, boolean isChecked) {
		    	SharedPreferences settings = mContext.getSharedPreferences(PREFS_NAME, 0);
				SharedPreferences.Editor editor = settings.edit();
				editor.putBoolean("excluFavoris", isChecked);
				editor.commit();
				
				refresh();
		    }
		});
		
	}
	
	public void refresh() {
		
		recentsButton.setText(getSelected(0, Integer.valueOf(settings.getInt("tousAfter", 0))));
		supprimerButton.setText(getSelected(1, Integer.valueOf(settings.getInt("deleteAfter", 0))));
		maximumButton.setText(getSelected(2, Integer.valueOf(settings.getInt("nbMaximum", 0))));
		
		exclureSwitch.setChecked(settings.getBoolean("excluFavoris", true));
		if (settings.getBoolean("excluFavoris", true)) {
			favorisTV.setText(getSelected(3, 1));			
		}
		else {
			favorisTV.setText(getSelected(3, 0));
		}
		
	}
	
	private void setupButton() {
		String username = settings.getString("username", "");
        String password = settings.getString("password", "");
		if (username.equals("") || password.equals("")) {
			setupNotConnected();
		}
		else {
			setupConnected();
		}
	}
	
	private void setupNotConnected() {
		
		connexionButton.setText("Me connecter");
		creerButton.setText("Créer mon compte");
		creerButton.setTextColor(mContext.getResources().getColor(R.color.blueLink));
		
		connexionButton.setOnClickListener(new OnClickListener() {
			
			@Override
			public void onClick(View v) {
                	
                	Intent intent = new Intent(mContext, ConnecterActivity.class).addFlags(Intent.FLAG_ACTIVITY_NEW_TASK);
        			mContext.startActivity(intent);
                	
            }
		});
		
		creerButton.setOnClickListener(new OnClickListener() {
			
			@Override
			public void onClick(View v) {
                	
                	Intent intent = new Intent(mContext, CreerCompteActivity.class).addFlags(Intent.FLAG_ACTIVITY_NEW_TASK);
        			mContext.startActivity(intent);
                	
            }
		});
	}
	
	private void setupConnected() {
		
		connexionButton.setText("Code d'activation");
		creerButton.setText("Déconnexion");
		creerButton.setTextColor(mContext.getResources().getColor(R.color.redButton));
		
		
		connexionButton.setOnClickListener(new OnClickListener() {
			
			@Override
			public void onClick(View v) {
                	
                	Intent intent = new Intent(mContext, CodeActivationActivity.class).addFlags(Intent.FLAG_ACTIVITY_NEW_TASK);
        			mContext.startActivity(intent);
                	
                	
            }
		});
		
		creerButton.setOnClickListener(new OnClickListener() {
			
			@Override
			public void onClick(View v) {
                	AlertDialog.Builder bld = new AlertDialog.Builder(mContext);
                	bld.setTitle("Avertissement");
                    bld.setMessage("Voulez-vous vraiment déconnecter votre compte de cette appareil ?");
                    bld.setPositiveButton( "Oui", new DialogInterface.OnClickListener() {
                        public void onClick(DialogInterface dialog, int which) {
                        	SharedPreferences.Editor editor = settings.edit();
                        	
                            editor.putInt("ekcredit", 0);
                            
                            editor.putString("username", "");
                            editor.putString("password", "");
                            
                            editor.commit();
                            
                            Intent intent = new Intent("SharedPreferencesReceiver");
                            LocalBroadcastManager.getInstance(mContext).sendBroadcast(intent);
                        }
                    });
                    bld.setNegativeButton( "Non", null);
                    bld.create().show();
                	
            }
		});
	}
	
	private BroadcastReceiver mSharedPreferencesReceiver = new BroadcastReceiver() {
    	
	    @Override
	    public void onReceive(Context context, Intent intent) {
	    	setupButton();
	    }
	
	};
	
	private String getSelected(int section, int selected) {
		
		String[] recentPendantString = new String[] {
				"7 jours", 
				"15 jours", 
				"30 jours", 
				"Toujours" 
				};
		String[] supprimerApresString = new String[] { 
				"15 jours", 
				"30 jours", 
				"60 jours", 
				"90 jours", 
				"illimités" 
				};
		String[] maximumString = new String[] { 
				"30 publications", 
				"60 publications", 
				"90 publications", 
				"120 publications", 
				"illimitées" 
				};
		
		String[] favorisString = new String[] { 
				"Vos favoris seront supprimés automatiquement par le nettoyage automatique",
				"Vos favoris ne seront pas supprimés automatiquement.",
				};
		
		String toreturn = "";
		
		switch (section) {
		case 0:
			toreturn =  recentPendantString[selected];
			break;
			
		case 1:
			toreturn =  supprimerApresString[selected];
			break;
			
		case 2:
			toreturn =  maximumString[selected];
			break;
			
		case 3:
			toreturn =  favorisString[selected];
			break;
			
		default:
			break;
		}
		
		return toreturn;
	}
	
}
