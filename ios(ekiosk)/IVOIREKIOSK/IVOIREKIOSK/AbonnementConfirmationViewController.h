//
//  AbonnementConfirmationViewController.h
//  IVOIREKIOSK
//
//  Created by Maxime Julien-Paquet on 2014-01-13.
//  Copyright (c) 2014 Maxime Julien-Paquet. All rights reserved.
//

#import <UIKit/UIKit.h>

@protocol AbonnementConfirmationViewControllerDelegate;

@interface AbonnementConfirmationViewController : UIViewController <UICollectionViewDataSource, UICollectionViewDelegate>

@property (nonatomic, strong) UICollectionView *mainCollectionView;
@property (nonatomic, strong) IBOutlet UISegmentedControl *segmentedControl;
@property (nonatomic, strong) IBOutlet UILabel *escompteLabel;
@property (nonatomic, strong) IBOutlet UILabel *abonnementLabel;
@property (nonatomic, strong) IBOutlet UIImageView *backgroundImageView;

@property (nonatomic, strong) NSMutableArray *dataArray;
@property (nonatomic, strong) NSMutableArray *packageArray;

@property (nonatomic, strong) NSMutableArray *journauxArray;

@property (nonatomic, weak) __weak id <AbonnementConfirmationViewControllerDelegate> delegate;

-(IBAction)modifierButton:(id)sender;
-(IBAction)confirmerButton:(id)sender;

-(IBAction)segmentedChange:(id)sender;

@end

@protocol AbonnementConfirmationViewControllerDelegate <NSObject>

-(void)didDismissAbonnementConfirmationViewController;

@end