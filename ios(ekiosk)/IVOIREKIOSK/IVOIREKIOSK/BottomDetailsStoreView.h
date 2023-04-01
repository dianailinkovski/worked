//
//  BottomDetailsStoreView.h
//  IVOIREKIOSK
//
//  Created by Maxime Julien-Paquet on 2013-12-24.
//  Copyright (c) 2013 Maxime Julien-Paquet. All rights reserved.
//

#import <UIKit/UIKit.h>
#import "Editions.h"

@class EditionImageView;

@protocol BottomDetailsStoreViewDelegate;

@interface BottomDetailsStoreView : UIView <UIGestureRecognizerDelegate>

@property (nonatomic, weak) __weak id <BottomDetailsStoreViewDelegate> delegate;

@property (nonatomic, strong) EditionImageView *imageView;
@property (nonatomic, strong) UILabel *dateLabel;
@property (nonatomic, strong) NSDictionary *data;
@property (nonatomic, strong) Editions *edition;

-(id)initWithFrame:(CGRect)frame AndDictionary:(NSDictionary*)dictionary;
-(id)initWithFrame:(CGRect)frame AndEdition:(Editions*)refEdition;
-(void)setSelected;
-(void)setUnselected;
-(BOOL)isSelected;

@end

@protocol BottomDetailsStoreViewDelegate <NSObject>

-(void)BottomDetailsStoreViewTouched:(BottomDetailsStoreView*)bottomDetailView;

@end
