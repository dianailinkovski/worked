//
//  MonCompteViewController.m
//  IVOIREKIOSK
//
//  Created by Maxime Julien-Paquet on 2014-01-09.
//  Copyright (c) 2014 Maxime Julien-Paquet. All rights reserved.
//

#import "MonCompteViewController.h"
//#import "TestIAPHelper.h"

@interface MonCompteViewController ()

@end

@implementation MonCompteViewController

@synthesize nomButton, courrielButton, mobileButton;

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

-(void)viewWillAppear:(BOOL)animated {
    [super viewWillAppear:animated];
    
    
    NSUserDefaults *defaults = [NSUserDefaults standardUserDefaults];
    NSString *username = [defaults objectForKey:@"username"];
    NSString *nom = [NSString stringWithFormat:@"%@ %@",[defaults objectForKey:@"prenom"], [defaults objectForKey:@"nom"]];
    NSString *mobile = [defaults objectForKey:@"mobile"];
    
    
    [self.nomButton setTitle:nom forState:UIControlStateNormal];
    [self.courrielButton setTitle:username forState:UIControlStateNormal];
    [self.mobileButton setTitle:mobile forState:UIControlStateNormal];
    
}

- (void)didReceiveMemoryWarning {
    [super didReceiveMemoryWarning];
    // Dispose of any resources that can be recreated.
}

-(void)logout:(id)sender {
    UIAlertView *alert = [[UIAlertView alloc] initWithTitle:@"Avertissement" message:@"Voulez-vous vraiment déconnecter votre compte de cette appareil ?" delegate:self cancelButtonTitle:@"Non" otherButtonTitles:@"Oui", nil];
    [alert setTag:1001];
    [alert show];
}
-(void)alertView:(UIAlertView *)alertView clickedButtonAtIndex:(NSInteger)buttonIndex {
    if ([alertView tag] == 1001 && buttonIndex == 1) {
        NSUserDefaults *defaults = [NSUserDefaults standardUserDefaults];
        [defaults setObject:nil forKey:@"username"];
        [defaults setObject:nil forKey:@"password"];
        [defaults setObject:nil forKey:@"prenom"];
        [defaults setObject:nil forKey:@"nom"];
        [defaults setObject:nil forKey:@"mobile"];
        [defaults synchronize];
        [self.navigationController popViewControllerAnimated:YES];
        [[NSNotificationCenter defaultCenter] postNotificationName:@"ChangementDeStatusDuCompte" object:nil];
    }
}

/*
-(void)restaurerLesAchats:(id)sender {
    [[TestIAPHelper sharedInstance] restoreCompletedTransactions];
}
*/

@end
