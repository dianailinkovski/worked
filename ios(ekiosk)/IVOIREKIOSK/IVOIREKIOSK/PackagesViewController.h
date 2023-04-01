//
//  PackagesViewController.h
//  IVOIREKIOSK
//
//  Created by Maxime Julien-Paquet on 2014-01-10.
//  Copyright (c) 2014 Maxime Julien-Paquet. All rights reserved.
//

#import <UIKit/UIKit.h>
#import "GetPackagesOperation.h"

@interface PackagesViewController : UIViewController <GetPackagesOperationDelegate, UICollectionViewDelegate, UICollectionViewDataSource>

@property (nonatomic, strong) UICollectionView *abonnementCollectionView;
@property (nonatomic, strong, readonly) NSOperationQueue *operationQueue;

@end
