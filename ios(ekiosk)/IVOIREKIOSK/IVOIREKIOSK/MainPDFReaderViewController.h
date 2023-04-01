//
//  MainPDFReaderViewController.h
//  NGSER
//
//  Created by Maxime Julien-Paquet on 2013-10-17.
//
//

#import <UIKit/UIKit.h>
#import "ReaderPageViewController.h"
#import "ReaderDocument.h"
#import "ReaderMainPagebar.h"

@interface MainPDFReaderViewController : UIViewController <UIScrollViewDelegate, ReaderPageViewControllerDelegate, ReaderMainPagebarDelegate> {
    CGFloat zoomAmount;
}

@property (nonatomic, strong) UIScrollView *scrollView;
@property (nonatomic, strong) ReaderPageViewController *readerPageVC;
@property (nonatomic, strong) ReaderMainPagebar *bottomBar;

- (id)initWithReaderDocument:(ReaderDocument *)object AndArray:(NSArray*)textArray;

@end
