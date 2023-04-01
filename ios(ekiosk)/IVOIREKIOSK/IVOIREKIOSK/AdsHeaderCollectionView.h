//
//  AdsHeaderCollectionView.h
//  eKiosk
//
//  Created by maxime on 2014-07-31.
//  Copyright (c) 2014 Maxime Julien-Paquet. All rights reserved.
//

#import <UIKit/UIKit.h>

@interface AdsHeaderCollectionView : UICollectionReusableView

@property (nonatomic, strong) NSURL *url;
@property (nonatomic, strong) UIActivityIndicatorView *activityIndicator;
@property (nonatomic, strong) UIImageView *imageView;
@property (nonatomic, strong) UIButton *overButton;

@property (nonatomic, strong) NSString *urlToOpen;

@property (nonatomic, strong) UIImage *downloadedImage;

-(void)startDownload;
-(void)setImageUrl:(NSString*)tempUrl;
-(void)clearView;

@end
