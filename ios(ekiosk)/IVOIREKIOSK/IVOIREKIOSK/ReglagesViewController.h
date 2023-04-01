//
//  ReglagesViewController.h
//  IVOIREKIOSK
//
//  Created by Maxime Julien-Paquet on 2014-01-08.
//  Copyright (c) 2014 Maxime Julien-Paquet. All rights reserved.
//

#import <UIKit/UIKit.h>
//#import "MiniVCLabel.h"

@interface ReglagesViewController : UIViewController

@property (nonatomic, strong) IBOutlet UILabel *profilLabel;
@property (nonatomic, strong) IBOutlet UILabel *favLabel;

-(void)setFavorisText:(BOOL)value;

@end
