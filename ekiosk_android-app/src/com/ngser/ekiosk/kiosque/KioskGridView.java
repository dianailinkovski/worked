package com.ngser.ekiosk.kiosque;

import java.text.ParseException;
import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Date;
import java.util.LinkedHashSet;
import java.util.List;
import java.util.Set;

import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

import android.content.Context;
import android.content.Intent;
import android.content.res.Configuration;
import android.content.res.Resources;
import android.graphics.Bitmap;
import android.graphics.Color;
import android.graphics.Point;
import android.os.AsyncTask;
import android.os.Bundle;
import android.util.AttributeSet;
import android.util.Log;
import android.util.TypedValue;
import android.view.Display;
import android.view.View;
import android.view.ViewGroup;
import android.widget.AdapterView;
import android.widget.AdapterView.OnItemClickListener;
import android.widget.LinearLayout.LayoutParams;
import android.widget.ArrayAdapter;
import android.widget.GridView;
import android.widget.LinearLayout;
import android.widget.ProgressBar;
import android.widget.ScrollView;
import android.widget.TextView;

import com.actionbarsherlock.internal.widget.IcsAdapterView;
import com.actionbarsherlock.internal.widget.IcsSpinner;
import com.devspark.progressfragment.ProgressFragment;
import com.ngser.ekiosk.R;
import com.ngser.ekiosk.AsyncTask.JSONParser;
import com.ngser.ekiosk.Model.EditionModelClass;

public class KioskGridView extends ProgressFragment {

	public static final String ARG_URLSTRING = "URLSTRING";
	private int position;

	ExpandableHeightGridView gridView;
	ScrollView scrollView;
	LinearLayout linearLayout;
	ProgressBar mainProgressBar;
	IcsSpinner countriesSpinner;
	KioskArrayAdapter adapter;

	ArrayList<EditionModelClass> templist = new ArrayList<EditionModelClass>(),
			list = new ArrayList<EditionModelClass>();
	List<String> countriesList = new ArrayList<String>();
	private JSONParse jsonAsync;

	private String urlToLoad = "";
	public JSONObject json = null;

	AdsImageView topAds;
	AdsImageView bottomAds;
	Bitmap topAdsBitmap = null;
	Bitmap bottomAdsBitmap = null;

	private boolean isLoading = false;

	public static KioskGridView newInstance(String urlString) {
		KioskGridView fragment = new KioskGridView();
		Bundle b = new Bundle();
		b.putString(ARG_URLSTRING, urlString);
		fragment.setArguments(b);
		return fragment;
	}

	private void removeDuplicateFromCountriesList() {
		if (countriesList == null) {
			return;
		}

		Set<String> hs = new LinkedHashSet<String>();
		hs.addAll(countriesList);
		countriesList.clear();
		for (String countryStr : hs) {
			if (countryStr != null && !countryStr.equals("null")) {
				countriesList.add(countryStr);
			}
		}
	}

	@Override
	public void onCreate(Bundle savedInstanceState) {
		// TODO Auto-generated method stub
		Log.v("onCreate", "onCreate");
		super.onCreate(savedInstanceState);
		this.urlToLoad = getArguments().getString(ARG_URLSTRING);
	}

	@Override
	public void onDestroy() {
		// TODO Auto-generated method stub
		super.onDestroy();
	}

	@Override
	public void onViewCreated(View view, Bundle savedInstanceState) {
		super.onViewCreated(view, savedInstanceState);
		float px;
		Resources r = getResources();
		if ((getResources().getConfiguration().screenLayout
				& Configuration.SCREENLAYOUT_SIZE_MASK) == Configuration.SCREENLAYOUT_SIZE_XLARGE
				|| (getResources().getConfiguration().screenLayout
						& Configuration.SCREENLAYOUT_SIZE_MASK) == Configuration.SCREENLAYOUT_SIZE_LARGE) {
			px = TypedValue.applyDimension(TypedValue.COMPLEX_UNIT_DIP, 140, r.getDisplayMetrics());
		} else {
			px = TypedValue.applyDimension(TypedValue.COMPLEX_UNIT_DIP, 100, r.getDisplayMetrics());
		}

		setEmptyText("Aucune publication trouvée");

		setContentView(R.layout.kiosque_main_gridview);

		this.scrollView = (ScrollView) getView().findViewById(R.id.scrollView);
		this.linearLayout = (LinearLayout) getView().findViewById(R.id.mainLinearLayout);
		this.mainProgressBar = (ProgressBar) getView().findViewById(R.id.mainProgressBar);

		gridView = new ExpandableHeightGridView(getActivity().getApplicationContext());
		gridView.setBackgroundColor(Color.TRANSPARENT);
		gridView.setColumnWidth((int) px);
		gridView.setNumColumns(GridView.AUTO_FIT);
		gridView.setBackgroundColor(Color.TRANSPARENT);
		gridView.setExpanded(true);

		Display display = getActivity().getWindowManager().getDefaultDisplay();
		Point size = new Point();
		display.getSize(size);
		float width = size.x;
		float height = width * 0.1388f;

		topAds = new AdsImageView(getActivity().getApplicationContext());
		LinearLayout.LayoutParams tempLayout = new LinearLayout.LayoutParams(LinearLayout.LayoutParams.WRAP_CONTENT,
				Math.round(height));
		tempLayout.setMargins(0, 0, 0, 40);
		linearLayout.addView(topAds, 0, tempLayout);
		linearLayout.addView(gridView);

		bottomAds = new AdsImageView(getActivity().getApplicationContext());
		LinearLayout.LayoutParams tempLayout2 = new LinearLayout.LayoutParams(LinearLayout.LayoutParams.WRAP_CONTENT,
				Math.round(height));
		tempLayout2.setMargins(0, 20, 0, 0);
		linearLayout.addView(bottomAds, tempLayout2);

		if (isLoading)
			return;

		if (json != null) {
			dataReceived();
			return;
		}

		obtainData();
	}

	@Override
	public void onDestroyView() {
		// TODO Auto-generated method stub
		super.onDestroyView();
		jsonAsync.cancel(true);
	}

	private void obtainData() {
		if (getActivity() instanceof KiosqueActivity) {
			LinearLayout parentCategoryLayout = ((KiosqueActivity) getActivity()).getCategoryParent();
			if (parentCategoryLayout != null && parentCategoryLayout.getChildCount() > 4) {
				parentCategoryLayout.removeViewAt(3);
				parentCategoryLayout.removeViewAt(2);
			}
		}
		jsonAsync = new JSONParse();
		jsonAsync.execute();
	}

	public void dataReceived() {
		try {
			JSONObject data = json.getJSONObject("data");
			JSONArray publicationsArray = data.getJSONArray("publications");
			JSONObject topAdsObject = data.getJSONObject("topPub");
			JSONObject bottomAdsObject = data.getJSONObject("bottomPub");
			countriesList.clear();
			if (publicationsArray.length() > 0)
				countriesList.add("Tous les pays");

			for (int i = 0; i < publicationsArray.length(); i++) {

				JSONObject c = publicationsArray.getJSONObject(i);
				EditionModelClass temp = new EditionModelClass();

				temp.nom = c.getString("nom");
				temp.pays_nom = c.getString("pays_nom");
				countriesList.add(temp.pays_nom);
				temp.type = c.getString("type");
				temp.categorie = c.getString("categorie");

				temp.id = Integer.parseInt(c.getString("id"));
				temp.id_journal = c.getString("id_journal");
				try {
					SimpleDateFormat format = new SimpleDateFormat("yyyy-MM-dd");
					Date date = format.parse(c.getString("datePublication"));
					temp.datePublication = date.getTime();
				} catch (ParseException e) {
					// e.printStackTrace();
					temp.datePublication = 0;
				}

				temp.downloadPath = c.getString("downloadPath");
				temp.coverPath = c.getString("coverPath");
				temp.prix = c.getString("prix");
				temp.telechargementRestant = c.getString("telechargementRestant");
				temp.isSubscription = c.getInt("isSubscription");

				templist.add(temp);
			}
			removeDuplicateFromCountriesList();

			if (topAdsObject.getString("image").equals("")) {
				topAds.setVisibility(View.GONE);
			} else {
				if (topAdsBitmap != null) {
					topAds.adsBitmap = topAdsBitmap;
				}

				if (topAdsObject.getString("url").equals("")) {
					topAds.setUrlString(topAdsObject.getString("image"), "");
				} else {
					topAds.setUrlString(topAdsObject.getString("image"), topAdsObject.getString("url"));
				}
			}

			if (bottomAdsObject.getString("image").equals("")) {
				bottomAds.setVisibility(View.GONE);
			} else {
				if (bottomAdsBitmap != null) {
					bottomAds.adsBitmap = bottomAdsBitmap;
				}

				if (bottomAdsObject.getString("url").equals("")) {
					bottomAds.setUrlString(bottomAdsObject.getString("image"), "");
				} else {
					bottomAds.setUrlString(bottomAdsObject.getString("image"), bottomAdsObject.getString("url"));
				}
			}

		} catch (JSONException e) {
			e.printStackTrace();
		}

		if (countriesList.size() > 0) {
			String[] array = new String[countriesList.size()];
			countriesList.toArray(array);
			createAndInitializeCountryFilterSpinner(array);
		}

		list.addAll(templist);
		sortListView(0);

		gridView.setOnItemClickListener(new OnItemClickListener() {

			@Override
			public void onItemClick(AdapterView<?> parent, View view, int position, long id) {
				// TODO Auto-generated method stub

				Intent intent = new Intent(gridView.getContext().getApplicationContext(),
						KioskEditionDetailActivity.class).setFlags(Intent.FLAG_ACTIVITY_NEW_TASK);

				EditionModelClass selectedEdition = templist.get(position);
				Bundle b = new Bundle();
				b.putInt("id_edition", selectedEdition.id);
				intent.putExtras(b); // Put your id to your next Intent
				gridView.getContext().getApplicationContext().startActivity(intent);
			}
		});

	}

	private class JSONParse extends AsyncTask<String, String, JSONObject> {

		@Override
		protected void onPreExecute() {
			super.onPreExecute();
			isLoading = true;
		}

		@Override
		protected JSONObject doInBackground(String... args) {
			JSONParser jParser = new JSONParser();
			// Getting JSON from URL
			if (isCancelled()) {
				return null;
			}

			Log.v("url publication", urlToLoad);

			JSONObject json2 = jParser.getJSONFromUrl(urlToLoad);

			if (isCancelled()) {
				return null;
			}
			return json2;
		}

		@Override
		protected void onPostExecute(JSONObject _json) {
			isLoading = false;

			if (isCancelled()) {
				return;
			}

			if (_json == null) {
				return;
			}

			json = _json;
			dataReceived();
			setContentShown(true);
			mainProgressBar.setVisibility(View.GONE);
		}
	}

	public class ExpandableHeightGridView extends GridView {

		boolean expanded = false;

		public ExpandableHeightGridView(Context context) {
			super(context);
		}

		public ExpandableHeightGridView(Context context, AttributeSet attrs) {
			super(context, attrs);
		}

		public ExpandableHeightGridView(Context context, AttributeSet attrs, int defStyle) {
			super(context, attrs, defStyle);
		}

		public boolean isExpanded() {
			return expanded;
		}

		@Override
		public void onMeasure(int widthMeasureSpec, int heightMeasureSpec) {
			// HACK! TAKE THAT ANDROID!
			if (isExpanded()) {
				// Calculate entire height by providing a very large height
				// hint.
				// But do not use the highest 2 bits of this integer; those are
				// reserved for the MeasureSpec mode.
				int expandSpec = MeasureSpec.makeMeasureSpec(Integer.MAX_VALUE >> 2, MeasureSpec.AT_MOST);
				super.onMeasure(widthMeasureSpec, expandSpec);

				ViewGroup.LayoutParams params = getLayoutParams();
				params.height = getMeasuredHeight();
			} else {
				super.onMeasure(widthMeasureSpec, heightMeasureSpec);
			}
		}

		public void setExpanded(boolean expanded) {
			this.expanded = expanded;
		}
	}

	/**
	 * Method will sort list according to Spinner Item Selected
	 * 
	 * @param spinnerPosition
	 */
	private void sortListView(int spinnerPosition) {
		if (list.size() == 0)
			return;
		templist.clear();

		if (spinnerPosition == 0) {
			templist.addAll(list);
		} else {
			for (int i = 0; i < list.size(); i++) {
				if (list.get(i).pays_nom.equals(countriesList.get(spinnerPosition))) {
					templist.add(list.get(i));
				}
			}
		}
		adapter = new KioskArrayAdapter(gridView.getContext(), android.R.layout.simple_list_item_1, templist, false,
				false);

		gridView.setAdapter(adapter);

	}

	private void createAndInitializeCountryFilterSpinner(String[] entries) {

		if (countriesSpinner == null) {
			countriesSpinner = new IcsSpinner(getActivity(), null, R.attr.actionDropDownStyle);

			countriesSpinner.setBackground(null);
			TextView paysTv = new TextView(getActivity());
			paysTv.setText("Pays");
			paysTv.setTextSize(TypedValue.COMPLEX_UNIT_SP, 12);
			((KiosqueActivity) getActivity()).getCategoryParent().addView(paysTv, 2,
					new LinearLayout.LayoutParams(LayoutParams.WRAP_CONTENT, LayoutParams.WRAP_CONTENT));
			((KiosqueActivity) getActivity()).getCategoryParent().addView(countriesSpinner, 3,
					new LinearLayout.LayoutParams(LayoutParams.WRAP_CONTENT, LayoutParams.WRAP_CONTENT));
			// Fahad
			// IcsLinearLayout ab = (IcsLinearLayout)
			// getActivity().getActionBar()
			// .getCustomView();
			// ab.addView(countriesSpinner);
		}
		ArrayAdapter<String> adapter = new ArrayAdapter<String>(getActivity(), R.layout.spinner_dropdown_item, entries);
		adapter.setDropDownViewResource(R.layout.spinner_dropdown_item);
		// create ICS spinner

		countriesSpinner.setAdapter(adapter);
		countriesSpinner.setOnItemSelectedListener(new IcsAdapterView.OnItemSelectedListener() {

			@Override
			public void onItemSelected(IcsAdapterView<?> parent, View view, int position, long id) {
				sortListView(position);

			}

			@Override
			public void onNothingSelected(IcsAdapterView<?> parent) {
				// TODO Auto-generated method stub

			}
		});

	}
}
