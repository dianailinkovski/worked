//
//  EditionView.h
//  IVOIREKIOSK
//
//  Created by Maxime Julien-Paquet on 2013-12-14.
//  Copyright (c) 2013 Maxime Julien-Paquet. All rights reserved.
//

#import <UIKit/UIKit.h>
#import "ViewController.h"

@class Editions;
@class EditionImageView;
@class FFCircularProgressView;

@interface EditionView : UICollectionViewCell {
    CGRect refFrame;
}

@property (nonatomic, strong) UIImageView *bannerImageView;

@property (nonatomic, strong) ViewController *refViewController;
@property (nonatomic, strong) UILongPressGestureRecognizer *longPressGestureRecognizer;
@property (nonatomic, strong) UITapGestureRecognizer *tapGestureRecognizer;

@property (nonatomic, strong) EditionImageView *coverImageView;
//@property (nonatomic, strong) UILabel *titleLabel;
@property (nonatomic, strong) Editions *edition;

@property (nonatomic, strong) UIImageView *overImageView;
@property (nonatomic, strong) FFCircularProgressView *progressView;

-(void)setEditionInView:(Editions *)refEdition;

-(void)setDownloading:(BOOL)val;
-(void)setProgression:(NSNumber*)progress;

-(BOOL)isDownloading;
-(void)handleTap:(UITapGestureRecognizer*)sender;

@end
