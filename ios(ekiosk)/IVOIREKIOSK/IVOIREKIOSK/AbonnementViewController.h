//
//  AbonnementViewController.h
//  IVOIREKIOSK
//
//  Created by Maxime Julien-Paquet on 2014-01-11.
//  Copyright (c) 2014 Maxime Julien-Paquet. All rights reserved.
//

#import <UIKit/UIKit.h>
#import "JournalPickerViewCell.h"
#import "AbonnementConfirmationViewController.h"
#import "GetJournauxForPackages.h"

@interface AbonnementViewController : UIViewController <GetJournauxForPackagesDelegate, UICollectionViewDataSource, UICollectionViewDelegate, UIAlertViewDelegate, AbonnementConfirmationViewControllerDelegate> {
    
}

@property (nonatomic, strong) UICollectionView *mainCollectionView;
@property (nonatomic, strong, readonly) NSOperationQueue *operationQueue;
@property (nonatomic, strong) NSMutableArray *packageArray;
@property (nonatomic, strong) NSMutableArray *selectedCountArray;


-(id)initWithPackage:(NSMutableArray*)package;

@end

