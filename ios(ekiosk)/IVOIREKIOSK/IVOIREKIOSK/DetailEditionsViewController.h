//
//  DetailEditionsViewController.h
//  IVOIREKIOSK
//
//  Created by Maxime Julien-Paquet on 2013-12-29.
//  Copyright (c) 2013 Maxime Julien-Paquet. All rights reserved.
//

#import <UIKit/UIKit.h>
#import "ViewController.h"
#import "BottomDetailsStoreView.h"

@class Editions;
@class EditionImageView;

@interface DetailEditionsViewController : UIViewController <UIAlertViewDelegate, BottomDetailsStoreViewDelegate, UICollectionViewDataSource, UICollectionViewDelegate>

@property (nonatomic, strong) IBOutlet UINavigationBar *navBar;
@property (nonatomic, strong) UICollectionView *collectionView;
@property (nonatomic, strong) NSMutableArray *bottomDataArray;

@property (nonatomic, strong) NSManagedObjectContext *managedObjectContext;

@property (nonatomic, strong) Editions *edition;

@property (nonatomic, strong) ViewController *viewController;


-(IBAction)dismissViewController:(id)sender;
//-(IBAction)deletePublication:(id)sender;

@end
