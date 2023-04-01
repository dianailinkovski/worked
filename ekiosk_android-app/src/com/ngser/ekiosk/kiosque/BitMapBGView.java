package com.ngser.ekiosk.kiosque;

import android.content.Context;
import android.content.res.Configuration;
import android.graphics.Bitmap;
import android.graphics.Canvas;
import android.util.Log;
import android.view.View;

public class BitMapBGView extends View {
	
	public Bitmap imageBitmap;
	private int mWidth;
	private int mHeight;
	
    public BitMapBGView(Context context) { 
        super(context); 
    } 
    
    @Override protected void onMeasure(int widthMeasureSpec, int heightMeasureSpec)
    {
        mWidth = View.MeasureSpec.getSize(widthMeasureSpec);
        mHeight = View.MeasureSpec.getSize(heightMeasureSpec);

        setMeasuredDimension(mWidth, mHeight);
    }
    
    @Override
    protected void onDraw(Canvas canvas) {
       super.onDraw(canvas);

       if(imageBitmap != null) {
    	   int cx = (mWidth - imageBitmap.getWidth()) / 2;
    	   int cy = (mHeight - imageBitmap.getHeight()) / 2;
    	   canvas.drawBitmap(imageBitmap, cx, cy, null); 
       }
    }
    
    @Override
    public void onConfigurationChanged(Configuration newConfig) {
        super.onConfigurationChanged(newConfig);
        imageBitmap = null;
        Log.e("view", "configurationdidchange");
    }
}
