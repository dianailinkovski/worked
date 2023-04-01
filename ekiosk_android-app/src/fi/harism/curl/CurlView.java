/*
   Copyright 2012 Harri Smatt

   Licensed under the Apache License, Version 2.0 (the "License");
   you may not use this file except in compliance with the License.
   You may obtain a copy of the License at

       http://www.apache.org/licenses/LICENSE-2.0

   Unless required by applicable law or agreed to in writing, software
   distributed under the License is distributed on an "AS IS" BASIS,
   WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
   See the License for the specific language governing permissions and
   limitations under the License.
 */

package fi.harism.curl;

import android.content.Context;
import android.graphics.Bitmap;
import android.graphics.PointF;
import android.graphics.RectF;
import android.opengl.GLSurfaceView;
import android.os.AsyncTask;
import android.os.Process;
import android.util.AttributeSet;
import android.util.Log;
import android.util.SparseArray;
import android.view.MotionEvent;
import android.view.ScaleGestureDetector;
import android.view.View;

/**
 * OpenGL ES View.
 * 
 * @author harism
 */
public class CurlView extends GLSurfaceView implements View.OnTouchListener,
		CurlRenderer.Observer {

	// Curl state. We are flipping none, left or right page.
	private static final int CURL_LEFT = 1;
	private static final int CURL_NONE = 0;
	private static final int CURL_RIGHT = 2;

	// Constants for mAnimationTargetEvent.
	private static final int SET_CURL_TO_LEFT = 1;
	private static final int SET_CURL_TO_RIGHT = 2;

	// Shows one page at the center of view.
	public static final int SHOW_ONE_PAGE = 1;
	// Shows two pages side by side.
	public static final int SHOW_TWO_PAGES = 2;

	private boolean mAllowLastPageCurl = true;

	private boolean mAnimate = false;
	private long mAnimationDurationTime = 500;
	private PointF mAnimationSource = new PointF();
	private long mAnimationStartTime;
	private PointF mAnimationTarget = new PointF();
	private int mAnimationTargetEvent;

	private PointF mCurlDir = new PointF();

	private PointF mCurlPos = new PointF();
	private int mCurlState = CURL_NONE;
	// Current bitmap index. This is always showed as front of right page.
	private int mCurrentIndex = 0;

	// Start position for dragging.
	private PointF mDragStartPos = new PointF();

	private boolean mEnableTouchPressure = false;
	// Bitmap size. These are updated from renderer once it's initialized.
	private int mPageBitmapHeight = -1;

	private int mPageBitmapWidth = -1;
	// Page meshes. Left and right meshes are 'static' while curl is used to
	// show page flipping.
	private CurlMesh mPageCurl;

	private CurlMesh mPageLeft;
	private PageProvider mPageProvider;
	private CurlMesh mPageRight;

	private PointerPosition mPointerPos = new PointerPosition();

	private CurlRenderer mRenderer;
	private boolean mRenderLeftPage = false;
	private SizeChangedObserver mSizeChangedObserver;

	// One page is the default.
	public int mViewMode = SHOW_ONE_PAGE;

	private float mScaleFactor = 6.0f;
	
	private ScaleGestureDetector mScaleDetector;
	
	private static final String TAG = "Touch" ;
	
	public CurlActivity parentActivity = null;
	
	/**
	 * Default constructor.
	 */
	public CurlView(Context ctx) {
		super(ctx);
		init(ctx);
	}

	/**
	 * Default constructor.
	 */
	public CurlView(Context ctx, AttributeSet attrs) {
		super(ctx, attrs);
		init(ctx);
	}

	/**
	 * Default constructor.
	 */
	public CurlView(Context ctx, AttributeSet attrs, int defStyle) {
		this(ctx, attrs);
	}

	/**
	 * Get current page index. Page indices are zero based values presenting
	 * page being shown on right side of the book.
	 */
	public int getCurrentIndex() {
		return mCurrentIndex;
	}
	
	public void setRatio(float tempRatio) {
		mRenderer.issueRatio = tempRatio;
		
	}
	
	public CurlView(Context ctx, float ratio) {
		super(ctx);
		init(ctx, ratio);
	}
	
	/**
	 * Initialize method.
	 */
	private void init(Context ctx) {
		
		
		mRenderer = new CurlRenderer(this);
		setRenderer(mRenderer);
		setRenderMode(GLSurfaceView.RENDERMODE_WHEN_DIRTY);
		setOnTouchListener(this);
		
		mScaleDetector = new ScaleGestureDetector(getContext(), new ScaleListener());
		
		// Even though left and right pages are static we have to allocate room
		// for curl on them too as we are switching meshes. Another way would be
		// to swap texture ids only.
		mPageLeft = new CurlMesh(10);
		mPageRight = new CurlMesh(10);
		mPageCurl = new CurlMesh(10);
		mPageLeft.setFlipTexture(true);
		mPageRight.setFlipTexture(false);
		
	}
	private void init(Context ctx, float ratio) {
		
		
		mRenderer = new CurlRenderer(this, ratio);
		setRenderer(mRenderer);
		setRenderMode(GLSurfaceView.RENDERMODE_WHEN_DIRTY);
		setOnTouchListener(this);
		
		mScaleDetector = new ScaleGestureDetector(getContext(), new ScaleListener());
		
		// Even though left and right pages are static we have to allocate room
		// for curl on them too as we are switching meshes. Another way would be
		// to swap texture ids only.
		mPageLeft = new CurlMesh(10);
		mPageRight = new CurlMesh(10);
		mPageCurl = new CurlMesh(10);
		mPageLeft.setFlipTexture(true);
		mPageRight.setFlipTexture(false);
		
	}
	
	@Override
	public void onDrawFrame() {
		// We are not animating.
		if (mAnimate == false) {
			return;
		}

		long currentTime = System.currentTimeMillis();
		// If animation is done.
		if (currentTime >= mAnimationStartTime + mAnimationDurationTime) {
			if (mAnimationTargetEvent == SET_CURL_TO_RIGHT) {
				// Switch curled page to right.
				CurlMesh right = mPageCurl;
				CurlMesh curl = mPageRight;
				right.setRect(mRenderer.getPageRect(CurlRenderer.PAGE_RIGHT));
				right.setFlipTexture(false);
				right.reset();
				mRenderer.removeCurlMesh(curl);
				mPageCurl = curl;
				mPageRight = right;
				// If we were curling left page update current index.
				//if (mCurlState == CURL_LEFT) {
				//	--mCurrentIndex;
				if (mCurlState == CURL_LEFT) {
					if (mViewMode == SHOW_TWO_PAGES) {
						--mCurrentIndex;
						--mCurrentIndex;
					}
					else {
						--mCurrentIndex;
					}
				}
			} else if (mAnimationTargetEvent == SET_CURL_TO_LEFT) {
				// Switch curled page to left.
				CurlMesh left = mPageCurl;
				CurlMesh curl = mPageLeft;
				left.setRect(mRenderer.getPageRect(CurlRenderer.PAGE_LEFT));
				left.setFlipTexture(true);
				left.reset();
				mRenderer.removeCurlMesh(curl);
				if (!mRenderLeftPage) {
					mRenderer.removeCurlMesh(left);
				}
				mPageCurl = curl;
				mPageLeft = left;
				// If we were curling right page update current index.
				if (mCurlState == CURL_RIGHT) {
					if (mViewMode == SHOW_TWO_PAGES) {
						++mCurrentIndex;
						++mCurrentIndex;
					}
					else {
						++mCurrentIndex;
					}
				}
			}
			mCurlState = CURL_NONE;
			mAnimate = false;
			requestRender();
			
			if (readyToRender) {
				startAsyncTask();
			}
			
		} else {
			mPointerPos.mPos.set(mAnimationSource);
			float t = 1f - ((float) (currentTime - mAnimationStartTime) / mAnimationDurationTime);
			t = 1f - (t * t * t * (3 - 2 * t));
			mPointerPos.mPos.x += (mAnimationTarget.x - mAnimationSource.x) * t;
			mPointerPos.mPos.y += (mAnimationTarget.y - mAnimationSource.y) * t;
			updateCurlPos(mPointerPos);
		}
		
		
		
	}

	@Override
	public void onPageSizeChanged(int width, int height) {
		mPageBitmapWidth = width;
		mPageBitmapHeight = height;
		updatePages();
		requestRender();
	}

	@Override
	public void onSizeChanged(int w, int h, int ow, int oh) {
		super.onSizeChanged(w, h, ow, oh);
		
		requestRender();
		if (mSizeChangedObserver != null) {
			mSizeChangedObserver.onSizeChanged(w, h);
		}
	}

	@Override
	public void onSurfaceCreated() {
		// In case surface is recreated, let page meshes drop allocated texture
		// ids and ask for new ones. There's no need to set textures here as
		// onPageSizeChanged should be called later on.
		mPageLeft.resetTexture();
		mPageRight.resetTexture();
		mPageCurl.resetTexture();
	}

	protected final float fingerDist(MotionEvent event){
	    float x = event.getX(0) - event.getX(1);
	    float y = event.getY(0) - event.getY(1);
	    return (float) Math.sqrt(x * x + y * y);
	}
	
	
	Boolean readyToRender = false;
	float mLastTouchX, mLastTouchY, cX, cY;
	private SparseArray<PointF> mActivePointers = new SparseArray<PointF>();
	
	Boolean boolean_pointer_down = false;
	class RunnableTouchCurl implements Runnable {
		
		@Override
        public void run() {
			try {
				synchronized (this) {
					wait(100);
				}
			} catch (InterruptedException e) {
				// TODO Auto-generated catch block
				e.printStackTrace();
			} //if the user didn't put more fingers in 300 ms he's not going to zoom
			
			if (!boolean_pointer_down){
				
				cancelRunningTask();
				
				// We need page rects quite extensively so get them for later use.
				RectF rightRect = mRenderer.getPageRect(CurlRenderer.PAGE_RIGHT);
				RectF leftRect = mRenderer.getPageRect(CurlRenderer.PAGE_LEFT);
				
				// Once we receive pointer down event its position is mapped to
    			// right or left edge of page and that'll be the position from where
    			// user is holding the paper to make curl happen.
    			mDragStartPos.set(mPointerPos.mPos);

    			// First we make sure it's not over or below page. Pages are
    			// supposed to be same height so it really doesn't matter do we use
    			// left or right one.
    			if (mDragStartPos.y > rightRect.top) {
    				mDragStartPos.y = rightRect.top;
    			} else if (mDragStartPos.y < rightRect.bottom) {
    				mDragStartPos.y = rightRect.bottom;
    			}

    			// Then we have to make decisions for the user whether curl is going
    			// to happen from left or right, and on which page.
    			if (mViewMode == SHOW_TWO_PAGES) {
    				
    				// If we have an open book and pointer is on the left from right
    				// page we'll mark drag position to left edge of left page.
    				// Additionally checking mCurrentIndex is higher than zero tells
    				// us there is a visible page at all.
    				if (mDragStartPos.x < rightRect.left && mCurrentIndex > 0) {
    					mDragStartPos.x = leftRect.left;
    					startCurl(CURL_LEFT);
    				}
    				// Otherwise check pointer is on right page's side.
    				else if (mDragStartPos.x >= rightRect.left && mCurrentIndex < mPageProvider.getPageCount()) {
    					mDragStartPos.x = rightRect.right;
    					if (!mAllowLastPageCurl	&& mCurrentIndex >= mPageProvider.getPageCount() - 1) {
    						//return false;
    					}
    					else {
    						
    						CurlActivity host = (CurlActivity) getContext();
    						host.runOnUiThread(new Runnable() {
    				            @Override
    				            public void run() {
    				            	startCurl(CURL_RIGHT);
    				            }
    				        });
    						
    					}
    				}
    			} else if (mViewMode == SHOW_ONE_PAGE) {
    				float halfX = (rightRect.right + rightRect.left) / 2;
    				if (mDragStartPos.x < halfX && mCurrentIndex > 0) {
    					mDragStartPos.x = rightRect.left;
    					startCurl(CURL_LEFT);
    				} else if (mDragStartPos.x >= halfX
    						&& mCurrentIndex < mPageProvider.getPageCount()) {
    					mDragStartPos.x = rightRect.right;
    					if (!mAllowLastPageCurl	&& mCurrentIndex >= mPageProvider.getPageCount() - 1) {
    						//return false;
    					}
    					else {
    						
    						CurlActivity host = (CurlActivity) getContext();
    						host.runOnUiThread(new Runnable() {
    				            @Override
    				            public void run() {
    				            	startCurl(CURL_RIGHT);
    				            }
    				        });
    						
    					}
    				}
    			}
				
				boolean_pointer_down = false;
			}
		}
	}
	@Override
	public boolean onTouch(View view, MotionEvent me) {
		// No dragging during animation at the moment.
		// TODO: Stop animation on touch event and return to drag mode.
		
		mScaleDetector.onTouchEvent(me);
		if (mAnimate || mPageProvider == null) {
			return false;
		}
		
		// get pointer index from the event object
	    int pointerIndex = me.getActionIndex();

	    // get pointer ID
	    int pointerId = me.getPointerId(pointerIndex);
		
		//dumpEvent(me);
		
		
		
		// We need page rects quite extensively so get them for later use.
		RectF rightRect = mRenderer.getPageRect(CurlRenderer.PAGE_RIGHT);
		RectF leftRect = mRenderer.getPageRect(CurlRenderer.PAGE_LEFT);

		// Store pointer position.
		mPointerPos.mPos.set(me.getX(), me.getY());
		mRenderer.translate(mPointerPos.mPos);
		if (mEnableTouchPressure) {
			mPointerPos.mPressure = me.getPressure();
		} else {
			mPointerPos.mPressure = 0.8f;
		}
		
		switch(me.getAction() & MotionEvent.ACTION_MASK) {

			case MotionEvent.ACTION_POINTER_DOWN:
	        case MotionEvent.ACTION_DOWN: {
	        	
	        	if ((me.getAction() & MotionEvent.ACTION_MASK) == MotionEvent.ACTION_POINTER_DOWN) {
	        		boolean_pointer_down = true;
				}
	        	
	        	PointF f = new PointF();
	            f.x = me.getX(pointerIndex);
	            f.y = me.getY(pointerIndex);
	            mActivePointers.put(pointerId, f);
	            
	        	final float x = me.getX();// screen X position
	            final float y = me.getY();// screen Y position
	            
	            // Remember where we started
	            mLastTouchX = x;
	            mLastTouchY = y;
	            
	            if (mActivePointers.size() == 1 && mScaleFactor == 6.0f) {
	            	boolean_pointer_down = false;
	            	new Thread(new RunnableTouchCurl()).start();
	            	
	            }
	            else {
	            	if (mCurlState == CURL_LEFT || mCurlState == CURL_RIGHT) {
						// Animation source is the point from where animation starts.
						// Also it's handled in a way we actually simulate touch events
						// meaning the output is exactly the same as if user drags the
						// page to other side. While not producing the best looking
						// result (which is easier done by altering curl position and/or
						// direction directly), this is done in a hope it made code a
						// bit more readable and easier to maintain.
						mAnimationSource.set(mPointerPos.mPos);
						mAnimationStartTime = System.currentTimeMillis();

						// Given the explanation, here we decide whether to simulate
						// drag to left or right end.
						/*
						if ((mViewMode == SHOW_ONE_PAGE && mPointerPos.mPos.x > (rightRect.left + rightRect.right) / 2)
								|| mViewMode == SHOW_TWO_PAGES
								&& mPointerPos.mPos.x > rightRect.left) {
							// On right side target is always right page's right border.
							mAnimationTarget.set(mDragStartPos);
							mAnimationTarget.x = mRenderer
									.getPageRect(CurlRenderer.PAGE_RIGHT).right;
							mAnimationTargetEvent = SET_CURL_TO_RIGHT;
						} else {
							// On left side target depends on visible pages.
							mAnimationTarget.set(mDragStartPos);
							if (mCurlState == CURL_RIGHT || mViewMode == SHOW_TWO_PAGES) {
								mAnimationTarget.x = leftRect.left;
							} else {
								mAnimationTarget.x = rightRect.left;
							}
							mAnimationTargetEvent = SET_CURL_TO_LEFT;
						}
						*/
						
						
						if (mCurlState == CURL_LEFT) {
							mCurlState = CURL_NONE;
							mAnimationTargetEvent = SET_CURL_TO_LEFT;
						}
						else {
							mCurlState = CURL_NONE;
							mAnimationTargetEvent = SET_CURL_TO_RIGHT;
						}
						
						mAnimate = true;
						requestRender();
					}
	            }
	            
	            
	            break;
	        }
	        /*case MotionEvent.ACTION_POINTER_DOWN: {
	        	
	        	
	        	
	        	break;
	        }*/
	        /*
	        case MotionEvent.ACTION_POINTER_UP: {
	        	
	        	zoomingBoolean = false;
	        	
	        	break;
	        }
	        */
	        case MotionEvent.ACTION_MOVE: {
	            
	        	int size = me.getPointerCount();
	        	
				if (mActivePointers.size() > 1) { // zoom scaling
					if (mCurlState == CURL_LEFT || mCurlState == CURL_RIGHT) {
						mAnimationSource.set(mPointerPos.mPos);
						mAnimationStartTime = System.currentTimeMillis();
						
						if (mCurlState == CURL_LEFT) {
							mCurlState = CURL_NONE;
							mAnimationTargetEvent = SET_CURL_TO_LEFT;
						}
						else {
							mCurlState = CURL_NONE;
							mAnimationTargetEvent = SET_CURL_TO_RIGHT;
						}
						
						mAnimate = true;
						requestRender();
					}
				}
				else if (mActivePointers.size() == 1 && mScaleFactor == 6.0f) { // curl page
	        		updateCurlPos(mPointerPos);
				}
				else if (mActivePointers.size() == 1 && mScaleFactor < 6.0f) { // move in zoomed page
					final float x = me.getX();
	                final float y = me.getY();
	        		
	        		float oldX = 0, oldY = 0;
	        		
	        		for (int i = 0; i < size; i++) {
						PointF point = mActivePointers.get(me.getPointerId(i));
						if (point != null) {
							oldX = point.x;
							oldY = point.y;
						}
		        	}
	        		
	        		
	        		final float dx = (x - oldX); // change in X
	                final float dy = (y - oldY); // change in Y
	        		
	        		mRenderer.zoom(mScaleFactor, dx, dy);
	    	        requestRender();
				}
				
				
	        	
	        	for (int i = 0; i < size; i++) {
					PointF point = mActivePointers.get(me.getPointerId(i));
					if (point != null) {
						point.x = me.getX(i);
						point.y = me.getY(i);
					}
	        	}
	            
	        	
	        	
	        	
	        	/*
	        	
	        	if (me.getPointerId(0) != 0) {
					break;
				}
	        	
	            // Only move if the ScaleGestureDetector isn't processing a gesture.
	            if (!mScaleDetector.isInProgress()) {
	            	final float x = me.getX();
	                final float y = me.getY();
	            	
	                final float dx = x - mLastTouchX; // change in X
	                final float dy = y - mLastTouchY; // change in Y
	                
	                //Log.e("Moving", "x="+String.valueOf(dx)+", y="+String.valueOf(dy));
	                
	                mRenderer.zoom(mScaleFactor, dx*mScaleFactor, dy*mScaleFactor);
	    	        requestRender();
	    	        
	    	        mLastTouchX = x;
	                mLastTouchY = y;
	            }
	            */
	            
	            
	            break;
	        }
	        /*
	        case MotionEvent.ACTION_UP: {
	            mLastTouchX = 0;
	            mLastTouchY = 0;
	            
	            requestRender();
	        }
	        */
	        
	        case MotionEvent.ACTION_UP:
	        case MotionEvent.ACTION_POINTER_UP:
	        case MotionEvent.ACTION_CANCEL: {
	        	
	        	mActivePointers.remove(pointerId);
	        	
	        	if (mActivePointers.size() == 0) {
	        		readyToRender = true;
				}
	        	
        		if (mScaleFactor == 6.0f) {
	        		if (mCurlState == CURL_LEFT || mCurlState == CURL_RIGHT) {
						// Animation source is the point from where animation starts.
						// Also it's handled in a way we actually simulate touch events
						// meaning the output is exactly the same as if user drags the
						// page to other side. While not producing the best looking
						// result (which is easier done by altering curl position and/or
						// direction directly), this is done in a hope it made code a
						// bit more readable and easier to maintain.
						mAnimationSource.set(mPointerPos.mPos);
						mAnimationStartTime = System.currentTimeMillis();

						// Given the explanation, here we decide whether to simulate
						// drag to left or right end.
						if ((mViewMode == SHOW_ONE_PAGE && mPointerPos.mPos.x > (rightRect.left + rightRect.right) / 2)
								|| mViewMode == SHOW_TWO_PAGES && mPointerPos.mPos.x > rightRect.left) {
							// On right side target is always right page's right border.
							mAnimationTarget.set(mDragStartPos);
							mAnimationTarget.x = mRenderer.getPageRect(CurlRenderer.PAGE_RIGHT).right;
							mAnimationTargetEvent = SET_CURL_TO_RIGHT;
						} else {
							// On left side target depends on visible pages.
							mAnimationTarget.set(mDragStartPos);
							if (mCurlState == CURL_RIGHT || mViewMode == SHOW_TWO_PAGES) {
								mAnimationTarget.x = leftRect.left;
							} else {
								mAnimationTarget.x = rightRect.left;
							}
							mAnimationTargetEvent = SET_CURL_TO_LEFT;
						}
						mAnimate = true;
						requestRender();
					}
				}
	        	else {
	        		if (mCurlState == CURL_LEFT || mCurlState == CURL_RIGHT) {
		        		mAnimationSource.set(mPointerPos.mPos);
						mAnimationStartTime = System.currentTimeMillis();
						
						if (mCurlState == CURL_LEFT) {
							mCurlState = CURL_NONE;
							mAnimationTargetEvent = SET_CURL_TO_LEFT;
						}
						else {
							mCurlState = CURL_NONE;
							mAnimationTargetEvent = SET_CURL_TO_RIGHT;
						}
						
						mAnimate = true;
						requestRender();
	        		}
	        	}
	        	
	          
	          break;
	        }
        }
	    
	
		return true;
		
	}
	
	public void startAsyncTask() {
		Log.e("startAsyncTask", "startAsyncTask");
		
		
		cancelRunningTask();
			
		
		
		final CurlActivity host = (CurlActivity) getContext();
		host.runOnUiThread(new Runnable() {
            @Override
            public void run() {
            	host.setProgressBarIndeterminateVisibility(true);
            }
        });
		
		//test = new Thread(new Task());
		//test.start();
		toto = new LoadFullQualityTask();
		toto.execute("");
		
	}
	
	public void cancelRunningTask() {
		/*
		if (test != null) {
			test.interrupt();
			
			synchronized (this) {
				try {
					wait(100);
				} catch (InterruptedException e) {
					// TODO Auto-generated catch block
					e.printStackTrace();
				}
			}
		}*/
		if (toto != null) {
			toto.cancel(true);
			final CurlActivity host = (CurlActivity) getContext();
			host.runOnUiThread(new Runnable() {

	            @Override
	            public void run() {
	            	host.setProgressBarIndeterminateVisibility(false);
	            }
	        });
		}
		
		
	}
	
	
	
	public LoadFullQualityTask toto;
	
	
	
	/*
	static public Thread test = null;
	
	class Task implements Runnable {

        @Override
        public void run() {
            
        	Bitmap left = null, right = null, leftSmall = null, rightSmall = null;
        	if (mViewMode == CurlView.SHOW_TWO_PAGES) {
				
	        	if (mCurrentIndex-1 >= 0) {
	        		left = mPageProvider.updatePageFullSize(null, mPageBitmapWidth, mPageBitmapHeight,	mCurrentIndex-1, true);				
				}
	        	
	        	if (mCurrentIndex < mPageProvider.getPageCount()) {
	        		right = mPageProvider.updatePageFullSize(null, mPageBitmapWidth, mPageBitmapHeight,	mCurrentIndex, false);
	        	}
	        	
	        	if (mCurrentIndex-2 >= 0) {
	        		leftSmall = mPageProvider.updatePageSmallSize(null, mPageBitmapWidth, mPageBitmapHeight,	mCurrentIndex-2, false, false);
	        	}
	        	
	        	if (mCurrentIndex+1 <= mPageProvider.getPageCount()) {
	        		rightSmall = mPageProvider.updatePageSmallSize(null, mPageBitmapWidth, mPageBitmapHeight,	mCurrentIndex+1, true, false);
	        	}
        	}
        	else {
        		if (mCurrentIndex-1 >= 0) {
	        		left = mPageProvider.updatePageSmallSize(null, mPageBitmapWidth, mPageBitmapHeight,	mCurrentIndex-1, false, false);
	        		leftSmall = mPageProvider.updatePageSmallSize(null, mPageBitmapWidth, mPageBitmapHeight, mCurrentIndex-1, false, true);
				}
        		
        		right = mPageProvider.updatePageFullSize(null, mPageBitmapWidth, mPageBitmapHeight,	mCurrentIndex, false);
	        	rightSmall = mPageProvider.updatePageSmallSize(null, mPageBitmapWidth, mPageBitmapHeight, mCurrentIndex, false, true);
        		
	        	
        	}
        	
        	if (Thread.currentThread().isInterrupted()) fullRenderCanceled();
        	
    		fullRenderCompleted(left, right, leftSmall, rightSmall);
    		
    		
        }
	}
	
	public void fullRenderCompleted(Bitmap leftBM, Bitmap rightBM, Bitmap backLeft, Bitmap backRight) {
		
		if (mViewMode == CurlView.SHOW_TWO_PAGES) {
			mRenderer.removeCurlMesh(mPageLeft);
			mRenderer.removeCurlMesh(mPageRight);
			
			
			mPageLeft.mTexturePage.setTexture(backLeft, CurlPage.SIDE_FRONT);
			mPageRight.mTexturePage.setTexture(backRight, CurlPage.SIDE_BACK);
			
			
			mPageLeft.mTexturePage.setTexture(leftBM, CurlPage.SIDE_BACK);
			mPageRight.mTexturePage.setTexture(rightBM, CurlPage.SIDE_FRONT);
			
			
			mRenderer.addCurlMesh(mPageLeft);
			mRenderer.addCurlMesh(mPageRight);
		}
		else {
			
			mRenderer.removeCurlMesh(mPageRight);
			mPageRight.mTexturePage.setTexture(rightBM, CurlPage.SIDE_FRONT);
			mPageRight.mTexturePage.setTexture(backRight, CurlPage.SIDE_BACK);
			mRenderer.addCurlMesh(mPageRight);
			
		}
		
		if (test == null || test.isInterrupted()) {
			fullRenderCanceled();
		}
		
		requestRender();
		
		final CurlActivity host = (CurlActivity) getContext();
		host.runOnUiThread(new Runnable() {

            @Override
            public void run() {
            	host.setProgressBarIndeterminateVisibility(false);
            }
        });
		
		test = null;
		
	}
	
	public void fullRenderCanceled() {
		
		final CurlActivity host = (CurlActivity) getContext();
		host.runOnUiThread(new Runnable() {

            @Override
            public void run() {
            	host.setProgressBarIndeterminateVisibility(false);
            	test = null;
            }
        });
	}
	*/
	
	/**
	 * Allow the last page to curl.
	 */
	public void setAllowLastPageCurl(boolean allowLastPageCurl) {
		mAllowLastPageCurl = allowLastPageCurl;
	}

	/**
	 * Sets background color - or OpenGL clear color to be more precise. Color
	 * is a 32bit value consisting of 0xAARRGGBB and is extracted using
	 * android.graphics.Color eventually.
	 */
	@Override
	public void setBackgroundColor(int color) {
		mRenderer.setBackgroundColor(color);
		requestRender();
	}

	/**
	 * Sets mPageCurl curl position.
	 */
	private void setCurlPos(PointF curlPos, PointF curlDir, double radius) {

		// First reposition curl so that page doesn't 'rip off' from book.
		if (mCurlState == CURL_RIGHT || (mCurlState == CURL_LEFT && mViewMode == SHOW_ONE_PAGE)) {
			RectF pageRect = mRenderer.getPageRect(CurlRenderer.PAGE_RIGHT);
			if (curlPos.x >= pageRect.right) {
				mPageCurl.reset();
				requestRender();
				return;
			}
			if (curlPos.x < pageRect.left) {
				curlPos.x = pageRect.left;
			}
			if (curlDir.y != 0) {
				float diffX = curlPos.x - pageRect.left;
				float leftY = curlPos.y + (diffX * curlDir.x / curlDir.y);
				if (curlDir.y < 0 && leftY < pageRect.top) {
					curlDir.x = curlPos.y - pageRect.top;
					curlDir.y = pageRect.left - curlPos.x;
				} else if (curlDir.y > 0 && leftY > pageRect.bottom) {
					curlDir.x = pageRect.bottom - curlPos.y;
					curlDir.y = curlPos.x - pageRect.left;
				}
			}
		} else if (mCurlState == CURL_LEFT) {
			RectF pageRect = mRenderer.getPageRect(CurlRenderer.PAGE_LEFT);
			if (curlPos.x <= pageRect.left) {
				mPageCurl.reset();
				requestRender();
				return;
			}
			if (curlPos.x > pageRect.right) {
				curlPos.x = pageRect.right;
			}
			if (curlDir.y != 0) {
				float diffX = curlPos.x - pageRect.right;
				float rightY = curlPos.y + (diffX * curlDir.x / curlDir.y);
				if (curlDir.y < 0 && rightY < pageRect.top) {
					curlDir.x = pageRect.top - curlPos.y;
					curlDir.y = curlPos.x - pageRect.right;
				} else if (curlDir.y > 0 && rightY > pageRect.bottom) {
					curlDir.x = curlPos.y - pageRect.bottom;
					curlDir.y = pageRect.right - curlPos.x;
				}
			}
		}

		// Finally normalize direction vector and do rendering.
		double dist = Math.sqrt(curlDir.x * curlDir.x + curlDir.y * curlDir.y);
		if (dist != 0) {
			curlDir.x /= dist;
			curlDir.y /= dist;
			mPageCurl.curl(curlPos, curlDir, radius);
		} else {
			mPageCurl.reset();
		}

		requestRender();
	}

	/**
	 * Set current page index. Page indices are zero based values presenting
	 * page being shown on right side of the book. E.g if you set value to 4;
	 * right side front facing bitmap will be with index 4, back facing 5 and
	 * for left side page index 3 is front facing, and index 2 back facing (once
	 * page is on left side it's flipped over).
	 * 
	 * Current index is rounded to closest value divisible with 2.
	 */
	public void setCurrentIndex(int index) {
		if (mPageProvider == null || index < 0) {
			mCurrentIndex = 0;
		} else {
			if (mAllowLastPageCurl) {
				mCurrentIndex = Math.min(index, mPageProvider.getPageCount());
			} else {
				mCurrentIndex = Math.min(index, mPageProvider.getPageCount() - 1);
			}
		}
		updatePages();
		requestRender();
		startAsyncTask();
	}

	/**
	 * If set to true, touch event pressure information is used to adjust curl
	 * radius. The more you press, the flatter the curl becomes. This is
	 * somewhat experimental and results may vary significantly between devices.
	 * On emulator pressure information seems to be flat 1.0f which is maximum
	 * value and therefore not very much of use.
	 */
	public void setEnableTouchPressure(boolean enableTouchPressure) {
		mEnableTouchPressure = enableTouchPressure;
	}

	/**
	 * Set margins (or padding). Note: margins are proportional. Meaning a value
	 * of .1f will produce a 10% margin.
	 */
	public void setMargins(float left, float top, float right, float bottom) {
		mRenderer.setMargins(left, top, right, bottom);
	}

	/**
	 * Update/set page provider.
	 */
	public void setPageProvider(PageProvider pageProvider) {
		mPageProvider = pageProvider;
		mCurrentIndex = 0;
		updatePages();
		requestRender();
	}

	/**
	 * Setter for whether left side page is rendered. This is useful mostly for
	 * situations where right (main) page is aligned to left side of screen and
	 * left page is not visible anyway.
	 */
	public void setRenderLeftPage(boolean renderLeftPage) {
		mRenderLeftPage = renderLeftPage;
	}

	/**
	 * Sets SizeChangedObserver for this View. Call back method is called from
	 * this View's onSizeChanged method.
	 */
	public void setSizeChangedObserver(SizeChangedObserver observer) {
		mSizeChangedObserver = observer;
	}

	/**
	 * Sets view mode. Value can be either SHOW_ONE_PAGE or SHOW_TWO_PAGES. In
	 * former case right page is made size of display, and in latter case two
	 * pages are laid on visible area.
	 */
	public void setViewMode(int viewMode) {
		switch (viewMode) {
		case SHOW_ONE_PAGE:
			mViewMode = viewMode;
			mPageLeft.setFlipTexture(true);
			mRenderer.setViewMode(CurlRenderer.SHOW_ONE_PAGE);
			break;
		case SHOW_TWO_PAGES:
			mViewMode = viewMode;
			mPageLeft.setFlipTexture(false);
			mRenderer.setViewMode(CurlRenderer.SHOW_TWO_PAGES);
			break;
		}
	}
	
	public int getViewMode() {
		return mViewMode;
	}

	/**
	 * Switches meshes and loads new bitmaps if available. Updated to support 2
	 * pages in landscape
	 */
	private void startCurl(int page) {
		switch (page) {

		// Once right side page is curled, first right page is assigned into
		// curled page. And if there are more bitmaps available new bitmap is
		// loaded into right side mesh.
		case CURL_RIGHT: {
			Log.e("int page = ", "CURL_RIGHT");
			// Remove meshes from renderer.
			mRenderer.removeCurlMesh(mPageLeft);
			mRenderer.removeCurlMesh(mPageRight);
			mRenderer.removeCurlMesh(mPageCurl);

			// We are curling right page.
			CurlMesh curl = mPageRight;
			mPageRight = mPageCurl;
			mPageCurl = curl;

			if (mCurrentIndex > 0) {
				mPageLeft.setFlipTexture(true);
				mPageLeft.setRect(mRenderer.getPageRect(CurlRenderer.PAGE_LEFT));
				mPageLeft.reset();
				if (mRenderLeftPage) {
					mRenderer.addCurlMesh(mPageLeft);
				}
			}
			if ((mCurrentIndex < mPageProvider.getPageCount() - 1 && mViewMode == SHOW_ONE_PAGE) || (mCurrentIndex < mPageProvider.getPageCount() - 2 && mViewMode == SHOW_TWO_PAGES)) {
				if (mViewMode == SHOW_TWO_PAGES) {
					updatePage(mPageRight.getTexturePage(), mCurrentIndex + 2);
				}
				else {
					updatePage(mPageRight.getTexturePage(), mCurrentIndex + 1);
				}
				
				//updatePage(mPageRight.getTexturePage(), mCurrentIndex + 1);
				mPageRight.setRect(mRenderer.getPageRect(CurlRenderer.PAGE_RIGHT));
				mPageRight.setFlipTexture(false);
				mPageRight.reset();
				mRenderer.addCurlMesh(mPageRight);
			}

			// Add curled page to renderer.
			mPageCurl.setRect(mRenderer.getPageRect(CurlRenderer.PAGE_RIGHT));
			mPageCurl.setFlipTexture(false);
			mPageCurl.reset();
			mRenderer.addCurlMesh(mPageCurl);

			mCurlState = CURL_RIGHT;
			break;
		}

		// On left side curl, left page is assigned to curled page. And if
		// there are more bitmaps available before currentIndex, new bitmap
		// is loaded into left page.
		case CURL_LEFT: {
			
			Log.e("int page = ", "CURL_LEFT");
			
			// Remove meshes from renderer.
			mRenderer.removeCurlMesh(mPageLeft);
			mRenderer.removeCurlMesh(mPageRight);
			mRenderer.removeCurlMesh(mPageCurl);

			// We are curling left page.
			CurlMesh curl = mPageLeft;
			mPageLeft = mPageCurl;
			mPageCurl = curl;

			if ((mCurrentIndex > 1 && mViewMode == SHOW_ONE_PAGE) || (mCurrentIndex > 3 && mViewMode == SHOW_TWO_PAGES)) {
				if (mViewMode == SHOW_TWO_PAGES) {
					updatePage(mPageLeft.getTexturePage(), mCurrentIndex - 4);
				}
				else {
					updatePage(mPageLeft.getTexturePage(), mCurrentIndex - 2);
				}
				//updatePage(mPageLeft.getTexturePage(), mCurrentIndex - 2);
				mPageLeft.setFlipTexture(true);
				mPageLeft.setRect(mRenderer.getPageRect(CurlRenderer.PAGE_LEFT));
				mPageLeft.reset();
				if (mRenderLeftPage) {
					mRenderer.addCurlMesh(mPageLeft);
				}
			}

			// If there is something to show on right page add it to renderer.
			if (mCurrentIndex < mPageProvider.getPageCount()) {
				mPageRight.setFlipTexture(false);
				mPageRight.setRect(mRenderer.getPageRect(CurlRenderer.PAGE_RIGHT));
				mPageRight.reset();
				mRenderer.addCurlMesh(mPageRight);
			}

			// How dragging previous page happens depends on view mode.
			if (mViewMode == SHOW_ONE_PAGE || (mCurlState == CURL_LEFT && mViewMode == SHOW_TWO_PAGES)) {
				mPageCurl.setRect(mRenderer.getPageRect(CurlRenderer.PAGE_RIGHT));
				mPageCurl.setFlipTexture(false);
			} else {
				mPageCurl.setRect(mRenderer.getPageRect(CurlRenderer.PAGE_LEFT));
				mPageCurl.setFlipTexture(true);
			}
			mPageCurl.reset();
			mRenderer.addCurlMesh(mPageCurl);

			mCurlState = CURL_LEFT;
			break;
		}

		}
	}

	/**
	 * Updates curl position.
	 */
	private void updateCurlPos(PointerPosition pointerPos) {

		// Default curl radius.
		double radius = mRenderer.getPageRect(CURL_RIGHT).width() / 3;
		// TODO: This is not an optimal solution. Based on feedback received so
		// far; pressure is not very accurate, it may be better not to map
		// coefficient to range [0f, 1f] but something like [.2f, 1f] instead.
		// Leaving it as is until get my hands on a real device. On emulator
		// this doesn't work anyway.
		radius *= Math.max(1f - pointerPos.mPressure, 0f);
		// NOTE: Here we set pointerPos to mCurlPos. It might be a bit confusing
		// later to see e.g "mCurlPos.x - mDragStartPos.x" used. But it's
		// actually pointerPos we are doing calculations against. Why? Simply to
		// optimize code a bit with the cost of making it unreadable. Otherwise
		// we had to this in both of the next if-else branches.
		mCurlPos.set(pointerPos.mPos);

		// If curl happens on right page, or on left page on two page mode,
		// we'll calculate curl position from pointerPos.
		if (mCurlState == CURL_RIGHT || (mCurlState == CURL_LEFT && mViewMode == SHOW_TWO_PAGES)) {

			mCurlDir.x = mCurlPos.x - mDragStartPos.x;
			mCurlDir.y = mCurlPos.y - mDragStartPos.y;
			float dist = (float) Math.sqrt(mCurlDir.x * mCurlDir.x + mCurlDir.y * mCurlDir.y);

			// Adjust curl radius so that if page is dragged far enough on
			// opposite side, radius gets closer to zero.
			float pageWidth = mRenderer.getPageRect(CurlRenderer.PAGE_RIGHT).width();
			double curlLen = radius * Math.PI;
			if (dist > (pageWidth * 2) - curlLen) {
				curlLen = Math.max((pageWidth * 2) - dist, 0f);
				radius = curlLen / Math.PI;
			}

			// Actual curl position calculation.
			if (dist >= curlLen) {
				double translate = (dist - curlLen) / 2;
				if (mViewMode == SHOW_TWO_PAGES) {
					mCurlPos.x -= mCurlDir.x * translate / dist;
				} else {
					float pageLeftX = mRenderer.getPageRect(CurlRenderer.PAGE_RIGHT).left;
					radius = Math.max(Math.min(mCurlPos.x - pageLeftX, radius), 0f);
				}
				mCurlPos.y -= mCurlDir.y * translate / dist;
			} else {
				double angle = Math.PI * Math.sqrt(dist / curlLen);
				double translate = radius * Math.sin(angle);
				mCurlPos.x += mCurlDir.x * translate / dist;
				mCurlPos.y += mCurlDir.y * translate / dist;
			}
		}
		// Otherwise we'll let curl follow pointer position.
		else if (mCurlState == CURL_LEFT) {

			// Adjust radius regarding how close to page edge we are.
			float pageLeftX = mRenderer.getPageRect(CurlRenderer.PAGE_RIGHT).left;
			radius = Math.max(Math.min(mCurlPos.x - pageLeftX, radius), 0f);

			float pageRightX = mRenderer.getPageRect(CurlRenderer.PAGE_RIGHT).right;
			mCurlPos.x -= Math.min(pageRightX - mCurlPos.x, radius);
			mCurlDir.x = mCurlPos.x + mDragStartPos.x;
			mCurlDir.y = mCurlPos.y - mDragStartPos.y;
		}

		setCurlPos(mCurlPos, mCurlDir, radius);
	}

	/**
	 * Updates given CurlPage via PageProvider for page located at index.
	 */
	private void updatePage(CurlPage page, int index) {
		// First reset page to initial state.
		page.reset();
		// Ask page provider to fill it up with bitmaps and colors.
		mPageProvider.updatePage(page, mPageBitmapWidth, mPageBitmapHeight,	index);
	}
	
	private void updatePageFullSize(CurlPage page, int index) {
		// First reset page to initial state.
		page.reset();
		// Ask page provider to fill it up with bitmaps and colors.
		mPageProvider.updatePageFullSize(page, mPageBitmapWidth, mPageBitmapHeight,	index, false);
	}

	/**
	 * Updates bitmaps for page meshes.
	 */
	private void updatePages() {
		if (mPageProvider == null || mPageBitmapWidth <= 0 || mPageBitmapHeight <= 0) {
			return;
		}

		// Remove meshes from renderer.
		mRenderer.removeCurlMesh(mPageLeft);
		mRenderer.removeCurlMesh(mPageRight);
		mRenderer.removeCurlMesh(mPageCurl);

		int leftIdx = mCurrentIndex - 1;
		int rightIdx = mCurrentIndex;
		int curlIdx = -1;
		if (mViewMode == SHOW_TWO_PAGES) {
			leftIdx = mCurrentIndex - 2;
			
			
			if (mCurlState == CURL_LEFT) {
				curlIdx = leftIdx;
				--leftIdx;
			} else if (mCurlState == CURL_RIGHT) {
				curlIdx = rightIdx;
				++rightIdx;
			}
			
		}
		else {
			if (mCurlState == CURL_LEFT) {
				curlIdx = leftIdx;
				--leftIdx;
			} else if (mCurlState == CURL_RIGHT) {
				curlIdx = rightIdx;
				++rightIdx;
			}
		}
		
		

		if (rightIdx >= 0 && rightIdx < mPageProvider.getPageCount()) {
			updatePage(mPageRight.getTexturePage(), rightIdx);
			mPageRight.setFlipTexture(false);
			mPageRight.setRect(mRenderer.getPageRect(CurlRenderer.PAGE_RIGHT));
			mPageRight.reset();
			mRenderer.addCurlMesh(mPageRight);
		}
		if (leftIdx >= 0 && leftIdx < mPageProvider.getPageCount()) {
			updatePage(mPageLeft.getTexturePage(), leftIdx);
			mPageLeft.setFlipTexture(true);
			mPageLeft.setRect(mRenderer.getPageRect(CurlRenderer.PAGE_LEFT));
			mPageLeft.reset();
			if (mRenderLeftPage) {
				mRenderer.addCurlMesh(mPageLeft);
			}
		}
		if (curlIdx >= 0 && curlIdx < mPageProvider.getPageCount()) {
			updatePage(mPageCurl.getTexturePage(), curlIdx);

			if (mCurlState == CURL_RIGHT) {
				mPageCurl.setFlipTexture(true);
				mPageCurl.setRect(mRenderer.getPageRect(CurlRenderer.PAGE_RIGHT));
			} else {
				mPageCurl.setFlipTexture(false);
				RectF rt = mRenderer.getPageRect(CurlRenderer.PAGE_LEFT);
				mPageCurl.setRect(mRenderer.getPageRect(CurlRenderer.PAGE_LEFT));
			}

			mPageCurl.reset();
			mRenderer.addCurlMesh(mPageCurl);
		}
	}

	/**
	 * Provider for feeding 'book' with bitmaps which are used for rendering
	 * pages.
	 */
	public interface PageProvider {

		/**
		 * Return number of pages available.
		 */
		public int getPageCount();

		/**
		 * Called once new bitmaps/textures are needed. Width and height are in
		 * pixels telling the size it will be drawn on screen and following them
		 * ensures that aspect ratio remains. But it's possible to return bitmap
		 * of any size though. You should use provided CurlPage for storing page
		 * information for requested page number.<br/>
		 * <br/>
		 * Index is a number between 0 and getBitmapCount() - 1.
		 */
		public void updatePage(CurlPage page, int width, int height, int index);

		public Bitmap updatePageFullSize(CurlPage page, int width, int height, int index, boolean fliped);

		public Bitmap updatePageSmallSize(CurlPage page, int width, int height, int index, boolean fliped, boolean transparent);
	}

	/**
	 * Simple holder for pointer position.
	 */
	private class PointerPosition {
		PointF mPos = new PointF();
		float mPressure;
	}

	/**
	 * Observer interface for handling CurlView size changes.
	 */
	public interface SizeChangedObserver {

		/**
		 * Called once CurlView size changes.
		 */
		public void onSizeChanged(int width, int height);
	}

	private class ScaleListener extends ScaleGestureDetector.SimpleOnScaleGestureListener {
		
	    @Override
	    public boolean onScale(ScaleGestureDetector detector) {
	    	//Log.e("scale", String.valueOf(detector.getScaleFactor()));
	    	
	    	//if(detector.getScaleFactor() < 1)
	    		mScaleFactor /= detector.getScaleFactor();//detector.getScaleFactor();
	    	//else
	    	//	mScaleFactor /= detector.getScaleFactor();

	        // Don't let the object get too small or too large.
	        mScaleFactor = Math.max(0.1f, Math.min(mScaleFactor, 6.0f));
	        
			mRenderer.zoom(mScaleFactor, 0, 0);
			
	        requestRender();
	        return true;
	    }
	}
	
	private void dumpEvent(MotionEvent event) {
		String names[] = { "DOWN" , "UP" , "MOVE" , "CANCEL" , "OUTSIDE" ,	"POINTER_DOWN" , "POINTER_UP" , "7?" , "8?" , "9?" };
		StringBuilder sb = new StringBuilder();
		int action = event.getAction();
		int actionCode = action & MotionEvent.ACTION_MASK;
		sb.append("event ACTION_" ).append(names[actionCode]);
		if (actionCode == MotionEvent.ACTION_POINTER_DOWN
			|| actionCode == MotionEvent.ACTION_POINTER_UP) {
			sb.append("(pid " ).append(action >> MotionEvent.ACTION_POINTER_ID_SHIFT);
			sb.append(")" );
		}
		sb.append("[" );
		for (int i = 0; i < event.getPointerCount(); i++) {
		sb.append("#" ).append(i);
		sb.append("(pid " ).append(event.getPointerId(i));
		sb.append(")=" ).append((int) event.getX(i));
		sb.append("," ).append((int) event.getY(i));
		if (i + 1 < event.getPointerCount())
			sb.append(";" );
		}
		sb.append("]" );
		Log.d(TAG, sb.toString());
	}
	
	public class LoadFullQualityTask extends AsyncTask<String, Process, Bitmap[]> {
		
		@Override
		protected Bitmap[] doInBackground(String... params) {
			if (isCancelled()) return null;
			
			Bitmap left = null, right = null, leftSmall = null, rightSmall = null;
        	if (mViewMode == CurlView.SHOW_TWO_PAGES) {
				
	        	if (mCurrentIndex-1 >= 0) {
	        		left = mPageProvider.updatePageFullSize(null, mPageBitmapWidth, mPageBitmapHeight,	mCurrentIndex-1, true);				
				}
	        	
	        	if (mCurrentIndex < mPageProvider.getPageCount()) {
	        		right = mPageProvider.updatePageFullSize(null, mPageBitmapWidth, mPageBitmapHeight,	mCurrentIndex, false);
	        	}
	        	
	        	if (mCurrentIndex-2 >= 0) {
	        		leftSmall = mPageProvider.updatePageSmallSize(null, mPageBitmapWidth, mPageBitmapHeight,	mCurrentIndex-2, false, false);
	        	}
	        	
	        	if (mCurrentIndex+1 <= mPageProvider.getPageCount()) {
	        		rightSmall = mPageProvider.updatePageSmallSize(null, mPageBitmapWidth, mPageBitmapHeight,	mCurrentIndex+1, true, false);
	        	}
        	}
        	else {
        		if (mCurrentIndex-1 >= 0) {
	        		left = mPageProvider.updatePageSmallSize(null, mPageBitmapWidth, mPageBitmapHeight,	mCurrentIndex-1, false, false);
	        		leftSmall = mPageProvider.updatePageSmallSize(null, mPageBitmapWidth, mPageBitmapHeight, mCurrentIndex-1, false, true);
				}
        		
        		right = mPageProvider.updatePageFullSize(null, mPageBitmapWidth, mPageBitmapHeight,	mCurrentIndex, false);
	        	rightSmall = mPageProvider.updatePageSmallSize(null, mPageBitmapWidth, mPageBitmapHeight, mCurrentIndex, false, true);
        		
	        	
        	}
        	
    		Bitmap[] test = new Bitmap[4]; 
    		test[0] = left;
    		test[1] = right;
    		test[2] = leftSmall;
    		test[3] = rightSmall;
    		
    		if (isCancelled()) return null;
			
    		
			//fullRenderCompleted(left, right, leftSmall, rightSmall);
			
    		
			return test;
		}
		
		@Override
		protected void onPostExecute(Bitmap[] result) {
			if (result == null) {
				return;
			}
			
			final Bitmap left = result[0];
			final Bitmap right = result[1];
			final Bitmap leftSmall = result[2];
			final Bitmap rightSmall = result[3];
			
			final CurlActivity host = (CurlActivity) getContext();
			host.runOnUiThread(new Runnable() {

	            @Override
	            public void run() {
	            	if (mViewMode == CurlView.SHOW_TWO_PAGES) {
	    				mRenderer.removeCurlMesh(mPageLeft);
	    				mRenderer.removeCurlMesh(mPageRight);
	    				
	    				
	    				mPageLeft.mTexturePage.setTexture(leftSmall, CurlPage.SIDE_FRONT);
	    				mPageRight.mTexturePage.setTexture(rightSmall, CurlPage.SIDE_BACK);
	    				
	    				
	    				mPageLeft.mTexturePage.setTexture(left, CurlPage.SIDE_BACK);
	    				mPageRight.mTexturePage.setTexture(right, CurlPage.SIDE_FRONT);
	    				
	    				
	    				mRenderer.addCurlMesh(mPageLeft);
	    				mRenderer.addCurlMesh(mPageRight);
	    			}
	    			else {
	    				
	    				mRenderer.removeCurlMesh(mPageRight);
	    				mPageRight.mTexturePage.setTexture(right, CurlPage.SIDE_FRONT);
	    				mPageRight.mTexturePage.setTexture(rightSmall, CurlPage.SIDE_BACK);
	    				mRenderer.addCurlMesh(mPageRight);
	    				
	    			}
	    			
	    			requestRender();
	    			
	            	host.setProgressBarIndeterminateVisibility(false);
	            }
	        });
		}
	}
	
}