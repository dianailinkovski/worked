package com.ngser.ekiosk.kiosque;

import java.lang.ref.WeakReference;
import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Date;

import uk.co.senab.bitmapcache.CacheableBitmapDrawable;
import android.content.Context;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ArrayAdapter;
import android.widget.ImageView;
import android.widget.ProgressBar;
import android.widget.TextView;

import com.ngser.ekiosk.R;
import com.ngser.ekiosk.Model.EditionModelClass;
import com.squareup.picasso.Picasso;

public class KioskArrayAdapter extends ArrayAdapter<EditionModelClass> {

	boolean showDateBool;
	boolean showDayBool;
	private Context mContext;

	public KioskArrayAdapter(Context context, int textViewResourceId,
			ArrayList<EditionModelClass> items, Boolean showdate,
			Boolean showDay) {
		super(context, textViewResourceId, items);

		this.showDateBool = showdate;
		this.showDayBool = showDay;
		this.mContext = context;
	}

	public View getView(int position, View convertView, ViewGroup parent) {

		if (null == convertView) {
			LayoutInflater inflater = (LayoutInflater) getContext()
					.getApplicationContext().getSystemService(
							Context.LAYOUT_INFLATER_SERVICE);
			convertView = inflater.inflate(R.layout.edition_cell, null);
		}

		ImageView imageView = (ImageView) convertView
				.findViewById(R.id.nciv_pug);
		TextView textView = (TextView) convertView.findViewById(R.id.textView1);

		ImageView iv_mark = (ImageView) convertView.findViewById(R.id.iv_mark);
		iv_mark.setVisibility(View.GONE);

		EditionModelClass item = getItem(position);

		if (this.showDateBool) {
			if (showDayBool) {
				SimpleDateFormat sdf = new SimpleDateFormat("EEEE dd");
				Date netDate = (new Date(item.datePublication));
				textView.setText(sdf.format(netDate));
			} else {
				SimpleDateFormat sdf = new SimpleDateFormat("yyyy-MM-dd");
				Date netDate = (new Date(item.datePublication));
				textView.setText(sdf.format(netDate));
			}
		} else {
			textView.setText(item.nom);
		}

		Picasso.with(mContext).load(item.coverPath).into(imageView);

		if (item.isSubscription == 0) {
			iv_mark.setVisibility(View.GONE);
		} else if (item.isSubscription == 1) {
			iv_mark.setVisibility(View.VISIBLE);
		}

		return convertView;
	}

	static class ImageLoadedListener implements
			NetworkedCacheableImageView.OnImageLoadedListener {
		private final WeakReference<ProgressBar> mProgressBarRef;

		public ImageLoadedListener(ProgressBar pb) {
			mProgressBarRef = new WeakReference<ProgressBar>(pb);
		}

		@Override
		public void onImageLoaded(CacheableBitmapDrawable result) {
			if (mProgressBarRef.get() != null) {
				mProgressBarRef.get().setVisibility(View.GONE);
			}
		}
	}

}
