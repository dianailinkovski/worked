//
//  DetailsStoreViewController.m
//  IVOIREKIOSK
//
//  Created by Maxime Julien-Paquet on 2013-12-22.
//  Copyright (c) 2013 Maxime Julien-Paquet. All rights reserved.
//

#import "DetailsStoreViewController.h"
#import "EditionImageView.h"
#import "StoreViewController.h"
#import "AppDelegate.h"
#import "Editions.h"

#import "EditionsStoreView.h"
#import "GTMHTTPFetcher.h"
#import "VirtualCurrencyViewController.h"

#import "DetailHeaderViewCell.h"
#import "AdsHeaderCollectionView.h"



@interface DetailsStoreViewController () {
    NSArray *_products;
    
    BOOL refreshing;
    
    int isCompteValidate;
    UICollectionViewFlowLayout *collectionViewLayout;
    
    float ratioPubTop;
    float ratioPubBottom;
    int coreHeaderHeight;
}

@end

@implementation DetailsStoreViewController

@synthesize delegate;
//@synthesize scrollView, dataDictionary, imageView, nomLabel, dateLabel, categorieLabel, prixButton, managedObjectContext, prixStringLabel, bottomCollectionView, bottomDataArray, loadingBottom, currentCreditLabel, headerView;
@synthesize dataDictionary, managedObjectContext, bottomCollectionView, bottomDataArray, loadingBottom, currentCreditLabel, pubArray;

-(id)initWithNibName:(NSString *)nibNameOrNil bundle:(NSBundle *)nibBundleOrNil {
    self = [super initWithNibName:nibNameOrNil bundle:nibBundleOrNil];
    if (self) {
        // Custom initialization
    }
    return self;
}

-(void)viewDidLoad {
    [super viewDidLoad];
    
    ratioPubTop = 0.13888;
    ratioPubBottom = 0.13888;
    
    refreshing = NO;
    isCompteValidate = -1;
    
    UIImageView *bgFingerPrint = [[UIImageView alloc] initWithFrame:CGRectMake(self.view.frame.size.width - 508, self.view.frame.size.height - 584, 508, 584)];
    bgFingerPrint.image = [UIImage imageNamed:@"fond_fingerprint.png"];
    bgFingerPrint.alpha = 0.1;
    bgFingerPrint.autoresizingMask = UIViewAutoresizingFlexibleLeftMargin | UIViewAutoresizingFlexibleTopMargin;
    [self.view addSubview:bgFingerPrint];
    [self.view sendSubviewToBack:bgFingerPrint];
    
    coreHeaderHeight = 235;
    if (isPad()) {
        coreHeaderHeight = 520;
    }
    
    collectionViewLayout = [[UICollectionViewFlowLayout alloc] init];
    if (isPad()) {
        collectionViewLayout.sectionInset = UIEdgeInsetsMake(20, 40, 20, 40);
        collectionViewLayout.minimumLineSpacing = 20;
        collectionViewLayout.itemSize = CGSizeMake(130.0f, 210.0f);
        collectionViewLayout.headerReferenceSize = CGSizeMake(self.view.frame.size.width, self.view.frame.size.width*ratioPubTop+coreHeaderHeight);
        collectionViewLayout.footerReferenceSize = CGSizeMake(self.view.frame.size.width, self.view.frame.size.width*ratioPubBottom);
    }
    else {
        //rendu la
        collectionViewLayout.sectionInset = UIEdgeInsetsMake(20, 20, 20, 20);
        collectionViewLayout.minimumLineSpacing = 20;
        collectionViewLayout.itemSize = CGSizeMake(77, 130);
        collectionViewLayout.headerReferenceSize = CGSizeMake(self.view.frame.size.width, self.view.frame.size.width*ratioPubTop+coreHeaderHeight);
        collectionViewLayout.footerReferenceSize = CGSizeMake(self.view.frame.size.width, self.view.frame.size.width*ratioPubBottom);
        
    }
    
    
    //if (isPad()) {
    //    bottomCollectionView = [[UICollectionView alloc]initWithFrame:CGRectMake(0, 524, 768, 500) collectionViewLayout:collectionViewLayout];
    //}
    //else {
        bottomCollectionView = [[UICollectionView alloc]initWithFrame:self.view.bounds collectionViewLayout:collectionViewLayout];
    //}
    
    bottomCollectionView.autoresizingMask = UIViewAutoresizingFlexibleWidth | UIViewAutoresizingFlexibleHeight;
    
    bottomCollectionView.backgroundColor = [UIColor clearColor];
    bottomCollectionView.delegate = self;
    bottomCollectionView.dataSource = self;
    bottomCollectionView.contentInset = UIEdgeInsetsMake(64, 0, 0, 0);
    [bottomCollectionView registerClass:[EditionsStoreView class] forCellWithReuseIdentifier:@"editionsStoreView"];
    [bottomCollectionView registerClass:[DetailHeaderViewCell class] forSupplementaryViewOfKind:UICollectionElementKindSectionHeader withReuseIdentifier:@"HeaderCellDetail"];
    [bottomCollectionView registerClass:[AdsHeaderCollectionView class] forSupplementaryViewOfKind:UICollectionElementKindSectionFooter withReuseIdentifier:@"FooterCellDetail"];
    
    [self.view addSubview:bottomCollectionView];
    
    //[self.view addSubview:[self currentCreditLabel]];
    
    UIBarButtonItem *temp = [[UIBarButtonItem alloc] initWithCustomView:[self currentCreditLabel]];
    [self.navigationItem setRightBarButtonItem:temp];
    
    
    
}

-(void)didReceiveMemoryWarning {
    [super didReceiveMemoryWarning];
    // Dispose of any resources that can be recreated.
}

-(void)willRotateToInterfaceOrientation:(UIInterfaceOrientation)toInterfaceOrientation duration:(NSTimeInterval)duration {
    [super willRotateToInterfaceOrientation:toInterfaceOrientation duration:duration];
    //[self.bottomCollectionView reloadData];
    if (isPad()) {
        if (UIInterfaceOrientationIsLandscape(toInterfaceOrientation)) {
            [[NSNotificationCenter defaultCenter] postNotificationName:@"HeaderSwitchToLandscape" object:nil];
        }
        else {
            [[NSNotificationCenter defaultCenter] postNotificationName:@"HeaderSwitchToPortrait" object:nil];
        }
    }
    
}

-(void)didRotateFromInterfaceOrientation:(UIInterfaceOrientation)fromInterfaceOrientation {
    [super didRotateFromInterfaceOrientation:fromInterfaceOrientation];
    
    collectionViewLayout.headerReferenceSize = CGSizeMake(bottomCollectionView.frame.size.width, bottomCollectionView.frame.size.width*ratioPubTop+coreHeaderHeight);
    collectionViewLayout.footerReferenceSize = CGSizeMake(self.view.frame.size.width, self.view.frame.size.width*ratioPubBottom);
    [self.bottomCollectionView setCollectionViewLayout:collectionViewLayout];
    [self.bottomCollectionView reloadData];
    //[self.bottomCollectionView.collectionViewLayout invalidateLayout];
    //[self.bottomCollectionView setContentOffset:CGPointMake(0, -64) animated:YES];
    //[bottomCollectionView performSelectorOnMainThread:@selector(setCollectionViewLayout:) withObject:collectionViewLayout waitUntilDone:YES];
    //[bottomCollectionView performSelectorOnMainThread:@selector(reloadData) withObject:nil waitUntilDone:YES];
    //[collectionViewLayout invalidateLayout];
    //[storeCollectionView reloadData];
    
    //[storeCollectionView reloadSections:[NSIndexSet indexSetWithIndexesInRange:NSMakeRange(0, 1)]];
    //[storeCollectionView.collectionViewLayout invalidateLayout];
}

-(UIActivityIndicatorView *)loadingBottom {
    if (loadingBottom == nil) {
        loadingBottom = [[UIActivityIndicatorView alloc] initWithActivityIndicatorStyle:UIActivityIndicatorViewStyleWhiteLarge];
        loadingBottom.autoresizingMask = UIViewAutoresizingFlexibleLeftMargin | UIViewAutoresizingFlexibleRightMargin | UIViewAutoresizingFlexibleTopMargin | UIViewAutoresizingFlexibleBottomMargin;
        loadingBottom.frame = CGRectMake(0, 0, 40, 40);
        loadingBottom.center = self.bottomCollectionView.center;
        loadingBottom.color = [UIColor blackColor];
        loadingBottom.hidesWhenStopped = YES;
        [self.view addSubview:loadingBottom];
    }
    return loadingBottom;
}

-(MiniVCLabel *)currentCreditLabel {
    if (currentCreditLabel == nil) {
        if (isPad()) {
            currentCreditLabel = [[MiniVCLabel alloc] initWithFrame:CGRectMake(self.view.frame.size.width-220, 2, 200, 40)];
        }
        else {
            currentCreditLabel = [[MiniVCLabel alloc] initWithFrame:CGRectMake(self.view.frame.size.width-120, 2, 100, 40)];
        }
        
    
    }
    return currentCreditLabel;
}

-(void)viewWillAppear:(BOOL)animated {
    [super viewWillAppear:animated];
    
    [self.bottomCollectionView reloadData];
    
    NSUserDefaults *defaults = [NSUserDefaults standardUserDefaults];
    int current = [[defaults valueForKey:@"ekcredit"] intValue];
    [self.currentCreditLabel.prixLabel setText:[NSString stringWithFormat:@"%d",current]];
    
    [self setIsCompteValide:-1];
    
    [self loadingBottom];
    [self loadBottom];
    
}

-(void)loadBottom {
    
    [self setPubArray:nil];
    [bottomCollectionView performSelectorOnMainThread:@selector(reloadData) withObject:nil waitUntilDone:YES];
    
    [self.loadingBottom startAnimating];
    NSUserDefaults *defaults = [NSUserDefaults standardUserDefaults];
    NSURL * url = [NSURL URLWithString:[NSString stringWithFormat:@"%@/getMemeEditeurs.php?id=%@&username=%@&password=%@", kAppBaseURL, [self.dataDictionary valueForKey:@"id_journal"], [defaults objectForKey:@"username"], [defaults objectForKey:@"password"]]];

    NSURLRequest *request = [NSURLRequest requestWithURL:url];
    GTMHTTPFetcher* myFetcher = [GTMHTTPFetcher fetcherWithRequest:request];
    [myFetcher beginFetchWithCompletionHandler:^(NSData *retrievedData, NSError *error) {
        if (error != nil) {
            // status code or network error
            NSLog(@"error getdatafromserveur");
            [self.loadingBottom stopAnimating];
        } else {
            // succeeded
            
            NSMutableArray *publicTimeline = [NSJSONSerialization
                                              JSONObjectWithData:retrievedData
                                              options:NSJSONReadingMutableContainers
                                              error:nil];
            if (publicTimeline == nil) {
                NSString *dataString = [[NSString alloc] initWithData:retrievedData encoding:NSUTF8StringEncoding];
                //NSLog(@"dataString = %@", dataString);
                UIAlertView *alert = [[UIAlertView alloc] initWithTitle:@"Erreur" message:dataString delegate:nil cancelButtonTitle:@"Retour" otherButtonTitles:nil];
                [alert show];
                [self.loadingBottom stopAnimating];
                return;
            }
            
            if ([[publicTimeline valueForKey:@"resultat"] isEqualToString:@"true"]) {
                [self setBottomDataArray:[[publicTimeline valueForKey:@"data"] valueForKey:@"publications"]];
                [self setPubArray:[NSMutableArray arrayWithObjects:[[publicTimeline valueForKey:@"data"] valueForKey:@"topPub"], [[publicTimeline valueForKey:@"data"] valueForKey:@"bottomPub"], nil]];
                [self performSelectorOnMainThread:@selector(reloadData) withObject:nil waitUntilDone:YES];
                [self.loadingBottom stopAnimating];
            }
            else {
                [[[UIAlertView alloc] initWithTitle:@"Erreur" message:[publicTimeline valueForKey:@"data"] delegate:nil cancelButtonTitle:@"Retour" otherButtonTitles:nil] show];
                [self.loadingBottom stopAnimating];
            }
        }
    }];
}

-(void)reloadData {
    
    if (![[[pubArray objectAtIndex:0] valueForKey:@"image"] isEqualToString:@""] && ![[[pubArray objectAtIndex:0] valueForKey:@"url"] isEqualToString:@""]) {
        ratioPubTop = 0.13888;
    }
    else {
        ratioPubTop = 0;
    }
    
    if (![[[pubArray objectAtIndex:1] valueForKey:@"image"] isEqualToString:@""] && ![[[pubArray objectAtIndex:1] valueForKey:@"url"] isEqualToString:@""]) {
        ratioPubBottom = 0.13888;
    }
    else {
        ratioPubBottom = 0;
    }
    
    collectionViewLayout.headerReferenceSize = CGSizeMake(self.view.frame.size.width, self.view.frame.size.width*ratioPubTop+coreHeaderHeight);
    collectionViewLayout.footerReferenceSize = CGSizeMake(self.view.frame.size.width, self.view.frame.size.width*ratioPubBottom);
    [bottomCollectionView setCollectionViewLayout:collectionViewLayout];
    
    [bottomCollectionView reloadData];
}

-(void)onTouchButton:(id)sender {
    
    UIButton *tempbutton = (UIButton*)sender;
    if ([tempbutton.titleLabel.text isEqualToString:@"Ouvrir"]) {
        if (delegate && [delegate respondsToSelector:@selector(openBoughtItem:)]) {
            [delegate openBoughtItem:self.dataDictionary];
        }
    }
    else if ([tempbutton.titleLabel.text isEqualToString:@"Télécharger"])
    {
        NSUserDefaults *defaults = [NSUserDefaults standardUserDefaults];
        [defaults setObject:[NSNumber numberWithBool:NO] forKey:@"showNoIssue"];
        [defaults synchronize];
        [self performSelectorOnMainThread:@selector(backOnMainThread) withObject:nil waitUntilDone:NO];
    }
    else {
        
        if (isCompteValidate == 0) {
            NSString *storyboardString = @"Main_iPhone";
            if (isPad()) {
                storyboardString = @"Main_iPad";
            }
            
            UIStoryboard *sb = [UIStoryboard storyboardWithName:storyboardString bundle:nil];
            
            CompteNonActiverViewController* controller = (CompteNonActiverViewController*)[sb instantiateViewControllerWithIdentifier:@"CompteNonActiverViewController"];
            [controller SetDimsissWhenEnded:YES];
            controller.delegate = self;
            
            UINavigationController *navCon = [[UINavigationController alloc] initWithRootViewController:controller];
            [navCon setModalPresentationStyle:UIModalPresentationFormSheet];
            [self presentViewController:navCon animated:YES completion:nil];
            return;
        }
        
        NSUserDefaults *defaults = [NSUserDefaults standardUserDefaults];
        NSString *username = [defaults objectForKey:@"username"];
        NSString *password = [defaults objectForKey:@"password"];
        BOOL skiped = NO;
        NSDate *lastSkipDate = [defaults objectForKey:@"lastSkipCompte"];
        if (lastSkipDate != nil) {
            NSUInteger unitFlags = NSDayCalendarUnit;
            NSCalendar *calendar = [[NSCalendar alloc] initWithCalendarIdentifier:NSGregorianCalendar];
            NSDateComponents *components = [calendar components:unitFlags fromDate:lastSkipDate toDate:[NSDate date] options:0];
            if ([components day]+1 < 3) {
                skiped = YES;
            }
        }
        
        
        
        if ((username == nil || [username isEqualToString:@""]) && (password == nil || [password isEqualToString:@""]) && !skiped) {
            
            //[[NSNotificationCenter defaultCenter] postNotificationName:@"pushActionSheetAccount" object:nil];
            NSString *storyboardString = @"Main_iPhone";
            if (isPad()) {
                storyboardString = @"Main_iPad";
            }
            
            UIStoryboard *sb = [UIStoryboard storyboardWithName:storyboardString bundle:nil];
            
            CompteViewController* controller = (CompteViewController*)[sb instantiateViewControllerWithIdentifier:@"CompteViewController"];
            [controller setDelegate:self];
            
            UINavigationController *navCon = [[UINavigationController alloc] initWithRootViewController:controller];
            [navCon setModalPresentationStyle:UIModalPresentationFormSheet];
            [self presentViewController:navCon animated:YES completion:nil];
            
        }
        else {
            BuyingWithVirtualCurrencyViewController *vc = [[BuyingWithVirtualCurrencyViewController alloc] initWithNibName:nil bundle:nil];
            [vc setAchatData:self.dataDictionary];
            [vc setDelegate:self];
            [vc setModalPresentationStyle:UIModalPresentationFormSheet];
            [self presentViewController:vc animated:YES completion:nil];
        }
    }
    
}

-(void)setIsCompteValide:(int)temp {
    // -1 = pas vérifier encore
    // 0 = non vérifier
    // 1 = vérifier
    
    isCompteValidate = temp;
    switch (isCompteValidate) {
        case -1: {
            [self checkCompteStatus];
        }
            break;
        case 0: {
            /*
            NSString *storyboardString = @"Main_iPhone";
            if (isPad()) {
                storyboardString = @"Main_iPad";
            }
            
            UIStoryboard *sb = [UIStoryboard storyboardWithName:storyboardString bundle:nil];
            
            CompteNonActiverViewController* controller = (CompteNonActiverViewController*)[sb instantiateViewControllerWithIdentifier:@"CompteNonActiverViewController"];
            
            controller.delegate = self;
            
            UINavigationController *navCon = [[UINavigationController alloc] initWithRootViewController:controller];
            [navCon setModalPresentationStyle:UIModalPresentationFormSheet];
            [self presentViewController:navCon animated:YES completion:nil];
            */
        }
            break;
        
        default:
            break;
    }
    [bottomCollectionView reloadData];
    
}
  
-(void)checkCompteStatus {
    
    NSUserDefaults *defaults = [NSUserDefaults standardUserDefaults];
    NSString *username = [defaults valueForKey:@"username"];
    NSString *password = [defaults valueForKey:@"password"];
    
    if (username == nil || password == nil) {
        [self setIsCompteValide:1];
        return;
    }
    
    NSURL *url = [NSURL URLWithString:[NSString stringWithFormat:@"%@/getCurrentCreditAndActivation.php",kAppBaseURL]];
    NSURLRequest *request = [NSURLRequest requestWithURL:url];
    GTMHTTPFetcher* myFetcher = [GTMHTTPFetcher fetcherWithRequest:request];
    
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
                //NSLog(@"dataString = %@", dataString);
                UIAlertView *alert = [[UIAlertView alloc] initWithTitle:@"Erreur" message:[NSString stringWithFormat:@"%@",dataString] delegate:nil cancelButtonTitle:@"Retour" otherButtonTitles:nil];
                [alert show];
                return;
            }
            
            //NSLog(@"%@",publicTimeline);
            if ([[publicTimeline valueForKey:@"resultat"] isEqualToString:@"true"]) {
                
                if ([[[publicTimeline valueForKey:@"data"] valueForKey:@"activated"] intValue] == 0) {
                    [self setIsCompteValide:0];
                }
                else {
                    [self setIsCompteValide:1];
                }
                
            }
            
        }
    }];
}

#pragma mark - Compte non activer delegate

-(void)compteActiver {
    [self setIsCompteValide:1];
}

-(void)dismissFromActivation {
    NSLog(@"DetailStoreView - dismissFromActivation");
    //[self.navigationController popViewControllerAnimated:YES];
}

#pragma mark - UICollectionViewController
-(NSInteger)numberOfSectionsInCollectionView:(UICollectionView *)collectionView {
    return 1;
}
-(NSInteger)collectionView:(UICollectionView *)collectionView numberOfItemsInSection:(NSInteger)section {
    return [bottomDataArray count];
}
-(UICollectionViewCell *)collectionView:(UICollectionView *)collectionView cellForItemAtIndexPath:(NSIndexPath *)indexPath {
    //static NSString *identifier = @"issueCell";
    EditionsStoreView *cell = (EditionsStoreView*)[collectionView dequeueReusableCellWithReuseIdentifier:@"editionsStoreView" forIndexPath:indexPath];
    
    [cell setMemeEditeurData:[bottomDataArray objectAtIndex:indexPath.row]];
    
    //if (indexPath.row > [self.storeViewLayout numberOfColumns]) {
    //    [cell.bordertop setHidden:NO];
    //}
    
    //[cell.borderright setHidden:NO];
    
    return cell;
}

-(UICollectionReusableView *)collectionView:(UICollectionView *)collectionView viewForSupplementaryElementOfKind:(NSString *)kind atIndexPath:(NSIndexPath *)indexPath {
    
    if ( kind == UICollectionElementKindSectionHeader ) {
        //[UIView setAnimationsEnabled:NO];
        DetailHeaderViewCell *tempCellView = [collectionView dequeueReusableSupplementaryViewOfKind:UICollectionElementKindSectionHeader withReuseIdentifier:@"HeaderCellDetail" forIndexPath:indexPath];
        
        [tempCellView.prixStringLabel setup];
        //[tempCellView.prixButton addTarget:self action:@selector(onTouchButton:) forControlEvents:UIControlEventTouchUpInside];
        
        [tempCellView.imageView setUrl:[NSURL URLWithString:[self.dataDictionary valueForKey:@"coverPath"]]];
        [tempCellView.imageView startDownload];
        
        //[self.navBar.topItem setTitle:[self.dataDictionary valueForKey:@"nom"]];
        [tempCellView.nomLabel setText:[self.dataDictionary valueForKey:@"nom"]];
        [tempCellView.categorieLabel setText:[self.dataDictionary valueForKey:@"categorie"]];
        NSDateFormatter *dateFormatter = [[NSDateFormatter alloc] init];
        [dateFormatter setDateFormat:@"yyyy-MM-dd"];
        NSDate *tempDate = [dateFormatter dateFromString:[self.dataDictionary valueForKey:@"datePublication"]];
        [dateFormatter setDateFormat:@"d"];
        
        NSString *dateString = [dateFormatter stringFromDate:tempDate];
        [dateFormatter setDateFormat:@"MMMM"];
        dateString = [dateString stringByAppendingFormat:@" %@ ", [self convertMonthStringToFR:[dateFormatter stringFromDate:tempDate]]];
        [dateFormatter setDateFormat:@"yyyy"];
        dateString = [dateString stringByAppendingFormat:@"%@",[dateFormatter stringFromDate:tempDate]];
        if (isPad()) {
            [tempCellView.dateLabel setText:[NSString stringWithFormat:@"Édition du %@", dateString]];
        }
        else {
            [tempCellView.dateLabel setText:[NSString stringWithFormat:@"Édition du\n%@", dateString]];
        }
        
        [tempCellView.prixStringLabel.prixLabel setText:[NSString stringWithFormat:@"%@",[self.dataDictionary valueForKey:@"prix"]]];
        
        [tempCellView.prixButton addTarget:self action:@selector(onTouchButton:) forControlEvents:UIControlEventTouchUpInside];
        
        
        
        managedObjectContext = [[NSManagedObjectContext alloc] init];
        [managedObjectContext setUndoManager:nil];
        [managedObjectContext setPersistentStoreCoordinator:[(AppDelegate*)[[UIApplication sharedApplication] delegate] persistentStoreCoordinator]];
        
        NSFetchRequest *request = [[NSFetchRequest alloc] init];
        [request setEntity:[NSEntityDescription entityForName:@"Editions" inManagedObjectContext:managedObjectContext]];
        
        NSError *error = nil;
        
        
        NSPredicate *predicate = [NSPredicate predicateWithFormat:@"id == %d", [[self.dataDictionary valueForKey:@"id"] intValue]];
        [request setPredicate:predicate];
        
        
        NSArray *results = [managedObjectContext executeFetchRequest:request error:&error];
        if ([results count] != 0) {
            [tempCellView.prixButton setTitle:@"Ouvrir" forState:UIControlStateNormal];
            [tempCellView.prixStringLabel setAlpha:0];
            [tempCellView.creditwarningLabel setAlpha:0];
            [tempCellView movePrixButtonBought:YES];
            
            if ([[self.dataDictionary valueForKey:@"telechargementRestant"] intValue] > 0) {
                
                
                if ([[self.dataDictionary valueForKey:@"telechargementRestant"] intValue] == 1) {
                    [tempCellView.noteButtonLabel setText:[NSString stringWithFormat:@"%d téléchargement restant",[[self.dataDictionary valueForKey:@"telechargementRestant"] intValue]]];
                }
                else {
                    [tempCellView.noteButtonLabel setText:[NSString stringWithFormat:@"%d téléchargements restant",[[self.dataDictionary valueForKey:@"telechargementRestant"] intValue]]];
                }
                
            }
            else if ([[self.dataDictionary valueForKey:@"telechargementRestant"] intValue] == 0) {
                
                [tempCellView.noteButtonLabel setText:@"Téléchargement par achat épuisé"];
                
            }
            
        }
        else if ([[self.dataDictionary valueForKey:@"telechargementRestant"] intValue] > 0) {
           
            [tempCellView.prixButton setTitle:@"Télécharger" forState:UIControlStateNormal];
            [tempCellView.prixStringLabel setAlpha:0];
            [tempCellView.creditwarningLabel setAlpha:0];
            
            [tempCellView movePrixButtonBought:YES];
            
            if ([[self.dataDictionary valueForKey:@"telechargementRestant"] intValue] == 1) {
                [tempCellView.noteButtonLabel setText:[NSString stringWithFormat:@"%d téléchargement restant",[[self.dataDictionary valueForKey:@"telechargementRestant"] intValue]]];
            }
            else {
                [tempCellView.noteButtonLabel setText:[NSString stringWithFormat:@"%d téléchargements restant",[[self.dataDictionary valueForKey:@"telechargementRestant"] intValue]]];
            }
            
        }
        else if ([[self.dataDictionary valueForKey:@"telechargementRestant"] intValue] == 0) {
            [tempCellView.prixButton setTitle:@"Acheter à nouveau" forState:UIControlStateNormal];
            [tempCellView.prixStringLabel setAlpha:0];
            [tempCellView.creditwarningLabel setAlpha:0];
            
            [tempCellView movePrixButtonBought:YES];
            
            [tempCellView.noteButtonLabel setText:@"Téléchargement par achat épuisé"];
            
            
        }
        /*
        else if ([[self.dataDictionary valueForKey:@"bought"] intValue] == 1) {
            [tempCellView.prixButton setTitle:@"Télécharger" forState:UIControlStateNormal];
            [tempCellView.prixStringLabel setAlpha:0];
            [tempCellView.creditwarningLabel setAlpha:0];
            
            [tempCellView movePrixButtonBought:YES];
            
        }
        */
        else if ([[self.dataDictionary valueForKey:@"telechargementRestant"] intValue] == -1 && [[self.dataDictionary valueForKey:@"isSubscription"] intValue] == 1 )
        {
            
            [tempCellView.prixButton setTitle:@"Acheter à nouveau" forState:UIControlStateNormal];
            [tempCellView.prixStringLabel setAlpha:0];
            [tempCellView.creditwarningLabel setAlpha:0];
            
            [tempCellView movePrixButtonBought:YES];
            
            [tempCellView.noteButtonLabel setText:@"Téléchargement par achat épuisé"];
        
        }
        else if ([[self.dataDictionary valueForKey:@"telechargementRestant"] intValue] == -1 && [[self.dataDictionary valueForKey:@"isSubscription"] intValue] == 0) {
            [tempCellView.prixButton setTitle:@"Acheter" forState:UIControlStateNormal];
            [tempCellView.prixStringLabel setAlpha:1];
            [tempCellView.creditwarningLabel setAlpha:1];
            
            [tempCellView.noteButtonLabel setText:@"3 téléchargements par achat"];
            
        }
        
        NSUserDefaults *defaults = [NSUserDefaults standardUserDefaults];
        NSString *username = [defaults objectForKey:@"username"];
        NSString *password = [defaults objectForKey:@"password"];
        if ((username == nil || [username isEqualToString:@""]) && (password == nil || [password isEqualToString:@""])) {
            [tempCellView.noteButtonLabel setText:@"Aucun retéléchargement\nsans compte ekiosk"];
        }
        
        if (isPad()) {
            UIInterfaceOrientation interfaceOrientation = self.interfaceOrientation;
            if (UIInterfaceOrientationIsLandscape(interfaceOrientation)) {
                NSLog(@"UIInterfaceOrientationIsLandscape");
                [tempCellView AnimationToLandscape:0.0];
            }
            else {
                NSLog(@"UIInterfaceOrientationIsPortrait");
                [tempCellView AnimationToPortrait:0.0];
            }
            
            if (refreshing) {
                [tempCellView.prixButton setEnabled:NO];
                [tempCellView.prixButton setAlpha:0.5];
            }
            else {
                [tempCellView.prixButton setEnabled:YES];
                [tempCellView.prixButton setAlpha:1];
            }
        }
        
        if (isCompteValidate == -1) {
            [tempCellView.verifAccountValideAI startAnimating];
            
            [tempCellView.prixButton setEnabled:NO];
            [tempCellView.prixButton setAlpha:0.6];
            [tempCellView.noteButtonLabel setAlpha:0.2];
            [tempCellView.noteButtonView setAlpha:0.2];
        }
        /*else if (isCompteValidate == 0) {
            [tempCellView.verifAccountValideAI stopAnimating];
            
            [tempCellView.prixButton setEnabled:NO];
            [tempCellView.prixButton setAlpha:0.6];
            [tempCellView.noteButtonLabel setAlpha:0.2];
            [tempCellView.noteButtonView setAlpha:0.2];
        }*/
        else {
            [tempCellView.verifAccountValideAI stopAnimating];
            
            [tempCellView.prixButton setEnabled:YES];
            [tempCellView.prixButton setAlpha:1];
            [tempCellView.noteButtonLabel setAlpha:1];
            [tempCellView.noteButtonView setAlpha:1];
        }
        
        
        if (ratioPubTop == 0) {
            [tempCellView PubModOff];
        }
        else {
            [tempCellView PubModOn];
            if (pubArray != nil) {
                if (![[[pubArray objectAtIndex:0] valueForKey:@"image"] isEqualToString:@""]) {
                    [tempCellView.adsView setImageUrl:[[pubArray objectAtIndex:0] valueForKey:@"image"]];
                    [tempCellView.adsView startDownload];
                }
                
                if (![[[pubArray objectAtIndex:0] valueForKey:@"url"] isEqualToString:@""]) {
                    [tempCellView.adsView setUrlToOpen:[[pubArray objectAtIndex:0] valueForKey:@"url"]];
                }
            }
            else {
                [tempCellView.adsView clearView];
            }
        }
        
        
        
        [tempCellView setClipsToBounds:YES];
        //[UIView setAnimationsEnabled:YES];
        return tempCellView;
        
    }
    else if ( kind == UICollectionElementKindSectionFooter ) {
        //[UIView setAnimationsEnabled:NO];
        AdsHeaderCollectionView *tempCellView = [collectionView dequeueReusableSupplementaryViewOfKind:UICollectionElementKindSectionFooter withReuseIdentifier:@"FooterCellDetail" forIndexPath:indexPath];
        
        if (pubArray != nil) {
            if (![[[pubArray objectAtIndex:1] valueForKey:@"image"] isEqualToString:@""]) {
                [tempCellView setImageUrl:[[pubArray objectAtIndex:1] valueForKey:@"image"]];
                [tempCellView startDownload];
            }
            
            if (![[[pubArray objectAtIndex:1] valueForKey:@"url"] isEqualToString:@""]) {
                [tempCellView setUrlToOpen:[[pubArray objectAtIndex:1] valueForKey:@"url"]];
            }
        }
        else {
            [tempCellView clearView];
        }
        
        return tempCellView;
        
    }
    
    return nil;
    
}

-(void)collectionView:(UICollectionView *)collectionView didSelectItemAtIndexPath:(NSIndexPath *)indexPath {
    [self setDataDictionary:[self.bottomDataArray objectAtIndex:indexPath.row]];
    
    [self.bottomCollectionView setContentOffset:CGPointMake(0, -64) animated:YES];
    
    [self.bottomCollectionView reloadData];

    [self performSelector:@selector(scrollToTop) withObject:nil afterDelay:0.01];
    
    /*
    [self.prixStringLabel setHidden:NO];
    [self.creditwarningLabel setHidden:NO];
    
    NSArray *array = [self.scrollView subviews];
    
    [self setDataDictionary:[self.bottomDataArray objectAtIndex:indexPath.row]];
    
    [self.imageView setUrl:[NSURL URLWithString:[self.dataDictionary valueForKey:@"coverPath"]]];
    [self.imageView startDownload];
    
    //[self.navBar.topItem setTitle:[self.dataDictionary valueForKey:@"nom"]];
    [self.categorieLabel setText:[self.dataDictionary valueForKey:@"categorie"]];
    
    
    
    NSDateFormatter *dateFormatter = [[NSDateFormatter alloc] init];
    [dateFormatter setDateFormat:@"yyyy-MM-dd"];
    NSDate *tempDate = [dateFormatter dateFromString:[self.dataDictionary valueForKey:@"datePublication"]];
    [dateFormatter setDateFormat:@"d"];
    
    NSString *dateString = [dateFormatter stringFromDate:tempDate];
    [dateFormatter setDateFormat:@"MMMM"];
    dateString = [dateString stringByAppendingFormat:@" %@ ", [self convertMonthStringToFR:[dateFormatter stringFromDate:tempDate]]];
    [dateFormatter setDateFormat:@"yyyy"];
    dateString = [dateString stringByAppendingFormat:@"%@",[dateFormatter stringFromDate:tempDate]];
    
    [self.dateLabel setText:[NSString stringWithFormat:@"Édition du %@", dateString]];
    [self.prixStringLabel.prixLabel setText:[NSString stringWithFormat:@"%@",[self.dataDictionary valueForKey:@"prix"]]];
    
    managedObjectContext = [[NSManagedObjectContext alloc] init];
    [managedObjectContext setUndoManager:nil];
    [managedObjectContext setPersistentStoreCoordinator:[(AppDelegate*)[[UIApplication sharedApplication] delegate] persistentStoreCoordinator]];
    
    NSFetchRequest *request = [[NSFetchRequest alloc] init];
    [request setEntity:[NSEntityDescription entityForName:@"Editions" inManagedObjectContext:managedObjectContext]];
    
    NSError *error = nil;
    
    
    NSPredicate *predicate = [NSPredicate predicateWithFormat:@"id == %d", [[self.dataDictionary valueForKey:@"id"] intValue]];
    [request setPredicate:predicate];
    
    NSArray *results = [managedObjectContext executeFetchRequest:request error:&error];
    NSString *titleString = @"";
    
    [UIView beginAnimations:nil context:nil];
    [UIView setAnimationDuration:0.2];
    
    if ([results count] != 0) {
        titleString = @"Ouvrir";
        //[self.prixLabel setText:[NSString stringWithFormat:@"Ouvrir"]];
        [self.prixStringLabel setAlpha:0];
        [self.creditwarningLabel setAlpha:0];
        
        //if (!isPad()) {
            [self movePrixButtonBought:YES];
        //}
    }
    else if ([[self.dataDictionary valueForKey:@"bought"] intValue] == 1) {
        titleString = @"Télécharger";
        //[self.prixLabel setText:@"Télécharger"];
        [self.prixStringLabel setAlpha:0];
        [self.creditwarningLabel setAlpha:0];
        //if (!isPad()) {
            [self movePrixButtonBought:YES];
        //}
    }
    else {
        titleString = @"Acheter";
        [self.prixStringLabel setAlpha:1];
        [self.creditwarningLabel setAlpha:1];
        //[self.prixLabel setText:@"Acheter cette édition"];
        //if (!isPad()) {
            [self movePrixButtonBought:NO];
        //}
    }
    [UIView commitAnimations];
    
    [self.prixButton setTitle:titleString forState:UIControlStateNormal];
    */
}

-(void)scrollToTop {
    [self.bottomCollectionView setContentOffset:CGPointMake(0, -74) animated:YES];
}

#pragma mark - buying function

-(void)CreditSelection {
    VirtualCurrencyViewController *vc = [[VirtualCurrencyViewController alloc] initWithNibName:nil bundle:nil];
    [self presentViewController:vc animated:YES completion:nil];
    [vc addNavigationBarAndBackground];
}

-(void)AchatComplete {
    [self performSelectorOnMainThread:@selector(backOnMainThread) withObject:nil waitUntilDone:NO];
}

-(void)backOnMainThread {
    //[self dismissViewControllerAnimated:YES completion:^{
    
    
    [self.navigationController popViewControllerAnimated:NO];
        if (delegate && [delegate respondsToSelector:@selector(didPurchaseItems:WithImage:AndFrame:)]) {
            StoreViewController *tempStoreView = (StoreViewController*)delegate;
            DetailHeaderViewCell *temp = [bottomCollectionView dequeueReusableSupplementaryViewOfKind:UICollectionElementKindSectionHeader withReuseIdentifier:@"HeaderCellDetail" forIndexPath:[NSIndexPath indexPathForRow:0 inSection:0]];
            CGRect frame = [tempStoreView.view convertRect:temp.imageView.frame fromView:self.view];
            [delegate didPurchaseItems:self.dataDictionary WithImage:[temp.imageView image] AndFrame:frame];
        }
    
    //}];
    
}

-(NSString*)convertMonthStringToFR:(NSString*)enMonthString {
    NSString *frString;
    
    if ([enMonthString isEqualToString:@"January"]) {
        frString = @"Janvier";
    }
    else if ([enMonthString isEqualToString:@"February"]) {
        frString = @"Février";
    }
    else if ([enMonthString isEqualToString:@"March"]) {
        frString = @"Mars";
    }
    else if ([enMonthString isEqualToString:@"April"]) {
        frString = @"Avril";
    }
    else if ([enMonthString isEqualToString:@"May"]) {
        frString = @"Mai";
    }
    else if ([enMonthString isEqualToString:@"June"]) {
        frString = @"Juin";
    }
    else if ([enMonthString isEqualToString:@"July"]) {
        frString = @"Juillet";
    }
    else if ([enMonthString isEqualToString:@"August"]) {
        frString = @"Août";
    }
    else if ([enMonthString isEqualToString:@"September"]) {
        frString = @"Septembre";
    }
    else if ([enMonthString isEqualToString:@"October"]) {
        frString = @"Octobre";
    }
    else if ([enMonthString isEqualToString:@"November"]) {
        frString = @"Novembre";
    }
    else if ([enMonthString isEqualToString:@"December"]) {
        frString = @"Décembre";
    }
    else {
        frString = enMonthString;
    }
    
    return frString;
}

-(void)getDataFromServeur {
    [self.loadingBottom startAnimating];
    refreshing = YES;
    
    [self.bottomCollectionView reloadData];
    //désactiver le bouton pour le temps du refresh
    
    NSUserDefaults *defaults = [NSUserDefaults standardUserDefaults];
    NSURL *url = [NSURL URLWithString:[NSString stringWithFormat:@"%@/getEditionRefreshed.php?editionId=%@&username=%@&password=%@",kAppBaseURL, [self.dataDictionary valueForKey:@"id"], [defaults objectForKey:@"username"], [defaults objectForKey:@"password"]]];
    NSURLRequest *request = [NSURLRequest requestWithURL:url];
    GTMHTTPFetcher* myFetcher = [GTMHTTPFetcher fetcherWithRequest:request];
    [myFetcher beginFetchWithCompletionHandler:^(NSData *retrievedData, NSError *error) {
        if (error != nil) {
            // status code or network error
            NSLog(@"error getdatafromserveur");
            [self.loadingBottom stopAnimating];
        } else {
            // succeeded
            
            NSMutableArray *publicTimeline = [NSJSONSerialization
                                              JSONObjectWithData:retrievedData
                                              options:NSJSONReadingMutableContainers
                                              error:nil];
            if (publicTimeline == nil) {
                NSString *dataString = [[NSString alloc] initWithData:retrievedData encoding:NSUTF8StringEncoding];
                //NSLog(@"dataString = %@", dataString);
                UIAlertView *alert = [[UIAlertView alloc] initWithTitle:@"Erreur" message:dataString delegate:nil cancelButtonTitle:@"Retour" otherButtonTitles:nil];
                [alert show];
                [self.loadingBottom stopAnimating];
                return;
            }
            
            //NSLog(@"%@",publicTimeline);
            if ([[publicTimeline valueForKey:@"resultat"] isEqualToString:@"true"]) {
                refreshing = NO;
                [self.dataDictionary setValue:[[publicTimeline valueForKey:@"data"] valueForKey:@"bought"] forKey:@"bought"];
                [self.loadingBottom stopAnimating];
                [bottomDataArray removeAllObjects];
                [self.bottomCollectionView reloadData];
                [UIApplication sharedApplication].networkActivityIndicatorVisible = NO;
                [self loadBottom];
                
                if ([[self.dataDictionary valueForKey:@"bought"] intValue] != 1) {
                    [self onTouchButton:nil];
                }
                
                
            }
            else {
                [[[UIAlertView alloc] initWithTitle:@"Erreur" message:[publicTimeline valueForKey:@"data"] delegate:nil cancelButtonTitle:@"Retour" otherButtonTitles:nil] show];
                [self.loadingBottom stopAnimating];
            }
        }
    }];
}

#pragma mark - CompteViewController
-(void)cancelActivationView {
    NSLog(@"DetailStoreView - cancelActivationVire");
    [self.navigationController popViewControllerAnimated:YES];
}

-(void)compteConnecter {
    NSLog(@"test compte connecter");
    NSLog(@"%@",dataDictionary);
    [self getDataFromServeur];
}
-(void)compteSkip {
    //[self getDataFromServeur];
    [self onTouchButton:nil];
}
/*
-(void)DismissCompteValider {
    NSLog(@"sdasdajksdhajkshdajksdhajksdhakjsdhakj--------------------------------------------------------------------------------------2");
    [self.navigationController popToRootViewControllerAnimated:YES];
}
*/
-(void)CompleteCompteValider {
    [self getDataFromServeur];
}


@end
