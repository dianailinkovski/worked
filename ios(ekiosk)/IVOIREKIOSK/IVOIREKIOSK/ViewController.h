//
//  ViewController.h
//  IVOIREKIOSK
//
//  Created by Maxime Julien-Paquet on 2013-12-03.
//  Copyright (c) 2013 Maxime Julien-Paquet. All rights reserved.
//

#import <UIKit/UIKit.h>
#import "DownloadOperation.h"

#import "FPPopoverKeyboardResponsiveController.h"

#import "SideMenuView.h"
#import "OverTutorielViewController.h"

@interface ViewController : UIViewController <DownloadOperationDelegate, UICollectionViewDataSource, UICollectionViewDelegate, UIActionSheetDelegate> {
    
    NSManagedObjectContext *managedObjectContext;
    int downloadCount, downloadTotal;
    
    UICollectionView *issuesCollectionView;
    
    CGFloat _keyboardHeight;
    FPPopoverKeyboardResponsiveController *popover;
    SideMenuView *popover2;
}

//@property (nonatomic, strong) UIScrollView *mainScrollView;
@property (nonatomic, strong) NSManagedObjectContext *managedObjectContext;

@property (nonatomic, strong, readonly) NSOperationQueue *operationQueue;

@property (nonatomic, strong) UIProgressView *progressView;
@property (nonatomic, strong) UILabel *countLabel;
@property (nonatomic, strong) UIImageView *backgroundProgressImageView;

@property (nonatomic, strong) IBOutlet UISegmentedControl *filtreSegmented;

@property (nonatomic, strong) IBOutlet UIBarButtonItem *kioskButtonItem;

@property (nonatomic, strong) UIImageView *noIssuesImageView;

@property (nonatomic, strong) OverTutorielViewController *overVC;

-(IBAction)reglages:(id)sender;
-(IBAction)segmentedSelectionChanged:(id)sender;

-(void)reloadCollectionView:(NSNotification*)notif;

-(void)pushReaderWithEdition:(Editions*)refEdition;

@end
