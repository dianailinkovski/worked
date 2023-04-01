//
//  PDFView.m
//  NGSER
//
//  Created by Maxime Julien-Paquet on 2013-10-16.
//
//

#import "PDFView.h"
#import "ReaderContentTile.h"
#import "CGPDFDocument.h"

@implementation PDFView

+ (Class)layerClass
{
#ifdef DEBUGX
	NSLog(@"%s", __FUNCTION__);
#endif
    
	return [ReaderContentTile class];
}

- (id)initWithFrame:(CGRect)frame {
    self = [super initWithFrame:frame];
    if (self) {
        // Initialization code
        self.autoresizesSubviews = NO;
        //self.userInteractionEnabled = NO;
        self.clearsContextBeforeDrawing = NO;
        self.contentMode = UIViewContentModeRedraw;
        self.autoresizingMask = UIViewAutoresizingNone;
    }
    
    return self;
}

- (id)initWithURL:(NSURL *)fileURL page:(NSInteger)page password:(NSString *)phrase {
    
	CGRect viewRect = CGRectZero; // View rect
    
	if (fileURL != nil) // Check for non-nil file URL
	{
		_PDFDocRef = CGPDFDocumentCreateX((__bridge CFURLRef)fileURL, phrase);
        
		if (_PDFDocRef != NULL) // Check for non-NULL CGPDFDocumentRef
		{
			if (page < 1) page = 1; // Check the lower page bounds
            
			NSInteger pages = CGPDFDocumentGetNumberOfPages(_PDFDocRef);
            
			if (page > pages) page = pages; // Check the upper page bounds
            
			_PDFPageRef = CGPDFDocumentGetPage(_PDFDocRef, page); // Get page
            
			if (_PDFPageRef != NULL) // Check for non-NULL CGPDFPageRef
			{
				CGPDFPageRetain(_PDFPageRef); // Retain the PDF page
                
				CGRect cropBoxRect = CGPDFPageGetBoxRect(_PDFPageRef, kCGPDFCropBox);
				CGRect mediaBoxRect = CGPDFPageGetBoxRect(_PDFPageRef, kCGPDFMediaBox);
				CGRect effectiveRect = CGRectIntersection(cropBoxRect, mediaBoxRect);
                
				_pageAngle = CGPDFPageGetRotationAngle(_PDFPageRef); // Angle
				switch (_pageAngle) // Page rotation angle (in degrees)
				{
					default: // Default case
					case 0: case 180: // 0 and 180 degrees
					{
						_pageWidth = effectiveRect.size.width;
						_pageHeight = effectiveRect.size.height;
						_pageOffsetX = effectiveRect.origin.x;
						_pageOffsetY = effectiveRect.origin.y;
						break;
					}
                        
					case 90: case 270: // 90 and 270 degrees
					{
						_pageWidth = effectiveRect.size.height;
						_pageHeight = effectiveRect.size.width;
						_pageOffsetX = effectiveRect.origin.y;
						_pageOffsetY = effectiveRect.origin.x;
						break;
					}
				}
                
				NSInteger page_w = _pageWidth; // Integer width
				NSInteger page_h = _pageHeight; // Integer height
                
				if (page_w % 2) page_w--; if (page_h % 2) page_h--; // Even
                
				viewRect.size = CGSizeMake(page_w, page_h); // View size
                viewRect.origin = CGPointMake(( [[UIScreen mainScreen] bounds].size.width - page_w )/2, ( [[UIScreen mainScreen] bounds].size.height - page_h )/2);
                
			}
			else // Error out with a diagnostic
			{
				CGPDFDocumentRelease(_PDFDocRef), _PDFDocRef = NULL;
                
				NSAssert(NO, @"CGPDFPageRef == NULL");
			}
		}
		else // Error out with a diagnostic
		{
			NSAssert(NO, @"CGPDFDocumentRef == NULL");
		}
	}
	else // Error out with a diagnostic
	{
		NSAssert(NO, @"fileURL == nil");
	}
    
	id view = [self initWithFrame:viewRect]; // UIView setup
    
	return view;
}

- (void)setParentFrame:(CGRect)newFrame {
    
    CGRect viewRect = CGRectZero;
    NSInteger page_w = _pageWidth; // Integer width
    NSInteger page_h = _pageHeight; // Integer height
    
    if (page_w % 2) page_w--; if (page_h % 2) page_h--; // Even

    float ratio_w = page_w/newFrame.size.width;
    float ratio_h = page_h/newFrame.size.height;

    if (ratio_h < ratio_w) {
        viewRect.size = CGSizeMake(newFrame.size.width, ((newFrame.size.width*page_h)/newFrame.size.height));
        viewRect.origin = CGPointMake(( newFrame.size.width - viewRect.size.width )/2, ( newFrame.size.height - viewRect.size.height )/2);
    }
    else {
        viewRect.size = CGSizeMake(((newFrame.size.height*page_w)/newFrame.size.width), newFrame.size.height);
        viewRect.origin = CGPointMake(( newFrame.size.width - viewRect.size.width )/2, ( newFrame.size.height - viewRect.size.height )/2);
    }
    
    [self setFrame:viewRect];
    [self setNeedsDisplay];
}

// Only override drawRect: if you perform custom drawing.
// An empty implementation adversely affects performance during animation.
- (void)drawRect:(CGRect)rect
{
    // Drawing code
}



-(void)drawLayer:(CALayer *)layer inContext:(CGContextRef)ctx {
    
    CGPDFPageRef drawPDFPageRef = NULL;
    
	CGPDFDocumentRef drawPDFDocRef = NULL;
    
	@synchronized(self) // Block any other threads
	{
		drawPDFDocRef = CGPDFDocumentRetain(_PDFDocRef);
        
		drawPDFPageRef = CGPDFPageRetain(_PDFPageRef);
	}
    
	CGContextSetRGBFillColor(ctx, 1.0f, 1.0f, 1.0f, 1.0f); // White
    
	CGContextFillRect(ctx, CGContextGetClipBoundingBox(ctx)); // Fill
    
	if (drawPDFPageRef != NULL) // Go ahead and render the PDF page into the context
	{
		CGContextTranslateCTM(ctx, 0.0f, self.bounds.size.height); CGContextScaleCTM(ctx, 1.0f, -1.0f);
        
		CGContextConcatCTM(ctx, CGPDFPageGetDrawingTransform(drawPDFPageRef, kCGPDFCropBox, self.bounds, 0, true));
        
		CGContextSetRenderingIntent(ctx, kCGRenderingIntentDefault); CGContextSetInterpolationQuality(ctx, kCGInterpolationDefault);
        
		CGContextDrawPDFPage(ctx, drawPDFPageRef); // Render the PDF page into the context
	}
    
	CGPDFPageRelease(drawPDFPageRef); CGPDFDocumentRelease(drawPDFDocRef); // Cleanup
}

- (void)dealloc {
    
	@synchronized(self) {
		CGPDFPageRelease(_PDFPageRef), _PDFPageRef = NULL;
		CGPDFDocumentRelease(_PDFDocRef), _PDFDocRef = NULL;
	}
    
}

@end
