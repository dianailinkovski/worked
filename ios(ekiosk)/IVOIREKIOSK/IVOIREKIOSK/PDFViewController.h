//
//  PDFViewController.h
//  NGSER
//
//  Created by Maxime Julien-Paquet on 2013-10-12.
//
//

#import <UIKit/UIKit.h>

@class PDFView;

@interface PDFViewController : UIViewController {
    NSURL *filePath;
    NSString *password;
    int pageNumber;
    BOOL isVisible;
    
    PDFView *pdfView;
}

-(id)initWithFilePath:(NSURL*)url Password:(NSString*)pass PageNumber:(int)page;

@end
