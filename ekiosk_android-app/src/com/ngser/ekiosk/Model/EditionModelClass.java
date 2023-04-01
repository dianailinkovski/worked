package com.ngser.ekiosk.Model;

import java.io.Serializable;
import java.text.ParseException;
import java.text.SimpleDateFormat;
import java.util.Date;

import org.json.JSONException;
import org.json.JSONObject;

@SuppressWarnings("serial")
public class EditionModelClass implements Serializable {

	public String nom;
	public String pays_nom;
	public String type;
	public String categorie;

	public int id;
	public String id_journal;
	public long datePublication;
	public String downloadPath;
	public String coverPath;
	public String prix;

	public String localpath;
	public long downloadDate;
	public long openDate;
	public String favoris;

	public String bought;
	public String telechargementRestant;
	public int isSubscription;

	public EditionModelClass() {
		this.nom = null;
		this.pays_nom = null;
		this.type = null;
		this.categorie = null;

		this.id = 0;
		this.id_journal = null;
		this.datePublication = 0;
		this.downloadPath = null;
		this.coverPath = null;
		this.prix = null;
		this.bought = null;
		this.telechargementRestant = null;

		this.localpath = null;
		this.downloadDate = 0;
		this.openDate = 0;
		this.favoris = "0";
		this.isSubscription = 0;
	}

	// constructor
	public EditionModelClass(JSONObject json) {
		try {
			this.nom = json.getString("nom");
			this.pays_nom = json.getString("pays_nom");
			this.type = json.getString("type");
			this.categorie = json.getString("categorie");

			this.id = Integer.parseInt(json.getString("id"));
			this.id_journal = json.getString("id_journal");

			try {
				SimpleDateFormat format = new SimpleDateFormat("yyyy-MM-dd");
				Date date = format.parse(json.getString("datePublication"));
				this.datePublication = date.getTime();
			} catch (ParseException e) {
				// e.printStackTrace();
				this.datePublication = 0;
			}

			this.downloadPath = json.getString("downloadPath");
			this.coverPath = json.getString("coverPath");
			this.prix = json.getString("prix");
			// this.bought = json.getString("bought");
			this.telechargementRestant = json
					.getString("telechargementRestant");

			this.localpath = null;
			this.downloadDate = 0;
			this.openDate = 0;
			this.favoris = "0";
			this.isSubscription = json.getInt("isSubscription");

		} catch (JSONException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		}

	}

	// constructor
	public EditionModelClass(int _id, String _id_journal, String _nom,
			String _pays_nom, String _type, String _categorie,
			long _datePublication, String _downloadPath, String _coverPath,
			String _prix, String _bought, String _localpath,
			long _downloadDate, long _openDate, String _favoris,
			int isSubscription) {

		this.nom = _nom;
		this.pays_nom = _pays_nom;
		this.type = _type;
		this.categorie = _categorie;

		this.id = _id;
		this.id_journal = _id_journal;
		this.datePublication = _datePublication;
		this.downloadPath = _downloadPath;
		this.coverPath = _coverPath;
		this.prix = _prix;
		this.bought = _bought;

		this.localpath = _localpath;
		this.downloadDate = _downloadDate;
		this.openDate = _openDate;
		this.favoris = _favoris;
		this.isSubscription = isSubscription;
	}

	// getting ID
	public String getNom() {
		return this.nom;
	}

	public String getPayNom() {
		return this.pays_nom;
	}

	// setting id
	public void setNom(String tempNom) {
		this.nom = tempNom;
	}

	public void setpayNom(String tempPayNom) {
		this.pays_nom = tempPayNom;
	}

	// getting ID
	public String getType() {
		return this.type;
	}

	// setting id
	public void setType(String tempType) {
		this.type = tempType;
	}

	// getting ID
	public String getCategorie() {
		return this.categorie;
	}

	// setting id
	public void setCategorie(String tempCategorie) {
		this.categorie = tempCategorie;
	}

	// getting ID
	public int getId() {
		return this.id;
	}

	// setting id
	public void setId(int tempId) {
		this.id = tempId;
	}

	// getting ID
	public String getId_journal() {
		return this.id_journal;
	}

	// setting id
	public void setId_journal(String tempId_journal) {
		this.id_journal = tempId_journal;
	}

	// getting ID
	public long getDatePublication() {
		return this.datePublication;
	}

	// setting id
	public void setDatePublication(long tempDatePublication) {
		this.datePublication = tempDatePublication;
	}

	// getting ID
	public String getDownloadPath() {
		return this.downloadPath;
	}

	// setting id
	public void setDownloadPath(String tempDownloadPath) {
		this.downloadPath = tempDownloadPath;
	}

	// getting ID
	public String getCoverPath() {
		return this.coverPath;
	}

	// setting id
	public void setCoverPath(String tempCoverPath) {
		this.coverPath = tempCoverPath;
	}

	// getting ID
	public String getPrix() {
		return this.prix;
	}

	// setting id
	public void setPrix(String tempPrix) {
		this.prix = tempPrix;
	}

	// getting ID
	public String getBought() {
		return this.bought;
	}

	// setting id
	public void setBought(String tempBought) {
		this.bought = tempBought;
	}

	// getting ID
	public String getTelechargementRestant() {
		return this.telechargementRestant;
	}

	// setting id
	public void setTelechargementRestant(String tempTelechargementRestant) {
		this.telechargementRestant = tempTelechargementRestant;
	}

	// getting ID
	public String getLocalPath() {
		return this.localpath;
	}

	// setting id
	public void setLocalPath(String tempLocalpath) {
		this.localpath = tempLocalpath;
	}

	// getting ID
	public long getDownloadDate() {
		return this.downloadDate;
	}

	// setting id
	public void setDownloadDate(long tempDownloadDate) {
		this.downloadDate = tempDownloadDate;
	}

	// getting ID
	public long getOpenDate() {
		return this.openDate;
	}

	// setting id
	public void setOpenDate(long tempOpenDate) {
		this.openDate = tempOpenDate;
	}

	// getting ID
	public String getFavoris() {
		return this.favoris;
	}

	// setting id
	public void setFavoris(String tempFavoris) {
		this.favoris = tempFavoris;
	}

	public int getIsSubscription() {
		return this.isSubscription;
	}

	public void setIsSubscription(int isSubscription) {
		this.isSubscription = isSubscription;
	}
}