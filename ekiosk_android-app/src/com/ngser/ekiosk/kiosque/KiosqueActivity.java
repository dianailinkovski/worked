package com.ngser.ekiosk.kiosque;

import java.math.BigInteger;
import java.security.SecureRandom;
import java.util.ArrayList;
import java.util.List;
import java.util.Random;

import org.apache.http.message.BasicNameValuePair;
import org.json.JSONException;
import org.json.JSONObject;

import android.app.AlertDialog;
import android.app.ProgressDialog;
import android.content.BroadcastReceiver;
import android.content.Context;
import android.content.Intent;
import android.content.IntentFilter;
import android.content.SharedPreferences;
import android.content.res.Configuration;
import android.graphics.Bitmap;
import android.graphics.BitmapFactory;
import android.graphics.Color;
import android.graphics.Point;
import android.graphics.drawable.BitmapDrawable;
import android.os.AsyncTask;
import android.os.Bundle;
import android.os.Handler;
import android.support.v4.app.ActionBarDrawerToggle;
import android.support.v4.app.Fragment;
import android.support.v4.app.FragmentManager;
import android.support.v4.app.FragmentStatePagerAdapter;
import android.support.v4.content.LocalBroadcastManager;
import android.support.v4.view.GravityCompat;
import android.support.v4.view.ViewPager;
import android.support.v4.view.ViewPager.OnPageChangeListener;
import android.support.v4.widget.DrawerLayout;
import android.util.Log;
import android.util.TypedValue;
import android.view.Display;
import android.view.Gravity;
import android.view.LayoutInflater;
import android.view.View;
import android.view.WindowManager;
import android.widget.ArrayAdapter;
import android.widget.CompoundButton.OnCheckedChangeListener;
import android.widget.LinearLayout;
import android.widget.LinearLayout.LayoutParams;
import android.widget.CompoundButton;
import android.widget.RelativeLayout;
import android.widget.TextView;
import android.widget.Toast;
import android.widget.ToggleButton;

import com.actionbarsherlock.app.ActionBar;
import com.actionbarsherlock.internal.widget.IcsAdapterView;
import com.actionbarsherlock.internal.widget.IcsAdapterView.OnItemSelectedListener;
import com.actionbarsherlock.internal.widget.IcsLinearLayout;
import com.actionbarsherlock.internal.widget.IcsSpinner;
import com.example.android.trivialdrivesample.util.IabHelper;
import com.example.android.trivialdrivesample.util.IabResult;
import com.example.android.trivialdrivesample.util.Inventory;
import com.example.android.trivialdrivesample.util.Purchase;
import com.ngser.ekiosk.BaseActivity;
import com.ngser.ekiosk.R;
import com.ngser.ekiosk.AsyncTask.BeforeBuyingTask;
import com.ngser.ekiosk.AsyncTask.BeforeBuyingTask.BeforeBuyingTaskListener;
import com.ngser.ekiosk.AsyncTask.ConsumePayloadBuyingTask;
import com.ngser.ekiosk.AsyncTask.ConsumePayloadBuyingTask.ConsumePayloadBuyingTaskListener;
import com.ngser.ekiosk.AsyncTask.JSONParser;
import com.ngser.ekiosk.AsyncTask.ValidatePayloadBuyingTask;
import com.ngser.ekiosk.AsyncTask.ValidatePayloadBuyingTask.ValidatePayloadBuyingTaskListener;
import com.ngser.ekiosk.googleslidingtabs.SlidingTabLayout;
import com.ngser.ekiosk.menu.MenuListener;

public class KiosqueActivity extends BaseActivity
		implements BeforeBuyingTaskListener, ValidatePayloadBuyingTaskListener, ConsumePayloadBuyingTaskListener {

	// DemoCollectionPagerAdapter mDemoCollectionPagerAdapter;
	ViewPager mViewPager;
	SlidingTabLayout mSlidingTabLayout;
	ProgressDialog progress;
	Context mContext;

	private DrawerLayout mDrawerLayout;
	private ActionBarDrawerToggle mDrawerToggle;
	private View mDrawerView;
	IcsSpinner spinner;
	MenuListener menuListener;
	TextView creditTV;
	private ToggleButton abonneToggleButton;

	LinearLayout categoryLayout;
	// IcsLinearLayout listNavLayout;
	LinearLayout listNavLayout;
	IcsLinearLayout listNavLayout2;

	final String PREFS_NAME = "eKioskPrefSetting";

	static final String TAG = "eKiosk-IAB";
	IabHelper mHelper;
	String SKU_SELECTED;
	String PRIX_SELECTED;
	String QUANTITE_SELECTED;

	Boolean showBundle;

	Bitmap bitmapBackGround;

	// (arbitrary) request code for the purchase flow
	static final int RC_REQUEST = 10001;
	private creditValidatorTask dcreditValidatorTask;
	private static final char[] symbols = new char[36];

	static {
		for (int idx = 0; idx < 10; ++idx)
			symbols[idx] = (char) ('0' + idx);
		for (int idx = 10; idx < 36; ++idx)
			symbols[idx] = (char) ('a' + idx - 10);
	}

	private String tempUrl = "";

	@SuppressWarnings("deprecation")
	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.kiosque_main_pager);

		mContext = this;

		setIAB();
		// //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		dcreditValidatorTask = new creditValidatorTask();
		dcreditValidatorTask.execute();
		// //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

		LocalBroadcastManager.getInstance(this).registerReceiver(mMessageReceiver, new IntentFilter("buyingSKU"));

		View tempViewMenu = findViewById(R.id.drawer_layout);

		menuListener = new MenuListener(tempViewMenu, this);

		mDrawerLayout = (DrawerLayout) findViewById(R.id.drawer_layout);
		mDrawerView = (View) findViewById(R.id.left_drawer);
		mDrawerToggle = new ActionBarDrawerToggle(this, /* host Activity */
				mDrawerLayout, /* DrawerLayout object */
				R.drawable.ic_drawer, /*
										 * nav drawer icon to replace 'Up' caret
										 */
				R.string.drawerTitle, /* "open drawer" description */
				R.string.app_name /* "close drawer" description */
		) {

			/**
			 * Called when a drawer has settled in a completely closed state.
			 */
			public void onDrawerClosed(View view) {
				super.onDrawerClosed(view);
			}

			/** Called when a drawer has settled in a completely open state. */
			public void onDrawerOpened(View drawerView) {
				super.onDrawerOpened(drawerView);
			}
		};

		categoryLayout = (LinearLayout) findViewById(R.id.categoryLayout);

		// set a custom shadow that overlays the main content when the drawer
		// opens
		mDrawerLayout.setDrawerShadow(R.drawable.drawer_shadow, GravityCompat.START);

		// Set the drawer toggle as the DrawerListener
		mDrawerLayout.setDrawerListener(mDrawerToggle);

		getActionBar().setDisplayHomeAsUpEnabled(true);
		getActionBar().setHomeButtonEnabled(true);

		ActionBar actionBar = getSupportActionBar();
		final Context context = actionBar.getThemedContext();
		final String[] entries = new String[] { "Tous", "Quotidien", "Hebdomadaire", "Mensuel", "Annuel", "Livre" };
		ArrayAdapter<String> adapter = new ArrayAdapter<String>(context, R.layout.spinner_dropdown_item, entries);
		adapter.setDropDownViewResource(R.layout.spinner_dropdown_item);

		creditTV = new TextView(context);
		creditTV.setTextSize(TypedValue.COMPLEX_UNIT_DIP, 14);
		Bitmap bm1 = BitmapFactory.decodeResource(context.getResources(), R.drawable.big_ekcredit);

		BitmapDrawable myIcon = new BitmapDrawable(context.getResources(), bm1);
		creditTV.setCompoundDrawablesWithIntrinsicBounds(null, null, myIcon, null);
		creditTV.setCompoundDrawablePadding(10);
		creditTV.setGravity(Gravity.RIGHT | Gravity.CENTER_VERTICAL);

		// configure custom view
		listNavLayout2 = (IcsLinearLayout) getLayoutInflater().inflate(R.layout.abs__action_bar_tab_bar_view, null);
		LinearLayout.LayoutParams paramsll2 = new LinearLayout.LayoutParams(LayoutParams.MATCH_PARENT,
				LayoutParams.MATCH_PARENT);
		paramsll2.weight = 1.0f;
		listNavLayout2.setLayoutParams(paramsll2);
		listNavLayout2.addView(creditTV, paramsll2);
		listNavLayout2.setPadding(0, 0, 10, 0);

		// create ICS spinner
		spinner = new IcsSpinner(this, null, R.attr.actionDropDownStyle);
		spinner.setAdapter(adapter);
		spinner.setOnItemSelectedListener(new OnItemSelectedListener() {
			@Override
			public void onItemSelected(IcsAdapterView<?> parent, View view, int position, long id) {
				if (listNavLayout.getChildCount() > 4) {
					listNavLayout.removeViewAt(3);
					listNavLayout.removeViewAt(2);
				}
				mViewPager.setAdapter(new SamplePagerAdapter2(getSupportFragmentManager()));
				mSlidingTabLayout.setViewPager(mViewPager);
			}

			@Override
			public void onNothingSelected(IcsAdapterView<?> parent) {
			}
		});

		listNavLayout = new LinearLayout(mContext);
		listNavLayout
				.setLayoutParams(new LinearLayout.LayoutParams(LayoutParams.MATCH_PARENT, LayoutParams.WRAP_CONTENT));
		LinearLayout.LayoutParams paramsll = new LinearLayout.LayoutParams(LayoutParams.WRAP_CONTENT,
				LayoutParams.WRAP_CONTENT);
		listNavLayout.setPadding(10, 0, 10, 0);
		// paramsll.gravity = Gravity.CENTER;
		spinner.setBackgroundResource(0);

		TextView categoryTv = new TextView(mContext);
		categoryTv.setText("Categorie");
		categoryTv.setTextSize(TypedValue.COMPLEX_UNIT_SP, 12);
		listNavLayout.addView(categoryTv, paramsll);
		listNavLayout.addView(spinner, paramsll);
		listNavLayout.setGravity(Gravity.CENTER_VERTICAL); // <-- align the
															// spinner to the
		// right

		listNavLayout.addView(LayoutInflater.from(mContext).inflate(R.layout.abonne_child_layout, null));
		abonneToggleButton = (ToggleButton) listNavLayout.findViewById(R.id.abonne_tb);
		abonneToggleButton.setOnCheckedChangeListener(new OnCheckedChangeListener() {

			@Override
			public void onCheckedChanged(CompoundButton buttonView, boolean isChecked) {
				if (listNavLayout.getChildCount() > 4) {
					listNavLayout.removeViewAt(3);
					listNavLayout.removeViewAt(2);
				}
				mViewPager.setAdapter(new SamplePagerAdapter2(getSupportFragmentManager()));
				mSlidingTabLayout.setViewPager(mViewPager);

			}
		});

		categoryLayout.removeAllViews();
		categoryLayout.addView(listNavLayout);
		actionBar.setDisplayShowCustomEnabled(true);

		BitMapBGView bgImageView = new BitMapBGView(getApplicationContext());
		bitmapBackGround = BitmapFactory.decodeResource(getApplicationContext().getResources(), R.drawable.bg_test);

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

		bgImageView.imageBitmap = Bitmap.createScaledBitmap(bitmapBackGround, scaleWidth, scaleHeight, true);

		RelativeLayout rl = (RelativeLayout) findViewById(R.id.relativeLayout);
		RelativeLayout.LayoutParams params = new RelativeLayout.LayoutParams(RelativeLayout.LayoutParams.MATCH_PARENT,
				RelativeLayout.LayoutParams.MATCH_PARENT);
		params.addRule(RelativeLayout.ALIGN_PARENT_LEFT, RelativeLayout.TRUE);
		params.addRule(RelativeLayout.ALIGN_PARENT_TOP, RelativeLayout.TRUE);

		rl.addView(bgImageView, params);
		LinearLayout ll = (LinearLayout) findViewById(R.id.linearLayout);
		ll.setBackgroundColor(Color.TRANSPARENT);
		rl.bringChildToFront(ll);

		mViewPager = (ViewPager) findViewById(R.id.viewpager);
		mViewPager.setAdapter(new SamplePagerAdapter2(getSupportFragmentManager()));

		mSlidingTabLayout = (SlidingTabLayout) findViewById(R.id.sliding_tabs);
		mSlidingTabLayout.setBackgroundColor(Color.WHITE);
		mSlidingTabLayout.setViewPager(mViewPager);

		// listen for page changes so we can track the current index
		mSlidingTabLayout.setOnPageChangeListener(new OnPageChangeListener() {

			public void onPageScrollStateChanged(int arg0) {
			}

			public void onPageScrolled(int arg0, float arg1, int arg2) {
			}

			public void onPageSelected(int currentPage) {
				// currentPage is the position that is currently displayed.
				ActionBar actionBar = getSupportActionBar();
				if (currentPage == 0) {
					// actionBar.setCustomView(listNavLayout,
					// new ActionBar.LayoutParams(Gravity.RIGHT));

					categoryLayout.removeAllViews();
					categoryLayout.addView(listNavLayout);

					// spinner.setVisibility(View.VISIBLE);
				} else {
					// actionBar.setCustomView(listNavLayout2,
					// new ActionBar.LayoutParams(Gravity.RIGHT));

					categoryLayout.removeAllViews();
					categoryLayout.addView(listNavLayout2);
					// spinner.setVisibility(View.GONE);
				}
			}

		});

		Bundle b = this.getIntent().getExtras();
		if (b != null) {
			showBundle = b.getBoolean("showBundle", false);
		} else {
			showBundle = false;
		}

	}

	private int dpToPx(int dp) {
		return (int) (dp * mContext.getResources().getDisplayMetrics().density);
	}

	public LinearLayout getCategoryParent() {
		return listNavLayout;
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
	public boolean onOptionsItemSelected(com.actionbarsherlock.view.MenuItem item) {

		int id = item.getItemId();
		if (id == android.R.id.home) {

			if (mDrawerLayout.isDrawerOpen(mDrawerView)) {
				mDrawerLayout.closeDrawer(mDrawerView);
			} else {
				mDrawerLayout.openDrawer(mDrawerView);
			}

			return true;
		}

		return super.onOptionsItemSelected(item);
	}

	@Override
	protected void onActivityResult(int requestCode, int resultCode, Intent data) {
		Log.d(TAG, "onActivityResult(" + requestCode + "," + resultCode + "," + data);
		if (mHelper == null)
			return;

		// Pass on the activity result to the helper for handling
		if (!mHelper.handleActivityResult(requestCode, resultCode, data)) {
			// not handled, so handle it ourselves (here's where you'd
			// perform any handling of activity results not related to in-app
			// billing...
			super.onActivityResult(requestCode, resultCode, data);
		} else {
			Log.d(TAG, "onActivityResult handled by IABUtil.");
		}
	}

	class SamplePagerAdapter2 extends FragmentStatePagerAdapter {

		public SamplePagerAdapter2(FragmentManager fm) {
			super(fm);
		}

		@Override
		public Fragment getItem(int arg0) {
			Log.v("Fragment getItem", "Fragment getItem");
			if (arg0 == 1) {
				CreditsEkioskRelativeLayout view = CreditsEkioskRelativeLayout.newInstance();
				return view;
			} else if (arg0 == 2) {
				ArchivesJournauxGridView view = ArchivesJournauxGridView.newInstance();
				return view;
			} else {
				int selected = spinner.getSelectedItemPosition();
				Log.v("------- selected", String.valueOf(selected));

				boolean isSubscription = abonneToggleButton.isChecked();
				StringBuilder strBuilder = new StringBuilder(
						"http://api.ngser.gnetix.com/v1.1/getRecentsParCategorie.php?categorie=");
				switch (selected) {
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
				case 6:
					isSubscription = true;
					break;
				default:
					break;
				}

				SharedPreferences settings = getSharedPreferences(PREFS_NAME, 0);
				String username = settings.getString("username", "");
				String password = settings.getString("password", "");

				strBuilder.append("&username=");
				strBuilder.append(username);
				strBuilder.append("&password=");
				strBuilder.append(password);

				String url = strBuilder.toString();
				if (!isSubscription)
					tempUrl = url;
				else {
					url = tempUrl + "&abonnement=1";
				}

				KioskGridView temp = KioskGridView.newInstance(url);
				Log.d("RequestLink", url);

				return temp;
			}
		}

		@Override
		public int getCount() {
			return 3;
		}

		@Override
		public CharSequence getPageTitle(int position) {
			String title = "";
			switch (position) {
			case 0:
				title = "Publications";
				break;
			case 1:
				title = "Crédits ekiosk";
				break;
			case 2:
				title = "Archives";
				break;

			default:
				break;
			}
			return title;
		}

	}

	// We're being destroyed. It's important to dispose of the helper here!
	@Override
	public void onDestroy() {
		super.onDestroy();
		menuListener.onDestroy();
		mContext = null;
		LocalBroadcastManager.getInstance(this).unregisterReceiver(mMessageReceiver);
		if (bitmapBackGround != null) {
			bitmapBackGround.recycle();
			bitmapBackGround = null;
		}
		// very important:
		Log.d(TAG, "Destroying helper.");
		if (mHelper != null) {
			mHelper.dispose();
			mHelper = null;
		}
		if (progress != null && progress.isShowing()) {
			progress.dismiss();
		}
	}

	@Override
	protected void onStart() {
		super.onStart();
		LocalBroadcastManager.getInstance(this).registerReceiver(mSharedPreferencesReceiver,
				new IntentFilter("SharedPreferencesReceiver"));

		SharedPreferences settings = getSharedPreferences(PREFS_NAME, 0);
		int ekcredit = settings.getInt("ekcredit", 0);
		creditTV.setText(String.valueOf(ekcredit));

		menuListener.refresh();
	}

	@Override
	protected void onStop() {
		super.onStop();
		LocalBroadcastManager.getInstance(this).unregisterReceiver(mSharedPreferencesReceiver);
	}

	@Override
	public void onResume() {
		super.onResume();
		if (showBundle) {
			Log.e("resume", "resume");
			mViewPager.setCurrentItem(1, false);

			showBundle = false;
		}

	}

	private BroadcastReceiver mMessageReceiver = new BroadcastReceiver() {

		@Override
		public void onReceive(Context context, Intent intent) {

			// String action = intent.getAction();
			SKU_SELECTED = intent.getStringExtra("SKU");
			PRIX_SELECTED = intent.getStringExtra("prix");
			QUANTITE_SELECTED = intent.getStringExtra("quantite");
			onBuyGasButtonClicked(null);

		}

	};

	// User clicked the "Buy Gas" button
	public void onBuyGasButtonClicked(View arg0) {
		Log.d(TAG, "Buy gas button clicked.");
		mContext = this;

		// launch the gas purchase UI flow.
		// We will be notified of completion via mPurchaseFinishedListener
		Log.d(TAG, "Launching purchase flow for gas.");

		/*
		 * TODO: for security, generate your payload here for verification. See
		 * the comments on verifyDeveloperPayload() for more info. Since this is
		 * a SAMPLE, we just use an empty string, but on a production app you
		 * should carefully generate this.
		 */

		RandomString randomString = new RandomString(36);
		// System.out.println("RandomString>>>>" + randomString.nextString());
		String payload = randomString.nextString();
		Log.e("Random generated Payload", ">>>>>" + payload);

		progress = ProgressDialog.show(mContext, "", "Vérification serveur eKiosk");

		BeforeBuyingTask beforeTask = new BeforeBuyingTask();
		beforeTask.mContext = getApplicationContext();
		beforeTask.setListener(this);
		beforeTask.mPayload = payload;
		beforeTask.mSKU = SKU_SELECTED;
		beforeTask.mPrix = PRIX_SELECTED;
		beforeTask.mQuantite = QUANTITE_SELECTED;
		beforeTask.execute();
	}

	@Override
	public void BeforeBuyingTaskFinish(String payload) {
		progress.dismiss();
		mHelper.launchPurchaseFlow(this, SKU_SELECTED, RC_REQUEST, mPurchaseFinishedListener, payload);
	}

	@Override
	public void BeforeBuyingTaskFinishWithError(Boolean payloadUsed, String errorString) {
		progress.dismiss();
		if (payloadUsed) {
			onBuyGasButtonClicked(null);
		} else {
			alert(errorString);
		}
	}

	void complain(String message) {
		Log.e(TAG, "**** TrivialDrive Error: " + message);
		alert("Error: " + message);
	}

	void alert(String message) {
		AlertDialog.Builder bld = new AlertDialog.Builder(this);
		bld.setMessage(message);
		bld.setNeutralButton("OK", null);
		Log.d(TAG, "Showing alert dialog: " + message);
		bld.create().show();
	}

	/** Verifies the developer payload of a purchase. */
	boolean verifyDeveloperPayload(Purchase p) {
		String payload = p.getDeveloperPayload();
		String sku = p.getSku();

		/*
		 * TODO: verify that the developer payload of the purchase is correct.
		 * It will be the same one that you sent when initiating the purchase.
		 * 
		 * WARNING: Locally generating a random string when starting a purchase
		 * and verifying it here might seem like a good approach, but this will
		 * fail in the case where the user purchases an item on one device and
		 * then uses your app on a different device, because on the other device
		 * you will not have access to the random string you originally
		 * generated.
		 * 
		 * So a good developer payload has these characteristics:
		 * 
		 * 1. If two different users purchase an item, the payload is different
		 * between them, so that one user's purchase can't be replayed to
		 * another user.
		 * 
		 * 2. The payload must be such that you can verify it even when the app
		 * wasn't the one who initiated the purchase flow (so that items
		 * purchased by the user on one device work on other devices owned by
		 * the user).
		 * 
		 * Using your own server to store and verify developer payloads across
		 * app installations is recommended.
		 */

		ValidatePayloadBuyingTask validatePayload = new ValidatePayloadBuyingTask();
		validatePayload.mContext = getApplicationContext();
		validatePayload.setListener(this);
		validatePayload.mPayload = payload;
		validatePayload.mSKU = sku;
		validatePayload.mPurchase = p;
		validatePayload.execute();

		// alert(p.toString());

		return true;
	}

	@Override
	public void ValidatePayloadBuyingTaskFinish(Purchase purchase, JSONObject mJson) {
		progress.dismiss();
		progress = ProgressDialog.show(mContext, "", "Ajout de vos crédits");
		// mHelper.queryInventoryAsync(mGotInventoryListener);
		mHelper.consumeAsync(purchase, mConsumeFinishedListener);

	}

	@Override
	public void ValidatePayloadBuyingTaskFinishWithError(String errorString) {
		progress.dismiss();
		alert(errorString);
	}

	void setIAB() {
		String base64EncodedPublicKey = "MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAlhtouQLna5zohIMj9FXFmxPCk1IfAi8k5RiR0JbVSDjka34UwKpYQmTDc4/jGQI1uGXVsTYExV/GfONN+vkE9RguexjYK7kIq4KVdao0tD1lRjxVzNOCZM8g6qpzDXKGs6aiJ15bp63r7HgmtgNCo2OWj7iv8nXtNA3aWNwgV00vSSXs1g7iApc0BEy2CCMS1ENjFs4aUBZtXYamcGxfobGNIxuZzko5T0zVBxJ6oQuTFnAK+fIMAlazNNthnuGwAcjhofIjldbPmdZbk8FeeI0K8R9qq7OFdkPzKpflTOSIWqd8NPah0KUowC9RGPAXkFma1AOc+7sNoFg10psDewIDAQAB";

		// String base64EncodedPublicKey = "";

		Log.d(TAG, "Creating IAB helper.");
		mHelper = new IabHelper(this, base64EncodedPublicKey);

		// enable debug logging (for a production application, you should set
		// this to false).
		mHelper.enableDebugLogging(false);

		// Start setup. This is asynchronous and the specified listener
		// will be called once setup completes.

		progress = ProgressDialog.show(mContext, "", "Vérification Google Play");

		Log.d(TAG, "Starting setup.");
		mHelper.startSetup(new IabHelper.OnIabSetupFinishedListener() {
			public void onIabSetupFinished(IabResult result) {
				Log.d(TAG, "Setup finished.");

				progress.dismiss();

				if (!result.isSuccess()) {
					// Oh noes, there was a problem.
					complain("Problem setting up in-app billing: " + result);
					return;
				}

				// Have we been disposed of in the meantime? If so, quit.
				if (mHelper == null)
					return;

				// IAB is fully set up. Now, let's get an inventory of stuff we
				// own.
				Log.d(TAG, "Setup successful. Querying inventory.");

				if (mContext != null) {
					progress = ProgressDialog.show(mContext, "", "Vérification des achats non complétés");
					mHelper.queryInventoryAsync(mGotInventoryListener);
				}
			}
		});
	}

	// Listener that's called when we finish querying the items and
	// subscriptions we own
	IabHelper.QueryInventoryFinishedListener mGotInventoryListener = new IabHelper.QueryInventoryFinishedListener() {
		public void onQueryInventoryFinished(IabResult result, Inventory inventory) {
			Log.d(TAG, "Query inventory finished.");

			if ((progress != null) && progress.isShowing()) {
				progress.dismiss();
			}

			// Have we been disposed of in the meantime? If so, quit.
			if (mHelper == null)
				return;

			// Is it a failure?
			if (result.isFailure()) {
				complain("Failed to query inventory: " + result);
				return;
			}

			Log.d(TAG, "Query inventory was successful.");

			/*
			 * Check for items we own. Notice that for each purchase, we check
			 * the developer payload to see if it's correct! See
			 * verifyDeveloperPayload().
			 */

			// Check for gas delivery -- if we own gas, we should fill up the
			// tank immediately
			/*
			 * Purchase gasPurchase = inventory.getPurchase(SKU_GAS); if
			 * (gasPurchase != null && verifyDeveloperPayload(gasPurchase)) {
			 * Log.d(TAG, "We have gas. Consuming it.");
			 * mHelper.consumeAsync(inventory.getPurchase(SKU_GAS),
			 * mConsumeFinishedListener); return; }
			 */
			/*
			 * Purchase gasPurchase = inventory.getPurchase(SKU_GAS); if
			 * (gasPurchase != null) { progress = ProgressDialog.show(mContext,
			 * "", "Validation de votre achat");
			 * verifyDeveloperPayload(gasPurchase); return; }
			 */

			if (!inventory.getAllPurchases().isEmpty()) {
				for (Purchase p : inventory.getAllPurchases()) {
					mHelper.consumeAsync(inventory.getPurchase(p.getSku()), mConsumeFinishedListener);
				}
			}

			// updateUi();
			// setWaitScreen(false);
			Log.d(TAG, "Initial inventory query finished; enabling main UI.");
		}
	};

	// Called when consumption is complete
	IabHelper.OnConsumeFinishedListener mConsumeFinishedListener = new IabHelper.OnConsumeFinishedListener() {
		public void onConsumeFinished(Purchase purchase, IabResult result) {
			Log.d(TAG, "Consumption finished. Purchase: " + purchase + ", result: " + result);

			progress.dismiss();

			// if we were disposed of in the meantime, quit.
			if (mHelper == null) {
				complain("mHelper est null1 ???");
				return;
			}

			// We know this is the "gas" sku because it's the only one we
			// consume,
			// so we don't check which sku was consumed. If you have more than
			// one
			// sku, you probably should check...
			if (result.isSuccess()) {
				// successfully consumed, so we apply the effects of the item in
				// our
				// game world's logic, which in our case means filling the gas
				// tank a bit
				Log.d(TAG, "Consumption successful. Provisioning.");

				readyToConsumePurchase(purchase);

				// mTank = mTank == TANK_MAX ? TANK_MAX : mTank + 1;
				// saveData();
				// alert("You filled 1/4 tank. Your tank is now " +
				// String.valueOf(mTank) + "/4 full!");
				// alert("You filled 1/4 tank. Your tank is now " +
				// String.valueOf(0) + "/4 full!");

			} else {
				complain("Error while consuming: " + result);
			}

			// updateUi();
			// setWaitScreen(false);

			Log.d(TAG, "End consumption flow.");
		}
	};

	public void readyToConsumePurchase(Purchase purchase) {
		progress = ProgressDialog.show(mContext, "", "Ajout de vos crédit é votre compte");

		String payload = purchase.getDeveloperPayload();
		String sku = purchase.getSku();

		ConsumePayloadBuyingTask consumePayload = new ConsumePayloadBuyingTask();
		consumePayload.mContext = getApplicationContext();
		consumePayload.setListener(this);
		consumePayload.mPayload = payload;
		consumePayload.mSKU = sku;
		consumePayload.execute();

	}

	@Override
	public void ConsumePayloadBuyingTaskFinish(String receivedTotalString) {

		SharedPreferences settings = getSharedPreferences(PREFS_NAME, 0);
		SharedPreferences.Editor editor = settings.edit();

		String username = settings.getString("username", "");
		String password = settings.getString("password", "");

		int total = 0;
		if (username.equals("") || password.equals("")) {
			int current = settings.getInt("ekcredit", 0);
			int added = Integer.valueOf(receivedTotalString);
			total = current + added;
		} else {
			total = Integer.valueOf(receivedTotalString);
		}

		editor.putInt("ekcredit", total);

		editor.commit();

		// Intent intent = new Intent("SharedPreferencesReceiver");
		// LocalBroadcastManager.getInstance(getApplicationContext()).sendBroadcast(intent);

		progress.dismiss();
		alert("achat complété. Vos crédits ont été ajouté é votre compte");
	}

	@Override
	public void ConsumePayloadBuyingTaskFinishWithError(String errorString) {
		progress.dismiss();
		alert(errorString);
	}

	// Callback for when a purchase is finished
	IabHelper.OnIabPurchaseFinishedListener mPurchaseFinishedListener = new IabHelper.OnIabPurchaseFinishedListener() {
		public void onIabPurchaseFinished(IabResult result, Purchase purchase) {
			Log.d(TAG, "Purchase finished: " + result + ", purchase: " + purchase);

			// if we were disposed of in the meantime, quit.
			if (mHelper == null) {
				complain("mHelper is null2 ???");
				return;
			}

			if (result.getResponse() == -1005) { // public static final int
													// IABHELPER_USER_CANCELLED
													// = -1005;
				return;
			}

			if (result.isFailure()) {
				complain("Error purchasing: " + result);
				return;
			}

			/*
			 * if (!verifyDeveloperPayload(purchase)) { complain(
			 * "Error purchasing. Authenticity verification failed."); return; }
			 * 
			 * Log.d(TAG, "Purchase successful.");
			 * 
			 * if (purchase.getSku().equals(SKU_GAS)) { // bought 1/4 tank of
			 * gas. So consume it. Log.d(TAG,
			 * "Purchase is gas. Starting gas consumption.");
			 * mHelper.consumeAsync(purchase, mConsumeFinishedListener); }
			 */

			if (purchase != null) {
				verifyDeveloperPayload(purchase);
				return;
			}
			complain("OnIabPurchaseFinishedListener puchase is null");
		}
	};

	public class RandomString {

		private final Random random = new Random();

		private final char[] buf;

		public RandomString(int length) {
			if (length < 1)
				throw new IllegalArgumentException("length < 1: " + length);
			buf = new char[length];
		}

		public String nextString() {
			for (int idx = 0; idx < buf.length; ++idx)
				buf[idx] = symbols[random.nextInt(symbols.length)];
			return new String(buf);
		}

	}

	public final class SessionIdentifierGenerator {

		private SecureRandom random = new SecureRandom();

		public String nextSessionId() {
			return new BigInteger(130, random).toString(32);
		}

	}

	private BroadcastReceiver mSharedPreferencesReceiver = new BroadcastReceiver() {

		@Override
		public void onReceive(Context context, Intent intent) {
			Intent currentintent = getIntent();
			finish();
			startActivity(currentintent);
		}

	};

	/*
	 * public void beforeBuying() { //RandomString randomString = new
	 * RandomString(36); //System.out.println("RandomString>>>>" +
	 * randomString.nextString()); //String payload = randomString.nextString();
	 * String payload = "asdvbn90!"; Log.e("Random generated Payload", ">>>>>" +
	 * payload);
	 * 
	 * progress = ProgressDialog.show(mContext, "",
	 * "... Vérification du status du serveur eKiosk ...");
	 * 
	 * BeforeBuyingTask beforeTask = new BeforeBuyingTask(); beforeTask.mContext
	 * = getApplicationContext(); beforeTask.setListener(this);
	 * beforeTask.mPayload = payload; beforeTask.mSKU = "skutest";
	 * beforeTask.execute(); }
	 * 
	 * public void validatePayload() {
	 * 
	 * progress = ProgressDialog.show(mContext, "",
	 * "... Vérification de votre achat ...");
	 * 
	 * ValidatePayloadBuyingTask validatePayload = new
	 * ValidatePayloadBuyingTask(); validatePayload.mContext =
	 * getApplicationContext(); validatePayload.setListener(this);
	 * validatePayload.mPayload = "asdvbn90!"; validatePayload.mSKU = "skutest";
	 * //validatePayload.mPurchase = p; validatePayload.execute(); }
	 * 
	 * public void consumeProduct() { progress = ProgressDialog.show(mContext,
	 * "", "... Ajout de vos crédit é votre compte ...");
	 * 
	 * 
	 * 
	 * ConsumePayloadBuyingTask consumePayload = new ConsumePayloadBuyingTask();
	 * consumePayload.mContext = getApplicationContext();
	 * consumePayload.setListener(this); consumePayload.mPayload = "asdvbn90!";
	 * consumePayload.mSKU = "skutest"; consumePayload.execute(); }
	 * 
	 * @Override public void BeforeBuyingTaskFinish(String payload) {
	 * progress.dismiss(); //mHelper.launchPurchaseFlow(this, SKU_GAS,
	 * RC_REQUEST, mPurchaseFinishedListener, payload); validatePayload(); }
	 * 
	 * @Override public void BeforeBuyingTaskFinishWithError(Boolean
	 * payloadUsed, String errorString) { progress.dismiss(); if (payloadUsed) {
	 * //onBuyGasButtonClicked(null); } else { alert(errorString); } }
	 * 
	 * 
	 * @Override public void ValidatePayloadBuyingTaskFinish(Purchase purchase,
	 * JSONObject mJson) { progress.dismiss(); //progress =
	 * ProgressDialog.show(mContext, "", "Ajout de vos crédits");
	 * consumeProduct(); //mHelper.queryInventoryAsync(mGotInventoryListener); }
	 * 
	 * @Override public void ValidatePayloadBuyingTaskFinishWithError(String
	 * errorString) { progress.dismiss(); alert(errorString); }
	 * 
	 * @Override public void ConsumePayloadBuyingTaskFinish() { //TODO mise a
	 * jour des credit total avec le retour du consumeProductTask
	 * progress.dismiss(); alert("achat complété"); }
	 * 
	 * @Override public void ConsumePayloadBuyingTaskFinishWithError(String
	 * errorString) { progress.dismiss(); alert(errorString); }
	 */

	private class creditValidatorTask extends AsyncTask<String, Void, JSONObject> {

		@Override
		protected JSONObject doInBackground(String... params) {
			JSONParser jParser = new JSONParser();
			// Getting JSON from URL
			if (isCancelled()) {
				return null;
			}

			StringBuilder strBuilder = new StringBuilder("http://api.ngser.gnetix.com/v1.1/getCurrentCredit.php");

			String url = strBuilder.toString();
			Log.v("url archives", url);

			List<BasicNameValuePair> nameValuePairs = new ArrayList<BasicNameValuePair>();

			SharedPreferences settings = getSharedPreferences(PREFS_NAME, 0);
			String username = settings.getString("username", "");
			String password = settings.getString("password", "");

			if (username.equals("") || password.equals("")) {
				return null;
			}

			nameValuePairs.add(new BasicNameValuePair("username", username));
			nameValuePairs.add(new BasicNameValuePair("password", password));

			JSONObject json = jParser.getJSONFromUrlWithPostArray(url, nameValuePairs);

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
				Log.e("resultat", resultat);
				JSONObject data = json.getJSONObject("data");

				if (resultat.equals("true")) {

					final String PREFS_NAME = "eKioskPrefSetting";
					SharedPreferences settings = getSharedPreferences(PREFS_NAME, 0);
					int total = Integer.valueOf(data.getString("quantite"));
					Log.e("total", String.valueOf(total));
					Log.e("ekcredit", String.valueOf(settings.getInt("ekcredit", 0)));
					if (total != settings.getInt("ekcredit", 0)) {

						SharedPreferences.Editor editor = settings.edit();
						editor.putInt("ekcredit", total);
						editor.commit();

						Toast.makeText(mContext, "Vos crédits ont été mise é jour automatiquement par le serveur",
								Toast.LENGTH_LONG).show();

						final Handler handler = new Handler();
						handler.postDelayed(new Runnable() {
							@Override
							public void run() {
								// Do something after 100ms
								Intent intent = new Intent("SharedPreferencesReceiver");
								LocalBroadcastManager.getInstance(getApplicationContext()).sendBroadcast(intent);
							}
						}, 800);

					}

				}

			} catch (JSONException e) {
				e.printStackTrace();
			}
		}
	}

}
