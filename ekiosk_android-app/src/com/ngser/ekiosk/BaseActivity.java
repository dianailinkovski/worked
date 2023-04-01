package com.ngser.ekiosk;

import com.actionbarsherlock.app.SherlockFragmentActivity;

import net.hockeyapp.android.CrashManager;
import net.hockeyapp.android.UpdateManager;
import android.os.Bundle;
import android.support.v4.app.FragmentActivity;

public class BaseActivity extends SherlockFragmentActivity {

	public String APP_ID = "2efb83ded7cb63b064527c40023f1d51";
	public String SECRECT_ID = "a71497a7cb17763aea6c9d4d3bf0b57c";
	
	@Override
	protected void onCreate(Bundle arg0) {
		// TODO Auto-generated method stub
		super.onCreate(arg0);
		
		checkForUpdates();
	}
	
	@Override
	protected void onPause() {
	  super.onPause();
	  UpdateManager.unregister();
	}
	
	@Override
	protected void onResume() {
	  super.onResume();
	  checkForCrashes();
	}
	
	private void checkForCrashes() {
	  CrashManager.register(this, APP_ID);
	}
	
	private void checkForUpdates() {
	  // Remove this for store / production builds!
	  UpdateManager.register(this, APP_ID);
	}
}
