//
//  ReglagesIphoneViewController.m
//  eKiosk
//
//  Created by Maxime Julien-Paquet on 2014-03-04.
//  Copyright (c) 2014 Maxime Julien-Paquet. All rights reserved.
//

#import "ReglagesIphoneViewController.h"

@interface ReglagesIphoneViewController ()

@end

@implementation ReglagesIphoneViewController

-(void)viewWillAppear:(BOOL)animated {
    [super viewWillAppear:animated];
    UIBarButtonItem *left = [[UIBarButtonItem alloc] initWithTitle:@"Fermer" style:UIBarButtonItemStyleBordered target:self action:@selector(fermer)];
    [self.navigationItem setLeftBarButtonItem:left];
    [self.navigationItem setTitle:@"Mon compte"];
    [self refreshInterface];
    
}

-(void)viewWillDisappear:(BOOL)animated {
    [super viewWillDisappear:animated];
}
-(void)refreshInterface {
    
    NSUserDefaults *defaults = [NSUserDefaults standardUserDefaults];
    
    NSString *username = [defaults objectForKey:@"username"];
    
    if (username != nil && ![username isEqualToString:@""]) {
        [self.profilLabel setText:[NSString stringWithFormat:@"%@",username]];
    }
    else {
        [self.profilLabel setText:@"Profil"];
    }
    
    
    BOOL exclureFavoris = [[defaults objectForKey:@"excluFavoris"] boolValue];
    [self setFavorisText:exclureFavoris];
    
}

-(void)fermer {
    [[NSNotificationCenter defaultCenter] postNotificationName:@"SideMenuHide" object:nil];
}

@end
