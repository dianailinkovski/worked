//
//  DetailEditionsHeaderViewCell.m
//  eKiosk
//
//  Created by Maxime Julien-Paquet on 2014-03-04.
//  Copyright (c) 2014 Maxime Julien-Paquet. All rights reserved.
//

#import "DetailEditionsHeaderViewCell.h"

@implementation DetailEditionsHeaderViewCell

@synthesize favorisButton, deleteButton;

-(id)init {
    self = [super init];
    if (self) {
        [self setup];
    }
    return self;
}

-(id)initWithFrame:(CGRect)frame {
    self = [super initWithFrame:frame];
    if (self) {
        // Initialization code
        [self setup];
    }
    return self;
}

-(void)dealloc {
    [[NSNotificationCenter defaultCenter] removeObserver:self name:@"HeaderSwitchToLandscape" object:nil];
    [[NSNotificationCenter defaultCenter] removeObserver:self name:@"HeaderSwitchToPortrait" object:nil];
}

-(void)prepareForReuse {
    [super prepareForReuse];
    
    [[NSNotificationCenter defaultCenter] removeObserver:self name:@"HeaderSwitchToLandscape" object:nil];
    [[NSNotificationCenter defaultCenter] removeObserver:self name:@"HeaderSwitchToPortrait" object:nil];
    
    
    [self.imageView removeFromSuperview];
    [self.nomLabel removeFromSuperview];
    [self.dateLabel removeFromSuperview];
    [self.categorieLabel removeFromSuperview];
    [self.favorisButton removeFromSuperview];
    [self.deleteButton removeFromSuperview];
    [self.firstLine removeFromSuperview];
    [self.secondLine removeFromSuperview];
    [self.otherIssuesLabel removeFromSuperview];
    [self.rightView removeFromSuperview];
    
    self.imageView = nil;
    self.nomLabel = nil;
    self.dateLabel = nil;
    self.categorieLabel = nil;
    self.favorisButton = nil;
    self.deleteButton = nil;
    self.firstLine = nil;
    self.secondLine = nil;
    self.otherIssuesLabel = nil;
    self.rightView = nil;
    
    [self setup];
}

-(void)setup {
    
    [[NSNotificationCenter defaultCenter] addObserver:self selector:@selector(AnimationToLandscape:) name:@"HeaderSwitchToLandscape" object:nil];
    [[NSNotificationCenter defaultCenter] addObserver:self selector:@selector(AnimationToPortrait:) name:@"HeaderSwitchToPortrait" object:nil];
    
    [self addSubview:[self imageView]];
    [self addSubview:[self rightView]];
    [self.rightView addSubview:[self nomLabel]];
    [self.rightView addSubview:[self dateLabel]];
    [self.rightView addSubview:[self categorieLabel]];
    [self.rightView addSubview:[self favorisButton]];
    [self.rightView addSubview:[self deleteButton]];
    [self.rightView addSubview:[self firstLine]];
    [self.rightView addSubview:[self secondLine]];
    [self.detailView addSubview:[self otherIssuesLabel]];
    
}

-(UIButton *)favorisButton {
    if (favorisButton == nil) {
        favorisButton = [UIButton buttonWithType:UIButtonTypeCustom];
        
        if (isPad()) {
            favorisButton.frame = CGRectMake(50, 200, 300, 45);
            [favorisButton.titleLabel setFont:[UIFont fontWithName:@"Helvetica" size:30]];
        }
        else {
            favorisButton.frame = CGRectMake(9, 110, 140, 30);
            [favorisButton.titleLabel setFont:[UIFont fontWithName:@"Helvetica" size:16]];
        }
        
        [favorisButton setBackgroundColor:[UIColor colorWithRed:82.f/255.f green:182.f/255.f blue:21.f/255.f alpha:1]];
        [favorisButton.layer setCornerRadius:5];
        [favorisButton setTitle:@"Favoris" forState:UIControlStateNormal];
    }
    return favorisButton;
}

-(UIButton *)deleteButton {
    if (deleteButton == nil) {
        deleteButton = [UIButton buttonWithType:UIButtonTypeCustom];
        
        if (isPad()) {
            deleteButton.frame = CGRectMake(50, 255, 300, 45);
            [deleteButton.titleLabel setFont:[UIFont fontWithName:@"Helvetica" size:30]];
        }
        else {
            deleteButton.frame = CGRectMake(9, 150, 140, 30);
            [deleteButton.titleLabel setFont:[UIFont fontWithName:@"Helvetica" size:16]];
        }
        
        [deleteButton setBackgroundColor:[UIColor redColor]];
        [deleteButton.layer setCornerRadius:5];
        [deleteButton setTitle:@"Supprimer" forState:UIControlStateNormal];
    }
    return deleteButton;
}

@end
