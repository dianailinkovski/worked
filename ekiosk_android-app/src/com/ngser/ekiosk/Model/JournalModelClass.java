package com.ngser.ekiosk.Model;

import java.io.Serializable;

import org.json.JSONException;
import org.json.JSONObject;

@SuppressWarnings("serial")
public class JournalModelClass implements Serializable {
    
    public String id;
    public String nom;
    public String image;
    public String isSubscription;
    //public String categorie;
    
    public JournalModelClass() {
    	this.id = null;
		this.nom = null;
    	this.image = null;
    	this.isSubscription = "0";
    	//this.categorie = null;
    }

    // constructor
    public JournalModelClass(JSONObject json) {
    	try {
			this.id = json.getString("id");
			this.nom = json.getString("nom");
	    	this.image = json.getString("image");
	    	this.isSubscription = json.getString("isSubscription");
		} catch (JSONException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		}
    	
    }
    
 // constructor
    public JournalModelClass(String _id, String _nom, String _image, String _categorie) {
    	this.id = _id;
		this.nom = _nom;
		this.image = _image;
		//this.categorie = _categorie;
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
    public String getImage() {
    	return this.image;
    }

    // setting id
    public void setImage(String tempImage) {
    	this.image = tempImage;
    }
    /*
 	// getting ID
    public String getCategorie() {
    	return this.categorie;
    }

    // setting id
    public void setCategorie(String tempCategorie) {
    	this.categorie = tempCategorie;
    }
    */
}