//
//  VirtualCurrencyViewController.m
//  eKiosk
//
//  Created by Maxime Julien-Paquet on 2014-02-19.
//  Copyright (c) 2014 Maxime Julien-Paquet. All rights reserved.
//

#import "VirtualCurrencyViewController.h"
#import "GTMHTTPFetcher.h"
#import "VirtualCurrencyViewCell.h"
#import "VirtualCurrencyHeaderViewCell.h"
#import "ConsumableIAPHelper.h"


@interface VirtualCurrencyViewController () {
    NSIndexPath *lastSelected;
    int isCompteValidate;
}

@end

@implementation VirtualCurrencyViewController

@synthesize dataArray, itunesArray, vcBundleCollectionView, navBar, loadingBundle, toolBar;

- (id)initWithNibName:(NSString *)nibNameOrNil bundle:(NSBundle *)nibBundleOrNil {
    self = [super initWithNibName:nibNameOrNil bundle:nibBundleOrNil];
    if (self) {
        // Custom initialization
    }
    return self;
}

- (void)viewDidLoad {
    [super viewDidLoad];
    NSLog(@"viewdidload");
    
    
	// Do any additional setup after loading the view.
    isCompteValidate = -1;
    lastSelected = nil;
    
    UICollectionViewFlowLayout *collectionViewLayout = [[UICollectionViewFlowLayout alloc] init];
    if (isPad()) {
        collectionViewLayout.sectionInset = UIEdgeInsetsMake(20, 15, 10, 15);
        collectionViewLayout.minimumLineSpacing = 20;
        collectionViewLayout.itemSize = CGSizeMake(230.0f, 315.0f);
        collectionViewLayout.headerReferenceSize = CGSizeMake(self.view.frame.size.width, 220);
    }
    else {
        collectionViewLayout.sectionInset = UIEdgeInsetsMake(20, 15, 20, 15);
        collectionViewLayout.minimumLineSpacing = 20;
        collectionViewLayout.itemSize = CGSizeMake(230.0f*0.6f, 315.0f*0.6f);
        collectionViewLayout.headerReferenceSize = CGSizeMake(self.view.frame.size.width, 160);
    }
    
    
    vcBundleCollectionView = [[UICollectionView alloc]initWithFrame:self.view.bounds collectionViewLayout:collectionViewLayout];
    vcBundleCollectionView.backgroundColor = [UIColor clearColor];
    vcBundleCollectionView.opaque = NO;
    vcBundleCollectionView.autoresizesSubviews = YES;
    vcBundleCollectionView.autoresizingMask = UIViewAutoresizingFlexibleWidth | UIViewAutoresizingFlexibleHeight;
    
    [vcBundleCollectionView registerClass:[VirtualCurrencyViewCell class] forCellWithReuseIdentifier:@"virtualCurrencyViewCell"];
    [vcBundleCollectionView registerClass:[VirtualCurrencyHeaderViewCell class] forSupplementaryViewOfKind:UICollectionElementKindSectionHeader withReuseIdentifier:@"virtualCurrencyHeaderViewCell"];
    [vcBundleCollectionView setDelegate:self];
    [vcBundleCollectionView setDataSource:self];
    [self.view addSubview:vcBundleCollectionView];
    [self.view sendSubviewToBack:vcBundleCollectionView];
    [self.view addSubview:[self loadingBundle]];
    
    [self setIsCompteValide:-1];
    
}

- (void)didReceiveMemoryWarning {
    [super didReceiveMemoryWarning];
    // Dispose of any resources that can be recreated.
}


#pragma maek - initialisation

-(UIActivityIndicatorView *)loadingBundle {
    if (loadingBundle == nil) {
        loadingBundle = [[UIActivityIndicatorView alloc] initWithActivityIndicatorStyle:UIActivityIndicatorViewStyleWhiteLarge];
        loadingBundle.autoresizingMask = UIViewAutoresizingFlexibleBottomMargin | UIViewAutoresizingFlexibleTopMargin | UIViewAutoresizingFlexibleLeftMargin | UIViewAutoresizingFlexibleRightMargin;
        loadingBundle.frame = CGRectMake(0, 0, 40, 40);
        loadingBundle.center = self.vcBundleCollectionView.center;
        loadingBundle.color = [UIColor blackColor];
        loadingBundle.hidesWhenStopped = YES;
    }
    return loadingBundle;
}

-(void)getDataFromServeur {
    [self.loadingBundle startAnimating];
    NSURL *url = [NSURL URLWithString:[NSString stringWithFormat:@"%@/getVCBundle.php",kAppBaseURL]];
    NSURLRequest *request = [NSURLRequest requestWithURL:url];
    GTMHTTPFetcher* myFetcher = [GTMHTTPFetcher fetcherWithRequest:request];
    [myFetcher beginFetchWithCompletionHandler:^(NSData *retrievedData, NSError *error) {
        if (error != nil) {
            // status code or network error
            NSLog(@"error getdatafromserveur");
            [self.loadingBundle stopAnimating];
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
                [self.loadingBundle stopAnimating];
                return;
            }
            
            NSLog(@"%@",publicTimeline);
            if ([[publicTimeline valueForKey:@"resultat"] isEqualToString:@"true"]) {
                [self setDataArray:[publicTimeline valueForKey:@"data"]];
                [self verificationDesBundleSurItunesConnect];
            }
            else {
                [[[UIAlertView alloc] initWithTitle:@"Erreur" message:[publicTimeline valueForKey:@"data"] delegate:nil cancelButtonTitle:@"Retour" otherButtonTitles:nil] show];
                [self.loadingBundle stopAnimating];
            }
        }
    }];
}

-(void)verificationDesBundleSurItunesConnect {
    
    NSMutableSet *productIdentifier = [[NSMutableSet alloc] init];
    
    for (int x = 0; x < [dataArray count]; ++x) {
        
        [productIdentifier addObject:[[dataArray objectAtIndex:x] valueForKey:@"itunes"]];
        
    }
    
    
    
    
    [[ConsumableIAPHelper sharedInstanceWithProduct:productIdentifier] requestProductsWithCompletionHandler:^(BOOL success, NSArray *products) {
        if (success) {
            itunesArray = products;
            NSLog(@"%@", itunesArray);
            [self removeBundleNotOnItunes];
            //[self.tableView reloadData];
        }
        else {
            [[[UIAlertView alloc] initWithTitle:@"Erreur" message:@"Impossible de se connecter à itunes. Vérifier votre connexion internet." delegate:nil cancelButtonTitle:@"Retour" otherButtonTitles:nil] show];
            [self.loadingBundle stopAnimating];
        }
        //[self.refreshControl endRefreshing];
    }];
    
}

-(void)removeBundleNotOnItunes {
    for (int x = 0; x < [dataArray count]; ++x) {
        BOOL notfound = YES;
        for (int y = 0; y < [itunesArray count]; ++y) {
            NSString *itunesString = [[dataArray objectAtIndex:x] valueForKey:@"itunes"];
            NSString *productString = [[itunesArray objectAtIndex:y] productIdentifier];
            if ([itunesString isEqualToString:productString]) {
                notfound = NO;
            }
        }
        
        if (notfound) {
            [dataArray removeObjectAtIndex:x];
        }
    }
    
    [self.loadingBundle stopAnimating];
    [vcBundleCollectionView reloadData];
    
}

-(void)addNavigationBarAndBackground {
    NSLog(@"addNavigationBarAndBackground");
    vcBundleCollectionView.contentInset = UIEdgeInsetsMake(64, 0, 0, 0);
    
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
    
    UIToolbar *tempTool = [[UIToolbar alloc] initWithFrame:CGRectMake(0, -24, self.view.frame.size.width, 44)];
    [self.view addSubview:tempTool];
    
    navBar = [[UINavigationBar alloc] initWithFrame:CGRectMake(0, 20, self.view.frame.size.width, 44)];
    navBar.autoresizingMask = UIViewAutoresizingFlexibleWidth | UIViewAutoresizingFlexibleBottomMargin;
    UINavigationItem *navItem = [[UINavigationItem alloc] initWithTitle:@"Crédits ekiosk"];
    UIBarButtonItem *barButtonItem = [[UIBarButtonItem alloc] initWithTitle:@"Retour" style:UIBarButtonItemStyleBordered target:self action:@selector(cancelAction:)];
    navItem.leftBarButtonItem = barButtonItem;
    [navBar pushNavigationItem:navItem animated:false];
    
    [self.view addSubview:navBar];
}

-(void)cancelAction:(id)sender {
    [self dismissViewControllerAnimated:YES completion:nil];
}

#pragma mark - UICollectionViewController
-(NSInteger)numberOfSectionsInCollectionView:(UICollectionView *)collectionView {
    return 1;
}
-(NSInteger)collectionView:(UICollectionView *)collectionView numberOfItemsInSection:(NSInteger)section {
    return [dataArray count];
}
-(UICollectionViewCell *)collectionView:(UICollectionView *)collectionView cellForItemAtIndexPath:(NSIndexPath *)indexPath {
    VirtualCurrencyViewCell *cell = (VirtualCurrencyViewCell*)[collectionView dequeueReusableCellWithReuseIdentifier:@"virtualCurrencyViewCell" forIndexPath:indexPath];
    
    [cell setDataInView:[dataArray objectAtIndex:indexPath.row]];
    
    return cell;
}
-(UICollectionReusableView *)collectionView:(UICollectionView *)collectionView viewForSupplementaryElementOfKind:(NSString *)kind atIndexPath:(NSIndexPath *)indexPath {
    
    UICollectionReusableView *reusableview = nil;
    
    if (kind == UICollectionElementKindSectionHeader) {
        VirtualCurrencyHeaderViewCell *headerView = [collectionView dequeueReusableSupplementaryViewOfKind:UICollectionElementKindSectionHeader withReuseIdentifier:@"virtualCurrencyHeaderViewCell" forIndexPath:indexPath];
        reusableview = headerView;
    }
    
    return reusableview;
    
}

-(void)collectionView:(UICollectionView *)collectionView didSelectItemAtIndexPath:(NSIndexPath *)indexPath {
    
    
    
    if (isCompteValidate == 0) {
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
        
        lastSelected = indexPath;
        
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
        return;
    }
    
    [collectionView deselectItemAtIndexPath:indexPath animated:YES];
    DetailVirtualCurrencyViewController *vc = [[DetailVirtualCurrencyViewController alloc] initWithNibName:nil bundle:nil];
    
    [vc setDataArray:[dataArray objectAtIndex:indexPath.row]];
    
    for (int y = 0; y < [itunesArray count]; ++y) {
        NSString *itunesString = [[dataArray objectAtIndex:indexPath.row] valueForKey:@"itunes"];
        NSString *productString = [[itunesArray objectAtIndex:y] productIdentifier];
        if ([itunesString isEqualToString:productString]) {
            [vc setItunesProduct:[itunesArray objectAtIndex:y]];
            break;
        }
    }
    if (self.navBar != nil) {
        [vc setDelegate:self];
    }
    [vc setModalPresentationStyle:UIModalPresentationFormSheet];
    
    [self presentViewController:vc animated:YES completion:nil];
    
    
}

-(void)EndBuyingCredit {
    [self dismissViewControllerAnimated:YES completion:nil];
}



-(void)checkCompteStatus {
    
    NSUserDefaults *defaults = [NSUserDefaults standardUserDefaults];
    NSString *username = [defaults valueForKey:@"username"];
    NSString *password = [defaults valueForKey:@"password"];
    
    if (username == nil || password == nil) {
        [self setIsCompteValide:1];
        return;
    }
    
    [self.loadingBundle startAnimating];
    NSURL *url = [NSURL URLWithString:[NSString stringWithFormat:@"%@/getCurrentCreditAndActivation.php",kAppBaseURL]];
    NSURLRequest *request = [NSURLRequest requestWithURL:url];
    GTMHTTPFetcher* myFetcher = [GTMHTTPFetcher fetcherWithRequest:request];
    
    [myFetcher setPostData:[[NSString stringWithFormat:@"username=%@&password=%@",username, password] dataUsingEncoding:NSUTF8StringEncoding]];
    [myFetcher beginFetchWithCompletionHandler:^(NSData *retrievedData, NSError *error) {
        [self.loadingBundle stopAnimating];
        
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
            [self getDataFromServeur];
        }
            break;
            
        case 1: {
            [self getDataFromServeur];
        }
            break;
            
        default:
            break;
    }

    
}

#pragma mark - CompteNonActiverViewController

-(void)compteActiver {
    [dataArray removeAllObjects];
    [vcBundleCollectionView performSelectorOnMainThread:@selector(reloadData) withObject:nil waitUntilDone:YES];
    
    [self setIsCompteValide:1];
}

-(void)dismissFromActivation {
    //[self dismissViewControllerAnimated:YES completion:nil];
    [dataArray removeAllObjects];
    [vcBundleCollectionView performSelectorOnMainThread:@selector(reloadData) withObject:nil waitUntilDone:YES];
    
    [self setIsCompteValide:-1];
}

#pragma mark - CompteViewController 

-(void)compteConnecter {
    NSLog(@"VirtualCurrency - compteDelegate");
    //[self collectionView:vcBundleCollectionView didSelectItemAtIndexPath:lastSelected];
    [dataArray removeAllObjects];
    [vcBundleCollectionView performSelectorOnMainThread:@selector(reloadData) withObject:nil waitUntilDone:YES];
    
    [self setIsCompteValide:-1];
}

-(void)compteSkip {
    [self collectionView:vcBundleCollectionView didSelectItemAtIndexPath:lastSelected];
}

-(void)cancelActivationView {
    [dataArray removeAllObjects];
    [vcBundleCollectionView performSelectorOnMainThread:@selector(reloadData) withObject:nil waitUntilDone:YES];
    
    [self setIsCompteValide:-1];
}

@end
