package com.ngser.ekiosk;

import java.lang.ref.WeakReference;
import java.util.ArrayList;

import com.ngser.ekiosk.Model.EditionModelClass;
import com.ngser.ekiosk.kiosque.NetworkedCacheableImageView;
import com.thbs.progressbutton.MasterLayout;

import android.content.Context;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ArrayAdapter;
import android.widget.ImageView;
import android.widget.ProgressBar;
import uk.co.senab.bitmapcache.CacheableBitmapDrawable;

public class BibliothequeArrayAdapter extends ArrayAdapter<EditionModelClass> {

	private int page_index = 0;

	public BibliothequeArrayAdapter(Context context, int textViewResourceId, ArrayList<EditionModelClass> items) {
		super(context, textViewResourceId, items);
	}

	public void setTabPosition(int position) {
		page_index = position;
	}

	public View getView(int position, View convertView, ViewGroup parent) {

		if (null == convertView) {
			LayoutInflater inflater = (LayoutInflater) getContext().getSystemService(Context.LAYOUT_INFLATER_SERVICE);
			convertView = inflater.inflate(R.layout.bibliotheque_edition_cell, null);
		}

		NetworkedCacheableImageView imageView = (NetworkedCacheableImageView) convertView.findViewById(R.id.nciv_pug);

		ProgressBar progressBar = (ProgressBar) convertView.findViewById(R.id.progressBar1);

		MasterLayout masterLayout = (MasterLayout) convertView.findViewById(R.id.MasterLayout01);
		ImageView imageView1 = (ImageView) convertView.findViewById(R.id.imageView1);
		ImageView imageView2 = (ImageView) convertView.findViewById(R.id.imageView2);
		ImageView iv_mark = (ImageView) convertView.findViewById(R.id.imageView3);

		EditionModelClass item = getItem(position);

		final boolean fromCache = imageView.loadImage(item.coverPath, false, new ImageLoadedListener(progressBar));

		if (fromCache) {
			progressBar.setVisibility(View.GONE);
		} else {
			progressBar.setVisibility(View.VISIBLE);
		}

		if (item.openDate == 0) {
			imageView1.setVisibility(View.VISIBLE);
		} else {
			imageView1.setVisibility(View.GONE);
		}

		if (page_index == 3) {
			imageView1.setVisibility(View.GONE);
			if (iv_mark != null)
				iv_mark.setVisibility(View.VISIBLE);
		} else {
			if (iv_mark != null)
				iv_mark.setVisibility(View.GONE);
		}

		if (item.localpath == null) {
			masterLayout.setVisibility(View.VISIBLE);
			masterLayout.animation();
		} else {
			masterLayout.setVisibility(View.GONE);
		}

		if (item.favoris.equals("1")) {
			imageView2.setVisibility(View.VISIBLE);
		} else {
			imageView2.setVisibility(View.GONE);
		}

		return convertView;
	}

	static class ImageLoadedListener implements NetworkedCacheableImageView.OnImageLoadedListener {
		private final WeakReference<ProgressBar> mProgressBarRef;

		public ImageLoadedListener(ProgressBar pb) {
			mProgressBarRef = new WeakReference<ProgressBar>(pb);
		}

		@Override
		public void onImageLoaded(CacheableBitmapDrawable result) {
			final ProgressBar pb = mProgressBarRef.get();

			if (pb == null) {
				return;
			}
			pb.setVisibility(View.GONE);
		}
	}

}
