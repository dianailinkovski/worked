//
//  ArchivesJournauxViewController.h
//  IVOIREKIOSK
//
//  Created by Maxime Julien-Paquet on 2014-01-17.
//  Copyright (c) 2014 Maxime Julien-Paquet. All rights reserved.
//

#import <UIKit/UIKit.h>
//#import "GetJournauxOperation.h"

@interface ArchivesJournauxViewController : UIViewController <UICollectionViewDataSource, UICollectionViewDelegate>

@property (nonatomic, strong) UICollectionView *journauxCollectionView;
@property (nonatomic, strong) NSMutableArray *dataArray,*subscriptionArray;
@property (nonatomic, strong) UIActivityIndicatorView *loadingAnimation;

@end
