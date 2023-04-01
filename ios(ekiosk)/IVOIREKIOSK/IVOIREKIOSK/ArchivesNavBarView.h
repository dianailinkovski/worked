//
//  ArchivesNavBarView.h
//  IVOIREKIOSK
//
//  Created by Maxime Julien-Paquet on 2014-01-17.
//  Copyright (c) 2014 Maxime Julien-Paquet. All rights reserved.
//

#import <UIKit/UIKit.h>

@protocol ArchivesNavBarViewDelegate <NSObject>

-(void)leftGetTouched;
-(void)rightGetTouched;

@end

@interface ArchivesNavBarView : UIView {
    int currentMonth;
    CGPoint nextCenter, nextSide;
    BOOL touched;
}

@property (nonatomic, weak) __weak id <ArchivesNavBarViewDelegate> delegate;

@property (nonatomic, strong) UILabel *leftLabel;
@property (nonatomic, strong) UILabel *centerLabel;
@property (nonatomic, strong) UILabel *rightLabel;

@property (nonatomic, strong) UIButton *leftButton;
@property (nonatomic, strong) UIButton *rightButton;

@property (nonatomic, strong) NSArray *monthArray;

-(void)setcurrentmonth:(int)currentmonth;
-(void)setup;

-(void)animationLeft;
-(void)animationRight;

@end
