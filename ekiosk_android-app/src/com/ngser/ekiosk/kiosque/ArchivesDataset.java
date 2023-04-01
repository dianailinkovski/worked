package com.ngser.ekiosk.kiosque;

import java.util.ArrayList;
import java.util.LinkedHashMap;

import org.json.JSONException;
import org.json.JSONObject;

import com.ngser.ekiosk.Model.JournalModelClass;

import android.database.Cursor;
import android.database.MatrixCursor;

public class ArchivesDataset {

	private LinkedHashMap<String, Integer> sectionItems = new LinkedHashMap<String, Integer>();

	public static final String DATA_COLUMN = "data";

	public static final int TYPE_DATA = 1;

	public static final String ITEM_PREFIX = "data-";

	public static final String[] COLUMNS = new String[] { DATA_COLUMN, "_id" };
	
	private static volatile int INDEX = 1;
	
	private LinkedHashMap<String, Cursor> sectionCursors = new LinkedHashMap<String, Cursor>();

	public void addSection(String sectionName, int numberOfItems) {
		sectionItems.put(sectionName, numberOfItems);
	}

	public Cursor getSectionCursor(String sectionName) {
		MatrixCursor cursor = (MatrixCursor) sectionCursors.get(sectionName);
		if( cursor == null) {
			cursor = new MatrixCursor(COLUMNS);
			int items = sectionItems.get(sectionName);

			// now add item rows
			for (int i = 0; i < items; i++) {
				cursor.addRow(new Object[] { sectionName + i , INDEX++ });
			}
			
			sectionCursors.put(sectionName, cursor);

		}
		
		return cursor;
	}
	
	public Cursor addSectionCursor(String sectionName, ArrayList<JournalModelClass> journaux) {
		
		MatrixCursor cursor = (MatrixCursor) sectionCursors.get(sectionName);
		if( cursor == null) {
			cursor = new MatrixCursor(COLUMNS);
			//int items = sectionItems.get(sectionName);

			// now add item rows
			for (int i = 0; i < journaux.size(); i++) {
				JSONObject jsonObj = new JSONObject();
				JournalModelClass temp = (JournalModelClass) journaux.get(i);
				try {
					jsonObj.put("id", temp.id);
					jsonObj.put("nom", temp.nom);
					jsonObj.put("image", temp.image);
					jsonObj.put("isSubscription", temp.isSubscription);
				} catch (JSONException e) {
					e.printStackTrace();
				}
				
				cursor.addRow(new Object[] { jsonObj.toString() , INDEX++ });
			}
			
			sectionCursors.put(sectionName, cursor);

		}
		
		return cursor;
	}
	
	
	public LinkedHashMap<String, Cursor> getSectionCursorMap() {
		if(sectionCursors.isEmpty()) {
			 for(String sectionName : sectionItems.keySet()) {
				 getSectionCursor(sectionName);
			 }
		}
		
		
		return sectionCursors;
		
	}

}
