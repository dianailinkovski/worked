package com.ngser.ekiosk.Model;

import java.io.Serializable;

import org.json.JSONException;
import org.json.JSONObject;

@SuppressWarnings("serial")
public class PackagesModelClass implements Serializable {
    
    public String id;
    public String nom;
    public String google;
    public String quantite;
    public String prix_usd;
    public String equivalent;
    public String bonis;
    
    public PackagesModelClass() {
    	this.id = null;
		this.nom = null;
    	this.google = null;
    	this.quantite = null;
    	this.prix_usd = null;
    	this.equivalent = null;
    	this.bonis = null;
    }

    // constructor
    public PackagesModelClass(JSONObject json) {
    	try {
			this.id = json.getString("id");
			this.nom = json.getString("nom");
	    	this.google = json.getString("google");
	    	this.quantite = json.getString("quantite");
	    	this.prix_usd = json.getString("prix_usd");
	    	this.equivalent = json.getString("equivalent");
	    	this.bonis = json.getString("bonis");
		} catch (JSONException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		}
    	
    }
    
 // constructor
    public PackagesModelClass(String _id, String _nom, String _google, String _quantite, 
    		String _prix_usd, String _equivalent, String _bonis) {
    	this.id = _id;
		this.nom = _nom;
		this.google = _google;
		this.quantite = _quantite;
		this.prix_usd = _prix_usd;
		this.equivalent = _equivalent;
		this.bonis = _bonis;
    }
    
    // getting ID
    public String getId() {
    	return this.id;
    }

    // setting id
    public void setId(String tempId) {
    	this.id = tempId;
    }
    
    // getting ID
    public String getNom() {
    	return this.nom;
    }

    // setting id
    public void setNom(String tempNom) {
    	this.nom = tempNom;
    }
    
    // getting ID
    public String getGoogle() {
    	return this.google;
    }

    // setting id
    public void setGoogle(String tempGoogle) {
    	this.google = tempGoogle;
    }

    // getting ID
    public String getQuantite() {
    	return this.quantite;
    }

    // setting id
    public void setQuantite(String tempQuantite) {
    	this.quantite = tempQuantite;
    }
    
    // getting ID
    public String getPrix_usd() {
    	return this.prix_usd;
    }

    // setting id
    public void setPrix_usd(String tempPrix_usd) {
    	this.prix_usd = tempPrix_usd;
    }
    
    // getting ID
    public String getEquivalent() {
    	return this.equivalent;
    }

    // setting id
    public void setEquivalent(String tempEquivalent) {
    	this.equivalent = tempEquivalent;
    }
    
    // getting ID
    public String getBonis() {
    	return this.bonis;
    }

    // setting id
    public void setBonis(String tempBonis) {
    	this.bonis = tempBonis;
    }
    
}