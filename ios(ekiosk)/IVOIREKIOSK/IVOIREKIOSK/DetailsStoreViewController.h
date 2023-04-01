//
//  DetailsStoreViewController.h
//  IVOIREKIOSK
//
//  Created by Maxime Julien-Paquet on 2013-12-22.
//  Copyright (c) 2013 Maxime Julien-Paquet. All rights reserved.
//

#import <UIKit/UIKit.h>
#import "VCLabel.h"
#import "BuyingWithVirtualCurrencyViewController.h"
#import "MiniVCLabel.h"
#import "CompteViewController.h"
#import "CompteNonActiverViewController.h"

@class EditionImageView;

@protocol DetailsStoreViewControllerDelegate <NSObject>

-(void)didPurchaseItems:(NSDictionary*)data WithImage:(UIImage*)image AndFrame:(CGRect)frame;
-(void)openBoughtItem:(NSDictionary*)data;
-(void)pushToAbonnementFromDetailView;

@end

@interface DetailsStoreViewController : UIViewController <UIActionSheetDelegate, UICollectionViewDataSource, UICollectionViewDelegate, BuyingWithVirtualCurrencyViewControllerDelegate, CompteViewControllerDelegate, CompteNonActiverViewControllerDelegate>

@property (nonatomic, weak) __weak id <DetailsStoreViewControllerDelegate> delegate;

//@property (nonatomic, strong) IBOutlet UINavigationBar *navBar;
@property (nonatomic, strong) NSMutableDictionary *dataDictionary;

//@property (nonatomic, strong) IBOutlet EditionImageView *imageView;
//@property (nonatomic, strong) IBOutlet UILabel *dateLabel;
//@property (nonatomic, strong) IBOutlet UILabel *nomLabel;
//@property (nonatomic, strong) IBOutlet UILabel *categorieLabel;
//@property (nonatomic, strong) IBOutlet VCLabel *prixStringLabel;
//@property (nonatomic, strong) IBOutlet UILabel *creditwarningLabel;
//@property (nonatomic, strong) UIButton *prixButton;
//
//@property (nonatomic, strong) IBOutlet UIView *headerView;

@property (nonatomic, strong) UICollectionView *bottomCollectionView;
@property (nonatomic, strong) NSMutableArray *bottomDataArray;
@property (nonatomic, strong) UIActivityIndicatorView *loadingBottom;

@property (nonatomic, strong) MiniVCLabel *currentCreditLabel;

@property (nonatomic, strong) NSManagedObjectContext *managedObjectContext;

@property (nonatomic, strong) NSMutableArray *pubArray;

@end
