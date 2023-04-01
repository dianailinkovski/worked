//
//  BuyingWithVirtualCurrencyViewController.m
//  eKiosk
//
//  Created by Maxime Julien-Paquet on 2014-02-22.
//  Copyright (c) 2014 Maxime Julien-Paquet. All rights reserved.
//

#import "BuyingWithVirtualCurrencyViewController.h"

@interface BuyingWithVirtualCurrencyViewController ()
@property (nonatomic, strong) UIButton *creditButton;
@end

@implementation BuyingWithVirtualCurrencyViewController

@synthesize achatData, creditButton, delegate;

-(void)viewWillAppear:(BOOL)animated {
    [super viewWillAppear:animated];
    [self.buyingLabel.prixLabel setTextColor:[UIColor redColor]];
    [self.buyingLabel.prixLabel setText:[NSString stringWithFormat:@"- %@", [self.achatData valueForKey:@"prix"]]];
}

-(void)confirmAction:(id)sender {
    if (Buying) {
        return;
    }
    [self setCurrentBuyingState:YES];
    NSURL *url = [NSURL URLWithString:[NSString stringWithFormat:@"%@/AddAchatIndividuel.php",kAppBaseURL]];
    NSURLRequest *request = [NSURLRequest requestWithURL:url];
    GTMHTTPFetcher* myFetcher = [GTMHTTPFetcher fetcherWithRequest:request];
    
    NSUserDefaults *defaults = [NSUserDefaults standardUserDefaults];

    NSString *username = [defaults valueForKey:@"username"];
    NSString *password = [defaults valueForKey:@"password"];
    
    if (username == nil || password == nil) {
        username = @"";
        password = @"";
    }
    
    NSDictionary *dic = [NSDictionary dictionaryWithObjectsAndKeys:
                         username, @"username",
                         password, @"password",
                         [self.achatData valueForKey:@"id"], @"editionid",
                         [self.achatData valueForKey:@"prix"], @"quantite",
                         nil];
    NSLog(@"dic = %@", dic);
    NSString *postString = [[NSString alloc] initWithData:[NSJSONSerialization dataWithJSONObject:dic options:0 error:nil] encoding:NSUTF8StringEncoding];
    [myFetcher setPostData:[[NSString stringWithFormat:@"data=%@",postString] dataUsingEncoding:NSUTF8StringEncoding]];
    [myFetcher beginFetchWithCompletionHandler:^(NSData *retrievedData, NSError *error) {
        if (error != nil) {
            // status code or network error
            NSLog(@"error confirmAction:");
            [[[UIAlertView alloc] initWithTitle:@"Erreur" message:@"Erreur de connexion internet." delegate:nil cancelButtonTitle:@"Retour" otherButtonTitles:nil] show];
            [self setCurrentBuyingState:NO];
        } else {
            // succeeded
            
            NSMutableArray *publicTimeline = [NSJSONSerialization
                                              JSONObjectWithData:retrievedData
                                              options:NSJSONReadingMutableContainers
                                              error:nil];
            if (publicTimeline == nil) {
                NSString *dataString = [[NSString alloc] initWithData:retrievedData encoding:NSUTF8StringEncoding];
                NSLog(@"dataString = %@", dataString);
                UIAlertView *alert = [[UIAlertView alloc] initWithTitle:@"Erreur" message:[NSString stringWithFormat:@"%@\n\n Transaction annulée.",dataString] delegate:nil cancelButtonTitle:@"Retour" otherButtonTitles:nil];
                [alert show];
                [self setCurrentBuyingState:NO];
                return;
            }
            
            NSLog(@"%@",publicTimeline);
            if ([[publicTimeline valueForKey:@"resultat"] isEqualToString:@"true"]) {
                
                int total = 0;
                
                NSUserDefaults *defaults = [NSUserDefaults standardUserDefaults];
                
                NSString *username = [defaults valueForKey:@"username"];
                NSString *password = [defaults valueForKey:@"password"];
                
                if (username == nil || password == nil) {
                    int current = [[defaults valueForKey:@"ekcredit"] intValue];
                    int added = [[[publicTimeline valueForKey:@"data"] valueForKey:@"total"] intValue];
                    total = current - added;
                    
                }
                else {
                    total = [[[publicTimeline valueForKey:@"data"] valueForKey:@"total"] intValue];
                }
                
                
                NSString *ekcreditString = [NSString stringWithFormat:@"%d", total];
                [defaults setObject:ekcreditString forKey:@"ekcredit"];
                [defaults setObject:[NSNumber numberWithBool:NO] forKey:@"showNoIssue"];
                
                [defaults synchronize];
                [[NSNotificationCenter defaultCenter] postNotificationName:@"UpdateCreditCount" object:nil];
                
                [self dismissViewControllerAnimated:YES completion:^{
                    if (delegate && [delegate respondsToSelector:@selector(AchatComplete)]) {
                        [delegate AchatComplete];
                    }
                }];
                
                
            }
            else {
                [[[UIAlertView alloc] initWithTitle:@"Erreur" message:[NSString stringWithFormat:@"%@\n\n Transaction annulée.",[publicTimeline valueForKey:@"data"]] delegate:nil cancelButtonTitle:@"Retour" otherButtonTitles:nil] show];
                [self setCurrentBuyingState:NO];
            }
        }
    }];
}

-(void)calculateSolde {
    int current = [self.currentLabel.prixLabel.text intValue];
    int buying = [self.buyingLabel.prixLabel.text intValue];
    [self.totalLabel.prixLabel setText:[NSString stringWithFormat:@"%d", current+buying]];
    
    if ((current + buying) < 0) {
        [self.totalLabel.prixLabel setTextColor:[UIColor redColor]];
        [self.confirmButton removeFromSuperview];
        [self.cancelButton removeFromSuperview];
        
        /*
        if (isPad()) {
            confirmButton.frame = CGRectMake(540/2+10, 520, 200, 50);
        }
        else {
            confirmButton.frame = CGRectMake(320/2+10, 380, 120, 50);
        }
        */
        
        UILabel *tempLabel;
        if (isPad()) {
            tempLabel = [[UILabel alloc] initWithFrame:CGRectMake(20, 490, 500, 40)];
        }
        else {
            tempLabel = [[UILabel alloc] initWithFrame:CGRectMake(20, 360, 280, 50)];
        }
        tempLabel.text = @"Vous n'avez pas suffisament de crédits pour cette transaction.";
        tempLabel.numberOfLines = 2;
        tempLabel.textColor = [UIColor redColor];
        tempLabel.textAlignment = NSTextAlignmentCenter;
        [self.view addSubview:tempLabel];
        
        
        creditButton = [UIButton buttonWithType:UIButtonTypeCustom];
        if (isPad()) {
            creditButton.frame = CGRectMake(540/2-150, 540, 300, 50);
        }
        else {
            creditButton.frame = CGRectMake(320/2-140, 415, 280, 50);
        }
        creditButton.layer.cornerRadius = 5;

        [creditButton.titleLabel setFont:[UIFont fontWithName:@"Helvetica-Bold" size:22]];
        [creditButton setBackgroundColor:[UIColor colorWithRed:82.f/255.f green:182.f/255.f blue:21.f/255.f alpha:1]];
        [creditButton setTitle:@"Acheter des crédits" forState:UIControlStateNormal];
        [creditButton addTarget:self action:@selector(pushCredit) forControlEvents:UIControlEventTouchUpInside];
        [self.view addSubview:creditButton];
    }
}

-(void)pushCredit {
    [self dismissViewControllerAnimated:YES completion:^{
        if (delegate && [delegate respondsToSelector:@selector(CreditSelection)]) {
            [delegate CreditSelection];
        }
    }];
}

@end
