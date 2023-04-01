//
//  DetailHeaderViewCell.h
//  eKiosk
//
//  Created by Maxime Julien-Paquet on 2014-03-03.
//  Copyright (c) 2014 Maxime Julien-Paquet. All rights reserved.
//

#import <UIKit/UIKit.h>

#import "EditionImageView.h"
#import "VCLabel.h"
#import "AdsHeaderCollectionView.h"

@interface DetailHeaderViewCell : UICollectionReusableView

@property (nonatomic, strong) EditionImageView *imageView;
@property (nonatomic, strong) UILabel *dateLabel;
@property (nonatomic, strong) UILabel *nomLabel;
@property (nonatomic, strong) UILabel *categorieLabel;
@property (nonatomic, strong) VCLabel *prixStringLabel;
@property (nonatomic, strong) UILabel *creditwarningLabel;
@property (nonatomic, strong) UIButton *prixButton;
@property (nonatomic, strong) UIView *noteButtonView;
@property (nonatomic, strong) UILabel *noteButtonLabel;

@property (nonatomic, strong) UIView *rightView;

@property (nonatomic, strong) UIImageView *firstLine;
@property (nonatomic, strong) UIImageView *secondLine;

@property (nonatomic, strong) UILabel *otherIssuesLabel;

@property (nonatomic, strong) UIActivityIndicatorView *verifAccountValideAI;

@property (nonatomic, strong) AdsHeaderCollectionView *adsView;

@property (nonatomic, strong) UIView *detailView;

@property (nonatomic, strong) UIImage *downloadedImage;

-(void)movePrixButtonBought:(BOOL)bought;

-(void)AnimationToLandscape:(float)duration;
-(void)AnimationToPortrait:(float)duration;
-(void)PubModOff;
-(void)PubModOn;

@end
