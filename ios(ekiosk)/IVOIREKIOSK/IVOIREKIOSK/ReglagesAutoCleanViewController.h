//
//  ReglagesSubViewController.h
//  IVOIREKIOSK
//
//  Created by Maxime Julien-Paquet on 2014-01-08.
//  Copyright (c) 2014 Maxime Julien-Paquet. All rights reserved.
//

#import <UIKit/UIKit.h>

@interface ReglagesAutoCleanViewController : UIViewController

@property (nonatomic, strong) IBOutlet UIButton *recentsdurantButton;
@property (nonatomic, strong) IBOutlet UIButton *nbMaximumButton;
@property (nonatomic, strong) IBOutlet UIButton *deleteAfterButton;
@property (nonatomic, strong) IBOutlet UISwitch *exclureFavorisSwitch;

-(IBAction)FavSwitchChanged:(id)sender;

@end
