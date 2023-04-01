//
//  PDFView.h
//  NGSER
//
//  Created by Maxime Julien-Paquet on 2013-10-16.
//
//

#import <UIKit/UIKit.h>

@interface PDFView : UIView {
@private
    CGPDFDocumentRef _PDFDocRef;
    
	CGPDFPageRef _PDFPageRef;
    
	NSInteger _pageAngle;
    
	CGFloat _pageWidth;
	CGFloat _pageHeight;
	CGFloat _pageOffsetX;
	CGFloat _pageOffsetY;
    
    CGRect tempFrame;
}

- (id)initWithURL:(NSURL *)fileURL page:(NSInteger)page password:(NSString *)phrase;
- (void)setParentFrame:(CGRect)newFrame;
@end
