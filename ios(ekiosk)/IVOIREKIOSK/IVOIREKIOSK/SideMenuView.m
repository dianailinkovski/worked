//
//  SideMenuView.m
//  eKiosk
//
//  Created by Maxime Julien-Paquet on 2014-03-04.
//  Copyright (c) 2014 Maxime Julien-Paquet. All rights reserved.
//

#import "SideMenuView.h"
#import <QuartzCore/QuartzCore.h>

@interface SideMenuView () {
    
    UIViewController *_viewController;
    UINavigationController *_navigationController;
    
}
@end

@implementation SideMenuView

@synthesize nc;

-(id)initWithFrame:(CGRect)frame {
    self = [super initWithFrame:frame];
    if (self) {
        // Initialization code
        
        //[[NSNotificationCenter defaultCenter] addObserver:self selector:@selector(show) name:@"SideMenuShow" object:nil];
        //[[NSNotificationCenter defaultCenter] addObserver:self selector:@selector(hide) name:@"SideMenuHide" object:nil];
        
        self.opaque = NO;
        self.backgroundColor = [UIColor clearColor];
        
        self.frame = CGRectMake(-frame.size.width, frame.origin.y, frame.size.width, frame.size.height);
        self.layer.shadowColor = [UIColor blackColor].CGColor;
        self.layer.shadowOpacity = 0.6;
        self.layer.shadowRadius = 5;
        self.layer.shadowOffset = CGSizeMake(20.0f, 20.0f);
        
    }
    return self;
}

-(void)dealloc {
    //[[NSNotificationCenter defaultCenter] removeObserver:self name:@"SideMenuShow" object:nil];
    //[[NSNotificationCenter defaultCenter] removeObserver:self name:@"SideMenuHide" object:nil];
}

-(void)setViewController:(UIViewController*)vc {
    _viewController = vc;
    self.nc = [[UINavigationController alloc] initWithRootViewController:_viewController];
    [self addSubview:nc.view];
    
}

-(void)show {
    [UIView animateWithDuration:0.5 animations:^{
        CGRect frame = self.frame;
        frame.origin.x = 0;
        self.frame = frame;
        self.alpha = 1;
    }];
}

-(void)hide {
    [UIView animateWithDuration:0.5 animations:^{
        CGRect frame = self.frame;
        frame.origin.x = -frame.size.width;
        self.frame = frame;
        self.alpha = 0;
    }];
}

@end
