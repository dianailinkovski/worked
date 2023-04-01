package com.ngser.ekiosk.kiosque;

import java.lang.ref.WeakReference;
import java.util.LinkedHashMap;

import org.json.JSONException;
import org.json.JSONObject;

import uk.co.senab.bitmapcache.CacheableBitmapDrawable;
import android.content.Context;
import android.database.Cursor;
import android.util.Log;
import android.util.SparseBooleanArray;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.BaseAdapter;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.ProgressBar;
import android.widget.TextView;

import com.ngser.ekiosk.R;
import com.squareup.picasso.Picasso;

public class SectionedGridViewAdapter extends BaseAdapter implements
		View.OnClickListener {

	private static final String TAG = "SectionedGridViewAdapter";
	private SparseBooleanArray idAnimations = new SparseBooleanArray();
	private int listItemRowWidth = -1;
	private int gridItemSize = -1;
	private int gridItemSizeWidth = -1;
	private int listViewHeight = -1;
	private LayoutInflater inflater;
	private int count = -1;
	private int numberOfChildrenInRow = -1;

	private int[] childrenSpacing = null;

	private int childSpacing = -1;

	private LinkedHashMap<String, Cursor> sectionCursors = null;

	private LinkedHashMap<String, Integer> sectionRowsCount = new LinkedHashMap<String, Integer>();

	private Context mContext = null;

	public static final int VIEW_TYPE_HEADER = 0;

	public static final int VIEW_TYPE_ROW = 1;

	public static final int MIN_SPACING = 0;

	public static interface OnGridItemClickListener {
		public void onGridItemClicked(String sectionName, int position, View v);
	}

	private OnGridItemClickListener listener = null;

	public SectionedGridViewAdapter(Context context,
			LinkedHashMap<String, Cursor> sectionCursors, int listItemRowSize,
			int listViewHeight, int gridItemSquareSize,
			int gridItemSquareSizeWidth) {

		this.sectionCursors = sectionCursors;
		this.listItemRowWidth = listItemRowSize;
		this.gridItemSize = gridItemSquareSize;
		this.gridItemSizeWidth = gridItemSquareSizeWidth;
		this.listViewHeight = listViewHeight;

		// griditem size is always less that list item size

		if (gridItemSize > this.listItemRowWidth) {
			throw new IllegalArgumentException(
					"Griditem size cannot be greater that list item row size");
		}
		// calculate items number of items that can fit into row size

		numberOfChildrenInRow = listItemRowWidth / gridItemSizeWidth;

		int reminder = listItemRowWidth % gridItemSizeWidth;

		if (reminder == 0) {
			numberOfChildrenInRow = numberOfChildrenInRow - 1;
			reminder = gridItemSizeWidth;
		}

		int numberOfGaps = 0;
		int toReduce = 0;
		while (childSpacing < MIN_SPACING) {
			try {
				numberOfChildrenInRow = numberOfChildrenInRow - toReduce;
				reminder += toReduce * gridItemSizeWidth;
				numberOfGaps = numberOfChildrenInRow - 1;
				if (numberOfGaps <= 0)
					numberOfGaps = 1;
				childSpacing = reminder / numberOfGaps;
				toReduce++;
			} catch (Exception e) {
				e.printStackTrace();
				continue;
			}
		}

		int spacingReminder = reminder % numberOfGaps;

		// distribute spacing gap equally first
		childrenSpacing = new int[numberOfGaps];

		for (int i = 0; i < numberOfGaps; i++) {
			childrenSpacing[i] = childSpacing;
		}

		// extra reminder distribute from beginning
		for (int i = 0; i < spacingReminder; i++) {
			childrenSpacing[i]++;
		}

		this.mContext = context;
		inflater = LayoutInflater.from(mContext);

	}

	@Override
	public int getCount() {

		if (count != -1)
			return count;
		sectionRowsCount.clear();

		// count is cursors count + sections count
		int sections = sectionCursors.size();

		count = sections;
		// count items in each section
		for (String sectionName : sectionCursors.keySet()) {
			int sectionCount = sectionCursors.get(sectionName).getCount();
			int numberOfRows = sectionCount / numberOfChildrenInRow;
			if (sectionCount % numberOfChildrenInRow != 0) {
				numberOfRows++;
			}

			sectionRowsCount.put(sectionName, numberOfRows);
			count += numberOfRows;
		}

		return count;
	}

	@Override
	public Object getItem(int position) {
		// TODO Auto-generated method stub
		return null;
	}

	@Override
	public long getItemId(int position) {
		// TODO Auto-generated method stub
		return 0;
	}

	@Override
	public View getView(int position, View convertView, ViewGroup parent) {
		View v = null;
		boolean isSectionheader = isSectionHeader(position);

		if (convertView == null) {
			if (isSectionheader) {
				v = inflater.inflate(R.layout.archives_journaux_section_header,
						null);
			} else {
				v = inflater.inflate(R.layout.archives_journaux_list_row, null);
				// v = ll;
				LinearLayout ll = (LinearLayout) v.findViewById(R.id.row_item);
				ll.removeAllViews();
				// add childrenCount to this
				for (int i = 0; i < numberOfChildrenInRow; i++) {
					// add a child
					View child = inflater.inflate(
							R.layout.archives_journaux_data_item, null);
					ll.addView(child, new LinearLayout.LayoutParams(
							gridItemSizeWidth, gridItemSize));

					if (i < numberOfChildrenInRow - 1) {
						// now add space view
						View spaceItem = new View(mContext);
						ll.addView(spaceItem, new LinearLayout.LayoutParams(
								childrenSpacing[i], ll.getHeight()));
					}
				}
			}

		} else {
			v = convertView;
		}

		String sectionName = whichSection(position);

		if (isSectionheader) {
			TextView tv = (TextView) v;
			tv.setText(sectionName);
		} else {
			LinearLayout ll = (LinearLayout) v;
			LinearLayout rowPanel = (LinearLayout) ll
					.findViewById(R.id.row_item);
			View divider = ll.findViewById(R.id.row_item_divider);
			divider.setVisibility(View.VISIBLE);

			// check if this position corresponds to last row
			boolean isLastRowInSection = isLastRowInSection(position);
			int positionInSection = positionInSection(position);

			Cursor c = sectionCursors.get(sectionName);

			// --
			int cursorStartAt = numberOfChildrenInRow * positionInSection;

			// set all children visible first
			for (int i = 0; i < 2 * numberOfChildrenInRow - 1; i++) {
				// we need to hide grid item and gap
				View child = rowPanel.getChildAt(i);
				child.setVisibility(View.VISIBLE);

				// leave alternate
				if (i % 2 == 0) {
					// its not gap
					String dataName = "";
					String dataUrl = "";
					String isSubscription = "0";

					if (c.moveToPosition(cursorStartAt)) {
						try {
							JSONObject jObj = new JSONObject(c.getString(0));
							Log.w("Test", jObj.toString());

							dataName = jObj.getString("nom");
							dataUrl = jObj.getString("image");
							isSubscription = jObj.getString("isSubscription");

							TextView tv = (TextView) child
									.findViewById(R.id.data_item_text);
							tv.setText(dataName);
						} catch (JSONException e) {
							e.printStackTrace();
						}
					}

					// set listener on image button
					ImageView button = (ImageView) child
							.findViewById(R.id.nciv_pug);

					if (dataUrl != null && !dataUrl.equals(""))
						Picasso.with(mContext).load(dataUrl).into(button);
					else {

					}

					ImageView iv_mark = (ImageView) child
							.findViewById(R.id.iv_mark);
					iv_mark.setVisibility(View.GONE);

					if (isSubscription.equals("1")) {
						iv_mark.setVisibility(View.VISIBLE);
					}

					ButtonViewHolder holder = new ButtonViewHolder();
					holder.sectionName = sectionName;
					holder.positionInSection = cursorStartAt;
					holder.parent = child;
					child.setTag(holder);
					child.setOnClickListener(this);

					cursorStartAt++;
				}
			}

			if (isLastRowInSection) {
				divider.setVisibility(View.INVISIBLE);
				// check how many items needs to be hidden in last row

				int sectionCount = sectionCursors.get(sectionName).getCount();

				int childrenInLastRow = sectionCount % numberOfChildrenInRow;

				if (childrenInLastRow > 0) {
					int gaps = childrenInLastRow - 1;

					for (int i = childrenInLastRow + gaps; i < rowPanel
							.getChildCount(); i++) {
						// we need to hide grid item and gap
						View child = rowPanel.getChildAt(i);
						child.setVisibility(View.INVISIBLE);
					}
				}
			}
		}

		return v;
	}

	private boolean isLastRowInSection(int position) {

		for (String key : sectionCursors.keySet()) {
			int size = sectionRowsCount.get(key) + 1;

			if (position == size - 1)
				return true;

			position -= size;
		}

		return false;
	}

	private boolean isSectionHeader(int position) {

		for (String key : sectionCursors.keySet()) {
			int size = sectionRowsCount.get(key) + 1;

			if (position == 0)
				return true;

			position -= size;
		}

		return false;

	}

	private String whichSection(int position) {

		for (String key : sectionCursors.keySet()) {
			int size = sectionRowsCount.get(key) + 1;

			if (position < size) {
				return key;
			}

			position -= size;
		}

		return null;

	}

	private int positionInSection(int position) {

		for (String key : sectionCursors.keySet()) {
			int size = sectionRowsCount.get(key) + 1;

			if (position < size) {
				return position - 1;
			}

			position -= size;
		}

		return -1;

	}

	@Override
	public int getViewTypeCount() {
		return 2;
	}

	@Override
	public int getItemViewType(int position) {
		if (isSectionHeader(position)) {
			return VIEW_TYPE_HEADER;
		}

		return VIEW_TYPE_ROW;
	}

	@Override
	public boolean isEnabled(int position) {
		return false;
	}

	public int gapBetweenChildrenInRow() {
		return childSpacing;
	}

	public void setListener(OnGridItemClickListener listener) {
		this.listener = listener;
	}

	@Override
	public void onClick(View v) {
		// TODO Auto-generated method stub
		ButtonViewHolder holder = (ButtonViewHolder) v.getTag();
		if (this.listener != null) {
			listener.onGridItemClicked(holder.sectionName,
					holder.positionInSection, holder.parent);
		}
	}

	public static class ButtonViewHolder {
		String sectionName;
		int positionInSection;
		View parent;
	}

	// TODO -- cleaning view and click listners and making sure context aint
	// leaked

	static class ImageLoadedListener implements
			NetworkedCacheableImageView.OnImageLoadedListener {
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
