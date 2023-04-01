//
//  ReglagesProfilViewController.m
//  IVOIREKIOSK
//
//  Created by Maxime Julien-Paquet on 2014-01-09.
//  Copyright (c) 2014 Maxime Julien-Paquet. All rights reserved.
//

#import "ReglagesProfilViewController.h"
#import "Login2ViewController.h"
#import "CreateProfil2ViewController.h"
#import "CodeActivationViewController.h"

@interface ReglagesProfilViewController () {
    BOOL loginAction;
}

@end

@implementation ReglagesProfilViewController

@synthesize firstButton, secondButton;

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
    [[NSNotificationCenter defaultCenter] addObserver:self
                                             selector:@selector(ChangementDeStatusDuCompte:)
                                                 name:@"ChangementDeStatusDuCompte"
                                               object:nil];
}

- (void)didReceiveMemoryWarning {
    [super didReceiveMemoryWarning];
    // Dispose of any resources that can be recreated.
}

-(void)dealloc {
    [[NSNotificationCenter defaultCenter] removeObserver:self name:@"ChangementDeStatusDuCompte" object:nil];
}

-(void)viewWillAppear:(BOOL)animated {
    [super viewWillAppear:animated];
    
    [self refreshInterface];
    
}

-(void)ChangementDeStatusDuCompte:(NSNotification*)notif {
    [self refreshInterface];
}

-(void)refreshInterface {
    NSUserDefaults *defaults = [NSUserDefaults standardUserDefaults];
    NSString *username = [defaults objectForKey:@"username"];
    NSString *password = [defaults objectForKey:@"password"];
    
    if (username == nil || [username isEqualToString:@""] ||
        password == nil || [password isEqualToString:@""]) {
        loginAction = YES;
        [firstButton setTitle:@"Me connecter" forState:UIControlStateNormal];
        [secondButton setTitle:@"Créer mon compte" forState:UIControlStateNormal];
        [secondButton setTitleColor:self.view.tintColor forState:UIControlStateNormal];
    }
    else {
        loginAction = NO;
        [firstButton setTitle:@"Code d'activation" forState:UIControlStateNormal];
        [secondButton setTitle:@"Déconnexion" forState:UIControlStateNormal];
        [secondButton setTitleColor:[UIColor redColor] forState:UIControlStateNormal];
    }
}

- (BOOL)shouldPerformSegueWithIdentifier:(NSString *)identifier sender:(id)sender {
    [super shouldPerformSegueWithIdentifier:identifier sender:sender];
    NSLog(@"%@",identifier);
    if (loginAction) {
        if ([identifier isEqualToString:@"MonCompteSegue"]) {
            NSString *storyboardString = @"Main_iPhone";
            if (isPad()) {
                storyboardString = @"Main_iPad";
            }
            
            UIStoryboard *sb = [UIStoryboard storyboardWithName:storyboardString bundle:nil];
            
            Login2ViewController* controller = (Login2ViewController*)[sb instantiateViewControllerWithIdentifier:@"Login2ViewController"];

            
            UINavigationController *navcon = [[UINavigationController alloc] initWithRootViewController:controller];
            [navcon setModalPresentationStyle:UIModalPresentationFormSheet];
            [[NSNotificationCenter defaultCenter] postNotificationName:@"CloseMenuPopupAndPushViewController" object:navcon];
            //[self.navigationController pushViewController:controller animated:YES];
            
        }
        else if ([identifier isEqualToString:@"MesAbonnementsSegue"]) {
            NSString *storyboardString = @"Main_iPhone";
            if (isPad()) {
                storyboardString = @"Main_iPad";
            }
            
            UIStoryboard *sb = [UIStoryboard storyboardWithName:storyboardString bundle:nil];
            
            CreateProfil2ViewController* controller = (CreateProfil2ViewController*)[sb instantiateViewControllerWithIdentifier:@"CreateProfil2ViewController"];
            //[controller setModalPresentationStyle:UIModalPresentationFormSheet];
            
            UINavigationController *navcon = [[UINavigationController alloc] initWithRootViewController:controller];
            [navcon setModalPresentationStyle:UIModalPresentationFormSheet];
            
            [[NSNotificationCenter defaultCenter] postNotificationName:@"CloseMenuPopupAndPushViewController" object:navcon];
        }
        
        return NO;
    }
    else {
        
        if ([identifier isEqualToString:@"MonCompteSegue"]) {
            NSString *storyboardString = @"Main_iPhone";
            if (isPad()) {
                storyboardString = @"Main_iPad";
            }
            
            UIStoryboard *sb = [UIStoryboard storyboardWithName:storyboardString bundle:nil];
            CodeActivationViewController* controller = (CodeActivationViewController*)[sb instantiateViewControllerWithIdentifier:@"CodeActivationViewController"];
            [self.navigationController pushViewController:controller animated:YES];
            
        }
        else if ([identifier isEqualToString:@"MesAbonnementsSegue"]) {
            [self logout:nil];
        }
        
        return NO;
    }
    
    return YES;
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
        [defaults setObject:nil forKey:@"ekcredit"];
        [defaults synchronize];
        [self.navigationController popViewControllerAnimated:YES];
        [[NSNotificationCenter defaultCenter] postNotificationName:@"CloseMenuPopupAndPushViewController" object:nil];
        [[NSNotificationCenter defaultCenter] postNotificationName:@"ChangementDeStatusDuCompte" object:nil];
    }
}

@end
