//
//  StoreTabBarViewController2.m
//  IVOIREKIOSK
//
//  Created by Maxime Julien-Paquet on 2014-01-13.
//  Copyright (c) 2014 Maxime Julien-Paquet. All rights reserved.
//

#import "StoreTabBarViewController2.h"

#import "Reachability.h"
#import "PackagesViewController.h"
#import "StoreViewController.h"
#import "VirtualCurrencyViewController.h"
#import "ArchivesJournauxViewController.h"
#import "ReglagesIphoneViewController.h"
#import "AppDelegate.h"
#import "ReglagesViewController.h"

#import "Login2ViewController.h"
#import "CreateProfilViewController.h"

#import "UIView+Toast.h"

@interface StoreTabBarViewController2 () {
    GTMHTTPFetcher* myFetcher;
}

@end

@implementation StoreTabBarViewController2

@synthesize tabBar, subViewController, menuButton;

-(id)initWithNibName:(NSString *)nibNameOrNil bundle:(NSBundle *)nibBundleOrNil {
    self = [super initWithNibName:nibNameOrNil bundle:nibBundleOrNil];
    if (self) {
        // Custom initialization
    }
    return self;
}

-(void)viewDidLoad {
    [super viewDidLoad];
	// Do any additional setup after loading the view.
    self.view.autoresizesSubviews = YES;
    self.view.autoresizingMask = UIViewAutoresizingFlexibleWidth | UIViewAutoresizingFlexibleHeight;
    
    [[NSNotificationCenter defaultCenter] addObserver:self
                                             selector:@selector(CloseMenuPopupAndPushViewController:) name:@"CloseMenuPopupAndPushViewController"
                                               object:nil];
    
    [[NSNotificationCenter defaultCenter] addObserver:self
                                             selector:@selector(ChangementDeStatusDuCompte:)
                                                 name:@"ChangementDeStatusDuCompte"
                                               object:nil];
    [[NSNotificationCenter defaultCenter] addObserver:self
                                             selector:@selector(CloseMenuPopupAndPushViewController:)
                                                 name:@"SideMenuHide"
                                               object:nil];
    [[NSNotificationCenter defaultCenter] addObserver:self
                                             selector:@selector(pushActionSheetAccount)
                                                 name:@"pushActionSheetAccount"
                                               object:nil];
    
    //KEYBOARD OBSERVERS
    /************************/
    [[NSNotificationCenter defaultCenter] addObserver:self
                                             selector:@selector(keyboardWillShow:)
                                                 name:UIKeyboardWillShowNotification
                                               object:nil];
    
    [[NSNotificationCenter defaultCenter] addObserver:self
                                             selector:@selector(keyboardWillHide:)
                                                 name:UIKeyboardWillHideNotification
                                               object:nil];
    /************************/
    
    
    UIImageView *bg;
    
    bg = [[UIImageView alloc] initWithFrame:self.view.bounds];
    bg.autoresizingMask = UIViewAutoresizingFlexibleHeight | UIViewAutoresizingFlexibleWidth;
    bg.backgroundColor = [UIColor whiteColor];
    bg.alpha = 0.3;
    [self.view addSubview:bg];
    [self.view sendSubviewToBack:bg];
    
    bg = [[UIImageView alloc] initWithFrame:self.view.bounds];
    bg.autoresizingMask = UIViewAutoresizingFlexibleWidth | UIViewAutoresizingFlexibleTopMargin | UIViewAutoresizingFlexibleBottomMargin;
    [bg setImage:[UIImage imageNamed:@"bg-street.jpg"]];
    [self.view addSubview:bg];
    [self.view sendSubviewToBack:bg];
    
    //tabBar.autoresizingMask = UIViewAutoresizingFlexibleTopMargin | UIViewAutoresizingFlexibleWidth;
    
    
    NSString *storyboardString = @"Main_iPhone";
    if (isPad()) {
        storyboardString = @"Main_iPad";
    }
    
    UIStoryboard *sb = [UIStoryboard storyboardWithName:storyboardString bundle:nil];
    
    StoreViewController *tempViewController = (StoreViewController*)[sb instantiateViewControllerWithIdentifier:@"StoreViewController"];
    if (!isPad()) {
        tempViewController.tabBar = self.tabBar;
    }
    tempViewController.storeTabBarViewController = self;
    tempViewController.view.autoresizingMask = UIViewAutoresizingFlexibleWidth | UIViewAutoresizingFlexibleHeight;
    tempViewController.view.frame = self.subView.bounds;
    subViewController = tempViewController;
    [self addChildViewController:subViewController];
    [self.subView addSubview:subViewController.view];
    [self.view bringSubviewToFront:self.tabBar];
    
    [self.tabBar setSelectedItem:[[self.tabBar items] objectAtIndex:0]];
    if (isPad()) {
        self.title = @"Publications";
    }
    else {
        //self.title = @"";
    }
    
    NSUserDefaults *defaults = [NSUserDefaults standardUserDefaults];
    //NSString *prenom = [defaults objectForKey:@"prenom"];
    //NSString *nom = [defaults objectForKey:@"nom"];
    NSString *username = [defaults objectForKey:@"username"];
    
    if (username != nil && ![username isEqualToString:@""]) {
        [self.menuButton setTitle:[NSString stringWithFormat:@"%@",username]];
    }
    else {
        [self.menuButton setTitle:@"Connexion"];
    }
    
    
}

-(void)dealloc {
    [[NSNotificationCenter defaultCenter] removeObserver:self name:@"CloseMenuPopupAndPushViewController" object:nil];
    [[NSNotificationCenter defaultCenter] removeObserver:self name:@"ChangementDeStatusDuCompte" object:nil];
    [[NSNotificationCenter defaultCenter] removeObserver:self name:@"SideMenuHide" object:nil];
    [[NSNotificationCenter defaultCenter] removeObserver:self name:@"pushActionSheetAccount" object:nil];
    
    //KEYBOARD OBSERVERS
    [[NSNotificationCenter defaultCenter] removeObserver:self name:UIKeyboardWillShowNotification object:nil];
    [[NSNotificationCenter defaultCenter] removeObserver:self name:UIKeyboardWillHideNotification object:nil];
}

-(void)didReceiveMemoryWarning {
    [super didReceiveMemoryWarning];
    // Dispose of any resources that can be recreated.
}

-(void)viewDidAppear:(BOOL)animated {
    [super viewDidAppear:animated];
    if (![self connected]) {
        [[[UIAlertView alloc] initWithTitle:@"Informations" message:@"Connexion internet perdu." delegate:nil cancelButtonTitle:@"Retour" otherButtonTitles:nil] show];
    }
    else {
        [self checkCurrentCredit];
    }
}

-(void)didRotateFromInterfaceOrientation:(UIInterfaceOrientation)fromInterfaceOrientation {
    NSLog(@"view = %@",NSStringFromCGRect(self.view.frame));
}

-(void)dismissViewController:(id)sender {
    [self dismissViewControllerAnimated:YES completion:nil];
}

-(void)checkCurrentCredit {
    
    NSUserDefaults *defaults = [NSUserDefaults standardUserDefaults];
    NSString *username = [defaults valueForKey:@"username"];
    NSString *password = [defaults valueForKey:@"password"];
    
    if (username == nil || password == nil) {
        return;
    }
    
    NSURL *url = [NSURL URLWithString:[NSString stringWithFormat:@"%@/getCurrentCreditAndActivation.php",kAppBaseURL]];
    NSURLRequest *request = [NSURLRequest requestWithURL:url];
    if (myFetcher != nil) {
        [myFetcher stopFetching];
    }
    
    
    myFetcher = [GTMHTTPFetcher fetcherWithRequest:request];
    
    [myFetcher setPostData:[[NSString stringWithFormat:@"username=%@&password=%@",username, password] dataUsingEncoding:NSUTF8StringEncoding]];
    [myFetcher beginFetchWithCompletionHandler:^(NSData *retrievedData, NSError *error) {
        
        if (error != nil) {
            // status code or network error
            [[[UIAlertView alloc] initWithTitle:@"Erreur" message:@"Erreur de connexion internet pour la vérification de vos crédits." delegate:nil cancelButtonTitle:@"Retour" otherButtonTitles:nil] show];
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
                
                int current = [[[publicTimeline valueForKey:@"data"] valueForKey:@"quantite"] intValue];
                int localCredit = [[defaults valueForKey:@"ekcredit"] intValue];
                if (current != localCredit) {
                    NSString *ekcreditString = [NSString stringWithFormat:@"%d", current];
                    [defaults setObject:ekcreditString forKey:@"ekcredit"];
                    [defaults synchronize];
                    [[NSNotificationCenter defaultCenter] postNotificationName:@"UpdateCreditCount" object:nil];
                    //UIAlertView *alert = [[UIAlertView alloc] initWithTitle:@"Information" message:@"Vos crédits ont été mis à jour automatiquement par le serveur." delegate:nil cancelButtonTitle:@"Continuer" otherButtonTitles:nil];
                    //[alert show];
                    
                    [self.view makeToast:@"Vos crédits ont été mis à jour automatiquement par le serveur."
                                duration:5.0
                                position:@"bottom"];
                }
                
            }

        }
    }];
}

#pragma mark - Menu Delegate

-(void)ChangementDeStatusDuCompte:(NSNotification*)notification {
    [myFetcher stopFetching];
    NSUserDefaults *defaults = [NSUserDefaults standardUserDefaults];
    //NSString *prenom = [defaults objectForKey:@"prenom"];
    //NSString *nom = [defaults objectForKey:@"nom"];
    NSString *username = [defaults objectForKey:@"username"];
    
    if (username != nil && ![username isEqualToString:@""]) {
        [self.menuButton setTitle:[NSString stringWithFormat:@"%@",username]];
    }
    else {
        //[self dismissViewControllerAnimated:YES completion:^{
        //    [[[UIAlertView alloc] initWithTitle:@"Information" message:@"Votre compte a été déconnecté." delegate:nil cancelButtonTitle:@"Retour" otherButtonTitles:nil] show];
        //}];
        [self.menuButton setTitle:@"Connexion"];
    }
}

-(void)CloseMenuPopupAndPushViewController:(NSNotification*)notif {
    if (isPad()) {
        if (notif.object == nil) {
            [popover dismissPopoverAnimated:YES];
            return;
        }
        
        [popover dismissPopoverAnimated:YES completion:^{
            [self presentViewController:notif.object animated:YES completion:nil];
        }];
    }
    else {
        [popover2 hide];
        
        if (notif.object != nil) {
            [self presentViewController:notif.object animated:YES completion:nil];
            return;
        }
        
    }
}

-(void)reglages:(id)sender {
    
    NSString *storyboardString = @"Main_iPhone";
    if (isPad()) {
        storyboardString = @"Main_iPad";
    }
    
    UIStoryboard *sb = [UIStoryboard storyboardWithName:storyboardString bundle:nil];
    
    if (isPad()) {
        ReglagesViewController* controller = (ReglagesViewController*)[sb instantiateViewControllerWithIdentifier:@"ReglagesViewController"];
        
        UINavigationController *navCon = [[UINavigationController alloc] initWithRootViewController:controller];
        
        [navCon.view setClipsToBounds:YES];
        
        [navCon setNavigationBarHidden:YES];
        popover = [[FPPopoverKeyboardResponsiveController alloc] initWithViewController:navCon];
        popover.border = NO;
        popover.tint = FPPopoverWhiteTint;
        popover.keyboardHeight = _keyboardHeight;
        
        popover.contentSize = CGSizeMake(300, 505);
        
        popover.arrowDirection = FPPopoverArrowDirectionUp;
        [popover presentPopoverFromPoint:CGPointMake(55, 54)];
        
        popover.view.layer.masksToBounds = NO;
        
        popover.view.layer.shadowColor = [UIColor blackColor].CGColor;
        popover.view.layer.shadowOpacity = 0.6;
        popover.view.layer.shadowRadius = 10;
        popover.view.layer.shadowOffset = CGSizeMake(10.0f, 10.0f);
    }
    else {
        if (popover2 != nil) {
            [popover2 removeFromSuperview];
            popover2 = nil;
        }
        ReglagesIphoneViewController* controller = (ReglagesIphoneViewController*)[sb instantiateViewControllerWithIdentifier:@"ReglagesIphoneViewController"];
        
        //UINavigationController *navCon = [[UINavigationController alloc] initWithRootViewController:controller];
        //[navCon.view setOpaque:NO];
        //[navCon.view setBackgroundColor:[UIColor clearColor]];
        //[navCon.view setClipsToBounds:YES];
        
        popover2 = [[SideMenuView alloc] initWithFrame:self.view.bounds];
        [popover2 setViewController:controller];
        //[popover2 setNavCon:navCon];
        //[popover2 addSubview:navCon.view];
        [popover2 setAlpha:0];
        //AppDelegate *appDelegate = [[UIApplication sharedApplication] delegate];
        //[appDelegate.window addSubview:popover2];
        [self.navigationController.view addSubview:popover2];
        [popover2 show];
        
    }
}

-(void)keyboardWillShow:(NSNotification*)notification {
    NSDictionary *info = notification.userInfo;
    CGRect keyboardRect = [[info valueForKey:UIKeyboardFrameBeginUserInfoKey] CGRectValue];
    _keyboardHeight = keyboardRect.size.height;
    
    //if the popover is present will be refreshed
    popover.keyboardHeight = _keyboardHeight;
    [popover setupView];
}

-(void)keyboardWillHide:(NSNotification*)notification {
    _keyboardHeight = 0.0;
    
    //if the popover is present will be refreshed
    popover.keyboardHeight = _keyboardHeight;
    [popover setupView];
}

#pragma mark - TabBar Delegate

-(void)goToAbonnement {
    [self.tabBar setSelectedItem:[[self.tabBar items] objectAtIndex:1]];
    [self tabBar:self.tabBar didSelectItem:[[self.tabBar items] objectAtIndex:1]];
}

-(void)tabBar:(UITabBar *)tabBar didSelectItem:(UITabBarItem *)item {
    NSLog(@"tabbar touched");

//        [self.subViewController.view removeFromSuperview];
//        [self.subViewController removeFromParentViewController];
//        self.subViewController = nil;
    
        NSString *storyboardString = @"Main_iPhone";
        if (isPad()) {
            storyboardString = @"Main_iPad";
        }
        UIStoryboard *sb = [UIStoryboard storyboardWithName:storyboardString bundle:nil];
        
        NSLog(@"test = %@",item.title);
        if ([item.title isEqualToString:@"Publications"]) {
            if ([subViewController isKindOfClass:[StoreViewController class]]) {
                return;
            }
            
            if (isPad()) {
                self.title = @"Publications";
            }
            else {
                //self.title = @"";
            }
            [self.subViewController.view removeFromSuperview];
            [self.subViewController removeFromParentViewController];
            self.subViewController = nil;

            StoreViewController *tempViewController = (StoreViewController*)[sb instantiateViewControllerWithIdentifier:@"StoreViewController"];
            if (!isPad()) {
                tempViewController.tabBar = self.tabBar;
            }
            tempViewController.storeTabBarViewController = self;
            tempViewController.view.autoresizingMask = UIViewAutoresizingFlexibleWidth | UIViewAutoresizingFlexibleHeight;
            tempViewController.view.frame = self.subView.bounds;
            subViewController = tempViewController;
            [self addChildViewController:subViewController];
            [self.subView addSubview:subViewController.view];
            [self.view bringSubviewToFront:self.tabBar];
        }
        /*else if ([item.title isEqualToString:@"Packages"]) {
            if ([subViewController isKindOfClass:[PackagesViewController class]]) {
                return;
            }
            self.title = @"Packages";
            [self.subViewController.view removeFromSuperview];
            [self.subViewController removeFromParentViewController];
            self.subViewController = nil;

            subViewController = (PackagesViewController*)[sb instantiateViewControllerWithIdentifier:@"PackagesViewController"];
            subViewController.view.autoresizingMask = UIViewAutoresizingFlexibleWidth | UIViewAutoresizingFlexibleHeight;
            subViewController.view.frame = self.subView.bounds;
            [self addChildViewController:subViewController];
            [self.subView addSubview:subViewController.view];
            [self.view bringSubviewToFront:self.tabBar];
        }
        else if ([item.title isEqualToString:@"Achats"]) {
            
        }*/
        else if ([item.title isEqualToString:@"Crédits ekiosk"]) {
            if ([subViewController isKindOfClass:[VirtualCurrencyViewController class]]) {
                return;
            }
            
            if (isPad()) {
                self.title = @"Crédits ekiosk";
            }
            else {
                //self.title = @"";
            }
            [self.subViewController.view removeFromSuperview];
            [self.subViewController removeFromParentViewController];
            self.subViewController = nil;
            
            subViewController = (VirtualCurrencyViewController*)[sb instantiateViewControllerWithIdentifier:@"VirtualCurrencyViewController"];
            subViewController.view.autoresizingMask = UIViewAutoresizingFlexibleWidth | UIViewAutoresizingFlexibleHeight;
            subViewController.view.frame = self.subView.bounds;
            [self addChildViewController:subViewController];
            [self.subView addSubview:subViewController.view];
            [self.view bringSubviewToFront:self.tabBar];
        }
        else if ([item.title isEqualToString:@"Archives"]) {
            if ([subViewController isKindOfClass:[ArchivesJournauxViewController class]]) {
                return;
            }
            
            if (isPad()) {
                self.title = @"Archives";
            }
            else {
                //self.title = @"";
            }
            [self.subViewController.view removeFromSuperview];
            [self.subViewController removeFromParentViewController];
            self.subViewController = nil;
            
            subViewController = [[ArchivesJournauxViewController alloc] initWithNibName:nil bundle:nil];
            subViewController.view.autoresizingMask = UIViewAutoresizingFlexibleWidth | UIViewAutoresizingFlexibleHeight;
            subViewController.view.frame = self.subView.bounds;
            [self addChildViewController:subViewController];
            [self.subView addSubview:subViewController.view];
            [self.view bringSubviewToFront:self.tabBar];
        }
}

#pragma mark - compte et connexion

-(void)pushActionSheetAccount {
    [self.navigationController popToRootViewControllerAnimated:YES];
    [self performSelector:@selector(test) withObject:nil afterDelay:0.5];
    //[[[UIActionSheet alloc] initWithTitle:@"Vous avez besoin d'un compte pour ouvrir le Kiosk." delegate:self cancelButtonTitle:@"Retour" destructiveButtonTitle:nil otherButtonTitles:@"Me connecter", @"Créer mon compte", nil] showInView:self.view];
}
-(void)test {
    //[[[UIActionSheet alloc] initWithTitle:@"Vous avez besoin d'un compte pour effectuer des achats." delegate:self cancelButtonTitle:@"Retour" destructiveButtonTitle:nil otherButtonTitles:@"Me connecter", @"Créer mon compte", nil] showInView:self.view];
    
    UIActionSheet *actionsheet = [[UIActionSheet alloc] initWithTitle:@"Vous avez besoin d'un compte pour effectuer des achats." delegate:self cancelButtonTitle:@"Retour" destructiveButtonTitle:nil otherButtonTitles:@"Me connecter", @"Créer mon compte", nil];
    
    
    UIWindow* window = [[[UIApplication sharedApplication] delegate] window];
    if ([window.subviews containsObject:self.view]) {
        [actionsheet showInView:self.view];
    } else {
        [actionsheet showInView:window];
    }
}

-(void)actionSheet:(UIActionSheet *)actionSheet clickedButtonAtIndex:(NSInteger)buttonIndex {
    switch (buttonIndex) {
        case 0: {
            NSString *storyboardString = @"Main_iPhone";
            if (isPad()) {
                storyboardString = @"Main_iPad";
            }
            
            UIStoryboard *sb = [UIStoryboard storyboardWithName:storyboardString bundle:nil];
            
            Login2ViewController* controller = (Login2ViewController*)[sb instantiateViewControllerWithIdentifier:@"Login2ViewController"];
            [controller setModalPresentationStyle:UIModalPresentationFormSheet];
            [self.subViewController presentViewController:controller animated:YES completion:nil];
            
        }
            break;
        case 1: {
            NSString *storyboardString = @"Main_iPhone";
            if (isPad()) {
                storyboardString = @"Main_iPad";
            }
            
            UIStoryboard *sb = [UIStoryboard storyboardWithName:storyboardString bundle:nil];
            
            CreateProfilViewController* controller = (CreateProfilViewController*)[sb instantiateViewControllerWithIdentifier:@"CreateProfilViewController"];
            [controller setModalPresentationStyle:UIModalPresentationFormSheet];
            [self.subViewController presentViewController:controller animated:YES completion:nil];
            
        }
            break;
            
        default:
            break;
    }
}

#pragma mark - internet
-(BOOL)connected {
    Reachability *reachability = [Reachability reachabilityForInternetConnection];
    NetworkStatus networkStatus = [reachability currentReachabilityStatus];
    return !(networkStatus == NotReachable);
}

@end
