//
//  MonCompteViewController.h
//  IVOIREKIOSK
//
//  Created by Maxime Julien-Paquet on 2014-01-09.
//  Copyright (c) 2014 Maxime Julien-Paquet. All rights reserved.
//

#import <UIKit/UIKit.h>

@interface MonCompteViewController : UIViewController <UIAlertViewDelegate>

@property (nonatomic, strong) IBOutlet UIButton *nomButton;
@property (nonatomic, strong) IBOutlet UIButton *courrielButton;
@property (nonatomic, strong) IBOutlet UIButton *mobileButton;

-(IBAction)logout:(id)sender;
-(IBAction)restaurerLesAchats:(id)sender;

@end
