//
//  LoginViewController.m
//  IVOIREKIOSK
//
//  Created by Maxime Julien-Paquet on 2014-01-09.
//  Copyright (c) 2014 Maxime Julien-Paquet. All rights reserved.
//

#import "LoginViewController.h"
#import "Reachability.h"

@interface LoginViewController ()

@end

@implementation LoginViewController

@synthesize usernameTextField, passwordTextField, loginButton, delegate;

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
    [usernameTextField becomeFirstResponder];
    
}

-(void)viewDidAppear:(BOOL)animated {
    [super viewDidAppear:animated];
    
    
}

- (void)didReceiveMemoryWarning {
    [super didReceiveMemoryWarning];
    // Dispose of any resources that can be recreated.
}

-(BOOL)textFieldShouldReturn:(UITextField *)textField {
    if (textField == usernameTextField) {
        [passwordTextField becomeFirstResponder];
    }
    if (textField == passwordTextField) {
        [textField resignFirstResponder];
        [self loginTouched:nil];
    }
    
    
    return YES;
}
-(void)textFieldDidBeginEditing:(UITextField *)textField {
    //[textField setBackgroundColor:[UIColor whiteColor]];
    textField.layer.borderColor = [[UIColor whiteColor] CGColor];
    textField.layer.borderWidth = 0;
}

-(void)loginTouched:(id)sender {
    if ([usernameTextField.text isEqualToString:@""]) {
        usernameTextField.layer.borderColor = [[UIColor colorWithRed:1 green:0 blue:0 alpha:0.5] CGColor];
        usernameTextField.layer.borderWidth = 1;
        //[usernameTextField setBackgroundColor:[UIColor colorWithRed:1 green:0 blue:0 alpha:0.5]];
    }
    else if ([passwordTextField.text isEqualToString:@""]) {
        passwordTextField.layer.borderColor = [[UIColor colorWithRed:1 green:0 blue:0 alpha:0.5] CGColor];
        passwordTextField.layer.borderWidth = 1;
        //[passwordTextField setBackgroundColor:[UIColor colorWithRed:1 green:0 blue:0 alpha:0.5]];
    }
    else {
        if (![self connected]) {
            UIAlertView *alert = [[UIAlertView alloc] initWithTitle:@"Erreur" message:@"Vérifier votre connexion internet et réessayer." delegate:nil cancelButtonTitle:@"Retour" otherButtonTitles:nil];
            [alert performSelectorOnMainThread:@selector(show) withObject:nil waitUntilDone:YES];
            return;
        }
        
        NSUserDefaults *defaults = [NSUserDefaults standardUserDefaults];
        int ekcredit = [[defaults valueForKey:@"ekcredit"] intValue];
        
        NSMutableURLRequest *theRequest = [[NSMutableURLRequest alloc] initWithURL:[NSURL URLWithString:[NSString stringWithFormat:@"%@/Login.php?username=%@&password=%@&ekcredit=%d", kAppBaseURL, [usernameTextField text], [passwordTextField text], ekcredit]] cachePolicy:NSURLRequestReloadIgnoringCacheData timeoutInterval:30];
        
        NSError        *error = nil;
        NSURLResponse  *response = nil;
        
        NSData *data = [NSURLConnection sendSynchronousRequest:theRequest returningResponse:&response error:&error];
        if (data == nil) {
            UIAlertView *alert = [[UIAlertView alloc] initWithTitle:@"Erreur" message:@"Erreur lors de la connexion" delegate:nil cancelButtonTitle:@"Retour" otherButtonTitles:nil];
            [alert performSelectorOnMainThread:@selector(show) withObject:nil waitUntilDone:YES];
            return;
        }
        NSArray *publicTimeline = [NSJSONSerialization JSONObjectWithData:data options:0 error:&error];
        if (publicTimeline == nil) {
            NSString *dataString = [[NSString alloc] initWithData:data encoding:NSUTF8StringEncoding];
            NSLog(@"dataString = %@", dataString);
            UIAlertView *alert = [[UIAlertView alloc] initWithTitle:@"Erreur" message:dataString delegate:nil cancelButtonTitle:@"Retour" otherButtonTitles:nil];
            [alert performSelectorOnMainThread:@selector(show) withObject:nil waitUntilDone:YES];
            return;
        }
        
        if ([[publicTimeline valueForKey:@"resultat"] isEqualToString:@"true"]) {
            NSLog(@"publicTimeline = %@", publicTimeline);
            NSString *usernameString = [[publicTimeline valueForKey:@"data"] valueForKey:@"email"];
            NSString *passwordString = [[publicTimeline valueForKey:@"data"] valueForKey:@"password"];
            NSString *prenomString = [[publicTimeline valueForKey:@"data"] valueForKey:@"first_name"];
            NSString *nomString = [[publicTimeline valueForKey:@"data"] valueForKey:@"last_name"];
            NSString *mobileString = [[publicTimeline valueForKey:@"data"] valueForKey:@"mobile"];
            NSString *ekcreditString = [[publicTimeline valueForKey:@"data"] valueForKey:@"ek_credit"];
            NSString *activatedString = [[publicTimeline valueForKey:@"data"] valueForKey:@"activated"];
            
            [defaults setObject:usernameString forKey:@"username"];
            [defaults setObject:passwordString forKey:@"password"];
            [defaults setObject:prenomString forKey:@"prenom"];
            [defaults setObject:nomString forKey:@"nom"];
            [defaults setObject:mobileString forKey:@"mobile"];
            [defaults setObject:ekcreditString forKey:@"ekcredit"];
            
            [defaults setObject:nil forKey:@"lastSkipCompte"];
            
            [defaults synchronize];
            
            [[NSNotificationCenter defaultCenter] postNotificationName:@"ChangementDeStatusDuCompte" object:nil];
            if ([activatedString isEqualToString:@"1"]) {
                if (delegate && [delegate respondsToSelector:@selector(loginComplete)]) {
                    [delegate loginComplete];
                }
                else {
                    [self dismissViewControllerAnimated:YES completion:^{
                        [[NSNotificationCenter defaultCenter] postNotificationName:@"ChangementDeStatusDuCompte" object:nil];
                    }];
                }
            
            }
            else {
                
                if (delegate && [delegate respondsToSelector:@selector(loginCompleteRequireActivation)]) {
                    [delegate loginCompleteRequireActivation];
                }
                else {
                    
                    NSLog(@"loginfrommenu");
                    NSString *storyboardString = @"Main_iPhone";
                    if (isPad()) {
                        storyboardString = @"Main_iPad";
                    }
                    
                    UIStoryboard *sb = [UIStoryboard storyboardWithName:storyboardString bundle:nil];
                    
                    CompteNonActiverViewController * controller = (CompteNonActiverViewController*)[sb instantiateViewControllerWithIdentifier:@"CompteNonActiverViewController"];
                    [controller setModalPresentationStyle:UIModalPresentationFormSheet];
                    [controller setDelegate:self];
                    [self.navigationController pushViewController:controller animated:YES];
                    
                }
                /*
                NSString *storyboardString = @"Main_iPhone";
                if (isPad()) {
                    storyboardString = @"Main_iPad";
                }
                
                UIStoryboard *sb = [UIStoryboard storyboardWithName:storyboardString bundle:nil];
                
                CompteNonActiverViewController * controller = (CompteNonActiverViewController*)[sb instantiateViewControllerWithIdentifier:@"CompteNonActiverViewController"];
                [controller setModalPresentationStyle:UIModalPresentationFormSheet];
                [controller setDelegate:self];
                [self.navigationController pushViewController:controller animated:YES];
                */
            }
            
            //[self.view removeFromSuperview];
            //[self dismissViewController];
        }
        else {
            [[[UIAlertView alloc] initWithTitle:@"Erreur" message:[NSString stringWithFormat:@"%@",[publicTimeline valueForKey:@"data"]] delegate:nil cancelButtonTitle:@"Retour" otherButtonTitles:nil] show];
        }
        
        
    }
}

-(void)dismissViewController {
    //[self.navigationController popViewControllerAnimated:YES];
    //[[NSNotificationCenter defaultCenter] postNotificationName:@"ChangementDeStatusDuCompte" object:nil];
    [self dismissViewControllerAnimated:YES completion:^{
        [[NSNotificationCenter defaultCenter] postNotificationName:@"ChangementDeStatusDuCompte" object:nil];
    }];
    //[self.view removeFromSuperview];
    //[self removeFromParentViewController];
}

#pragma mark - CompteNonActiverDelegate

-(void)dismissFromActivation {
    NSLog(@"retour2");
    [self dismissViewControllerAnimated:YES completion:^{
        NSLog(@"retour2 - completion");
        [[NSNotificationCenter defaultCenter] postNotificationName:@"ChangementDeStatusDuCompte" object:nil];
    
    }];
}

-(void)compteActiver {
    [self dismissViewControllerAnimated:YES completion:^{
        [[NSNotificationCenter defaultCenter] postNotificationName:@"ChangementDeStatusDuCompte" object:nil];
        
    }];
}

- (BOOL)connected {
    Reachability *reachability = [Reachability reachabilityForInternetConnection];
    NetworkStatus networkStatus = [reachability currentReachabilityStatus];
    return !(networkStatus == NotReachable);
}

@end
