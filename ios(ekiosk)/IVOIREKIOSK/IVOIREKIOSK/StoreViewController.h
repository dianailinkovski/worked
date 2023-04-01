//
//  StoreViewController.h
//  IVOIREKIOSK
//
//  Created by Maxime Julien-Paquet on 2013-12-18.
//  Copyright (c) 2013 Maxime Julien-Paquet. All rights reserved.
//

#import <UIKit/UIKit.h>
//#import "GetWithCategories.h"
#import "DetailsStoreViewController.h"
#import "StoreTabBarViewController2.h"
#import "MiniVCLabel.h"

@interface StoreViewController : UIViewController <DetailsStoreViewControllerDelegate, UIActionSheetDelegate, UIAlertViewDelegate, UICollectionViewDataSource, UICollectionViewDelegate> {
    NSManagedObjectContext *insertionContext;
    NSEntityDescription *editionEntityDescription;
    
    UICollectionView *storeCollectionView;
}

@property (nonatomic, strong) UITabBar *tabBar;

@property (nonatomic, strong) StoreTabBarViewController2 *storeTabBarViewController;

@property (nonatomic, retain, readonly) NSManagedObjectContext *insertionContext;
@property (nonatomic, retain, readonly) NSEntityDescription *editionEntityDescription;


//@property (nonatomic, strong) IBOutlet UIScrollView  *mainScrolView;
@property (nonatomic, strong) IBOutlet UIPageControl *pageControl;

@property (nonatomic, strong) IBOutlet UIButton *categorieButton;
@property (nonatomic, strong) IBOutlet UISwitch *abonnementSwitch;

@property (nonatomic, strong) NSDictionary *tempDictionary;
@property (nonatomic, strong) UIImageView *tempCoverAnimationView;

@property (nonatomic, strong) MiniVCLabel *currentCreditLabel;

@property (nonatomic, strong) NSMutableArray *dataArray;
@property (nonatomic, strong) UIActivityIndicatorView *loadingAnimation;

-(IBAction)onTouchCategorie:(id)sender;

-(IBAction)archiveTouched:(id)sender;

@end
