package com.ngser.ekiosk.kiosque;

import java.util.ArrayList;
import java.util.LinkedHashMap;

import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

import com.devspark.progressfragment.ProgressListFragment;
import com.ngser.ekiosk.R;
import com.ngser.ekiosk.AsyncTask.JSONParser;
import com.ngser.ekiosk.Model.JournalModelClass;
import com.ngser.ekiosk.kiosque.SectionedGridViewAdapter.OnGridItemClickListener;

import android.content.Intent;
import android.content.SharedPreferences;
import android.content.res.Resources;
import android.database.Cursor;
import android.graphics.Color;
import android.graphics.Point;
import android.os.AsyncTask;
import android.os.Bundle;
import android.util.Log;
import android.util.TypedValue;
import android.view.Display;
import android.view.View;
import android.view.ViewTreeObserver.OnGlobalLayoutListener;

public class ArchivesJournauxGridView extends ProgressListFragment {

	private int position;
	ArrayList<String> categorieList;
	ArrayList<ArrayList<JournalModelClass>> templist;
	private JSONParse jsonAsync;
	final String PREFS_NAME = "eKioskPrefSetting";

	private ArchivesDataset dataSet;
	private SectionedGridViewAdapter adapter = null;
	private LinkedHashMap<String, Cursor> cursorMap;
	// private Context mContext;

	public static ArchivesJournauxGridView newInstance() {
		ArchivesJournauxGridView fragment = new ArchivesJournauxGridView();
		return fragment;
	}

	@Override
	public void onCreate(Bundle savedInstanceState) {
		// TODO Auto-generated method stub
		Log.v("onCreate", "onCreate");
		super.onCreate(savedInstanceState);

	}

	@Override
	public void onDestroyView() {
		// TODO Auto-generated method stub
		super.onDestroyView();
		jsonAsync.cancel(true);
	}

	@Override
	public void onViewCreated(View view, Bundle savedInstanceState) {
		super.onViewCreated(view, savedInstanceState);
		Resources r = getResources();
		float px = TypedValue.applyDimension(TypedValue.COMPLEX_UNIT_DIP, 100, r.getDisplayMetrics());

		setEmptyText("Aucune publication trouvée");

		getListView().setBackgroundColor(Color.TRANSPARENT);
		getListView().setDividerHeight(0);

		obtainData();
	}

	private void obtainData() {
		jsonAsync = new JSONParse();
		jsonAsync.execute();
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

			SharedPreferences settings = getActivity().getApplicationContext().getSharedPreferences(PREFS_NAME, 0);
			String username = settings.getString("username", "");
			String password = settings.getString("password", "");

			StringBuilder strBuilder = new StringBuilder("http://api.ngser.gnetix.com/v1.1/getJournauxArchive.php?");
			strBuilder.append("username=");
			strBuilder.append(username);
			strBuilder.append("&password=");
			strBuilder.append(password);
			String url = strBuilder.toString();
			Log.v("url archives", url);

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
				JSONArray data = json.getJSONArray("data");
				Log.d("resultat", resultat);
				Log.d("data", json.getString("data"));

				templist = new ArrayList<ArrayList<JournalModelClass>>();
				categorieList = new ArrayList<String>();

				for (int x = 0; x < data.length(); x++) {

					JSONObject tempArray = data.getJSONObject(x);
					String categorie = tempArray.getString("categorie");
					categorieList.add(categorie);
					JSONArray journauxArray = tempArray.getJSONArray("journaux");

					ArrayList<JournalModelClass> test = new ArrayList<JournalModelClass>();

					for (int i = 0; i < journauxArray.length(); i++) {
						if (isCancelled()) {
							return;
						}

						JSONObject c = journauxArray.getJSONObject(i);

						JournalModelClass temp = new JournalModelClass();

						temp.id = c.getString("id");
						temp.nom = c.getString("nom");
						temp.isSubscription = c.getString("isSubscription");
						temp.image = c.getString("image");
						temp.image = temp.image.substring(0, temp.image.length() - 4) + "_a.jpg";

						test.add(temp);
					}
					templist.add(test);
				}

			} catch (JSONException e) {
				e.printStackTrace();
			}

			if (isCancelled()) {
				return;
			}

			// Log.e("size templist", String.valueOf(templist.size()));

			dataReceived();

		}
	}

	public void dataReceived() {

		dataSet = new ArchivesDataset();
		if (categorieList != null) {
			for (int i = 0; i < categorieList.size(); i++) {
				dataSet.addSection(categorieList.get(i), templist.get(i).size());
				dataSet.addSectionCursor(categorieList.get(i), templist.get(i));
			}
		}

		cursorMap = dataSet.getSectionCursorMap();

		setListShown(true);

		getListView().getViewTreeObserver().addOnGlobalLayoutListener(new OnGlobalLayoutListener() {

			@Override
			public void onGlobalLayout() {
				getListView().getViewTreeObserver().removeGlobalOnLayoutListener(this);

				// now check the width of the list view
				Display display = getActivity().getWindowManager().getDefaultDisplay();
				Point size = new Point();
				display.getSize(size);
				int width = size.x;
				// int width = getListView().getWidth();
				Log.e("width = ", String.valueOf(width));
				adapter = new SectionedGridViewAdapter(getListView().getContext(), cursorMap, size.x, size.y,
						getResources().getDimensionPixelSize(R.dimen.grid_item_size),
						getResources().getDimensionPixelSize(R.dimen.grid_item_size));
						//getResources().getDimensionPixelSize(R.dimen.grid_item_size_width));

				adapter.setListener(new OnGridItemClickListener() {

					@Override
					public void onGridItemClicked(String sectionName, int position, View v) {

						Cursor sectionCursor = cursorMap.get(sectionName);
						if (sectionCursor.moveToPosition(position)) {

							try {
								JSONObject jObj = new JSONObject(sectionCursor.getString(0));
								String dataId = jObj.getString("id");

								Intent intent = new Intent(getListView().getContext(), ArchivesMoisActivity.class)
										.setFlags(Intent.FLAG_ACTIVITY_NEW_TASK);

								// EditionModelClass selectedEdition =
								// templist.get(position);
								Bundle b = new Bundle();
								b.putInt("id_journal", Integer.valueOf(dataId));

								intent.putExtras(b); // Put your id to your next
														// Intent
								getListView().getContext().startActivity(intent);

							} catch (JSONException e) {
								// TODO Auto-generated catch block
								e.printStackTrace();
							}

						}
					}
				});
				getListView().setAdapter(adapter);

			}
		});
	}
}
