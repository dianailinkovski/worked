package com.ngser.ekiosk;

import java.io.BufferedOutputStream;
import java.io.File;
import java.io.FileInputStream;
import java.io.FileOutputStream;
import java.util.zip.ZipEntry;
import java.util.zip.ZipInputStream;

import android.util.Log;



public class Decompress {
	private String _zipFile; 
	  private String _location; 
	 
	  public Decompress(String zipFile, String location) { 
	    _zipFile = zipFile; 
	    _location = location; 
	 
	    _dirChecker(""); 
	  } 
	 
	  public Boolean unzip() { 
	    try  { 
	      FileInputStream fin = new FileInputStream(_zipFile); 
	      ZipInputStream zin = new ZipInputStream(fin); 
	      ZipEntry ze = null; 
	      while ((ze = zin.getNextEntry()) != null) { 
	        Log.v("Decompress", "Unzipping " + ze.getName()); 
	 
	        if(ze.isDirectory()) { 
	        	_dirChecker(ze.getName()); 
	        } else { 
	        
	          byte[] buffer = new byte[2048];

	          FileOutputStream fout = new FileOutputStream(_location + ze.getName());
	          BufferedOutputStream bos = new BufferedOutputStream(fout, buffer.length);

	          int c = 0;
	          int size;

	           while ((size = zin.read(buffer, 0, buffer.length)) != -1) {
                   bos.write(buffer, 0, size);
               }
                   //Close up shop..
               bos.flush();
               bos.close();

               fout.flush();
               fout.close();
               zin.closeEntry();
               
	        } 
	         
	      } 
	      zin.close(); 
	    } catch(Exception e) { 
	      Log.e("Decompress", "unzip", e);
	      return false;
	    } 
	    Log.i("unzip", "closed");
	    return true;
	  }
	 
	  private void _dirChecker(String dir) { 
	    File f = new File(_location + dir); 
	 
	    if(!f.isDirectory()) { 
	      f.mkdirs(); 
	    } 
	  } 
}
