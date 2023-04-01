//
//  CompteNonActiverViewController.m
//  eKiosk
//
//  Created by maxime on 2014-07-23.
//  Copyright (c) 2014 Maxime Julien-Paquet. All rights reserved.
//

#import "CompteNonActiverViewController.h"
#import "GTMHTTPFetcher.h"

@interface CompteNonActiverViewController () {
    BOOL isDismissWhenEnded;
}


@end

@implementation CompteNonActiverViewController

@synthesize activityIndicator, resendMailButton, validateActiviationButton, scrollview, delegate;

- (id)initWithNibName:(NSString *)nibNameOrNil bundle:(NSBundle *)nibBundleOrNil {
    self = [super initWithNibName:nibNameOrNil bundle:nibBundleOrNil];
    if (self) {
        // Custom initialization
        isDismissWhenEnded = NO;
    }
    return self;
}

- (void)viewDidLoad {
    [super viewDidLoad];
    // Do any additional setup after loading the view.
    
    UIBarButtonItem *temp =[[UIBarButtonItem alloc] initWithTitle:@"Retour" style:UIBarButtonItemStyleDone target:self action:@selector(retour)];
    self.navigationItem.leftBarButtonItem = temp;
    
}

- (void)didReceiveMemoryWarning {
    [super didReceiveMemoryWarning];
    // Dispose of any resources that can be recreated.
}

-(void)viewDidAppear:(BOOL)animated {
    [super viewDidAppear:animated];
    if (!isPad()) {
        NSLog(@"frame = %@", NSStringFromCGRect(self.scrollview.frame));
        
        if([UIScreen mainScreen].bounds.size.height != 568.0) {
            
            self.scrollview.frame = CGRectMake(0, 0, 320, 480);
            [self.scrollview setContentSize:CGSizeMake(self.view.frame.size.width, 520)];
            
            NSLog(@"frame = %@", NSStringFromCGRect(self.view.frame));
            
        }
        else {
            //self.scrollview.frame = CGRectMake(0, 0, 320, 508);
            [self.scrollview setContentSize:CGSizeMake(self.view.frame.size.width, 520)];
            
            NSLog(@"frame = %@", NSStringFromCGRect(self.view.frame));
        }
    }
}

-(void)SetDimsissWhenEnded:(BOOL)dismiss {
    isDismissWhenEnded = dismiss;
}

-(void)retour {
    //[self.navigationController dismissViewControllerAnimated:YES completion:nil];
    NSLog(@"retour");
    [self dismissViewControllerAnimated:YES completion:^{
        NSLog(@"retour - completion");
        if (delegate && [delegate respondsToSelector:@selector(dismissFromActivation)]) {
            NSLog(@"retour - delegate");
            [delegate dismissFromActivation];
        }
    }];
}

-(void)resendMail:(id)sender {
    
    NSUserDefaults *defaults = [NSUserDefaults standardUserDefaults];
    NSString *username = [defaults valueForKey:@"username"];
    NSString *password = [defaults valueForKey:@"password"];
    
    if (username == nil || password == nil) {
        return;
    }
    
    [validateActiviationButton setEnabled:NO];
    [resendMailButton setEnabled:NO];
    [activityIndicator startAnimating];
    
    NSLog(@"%@/resendActivationMail?username=%@&password=%@",kAppBaseURL, username, password);
    NSURL *url = [NSURL URLWithString:[NSString stringWithFormat:@"%@/resendActivationMail?username=%@&password=%@",kAppBaseURL, username, password]];
    NSURLRequest *request = [NSURLRequest requestWithURL:url];
    GTMHTTPFetcher* myFetcher = [GTMHTTPFetcher fetcherWithRequest:request];
    
    [myFetcher beginFetchWithCompletionHandler:^(NSData *retrievedData, NSError *error) {
        
        [validateActiviationButton setEnabled:YES];
        [resendMailButton setEnabled:YES];
        
        [activityIndicator stopAnimating];
        
        if (error != nil) {
            // status code or network error
            [[[UIAlertView alloc] initWithTitle:@"Erreur" message:@"Erreur de connexion internet." delegate:nil cancelButtonTitle:@"Retour" otherButtonTitles:nil] show];
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
                return;
            }
            
            if ([[publicTimeline valueForKey:@"resultat"] isEqualToString:@"true"]) {
                UIAlertView *alert = [[UIAlertView alloc] initWithTitle:@"Information" message:[publicTimeline valueForKey:@"data"] delegate:nil cancelButtonTitle:@"Retour" otherButtonTitles:nil];
                [alert show];
            }
            
            
        }
    }];
}

-(void)validateActivation:(id)sender {
    
    
    
    NSUserDefaults *defaults = [NSUserDefaults standardUserDefaults];
    NSString *username = [defaults valueForKey:@"username"];
    NSString *password = [defaults valueForKey:@"password"];
    
    if (username == nil || password == nil) {
        return;
    }
    
    [validateActiviationButton setEnabled:NO];
    [resendMailButton setEnabled:NO];
    [activityIndicator startAnimating];
    
    NSLog(@"%@/validateMemberActivation.php?username=%@&password=%@",kAppBaseURL, username, password);
    NSURL *url = [NSURL URLWithString:[NSString stringWithFormat:@"%@/validateMemberActivation.php?username=%@&password=%@",kAppBaseURL, username, password]];
    NSURLRequest *request = [NSURLRequest requestWithURL:url];
    GTMHTTPFetcher* myFetcher = [GTMHTTPFetcher fetcherWithRequest:request];
    
    [myFetcher beginFetchWithCompletionHandler:^(NSData *retrievedData, NSError *error) {
        
        [validateActiviationButton setEnabled:YES];
        [resendMailButton setEnabled:YES];
        
        [activityIndicator stopAnimating];
        
        if (error != nil) {
            // status code or network error
            [[[UIAlertView alloc] initWithTitle:@"Erreur" message:@"Erreur de connexion internet." delegate:nil cancelButtonTitle:@"Retour" otherButtonTitles:nil] show];
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
                return;
            }
            
            NSLog(@"%@",publicTimeline);
            if ([[publicTimeline valueForKey:@"resultat"] isEqualToString:@"true"]) {
                
                if ([[[publicTimeline valueForKey:@"data"] valueForKey:@"activated"] intValue] == 1) {
                    NSLog(@"before isDismissWhenEnded");
                    if (isDismissWhenEnded) {
                        NSLog(@"before isDismissWhenEnded = true");
                        [self dismissViewControllerAnimated:YES completion:^{
                            if (delegate && [delegate respondsToSelector:@selector(compteActiver)]) {
                                [delegate compteActiver];
                            }
                        }];
                    }
                    else {
                        NSLog(@"before isDismissWhenEnded = false");
                        //[self dismissViewControllerAnimated:YES completion:^{
                        if (delegate && [delegate respondsToSelector:@selector(compteActiver)]) {
                            [delegate compteActiver];
                        }
                        //}];
                    }
                    
                    
                }
                else {
                    UIAlertView *alert = [[UIAlertView alloc] initWithTitle:@"Information" message:@"Votre compte n'est toujours pas activé" delegate:nil cancelButtonTitle:@"Retour" otherButtonTitles:nil];
                    [alert show];
                }
                
                
            }
            
        }
    }];
    
}

@end
