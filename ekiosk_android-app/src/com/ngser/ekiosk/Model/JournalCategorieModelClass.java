package com.ngser.ekiosk.Model;

import java.util.ArrayList;


public class JournalCategorieModelClass {
	
	public String type;
	public ArrayList<JournalModelClass> journal;
	
	public JournalCategorieModelClass() {
		this.type = null;
		this.journal = new ArrayList<JournalModelClass>();
	}
}