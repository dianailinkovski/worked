//
//  DetailVirtualCurrencyViewController.m
//  eKiosk
//
//  Created by Maxime Julien-Paquet on 2014-02-21.
//  Copyright (c) 2014 Maxime Julien-Paquet. All rights reserved.
//

#import "DetailVirtualCurrencyViewController.h"
#import <QuartzCore/QuartzCore.h>
#import "ConsumableIAPHelper.h"
#import "NSData+MKBase64.h"

@interface DetailVirtualCurrencyViewController ()

@end

@implementation DetailVirtualCurrencyViewController

@synthesize dataArray, itunesProduct, currentLabel, buyingLabel, totalLabel, currentTextLabel, buyingTextLabel, totalTextLabel, confirmButton, cancelButton, idTransaction, pendingTransaction, barButtonItem, loadingCurrentEK, delegate, loadingPurchase;

- (id)initWithNibName:(NSString *)nibNameOrNil bundle:(NSBundle *)nibBundleOrNil {
    self = [super initWithNibName:nibNameOrNil bundle:nibBundleOrNil];
    if (self) {
        // Custom initialization
    }
    return self;
}

- (void)viewDidLoad {
    [super viewDidLoad];
    Buying = NO;
	// Do any additional setup after loading the view.
    self.view.backgroundColor = [UIColor whiteColor];
    UIImageView *imageViewBG;
    if (isPad()) {
        imageViewBG = [[UIImageView alloc] initWithFrame:CGRectMake((self.view.frame.size.width - 341) / 2, 44+20, 341, 130)];
    }
    else {
        imageViewBG = [[UIImageView alloc] initWithFrame:CGRectMake((self.view.frame.size.width - 170) / 2, 44+40, 170, 65)];
    }
    
    imageViewBG.image = [UIImage imageNamed:@"logo_ekiosk.png"];
    imageViewBG.autoresizingMask = UIViewAutoresizingFlexibleRightMargin | UIViewAutoresizingFlexibleLeftMargin | UIViewAutoresizingFlexibleBottomMargin;
    [self.view addSubview:imageViewBG];
    
    
    UINavigationBar *navBar;
    
    if (isPad()) {
        navBar = [[UINavigationBar alloc] initWithFrame:CGRectMake(0, 0, self.view.frame.size.width, 44)];
    }
    else {
        navBar = [[UINavigationBar alloc] initWithFrame:CGRectMake(0, 20, self.view.frame.size.width, 44)];
    }
    navBar.autoresizingMask = UIViewAutoresizingFlexibleWidth | UIViewAutoresizingFlexibleBottomMargin;
    UINavigationItem *navItem = [[UINavigationItem alloc] initWithTitle:@"Confirmer votre achat"];
    barButtonItem = [[UIBarButtonItem alloc] initWithTitle:@"Retour" style:UIBarButtonItemStyleBordered target:self action:@selector(cancelAction:)];
    navItem.leftBarButtonItem = barButtonItem;
    [navBar pushNavigationItem:navItem animated:false];
    
    [self.view addSubview:navBar];
    
    [self.view addSubview:[self currentTextLabel]];
    [self.view addSubview:[self currentLabel]];
    [self.view addSubview:[self buyingTextLabel]];
    [self.view addSubview:[self buyingLabel]];
    [self.view addSubview:[self subtotalImageView]];
    [self.view addSubview:[self totalTextLabel]];
    [self.view addSubview:[self totalLabel]];
    
    [self.view addSubview:[self confirmButton]];
    [self.view addSubview:[self cancelButton]];
    
    [self.view addSubview:[self loadingCurrentEK]];
    [self.view addSubview:[self loadingPurchase]];
    
}

- (void)didReceiveMemoryWarning {
    [super didReceiveMemoryWarning];
    // Dispose of any resources that can be recreated.
}

-(void)viewWillAppear:(BOOL)animated {
    [super viewWillAppear:animated];
    
    //NSUserDefaults *defaults = [NSUserDefaults standardUserDefaults];
    //int current = [[defaults valueForKey:@"ekcredit"] intValue];
    int buying = [[self.dataArray valueForKey:@"quantite"] intValue];
    
    
    //[self.currentLabel.prixLabel setText:[NSString stringWithFormat:@"%d", current]];
    [self.buyingLabel.prixLabel setText:[NSString stringWithFormat:@"+ %d", buying]];
    //[self.totalLabel.prixLabel setText:[NSString stringWithFormat:@"%d", buying+current]];
    
}
-(void)viewDidAppear:(BOOL)animated {
    [super viewDidAppear:animated];
    
    [self checkCurrentCredit];
}

-(void)checkCurrentCredit {
    [self setCurrentBuyingState:YES];
    [self.loadingCurrentEK startAnimating];
    
    NSURL *url = [NSURL URLWithString:[NSString stringWithFormat:@"%@/getCurrentCredit.php",kAppBaseURL]];
    NSURLRequest *request = [NSURLRequest requestWithURL:url];
    GTMHTTPFetcher* myFetcher = [GTMHTTPFetcher fetcherWithRequest:request];
    
    NSUserDefaults *defaults = [NSUserDefaults standardUserDefaults];
    NSString *username = [defaults valueForKey:@"username"];
    NSString *password = [defaults valueForKey:@"password"];
    
    if (username == nil || password == nil) {
        [self setCurrentBuyingState:NO];
        int ekcredit = [[defaults valueForKey:@"ekcredit"] intValue];
        [self.currentLabel.prixLabel setText:[NSString stringWithFormat:@"%d",ekcredit]];
        [self calculateSolde];
        [self.loadingCurrentEK stopAnimating];
        return;
    }
    
    
    [myFetcher setPostData:[[NSString stringWithFormat:@"username=%@&password=%@",username, password] dataUsingEncoding:NSUTF8StringEncoding]];
    [myFetcher beginFetchWithCompletionHandler:^(NSData *retrievedData, NSError *error) {
        if (error != nil) {
            // status code or network error
            NSLog(@"error confirmAction:");
            [[[UIAlertView alloc] initWithTitle:@"Erreur" message:@"Erreur de connexion internet." delegate:nil cancelButtonTitle:@"Retour" otherButtonTitles:nil] show];
            [self setCurrentBuyingState:NO];
            [self.loadingCurrentEK stopAnimating];
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
                [self.currentLabel.prixLabel setText:@"?"];
                [self setCurrentBuyingState:NO];
                [self.confirmButton setEnabled:NO];
                [self.confirmButton setAlpha:0.2];
                [self.loadingCurrentEK stopAnimating];
                return;
            }
            
            NSLog(@"%@",publicTimeline);
            if ([[publicTimeline valueForKey:@"resultat"] isEqualToString:@"true"]) {
                [self setCurrentBuyingState:NO];
                [self.currentLabel.prixLabel setText:[[publicTimeline valueForKey:@"data"] valueForKey:@"quantite"]];
                [self calculateSolde];
                int current = [self.currentLabel.prixLabel.text intValue];
                
                NSString *ekcreditString = [NSString stringWithFormat:@"%d", current];
                [defaults setObject:ekcreditString forKey:@"ekcredit"];
                [defaults synchronize];
                [[NSNotificationCenter defaultCenter] postNotificationName:@"UpdateCreditCount" object:nil];
                
                [self.loadingCurrentEK stopAnimating];
            }
            else {
                [[[UIAlertView alloc] initWithTitle:@"Erreur" message:[NSString stringWithFormat:@"%@",[publicTimeline valueForKey:@"data"]] delegate:nil cancelButtonTitle:@"Retour" otherButtonTitles:nil] show];
                [self.currentLabel.prixLabel setText:@"?"];
                [self setCurrentBuyingState:NO];
                [self.confirmButton setEnabled:NO];
                [self.confirmButton setAlpha:0.2];
                [self.loadingCurrentEK stopAnimating];
            }
        }
    }];
}

-(UIActivityIndicatorView *)loadingCurrentEK {
    if (loadingCurrentEK == nil) {
        loadingCurrentEK = [[UIActivityIndicatorView alloc] initWithActivityIndicatorStyle:UIActivityIndicatorViewStyleWhiteLarge];
        if (isPad()) {
            loadingCurrentEK.frame = CGRectMake((540-200), 237, 40, 40);
        }
        else {
            loadingCurrentEK.frame = CGRectMake((320-120), 177, 40, 40);
        }
        
        loadingCurrentEK.color = [UIColor blackColor];
        loadingCurrentEK.hidesWhenStopped = YES;
    }
    return loadingCurrentEK;
}
-(UIActivityIndicatorView *)loadingPurchase {
    if (loadingPurchase == nil) {
        loadingPurchase = [[UIActivityIndicatorView alloc] initWithActivityIndicatorStyle:UIActivityIndicatorViewStyleWhiteLarge];
        loadingPurchase.autoresizingMask = UIViewAutoresizingFlexibleBottomMargin | UIViewAutoresizingFlexibleLeftMargin | UIViewAutoresizingFlexibleRightMargin | UIViewAutoresizingFlexibleTopMargin;
        loadingPurchase.frame = CGRectMake(0, 0, 40, 40);
        loadingPurchase.center = self.view.center;
        loadingPurchase.color = [UIColor blackColor];
        loadingPurchase.hidesWhenStopped = YES;
    }
    return loadingPurchase;
}

-(VCLabel *)currentLabel {
    if (currentLabel == nil) {
        if (isPad()) {
            currentLabel = [[VCLabel alloc] initWithFrame:CGRectMake((540-350), 230, 300, 53)];
        }
        else {
            currentLabel = [[VCLabel alloc] initWithFrame:CGRectMake((320-160), 170, 140, 53)];
        }
        
    }
    return currentLabel;
}
-(VCLabel *)buyingLabel {
    if (buyingLabel == nil) {
        if (isPad()) {
            buyingLabel = [[VCLabel alloc] initWithFrame:CGRectMake((540-350), 310, 300, 53)];
        }
        else {
            buyingLabel = [[VCLabel alloc] initWithFrame:CGRectMake((320-160), 230, 140, 53)];
        }
        
        [buyingLabel.prixLabel setTextColor:[UIColor colorWithRed:82.f/255.f green:182.f/255.f blue:21.f/255.f alpha:1]];
    }
    return buyingLabel;
}
-(VCLabel *)totalLabel {
    if (totalLabel == nil) {
        if (isPad()) {
            totalLabel = [[VCLabel alloc] initWithFrame:CGRectMake((540-350), 420, 300, 53)];
        }
        else {
            totalLabel = [[VCLabel alloc] initWithFrame:CGRectMake((320-160), 300, 140, 53)];
        }
        
    }
    return totalLabel;
}

-(UILabel *)currentTextLabel {
    if (currentTextLabel == nil) {
        if (isPad()) {
            currentTextLabel = [[UILabel alloc] initWithFrame:CGRectMake(70, 230, 160, 53)];
            currentTextLabel.font = [UIFont fontWithName:@"Helvetica" size:26];
        }
        else {
            currentTextLabel = [[UILabel alloc] initWithFrame:CGRectMake(20, 170, 140, 53)];
            currentTextLabel.font = [UIFont fontWithName:@"Helvetica" size:20];
        }
        
        currentTextLabel.textColor = [UIColor grayColor];
        currentTextLabel.text = @"Solde actuel";
    }
    return currentTextLabel;
}
-(UILabel *)buyingTextLabel {
    if (buyingTextLabel == nil) {
        if (isPad()) {
            buyingTextLabel = [[UILabel alloc] initWithFrame:CGRectMake(70, 310, 160, 53)];
            buyingTextLabel.font = [UIFont fontWithName:@"Helvetica" size:26];
        }
        else {
            buyingTextLabel = [[UILabel alloc] initWithFrame:CGRectMake(20, 230, 140, 53)];
            buyingTextLabel.font = [UIFont fontWithName:@"Helvetica" size:20];
        }
        
        buyingTextLabel.textColor = [UIColor grayColor];
        buyingTextLabel.text = @"Votre achat";
    }
    return buyingTextLabel;
}
-(UILabel *)totalTextLabel {
    if (totalTextLabel == nil) {
        if (isPad()) {
            totalTextLabel = [[UILabel alloc] initWithFrame:CGRectMake(70, 420, 180, 53)];
            totalTextLabel.font = [UIFont fontWithName:@"Helvetica" size:26];
        }
        else {
            totalTextLabel = [[UILabel alloc] initWithFrame:CGRectMake(20, 300, 140, 53)];
            totalTextLabel.font = [UIFont fontWithName:@"Helvetica" size:20];
        }
        
        totalTextLabel.textColor = [UIColor grayColor];
        totalTextLabel.text = @"Nouveau solde";
    }
    return totalTextLabel;
}

-(UIButton *)confirmButton {
    if (confirmButton == nil) {
        confirmButton = [UIButton buttonWithType:UIButtonTypeCustom];
        if (isPad()) {
            confirmButton.frame = CGRectMake(540/2+10, 520, 200, 50);
            [confirmButton.titleLabel setFont:[UIFont fontWithName:@"Helvetica-Bold" size:24]];
        }
        else {
            confirmButton.frame = CGRectMake(320/2+10, 380, 120, 50);
            [confirmButton.titleLabel setFont:[UIFont fontWithName:@"Helvetica-Bold" size:20]];
        }
        confirmButton.layer.cornerRadius = 5;
        [confirmButton setBackgroundColor:[UIColor colorWithRed:82.f/255.f green:182.f/255.f blue:21.f/255.f alpha:1]];
        [confirmButton setTitle:@"Confirmer" forState:UIControlStateNormal];
        [confirmButton addTarget:self action:@selector(confirmAction:) forControlEvents:UIControlEventTouchUpInside];
    }
    return confirmButton;
}
-(UIButton *)cancelButton {
    if (cancelButton == nil) {
        cancelButton = [UIButton buttonWithType:UIButtonTypeCustom];
        if (isPad()) {
            cancelButton.frame = CGRectMake(540/2-200-10, 520, 200, 50);
            [cancelButton.titleLabel setFont:[UIFont fontWithName:@"Helvetica" size:24]];
        }
        else {
            cancelButton.frame = CGRectMake(320/2-120-10, 380, 120, 50);
            [cancelButton.titleLabel setFont:[UIFont fontWithName:@"Helvetica" size:20]];
        }
        
        cancelButton.layer.cornerRadius = 5;
        [cancelButton setBackgroundColor:[UIColor redColor]];
        [cancelButton setTitle:@"Annuler" forState:UIControlStateNormal];
        [cancelButton addTarget:self action:@selector(cancelAction:) forControlEvents:UIControlEventTouchUpInside];
    }
    return cancelButton;
}

-(UIImageView*)subtotalImageView {
    UIImageView *subtotalLigne;
    if (isPad()) {
        subtotalLigne = [[UIImageView alloc] initWithFrame:CGRectMake(70, 391, 540-130, 1)];
    }
    else {
        subtotalLigne = [[UIImageView alloc] initWithFrame:CGRectMake(20, 291, 280, 1)];
    }
    
    [subtotalLigne setBackgroundColor:[UIColor grayColor]];
    return subtotalLigne;
}

-(void)cancelAction:(id)sender {
    if (Buying) {
        return;
    }
    [self dismissViewControllerAnimated:YES completion:nil];
}

-(void)confirmAction:(id)sender {
    if (Buying) {
        return;
    }
    [self setCurrentBuyingState:YES];
    [self.loadingPurchase startAnimating];
    NSURL *url = [NSURL URLWithString:[NSString stringWithFormat:@"%@/AddAchatConsumable.php",kAppBaseURL]];
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
                         [self.dataArray valueForKey:@"id"], @"vcbought",
                         [self.dataArray valueForKey:@"quantite"], @"quantite",
                         [self.dataArray valueForKey:@"prix_usd"], @"prix",
                         nil];
    
    NSString *postString = [[NSString alloc] initWithData:[NSJSONSerialization dataWithJSONObject:dic options:0 error:nil] encoding:NSUTF8StringEncoding];
    [myFetcher setPostData:[[NSString stringWithFormat:@"data=%@",postString] dataUsingEncoding:NSUTF8StringEncoding]];
    [myFetcher beginFetchWithCompletionHandler:^(NSData *retrievedData, NSError *error) {
        if (error != nil) {
            // status code or network error
            NSLog(@"error confirmAction:");
            [[[UIAlertView alloc] initWithTitle:@"Erreur" message:@"Erreur de connexion internet." delegate:nil cancelButtonTitle:@"Retour" otherButtonTitles:nil] show];
            [self setCurrentBuyingState:NO];
            [self.loadingPurchase stopAnimating];
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
                [self.loadingPurchase stopAnimating];
                return;
            }
            
            NSLog(@"%@",publicTimeline);
            if ([[publicTimeline valueForKey:@"resultat"] isEqualToString:@"true"]) {
                [self setIdTransaction:[[publicTimeline valueForKey:@"data"] valueForKey:@"idachat"]];
                
                [self itunesTransaction];
            }
            else {
                [[[UIAlertView alloc] initWithTitle:@"Erreur" message:[NSString stringWithFormat:@"%@\n\n Transaction annulée.",[publicTimeline valueForKey:@"data"]] delegate:nil cancelButtonTitle:@"Retour" otherButtonTitles:nil] show];
                [self setCurrentBuyingState:NO];
                [self.loadingPurchase stopAnimating];
            }
        }
    }];
}

-(void)itunesTransaction {
    
    [[ConsumableIAPHelper sharedInstance] buyProduct:self.itunesProduct WithCompletionHandler:^(BOOL success, SKPaymentTransaction *transaction) {
        if (success) {
            [self setPendingTransaction:transaction];
            [self completeTransaction];
        }
        else {
            if (transaction.error.code != SKErrorPaymentCancelled) {
                NSLog(@"Other error");
                [[[UIAlertView alloc] initWithTitle:@"Erreur" message:@"Erreur lors de la connexion avec le serveur ItunesConnect.\n\n Transaction annulée." delegate:nil cancelButtonTitle:@"Retour" otherButtonTitles:nil] show];
                [self.loadingPurchase stopAnimating];
            } else {
                NSLog(@"User canceled");
                [self.loadingPurchase stopAnimating];
            }
            
            
            [self setCurrentBuyingState:NO];
        }
    }];
    
}

-(void)completeTransaction {
    NSLog(@"completeTransaction");
    NSURL *url = [NSURL URLWithString:[NSString stringWithFormat:@"%@/in-app/verifyProduct.php",kAppBaseURL]];
    
    NSURLRequest *request = [NSURLRequest requestWithURL:url];
    
    NSUserDefaults *defaults = [NSUserDefaults standardUserDefaults];
    NSString *receiptDataString = [self.pendingTransaction.transactionReceipt base64EncodedString];
	NSString *postData = [NSString stringWithFormat:@"receiptdata=%@", receiptDataString];
    postData = [postData stringByAppendingFormat:@"&achatid=%@", self.idTransaction];

    
    NSString *username = [defaults valueForKey:@"username"];
    NSString *password = [defaults valueForKey:@"password"];
    if (username == nil || password == nil) {
        username = @"";
        password = @"";
    }
    postData = [postData stringByAppendingFormat:@"&username=%@", username];
    postData = [postData stringByAppendingFormat:@"&password=%@", password];
    GTMHTTPFetcher* myFetcher = [GTMHTTPFetcher fetcherWithRequest:request];
    [myFetcher setPostData:[postData dataUsingEncoding:NSUTF8StringEncoding]];
    [myFetcher beginFetchWithCompletionHandler:^(NSData *retrievedData, NSError *error) {
        if (error != nil) {
            // status code or network error
            NSLog(@"error getdatafromserveur");
            [[[UIAlertView alloc] initWithTitle:@"Erreur" message:@"Erreur de connexion internet." delegate:nil cancelButtonTitle:@"Retour" otherButtonTitles:nil] show];
            [self setCurrentBuyingState:NO];
            [self.loadingPurchase stopAnimating];
        } else {
            // succeeded
            
            NSMutableArray *publicTimeline = [NSJSONSerialization
                                              JSONObjectWithData:retrievedData
                                              options:NSJSONReadingMutableContainers
                                              error:nil];
            if (publicTimeline == nil) {
                NSString *dataString = [[NSString alloc] initWithData:retrievedData encoding:NSUTF8StringEncoding];
                NSLog(@"dataString = %@", dataString);
                UIAlertView *alert = [[UIAlertView alloc] initWithTitle:@"Erreur" message:dataString delegate:nil cancelButtonTitle:@"Retour" otherButtonTitles:nil];
                [alert show];
                [self setCurrentBuyingState:NO];
                [self.loadingPurchase stopAnimating];
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
                    total = current + added;
                }
                else {
                    total = [[[publicTimeline valueForKey:@"data"] valueForKey:@"total"] intValue];
                }
                
                
                NSString *ekcreditString = [NSString stringWithFormat:@"%d", total];
                [defaults setObject:ekcreditString forKey:@"ekcredit"];
                [defaults synchronize];
                
                [self completed];
                [self setCurrentBuyingState:NO];
                [self.loadingPurchase stopAnimating];
                
            }
            else {
                [[[UIAlertView alloc] initWithTitle:@"Erreur" message:[NSString stringWithFormat:@"%@\n\nUne erreur innattendu s'est produit. Communiquez avec nous: info@ngser.com",[publicTimeline valueForKey:@"data"]] delegate:nil cancelButtonTitle:@"Retour" otherButtonTitles:nil] show];
                [self setCurrentBuyingState:NO];
                [self.loadingPurchase stopAnimating];
            }
        }
    }];
}

-(void)completed {
    [self dismissViewControllerAnimated:YES completion:^{
        if (delegate && [delegate respondsToSelector:@selector(EndBuyingCredit)]) {
            [delegate EndBuyingCredit];
        }

        [[[UIAlertView alloc] initWithTitle:@"Informations" message:@"Transaction complétée.\n\nVos crédit EK sont maintenant disponible." delegate:nil cancelButtonTitle:@"Continuer" otherButtonTitles:nil] show];
    }];
}

-(void)setCurrentBuyingState:(BOOL)buyingState {
    Buying = buyingState;
    if (Buying) {
        [self.confirmButton setAlpha:0.2];
        [self.cancelButton setAlpha:0.2];
        [self.barButtonItem setEnabled:NO];
    }
    else {
        [self.confirmButton setAlpha:1];
        [self.cancelButton setAlpha:1];
        [self.barButtonItem setEnabled:YES];
    }
}

-(void)calculateSolde {
    int current = [self.currentLabel.prixLabel.text intValue];
    int buying = [self.buyingLabel.prixLabel.text intValue];
    [self.totalLabel.prixLabel setText:[NSString stringWithFormat:@"%d", buying+current]];
}

@end
