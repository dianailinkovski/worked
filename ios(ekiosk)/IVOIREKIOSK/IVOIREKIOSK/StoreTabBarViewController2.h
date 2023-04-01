//
//  StoreTabBarViewController2.h
//  IVOIREKIOSK
//
//  Created by Maxime Julien-Paquet on 2014-01-13.
//  Copyright (c) 2014 Maxime Julien-Paquet. All rights reserved.
//

#import <UIKit/UIKit.h>

#import "FPPopoverKeyboardResponsiveController.h"
#import "SideMenuView.h"

@interface StoreTabBarViewController2 : UIViewController <UITabBarDelegate, UIActionSheetDelegate> {
    FPPopoverKeyboardResponsiveController *popover;
    SideMenuView *popover2;
    CGFloat _keyboardHeight;
    
}

@property (nonatomic, strong) IBOutlet UIBarButtonItem *menuButton;
@property (nonatomic, strong) IBOutlet UITabBar *tabBar;
@property (nonatomic, strong) UIViewController *subViewController;
@property (nonatomic, strong) IBOutlet UIView *subView;

-(IBAction)dismissViewController:(id)sender;
-(IBAction)reglages:(id)sender;
-(void)goToAbonnement;

@end
