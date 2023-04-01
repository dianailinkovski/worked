//
//  VirtualCurrencyViewController.h
//  eKiosk
//
//  Created by Maxime Julien-Paquet on 2014-02-19.
//  Copyright (c) 2014 Maxime Julien-Paquet. All rights reserved.
//

#import <UIKit/UIKit.h>
#import "DetailVirtualCurrencyViewController.h"
#import "CompteViewController.h"
#import "CompteNonActiverViewController.h"

@interface VirtualCurrencyViewController : UIViewController <UICollectionViewDataSource, UICollectionViewDelegate, DetailVirtualCurrencyViewControllerDelegate, CompteViewControllerDelegate, CompteNonActiverViewControllerDelegate>

@property (nonatomic, strong) NSMutableArray *dataArray;
@property (nonatomic, strong) NSArray *itunesArray;

@property (nonatomic, strong) UICollectionView *vcBundleCollectionView;

@property (nonatomic, strong) UINavigationBar *navBar;
@property (nonatomic, strong) UIToolbar *toolBar;

@property (nonatomic, strong) UIActivityIndicatorView *loadingBundle;

-(void)addNavigationBarAndBackground;

@end
