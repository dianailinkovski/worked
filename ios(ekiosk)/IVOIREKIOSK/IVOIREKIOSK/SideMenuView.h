//
//  SideMenuView.h
//  eKiosk
//
//  Created by Maxime Julien-Paquet on 2014-03-04.
//  Copyright (c) 2014 Maxime Julien-Paquet. All rights reserved.
//

#import <UIKit/UIKit.h>

@interface SideMenuView : UIView

@property (nonatomic, strong) UINavigationController *nc;

-(void)setViewController:(UIViewController*)vc;
-(void)show;
-(void)hide;

@end
