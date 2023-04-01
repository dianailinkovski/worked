//
//  ArchivesMonthViewController.h
//  IVOIREKIOSK
//
//  Created by Maxime Julien-Paquet on 2014-01-15.
//  Copyright (c) 2014 Maxime Julien-Paquet. All rights reserved.
//

#import <UIKit/UIKit.h>
#import "DetailsStoreViewController.h"

@interface ArchivesMonthViewController : UIViewController <UICollectionViewDataSource, UICollectionViewDelegate, DetailsStoreViewControllerDelegate>

@property (nonatomic, strong) UICollectionView *monthCollectionView;
@property (nonatomic, strong) NSMutableArray *dataArray;

@property (nonatomic, strong) NSString *idJournalString;
@property (nonatomic, strong) NSString *dateString;

@property (nonatomic, retain, readonly) NSManagedObjectContext *insertionContext;
@property (nonatomic, retain, readonly) NSEntityDescription *editionEntityDescription;
@property (nonatomic, strong) NSDictionary *tempDictionary;
@property (nonatomic, strong) UIImageView *tempCoverAnimationView;
@property (nonatomic, strong) UIActivityIndicatorView *loadingAnimation;

-(id)initWithIdJournal:(NSString*)idjournal  AndDate:(NSString *)date;

@end
