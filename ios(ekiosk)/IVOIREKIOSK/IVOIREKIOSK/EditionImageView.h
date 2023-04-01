//
//  EditionImageView.h
//  IVOIREKIOSK
//
//  Created by Maxime Julien-Paquet on 2013-12-15.
//  Copyright (c) 2013 Maxime Julien-Paquet. All rights reserved.
//

#import <UIKit/UIKit.h>
#import "FTWCache.h"
#import "NSString+MD5.h"

#define STATIC_EDITIONSIMAGEVIEW_WIDTH 110
#define STATIC_EDITIONSIMAGEVIEW_HEIGHT 150

@interface EditionImageView : UIImageView

@property (nonatomic, strong) NSURL *url;
@property (nonatomic, strong) UIActivityIndicatorView *activityIndicator;
@property (nonatomic, strong) UIImageView *favImageView;

-(void)startDownload;

-(void)addBorder;
-(void)addBorderAndDropShadow;
-(void)addInnerShadow;

-(void)showFav;
-(void)hideFav;
-(void)showFavAnimated;
-(void)hideFavAnimated;

@end
