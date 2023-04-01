//
//  CodeActivationViewController.m
//  eKiosk
//
//  Created by Maxime Julien-Paquet on 2014-02-23.
//  Copyright (c) 2014 Maxime Julien-Paquet. All rights reserved.
//

#import "CodeActivationViewController.h"
#import "Reachability.h"
#import "GTMHTTPFetcher.h"

@interface CodeActivationViewController ()

@end

@implementation CodeActivationViewController

@synthesize codeTextField, submitButton, loadingCodeActivation;

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
    [self.view addSubview:[self loadingCodeActivation]];
}

-(void)viewDidAppear:(BOOL)animated {
    [super viewDidAppear:animated];
    [codeTextField becomeFirstResponder];
    
}

- (void)didReceiveMemoryWarning {
    [super didReceiveMemoryWarning];
    // Dispose of any resources that can be recreated.
}

-(UIActivityIndicatorView *)loadingCodeActivation {
    if (loadingCodeActivation == nil) {
        loadingCodeActivation = [[UIActivityIndicatorView alloc] initWithActivityIndicatorStyle:UIActivityIndicatorViewStyleWhiteLarge];
        loadingCodeActivation.autoresizingMask = UIViewAutoresizingFlexibleBottomMargin | UIViewAutoresizingFlexibleLeftMargin | UIViewAutoresizingFlexibleRightMargin | UIViewAutoresizingFlexibleTopMargin;
        loadingCodeActivation.frame = CGRectMake(0, 0, 40, 40);
        loadingCodeActivation.center = self.view.center;
        loadingCodeActivation.color = [UIColor blackColor];
        loadingCodeActivation.hidesWhenStopped = YES;
    }
    return loadingCodeActivation;
}

- (BOOL)textFieldShouldReturn:(UITextField *)textField {
    if (textField == codeTextField) {
        [textField resignFirstResponder];
        [self submitTouched];
    }
    
    return YES;
}
-(void)textFieldDidBeginEditing:(UITextField *)textField {
    //[textField setBackgroundColor:[UIColor whiteColor]];
    textField.layer.borderColor = [[UIColor whiteColor] CGColor];
    textField.layer.borderWidth = 0;
}

-(void)submitTouched {
    if ([codeTextField.text isEqualToString:@""]) {
        codeTextField.layer.borderColor = [[UIColor colorWithRed:1 green:0 blue:0 alpha:0.5] CGColor];
        codeTextField.layer.borderWidth = 1;
    }
    else {
        if (![self connected]) {
            UIAlertView *alert = [[UIAlertView alloc] initWithTitle:@"Erreur" message:@"Vérifier votre connexion internet et réessayer." delegate:nil cancelButtonTitle:@"Retour" otherButtonTitles:nil];
            [alert performSelectorOnMainThread:@selector(show) withObject:nil waitUntilDone:YES];
            return;
        }
        
        [submitButton setEnabled:NO];
        [self.loadingCodeActivation startAnimating];
        
        NSURL *url = [NSURL URLWithString:[NSString stringWithFormat:@"%@/AddCreditWithCode.php",kAppBaseURL]];
        NSURLRequest *request = [NSURLRequest requestWithURL:url];
        GTMHTTPFetcher* myFetcher = [GTMHTTPFetcher fetcherWithRequest:request];
        
        NSUserDefaults *defaults = [NSUserDefaults standardUserDefaults];
        NSDictionary *dic = [NSDictionary dictionaryWithObjectsAndKeys:
                             [defaults valueForKey:@"username"], @"username",
                             [defaults valueForKey:@"password"], @"password",
                             codeTextField.text, @"code",
                             nil];
        
        NSString *postString = [[NSString alloc] initWithData:[NSJSONSerialization dataWithJSONObject:dic options:0 error:nil] encoding:NSUTF8StringEncoding];
        [myFetcher setPostData:[[NSString stringWithFormat:@"data=%@",postString] dataUsingEncoding:NSUTF8StringEncoding]];
        [myFetcher beginFetchWithCompletionHandler:^(NSData *retrievedData, NSError *error) {
            if (error != nil) {
                // status code or network error
                NSLog(@"error confirmAction:");
                [[[UIAlertView alloc] initWithTitle:@"Erreur" message:@"Erreur de connexion internet." delegate:nil cancelButtonTitle:@"Retour" otherButtonTitles:nil] show];
                [submitButton setEnabled:YES];
                [self.loadingCodeActivation stopAnimating];
            } else {
                // succeeded
                
                NSMutableArray *publicTimeline = [NSJSONSerialization
                                                  JSONObjectWithData:retrievedData
                                                  options:NSJSONReadingMutableContainers
                                                  error:nil];
                if (publicTimeline == nil) {
                    NSString *dataString = [[NSString alloc] initWithData:retrievedData encoding:NSUTF8StringEncoding];
                    NSLog(@"dataString = %@", dataString);
                    UIAlertView *alert = [[UIAlertView alloc] initWithTitle:@"Erreur" message:[NSString stringWithFormat:@"%@",dataString] delegate:nil cancelButtonTitle:@"Retour" otherButtonTitles:nil];
                    [alert show];
                    [submitButton setEnabled:YES];
                    [self.loadingCodeActivation stopAnimating];
                    
                    return;
                }
                
                NSLog(@"%@",publicTimeline);
                if ([[publicTimeline valueForKey:@"resultat"] isEqualToString:@"true"]) {
                    //TODO
                   /* int total = [[[publicTimeline valueForKey:@"data"] valueForKey:@"total"] intValue];
                    NSString *ekcreditString = [NSString stringWithFormat:@"%d", total];
                    [defaults setObject:ekcreditString forKey:@"ekcredit"];
                    [defaults synchronize];*/
                    
                    [self completed];
                    [submitButton setEnabled:YES];
                    [self.loadingCodeActivation stopAnimating];
                    
                }
                else {
                    [[[UIAlertView alloc] initWithTitle:@"Erreur" message:[NSString stringWithFormat:@"%@",[publicTimeline valueForKey:@"data"]] delegate:nil cancelButtonTitle:@"Retour" otherButtonTitles:nil] show];
                    [submitButton setEnabled:YES];
                    [self.loadingCodeActivation stopAnimating];
                }
            }
        }];
        
        
    }
    
    
}

-(void)completed {
    [self.navigationController popViewControllerAnimated:YES];
    [[NSNotificationCenter defaultCenter] postNotificationName:@"CloseMenuPopupAndPushViewController" object:nil];
    [[NSNotificationCenter defaultCenter] postNotificationName:@"UpdateCreditCount" object:nil];
    //Modifier le message
    [[[UIAlertView alloc] initWithTitle:@"Informations" message:@"Votre abonnement EKIOSK MOBILE a été pris en compte." delegate:nil cancelButtonTitle:@"Continuer" otherButtonTitles:nil] show];
}

-(BOOL)connected {
    Reachability *reachability = [Reachability reachabilityForInternetConnection];
    NetworkStatus networkStatus = [reachability currentReachabilityStatus];
    return !(networkStatus == NotReachable);
}

@end
