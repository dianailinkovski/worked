//
//  ReaderPageViewController.h
//  NGSER
//
//  Created by Maxime Julien-Paquet on 2013-10-10.
//
//

#import <UIKit/UIKit.h>
#import "ReaderDocument.h"

@protocol ReaderPageViewControllerDelegate;

@interface ReaderPageViewController : UIViewController <UIPageViewControllerDataSource, UIPageViewControllerDelegate> {
    int ind;
    __weak id <ReaderPageViewControllerDelegate> delegate;
}

@property (nonatomic, strong) UIPageViewController *pageVC;
@property (nonatomic, strong) NSMutableArray *vcArray;
@property (nonatomic, weak) id delegate;

- (id)initWithReaderDocument:(ReaderDocument *)object;
- (int)getCurrentPageId;
- (void)toPage:(int)pageId;
//- (void)hideNavBar;
//- (void)showNavBar;

@end
@protocol ReaderPageViewControllerDelegate <NSObject>

-(void)ReaderPageViewController:(ReaderPageViewController*)readerPageViewController RefreshZoom:(BOOL)refresh;
-(void)ReaderPageViewController:(ReaderPageViewController*)readerPageViewController CurrentPage:(int)page;

@end