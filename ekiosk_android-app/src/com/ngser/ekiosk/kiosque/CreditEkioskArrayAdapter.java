package com.ngser.ekiosk.kiosque;

import java.util.ArrayList;

import android.content.Context;
import android.content.Intent;
import android.graphics.Bitmap;
import android.graphics.BitmapFactory;
import android.graphics.drawable.BitmapDrawable;
import android.support.v4.content.LocalBroadcastManager;
import android.view.LayoutInflater;
import android.view.View;
import android.view.View.OnClickListener;
import android.view.ViewGroup;
import android.widget.ArrayAdapter;
import android.widget.Button;
import android.widget.TextView;

import com.ngser.ekiosk.R;
import com.ngser.ekiosk.Model.PackagesModelClass;

public class CreditEkioskArrayAdapter extends ArrayAdapter<PackagesModelClass> {

	int AccountActivated = -1;
	private LayoutInflater inflater;
	private BitmapDrawable myIcon;

	public CreditEkioskArrayAdapter(Context context, int textViewResourceId,
			ArrayList<PackagesModelClass> items, int activatedAccount) {
		super(context, textViewResourceId, items);
		this.AccountActivated = activatedAccount;
		inflater = LayoutInflater.from(context);
		Bitmap bm = BitmapFactory.decodeResource(context.getResources(),
				R.drawable.big_ekcredit);
		myIcon = new BitmapDrawable(context.getResources(), bm);

	}

	public View getView(final int position, View convertView, ViewGroup parent) {
		if (null == convertView) {
			convertView = inflater.inflate(R.layout.credit_ekiosk_cell, parent, false);
		}
		TextView creditTV = (TextView) convertView.findViewById(R.id.creditTV);
		TextView equivalenceTV = (TextView) convertView
				.findViewById(R.id.equivalenceTV);
		TextView bonisTV = (TextView) convertView.findViewById(R.id.bonisTV);
		Button buyButton = (Button) convertView.findViewById(R.id.buyButton);
		buyButton.setOnClickListener(new OnClickListener() {

			@Override
			public void onClick(View v) {

				if (AccountActivated == 0) {

					Intent intent2 = new Intent(getContext(),
							ActivationActivity.class);
					intent2.addFlags(Intent.FLAG_ACTIVITY_NEW_TASK);
					getContext().startActivity(intent2);

					return;
				}

				PackagesModelClass packageModel = (PackagesModelClass) getItem(position);

				Intent intent = new Intent("buyingSKU");
				intent.putExtra("SKU", packageModel.google);
				intent.putExtra("prix", packageModel.prix_usd);
				intent.putExtra("quantite", packageModel.quantite);
				LocalBroadcastManager.getInstance(getContext()).sendBroadcast(
						intent);

			}
		});

		PackagesModelClass item = getItem(position);

		/*
		 * Options opts = new BitmapFactory.Options();
		 * 
		 * DisplayMetrics dm = new DisplayMetrics();
		 * 
		 * int dpiClassification = dm.densityDpi;
		 * 
		 * opts.inDensity = dm.DENSITY_MEDIUM;
		 * 
		 * opts.inTargetDensity = dpiClassification; opts.inScaled =true;
		 */

		creditTV.setCompoundDrawablesWithIntrinsicBounds(null, null, myIcon,
				null);
		creditTV.setCompoundDrawablePadding(10);

		creditTV.setText(item.quantite);
		equivalenceTV.setText(item.equivalent);
		bonisTV.setText(item.bonis);
		buyButton.setText(item.prix_usd + " $USD");

		return convertView;

	}

}
