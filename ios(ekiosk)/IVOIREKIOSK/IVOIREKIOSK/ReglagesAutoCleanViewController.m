//
//  ReglagesSubViewController.m
//  IVOIREKIOSK
//
//  Created by Maxime Julien-Paquet on 2014-01-08.
//  Copyright (c) 2014 Maxime Julien-Paquet. All rights reserved.
//

#import "ReglagesAutoCleanViewController.h"

@interface ReglagesAutoCleanViewController ()

@end

@implementation ReglagesAutoCleanViewController

@synthesize nbMaximumButton, deleteAfterButton, exclureFavorisSwitch;

- (id)initWithNibName:(NSString *)nibNameOrNil bundle:(NSBundle *)nibBundleOrNil {
    self = [super initWithNibName:nibNameOrNil bundle:nibBundleOrNil];
    if (self) {
        // Custom initialization
    }
    return self;
}

- (void)viewDidLoad {
    [super viewDidLoad];
	// Do any additional setup after loading the view.
}

- (void)didReceiveMemoryWarning {
    [super didReceiveMemoryWarning];
    // Dispose of any resources that can be recreated.
}

-(void)viewWillAppear:(BOOL)animated {
    [super viewWillAppear:animated];
    
    NSUserDefaults *defaults = [NSUserDefaults standardUserDefaults];
    int tousAfter = [[defaults objectForKey:@"tousAfter"] intValue];
    int nbMaximum = [[defaults objectForKey:@"nbMaximum"] intValue];
    int deleteAfter = [[defaults objectForKey:@"deleteAfter"] intValue];
    BOOL exclureFavoris = [[defaults objectForKey:@"excluFavoris"] boolValue];
    
    [self.recentsdurantButton setTitle:[self getRecentAfterText:tousAfter] forState:UIControlStateNormal];
    [self.nbMaximumButton setTitle:[self getNbMaximumText:nbMaximum] forState:UIControlStateNormal];
    [self.deleteAfterButton setTitle:[self getDeleteAfterText:deleteAfter] forState:UIControlStateNormal];
    [self.exclureFavorisSwitch setOn:exclureFavoris];
    
}

-(NSString*)getNbMaximumText:(int)indice {
    switch (indice) {
        case 0:
            return @"30 publications";
            break;
        case 1:
            return @"60 publications";
            break;
        case 2:
            return @"90 publications";
            break;
        case 3:
            return @"120 publications";
            break;
        case 4:
            return @"illimité";
            break;
            
        default:
            return @"";
            break;
    }
}

-(NSString*)getDeleteAfterText:(int)indice {
    switch (indice) {
        case 0:
            return @"15 jours";
            break;
        case 1:
            return @"30 jours";
            break;
        case 2:
            return @"60 jours";
            break;
        case 3:
            return @"90 jours";
            break;
        case 4:
            return @"illimité";
            break;
            
        default:
            return @"";
            break;
    }
}

-(NSString*)getRecentAfterText:(int)indice {
    switch (indice) {
        case 0:
            return @"7 jours";
            break;
        case 1:
            return @"15 jours";
            break;
        case 2:
            return @"30 jours";
            break;
        case 3:
            return @"Toujours";
            break;
            
        default:
            return @"";
            break;
    }
}
-(void)FavSwitchChanged:(id)sender {
    UISwitch *temp = (UISwitch*)sender;
    [[NSNotificationCenter defaultCenter] postNotificationName:@"FavorisSwitchChanged" object:[NSNumber numberWithBool:temp.isOn]];
}

@end
