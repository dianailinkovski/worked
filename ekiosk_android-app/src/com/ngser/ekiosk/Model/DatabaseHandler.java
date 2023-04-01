package com.ngser.ekiosk.Model;

import java.util.ArrayList;

import android.content.ContentValues;
import android.content.Context;
import android.database.Cursor;
import android.database.sqlite.SQLiteDatabase;
import android.database.sqlite.SQLiteOpenHelper;
import android.util.Log;

public class DatabaseHandler extends SQLiteOpenHelper {

    // All Static variables
    // Database Version
    private static final int DATABASE_VERSION = 1;

    // Database Name
    private static final String DATABASE_NAME = "NGSERManager";

    // Contacts table name
    private static final String TABLE_EDITIONS = "editions";

    // Contacts Table Columns names
    private static final String KEY_NOM = "nom";
    private static final String KEY_TYPE = "type";
    private static final String KEY_CATEGORIE = "categorie";
    
    private static final String KEY_ID = "id";
    private static final String KEY_ID_JOURNAL = "id_journal";
    private static final String KEY_DATEPUBLICATION = "datePublication";
    private static final String KEY_DOWNLOADPATH = "downloadPath";
    private static final String KEY_COVERPATH = "coverPath";
    private static final String KEY_PRIX = "prix";
    private static final String KEY_BOUGHT = "bought";
    
    private static final String KEY_LOCALPATH = "localpath";
    private static final String KEY_DOWNLOADDATE = "downloadDate";
    private static final String KEY_OPENDATE = "openDate";
    private static final String KEY_FAVORIS = "favoris";
    private static final String KEY_ISSUBSCRIPTION = "subscription";
    
    private final ArrayList<EditionModelClass> editions_list = new ArrayList<EditionModelClass>();

    public DatabaseHandler(Context context) {
    	super(context, DATABASE_NAME, null, DATABASE_VERSION);
    }

    // Creating Tables
    @Override
    public void onCreate(SQLiteDatabase db) {
		String CREATE_CONTACTS_TABLE = "CREATE TABLE " + TABLE_EDITIONS + "("
			+ KEY_ID + " INTEGER PRIMARY KEY,"
			+ KEY_ID_JOURNAL + " TEXT," 
			+ KEY_NOM + " TEXT," 
			+ KEY_TYPE + " TEXT," 
			+ KEY_CATEGORIE + " TEXT," 
			+ KEY_DATEPUBLICATION + " TEXT," 
			+ KEY_DOWNLOADPATH + " TEXT," 
			+ KEY_COVERPATH + " TEXT," 
			+ KEY_PRIX + " TEXT," 
			+ KEY_BOUGHT + " TEXT," 
			+ KEY_LOCALPATH + " TEXT,"
			+ KEY_DOWNLOADDATE + " DATE,"
			+ KEY_OPENDATE + " DATE,"
			+ KEY_FAVORIS + " TEXT,"
			+ KEY_ISSUBSCRIPTION + " INTEGER"
			+ ")";
		db.execSQL(CREATE_CONTACTS_TABLE);
    }

    // Upgrading database
    @Override
    public void onUpgrade(SQLiteDatabase db, int oldVersion, int newVersion) {
		// Drop older table if existed
		db.execSQL("DROP TABLE IF EXISTS " + TABLE_EDITIONS);
		
		// Create tables again
		onCreate(db);
    }

    /**
     * All CRUD(Create, Read, Update, Delete) Operations
     */

    // Adding new contact
    public void Add_Edition(EditionModelClass edition) {
		SQLiteDatabase db = this.getWritableDatabase();
		ContentValues values = new ContentValues();
		values.put(KEY_ID, edition.getId());
		values.put(KEY_ID_JOURNAL, edition.getId_journal()); 
		values.put(KEY_NOM, edition.getNom());
		values.put(KEY_TYPE, edition.getType());
		values.put(KEY_CATEGORIE, edition.getCategorie());
		values.put(KEY_DATEPUBLICATION, edition.getDatePublication());
		values.put(KEY_DOWNLOADPATH, edition.getDownloadPath());
		values.put(KEY_COVERPATH, edition.getCoverPath());
		values.put(KEY_PRIX, edition.getPrix());
		values.put(KEY_BOUGHT, edition.getBought());
		values.put(KEY_LOCALPATH, edition.getLocalPath());
		values.put(KEY_DOWNLOADDATE, edition.getDownloadDate());
		values.put(KEY_OPENDATE, edition.getOpenDate());
		values.put(KEY_FAVORIS, edition.getFavoris());
		values.put(KEY_ISSUBSCRIPTION, edition.getIsSubscription());
		
		// Inserting Row
		db.insert(TABLE_EDITIONS, null, values);
		db.close(); // Closing database connection
    }

    // Getting single contact
    public EditionModelClass Get_Edition(int id) {
		SQLiteDatabase db = this.getReadableDatabase();
	
		Cursor cursor = db.query(
				TABLE_EDITIONS, 
				new String[] { KEY_ID, KEY_ID_JOURNAL, KEY_NOM, KEY_TYPE, KEY_CATEGORIE, KEY_DATEPUBLICATION, KEY_DOWNLOADPATH, KEY_COVERPATH, KEY_PRIX, KEY_BOUGHT, KEY_LOCALPATH, KEY_DOWNLOADDATE, KEY_OPENDATE, KEY_FAVORIS, KEY_ISSUBSCRIPTION }, 
				KEY_ID + "=?",
				new String[] { String.valueOf(id) }, 
				null, null, null, null);
		
		EditionModelClass contact = null;
		
		if (cursor != null) {
			Boolean valide = cursor.moveToFirst();
			if (valide) {
				contact = new EditionModelClass(
						Integer.parseInt(cursor.getString(0)), 
						cursor.getString(1), 
						cursor.getString(2), 
						cursor.getString(2),
						cursor.getString(3),
						cursor.getString(4),
						Long.parseLong(cursor.getString(5)),
						cursor.getString(6),
						cursor.getString(7),
						cursor.getString(8),
						cursor.getString(9),
						cursor.getString(10),
						Long.parseLong(cursor.getString(11)),
						Long.parseLong(cursor.getString(12)),
						cursor.getString(13),
						Integer.parseInt(cursor.getString(14))
					);				
			}
			
		}
			
		// return contact
		cursor.close();
		db.close();
	
		return contact;
    }

    // Getting All Contacts
    public ArrayList<EditionModelClass> Get_Editions_Favoris() {
		try {
			editions_list.clear();
	
		    // Select All Query
		    String selectQuery = "SELECT  * FROM " + TABLE_EDITIONS + " WHERE " + KEY_FAVORIS + "= '1'";
	
		    SQLiteDatabase db = this.getReadableDatabase();
		    Cursor cursor = db.rawQuery(selectQuery, null);
	
		    // looping through all rows and adding to list
		    if (cursor.moveToFirst()) {
				do {
				    EditionModelClass edition = new EditionModelClass();
				    edition.setId(Integer.parseInt(cursor.getString(0)));
				    edition.setId_journal(cursor.getString(1));
				    edition.setNom(cursor.getString(2));
				    edition.setType(cursor.getString(3));
				    edition.setCategorie(cursor.getString(4));
				    edition.setDatePublication(Long.parseLong(cursor.getString(5)));
				    edition.setDownloadPath(cursor.getString(6));
				    edition.setCoverPath(cursor.getString(7));
				    edition.setPrix(cursor.getString(8));
				    edition.setBought(cursor.getString(9));
				    edition.setLocalPath(cursor.getString(10));
				    edition.setDownloadDate(Long.parseLong(cursor.getString(11)));
				    edition.setOpenDate(Long.parseLong(cursor.getString(12)));
				    edition.setFavoris(cursor.getString(13));
				    edition.setIsSubscription(Integer.parseInt(cursor.getString(14)));
				    // Adding contact to list
				    editions_list.add(edition);
				} while (cursor.moveToNext());
		    }
	
		    // return contact list
		    cursor.close();
		    db.close();
		    return editions_list;
		} catch (Exception e) {
		    // TODO: handle exception
		    Log.e("all_contact", "" + e);
		}
	
		return editions_list;
    }
    
 // Getting All Contacts
    public ArrayList<EditionModelClass> Get_Editions_Recents(int jour) {
		try {
			editions_list.clear();
			
			long recentLimitDate = System.currentTimeMillis() ;
			//int jour = 7;
			recentLimitDate = recentLimitDate - (jour * 60 * 60 * 24 * 1000);
			
			Log.e("recentLimitDate", String.valueOf(recentLimitDate));
		    // Select All Query
		    String selectQuery = "SELECT  * FROM " + TABLE_EDITIONS + " WHERE " +  KEY_OPENDATE + " > " + String.valueOf(recentLimitDate) + " OR " + KEY_OPENDATE + " = 0";
		    
		    SQLiteDatabase db = this.getReadableDatabase();
		    Cursor cursor = db.rawQuery(selectQuery, null);
	
		    // looping through all rows and adding to list
		    if (cursor.moveToFirst()) {
				do {
				    EditionModelClass edition = new EditionModelClass();
				    edition.setId(Integer.parseInt(cursor.getString(0)));
				    edition.setId_journal(cursor.getString(1));
				    edition.setNom(cursor.getString(2));
				    edition.setType(cursor.getString(3));
				    edition.setCategorie(cursor.getString(4));
				    edition.setDatePublication(Long.parseLong(cursor.getString(5)));
				    edition.setDownloadPath(cursor.getString(6));
				    edition.setCoverPath(cursor.getString(7));
				    edition.setPrix(cursor.getString(8));
				    edition.setBought(cursor.getString(9));
				    edition.setLocalPath(cursor.getString(10));
				    edition.setDownloadDate(Long.parseLong(cursor.getString(11)));
				    edition.setOpenDate(Long.parseLong(cursor.getString(12)));
				    edition.setFavoris(cursor.getString(13));
				    edition.setIsSubscription(Integer.parseInt(cursor.getString(14)));
				    
				    // Adding contact to list
				    editions_list.add(edition);
				} while (cursor.moveToNext());
		    }
	
		    // return contact list
		    cursor.close();
		    db.close();
		    return editions_list;
		} catch (Exception e) {
		    // TODO: handle exception
		    Log.e("all_contact", "" + e);
		}
	
		return editions_list;
    }
    
    public ArrayList<EditionModelClass> Get_Editions_Abonnement() {
		try {
			editions_list.clear();			
			
		    // Select All Query
		    String selectQuery = "SELECT  * FROM " + TABLE_EDITIONS + " WHERE " +  KEY_ISSUBSCRIPTION + " = 1";
		    
		    SQLiteDatabase db = this.getReadableDatabase();
		    Cursor cursor = db.rawQuery(selectQuery, null);
	
		    // looping through all rows and adding to list
		    if (cursor.moveToFirst()) {
				do {
				    EditionModelClass edition = new EditionModelClass();
				    edition.setId(Integer.parseInt(cursor.getString(0)));
				    edition.setId_journal(cursor.getString(1));
				    edition.setNom(cursor.getString(2));
				    edition.setType(cursor.getString(3));
				    edition.setCategorie(cursor.getString(4));
				    edition.setDatePublication(Long.parseLong(cursor.getString(5)));
				    edition.setDownloadPath(cursor.getString(6));
				    edition.setCoverPath(cursor.getString(7));
				    edition.setPrix(cursor.getString(8));
				    edition.setBought(cursor.getString(9));
				    edition.setLocalPath(cursor.getString(10));
				    edition.setDownloadDate(Long.parseLong(cursor.getString(11)));
				    edition.setOpenDate(Long.parseLong(cursor.getString(12)));
				    edition.setFavoris(cursor.getString(13));
				    edition.setIsSubscription(Integer.parseInt(cursor.getString(14)));
				    
				    // Adding contact to list
				    editions_list.add(edition);
				} while (cursor.moveToNext());
		    }
	
		    // return contact list
		    cursor.close();
		    db.close();
		    return editions_list;
		} catch (Exception e) {
		    // TODO: handle exception
		    Log.e("all_contact", "" + e);
		}
	
		return editions_list;
    }
    
    // Getting All Contacts
    public ArrayList<EditionModelClass> Get_Editions() {
		try {
			editions_list.clear();
	
		    // Select All Query
		    String selectQuery = "SELECT  * FROM " + TABLE_EDITIONS;
	
		    SQLiteDatabase db = this.getReadableDatabase();
		    Cursor cursor = db.rawQuery(selectQuery, null);
	
		    // looping through all rows and adding to list
		    if (cursor.moveToFirst()) {
				do {
				    EditionModelClass edition = new EditionModelClass();
				    edition.setId(Integer.parseInt(cursor.getString(0)));
				    edition.setId_journal(cursor.getString(1));
				    edition.setNom(cursor.getString(2));
				    edition.setType(cursor.getString(3));
				    edition.setCategorie(cursor.getString(4));
				    edition.setDatePublication(Long.parseLong(cursor.getString(5)));
				    edition.setDownloadPath(cursor.getString(6));
				    edition.setCoverPath(cursor.getString(7));
				    edition.setPrix(cursor.getString(8));
				    edition.setBought(cursor.getString(9));
				    edition.setLocalPath(cursor.getString(10));
				    edition.setDownloadDate(Long.parseLong(cursor.getString(11)));
				    edition.setOpenDate(Long.parseLong(cursor.getString(12)));
				    edition.setFavoris(cursor.getString(13));
				    edition.setIsSubscription(Integer.parseInt(cursor.getString(14)));
				    
				    // Adding contact to list
				    editions_list.add(edition);
				} while (cursor.moveToNext());
		    }
	
		    // return contact list
		    cursor.close();
		    db.close();
		    return editions_list;
		} catch (Exception e) {
		    // TODO: handle exception
		    Log.e("all_contact", "" + e);
		}
	
		return editions_list;
    }
    
    public ArrayList<EditionModelClass> Get_EditionsWithJournal(String journal_id_ref) {
		try {
			editions_list.clear();
	
		    // Select All Query
		    String selectQuery = "SELECT  * FROM " + TABLE_EDITIONS + " WHERE " + KEY_ID_JOURNAL + " = '" + journal_id_ref + "'";
	
		    SQLiteDatabase db = this.getReadableDatabase();
		    Cursor cursor = db.rawQuery(selectQuery, null);
	
		    // looping through all rows and adding to list
		    if (cursor.moveToFirst()) {
				do {
				    EditionModelClass edition = new EditionModelClass();
				    edition.setId(Integer.parseInt(cursor.getString(0)));
				    edition.setId_journal(cursor.getString(1));
				    edition.setNom(cursor.getString(2));
				    edition.setType(cursor.getString(3));
				    edition.setCategorie(cursor.getString(4));
				    edition.setDatePublication(Long.parseLong(cursor.getString(5)));
				    edition.setDownloadPath(cursor.getString(6));
				    edition.setCoverPath(cursor.getString(7));
				    edition.setPrix(cursor.getString(8));
				    edition.setBought(cursor.getString(9));
				    edition.setLocalPath(cursor.getString(10));
				    edition.setDownloadDate(Long.parseLong(cursor.getString(11)));
				    edition.setOpenDate(Long.parseLong(cursor.getString(12)));
				    edition.setFavoris(cursor.getString(13));
				    edition.setIsSubscription(Integer.parseInt(cursor.getString(14)));
				    
				    // Adding contact to list
				    editions_list.add(edition);
				} while (cursor.moveToNext());
		    }
	
		    // return contact list
		    cursor.close();
		    db.close();
		    return editions_list;
		} catch (Exception e) {
		    // TODO: handle exception
		    Log.e("all_contact", "" + e);
		}
	
		return editions_list;
    }

    // Updating single contact
    public int Update_Edition(EditionModelClass edition) {
		SQLiteDatabase db = this.getWritableDatabase();
	
		ContentValues values = new ContentValues();
		values.put(KEY_ID, edition.getId());
		values.put(KEY_ID_JOURNAL, edition.getId_journal());
		values.put(KEY_NOM, edition.getNom());
		values.put(KEY_TYPE, edition.getType());
		values.put(KEY_CATEGORIE, edition.getCategorie());
		values.put(KEY_DATEPUBLICATION, edition.getDatePublication());
		values.put(KEY_DOWNLOADPATH, edition.getDownloadPath());
		values.put(KEY_COVERPATH, edition.getCoverPath());
		values.put(KEY_PRIX, edition.getPrix());
		values.put(KEY_BOUGHT, edition.getBought());
		values.put(KEY_LOCALPATH, edition.getLocalPath());
		values.put(KEY_DOWNLOADDATE, edition.getDownloadDate());
		values.put(KEY_OPENDATE, edition.getOpenDate());
		values.put(KEY_FAVORIS, edition.getFavoris());
		values.put(KEY_ISSUBSCRIPTION, edition.getIsSubscription());
		
		// updating row
		return db.update(TABLE_EDITIONS, values, KEY_ID + " = ?",
			new String[] { String.valueOf(edition.getId()) });
    }

    // Deleting single contact
    public void Delete_Edition(int id) {
		SQLiteDatabase db = this.getWritableDatabase();
		db.delete(TABLE_EDITIONS, KEY_ID + " = ?",
			new String[] { String.valueOf(id) });
		db.close();
    }
	
    // Getting contacts Count
    public int Get_Total_Contacts() {
		String countQuery = "SELECT  * FROM " + TABLE_EDITIONS;
		SQLiteDatabase db = this.getReadableDatabase();
		Cursor cursor = db.rawQuery(countQuery, null);
		int count = cursor.getCount();
		cursor.close();
				
		// return count
		return count;
    }
    
 // Getting All Contacts
    public ArrayList<EditionModelClass> Get_Editions_Supprimer_Apres(long jour, Boolean favoris) {
		try {
			editions_list.clear();
			
			//Log.e("jour = ", String.valueOf(jour));
			long recentLimitDate = System.currentTimeMillis() ;
			//Log.v("currentMillis = ", String.valueOf(recentLimitDate));
			long unixJour = (jour * 60 * 60 * 24 * 1000);
			//Log.v("unixJour = ", String.valueOf(unixJour));
			recentLimitDate -= unixJour;
			//Log.v("supprimerapres = ", String.valueOf(recentLimitDate));
			
		    // Select All Query
			StringBuilder sb = new StringBuilder();
			sb.append("SELECT  * FROM " + TABLE_EDITIONS + " WHERE " + KEY_DOWNLOADDATE + " != 0 AND " +  KEY_DOWNLOADDATE + " < " + String.valueOf(recentLimitDate));
			
			if (favoris) {
				sb.append(" AND " + KEY_FAVORIS + " != '1'");
			}
			
		    String selectQuery = sb.toString();
		    
		    
		    
		    SQLiteDatabase db = this.getReadableDatabase();
		    Cursor cursor = db.rawQuery(selectQuery, null);
		    
		    // looping through all rows and adding to list
		    if (cursor.moveToFirst()) {
				do {
				    EditionModelClass edition = new EditionModelClass();
				    edition.setId(Integer.parseInt(cursor.getString(0)));
				    edition.setId_journal(cursor.getString(1));
				    edition.setNom(cursor.getString(2));
				    edition.setType(cursor.getString(3));
				    edition.setCategorie(cursor.getString(4));
				    edition.setDatePublication(Long.parseLong(cursor.getString(5)));
				    edition.setDownloadPath(cursor.getString(6));
				    edition.setCoverPath(cursor.getString(7));
				    edition.setPrix(cursor.getString(8));
				    edition.setBought(cursor.getString(9));
				    edition.setLocalPath(cursor.getString(10));
				    edition.setDownloadDate(Long.parseLong(cursor.getString(11)));
				    edition.setOpenDate(Long.parseLong(cursor.getString(12)));
				    edition.setFavoris(cursor.getString(13));
				    edition.setIsSubscription(Integer.parseInt(cursor.getString(14)));
				    
				    // Adding contact to list
				    editions_list.add(edition);
				} while (cursor.moveToNext());
		    }
	
		    // return contact list
		    cursor.close();
		    db.close();
		    return editions_list;
		} catch (Exception e) {
		    // TODO: handle exception
		    Log.e("all_contact", "" + e);
		}
	
		return editions_list;
    }
    
    public ArrayList<EditionModelClass> Get_Editions_Last_X(int nbToGet, Boolean favoris) {
		try {
			editions_list.clear();
			
		    // Select All Query
			StringBuilder sb = new StringBuilder();
			sb.append("SELECT  * FROM " + TABLE_EDITIONS + " ");
			
			if (favoris) {
				sb.append(" WHERE " + KEY_FAVORIS + " != '1' ");
			}
			sb.append(" ORDER BY " + KEY_DOWNLOADDATE + " DESC LIMIT " + String.valueOf(nbToGet) + " ");
			
			
		    String selectQuery = sb.toString();
		    
		    SQLiteDatabase db = this.getReadableDatabase();
		    Cursor cursor = db.rawQuery(selectQuery, null);
		    
		    // looping through all rows and adding to list
		    if (cursor.moveToFirst()) {
				do {
				    EditionModelClass edition = new EditionModelClass();
				    edition.setId(Integer.parseInt(cursor.getString(0)));
				    edition.setId_journal(cursor.getString(1));
				    edition.setNom(cursor.getString(2));
				    edition.setType(cursor.getString(3));
				    edition.setCategorie(cursor.getString(4));
				    edition.setDatePublication(Long.parseLong(cursor.getString(5)));
				    edition.setDownloadPath(cursor.getString(6));
				    edition.setCoverPath(cursor.getString(7));
				    edition.setPrix(cursor.getString(8));
				    edition.setBought(cursor.getString(9));
				    edition.setLocalPath(cursor.getString(10));
				    edition.setDownloadDate(Long.parseLong(cursor.getString(11)));
				    edition.setOpenDate(Long.parseLong(cursor.getString(12)));
				    edition.setFavoris(cursor.getString(13));
				    edition.setIsSubscription(Integer.parseInt(cursor.getString(14)));
				    
				    // Adding contact to list
				    editions_list.add(edition);
				} while (cursor.moveToNext());
		    }
	
		    // return contact list
		    cursor.close();
		    db.close();
		    return editions_list;
		} catch (Exception e) {
		    // TODO: handle exception
		    Log.e("all_contact", "" + e);
		}
	
		return editions_list;
    }
    
}
