//
//  StoreViewController.m
//  IVOIREKIOSK
//
//  Created by Maxime Julien-Paquet on 2013-12-18.
//  Copyright (c) 2013 Maxime Julien-Paquet. All rights reserved.
//

#import "StoreViewController.h"
#import "EditionsStoreView.h"
#import "EditionImageView.h"
#import "AbonnementRowView.h"
#import "Editions.h"
#import "AppDelegate.h"

//#import "StoreViewLayout.h"
#import "ArchivesJournauxViewController.h"
#import "GTMHTTPFetcher.h"

#import <StoreKit/StoreKit.h>
//#import "SingleIssueIAPHelper.h"

#import "AdsHeaderCollectionView.h"

static NSString * const storeCellIdentifier = @"storeCell";

@interface StoreViewController () {
    
    NSArray *_products;
    
    UICollectionViewFlowLayout *collectionViewLayout;
    
    float ratioPubTop;
    float ratioPubBottom;
}

//@property (nonatomic, strong) StoreViewLayout *storeViewLayout;

@end

@implementation StoreViewController

@synthesize storeTabBarViewController;
//@synthesize storeViewLayout;
@synthesize insertionContext, editionEntityDescription;
@synthesize pageControl, categorieButton, tempDictionary, tempCoverAnimationView, currentCreditLabel, tabBar, loadingAnimation, dataArray,abonnementSwitch;
//@synthesize mainScrolView;

- (id)initWithNibName:(NSString *)nibNameOrNil bundle:(NSBundle *)nibBundleOrNil {
    self = [super initWithNibName:nibNameOrNil bundle:nibBundleOrNil];
    if (self) {
        // Custom initialization
        
        
    }
    return self;
}

- (void)viewDidLoad {
    [super viewDidLoad];
    ratioPubTop = 0.13888;
    ratioPubBottom = 0.13888;
    [[NSNotificationCenter defaultCenter] addObserver:self
                                             selector:@selector(ChangementDeStatusDuCompte:)
                                                 name:@"ChangementDeStatusDuCompte"
                                               object:nil];
    
	// Do any additional setup after loading the view.
    //self.view.backgroundColor = [UIColor colorWithWhite:0.9 alpha:1];
    
    //self.mainScrolView.backgroundColor = [UIColor colorWithWhite:0.8 alpha:1];
    
    collectionViewLayout = [[UICollectionViewFlowLayout alloc] init];
    if (isPad()) {
        collectionViewLayout.sectionInset = UIEdgeInsetsMake(20, 40, 20, 40);
        collectionViewLayout.minimumLineSpacing = 20;
        collectionViewLayout.itemSize = CGSizeMake(130.0f, 210.0f);
        collectionViewLayout.headerReferenceSize = CGSizeMake(self.view.frame.size.width, self.view.frame.size.width*ratioPubTop);
        collectionViewLayout.footerReferenceSize = CGSizeMake(self.view.frame.size.width, self.view.frame.size.width*ratioPubBottom);
    }
    else {
        //rendu la
        collectionViewLayout.sectionInset = UIEdgeInsetsMake(20, 20, 20, 20);
        collectionViewLayout.minimumLineSpacing = 20;
        collectionViewLayout.itemSize = CGSizeMake(77, 130);
        collectionViewLayout.headerReferenceSize = CGSizeMake(self.view.frame.size.width, self.view.frame.size.width*ratioPubTop);
        collectionViewLayout.footerReferenceSize = CGSizeMake(self.view.frame.size.width, self.view.frame.size.width*ratioPubBottom);
        
    }
    
    //self.storeViewLayout = [[StoreViewLayout alloc] init];
    storeCollectionView = [[UICollectionView alloc]initWithFrame:CGRectMake(0, 45, self.view.frame.size.width, self.view.frame.size.height - 46)
                                             collectionViewLayout:collectionViewLayout];
    storeCollectionView.autoresizingMask = UIViewAutoresizingFlexibleWidth | UIViewAutoresizingFlexibleHeight;
    
    //storeCollectionView.contentInset = UIEdgeInsetsMake(74, 0, 0, 0);
    storeCollectionView.contentInset = UIEdgeInsetsMake(0, 0, 0, 0);
    storeCollectionView.backgroundColor = [UIColor clearColor];
    storeCollectionView.delegate = self;
    storeCollectionView.dataSource = self;
    [storeCollectionView registerClass:[EditionsStoreView class] forCellWithReuseIdentifier:storeCellIdentifier];
    [storeCollectionView registerClass:[AdsHeaderCollectionView class] forSupplementaryViewOfKind:UICollectionElementKindSectionHeader withReuseIdentifier:@"HeaderCell"];
    [storeCollectionView registerClass:[AdsHeaderCollectionView class] forSupplementaryViewOfKind:UICollectionElementKindSectionFooter withReuseIdentifier:@"FooterCell"];
    
    [self.view addSubview:storeCollectionView];
    
    [self.view addSubview:[self currentCreditLabel]];
    
    [self.view addSubview:[self loadingAnimation]];
    
    [self.view sendSubviewToBack:storeCollectionView];
    
    [self getDataFromServeur];
}

- (void)didReceiveMemoryWarning {
    [super didReceiveMemoryWarning];
    // Dispose of any resources that can be recreated.
}

-(void)dealloc {
    [[NSNotificationCenter defaultCenter] removeObserver:self
                                                    name:@"ChangementDeStatusDuCompte"
                                                  object:nil];
}

-(UIActivityIndicatorView *)loadingAnimation {
    if (loadingAnimation == nil) {
        loadingAnimation = [[UIActivityIndicatorView alloc] initWithActivityIndicatorStyle:UIActivityIndicatorViewStyleWhiteLarge];
        loadingAnimation.autoresizingMask = UIViewAutoresizingFlexibleBottomMargin | UIViewAutoresizingFlexibleTopMargin | UIViewAutoresizingFlexibleLeftMargin | UIViewAutoresizingFlexibleRightMargin;
        loadingAnimation.frame = CGRectMake(0, 0, 40, 40);
        loadingAnimation.center = storeCollectionView.center;
        loadingAnimation.color = [UIColor blackColor];
        loadingAnimation.hidesWhenStopped = YES;
    }
    return loadingAnimation;
}

-(MiniVCLabel *)currentCreditLabel {
    if (currentCreditLabel == nil) {
        if (isPad()) {
            currentCreditLabel = [[MiniVCLabel alloc] initWithFrame:CGRectMake(self.view.frame.size.width-220, 2, 200, 40)];
        }
        else {
            currentCreditLabel = [[MiniVCLabel alloc] initWithFrame:CGRectMake(self.view.frame.size.width-120, 2, 100, 40)];
        }
        
        currentCreditLabel.autoresizingMask = UIViewAutoresizingFlexibleLeftMargin;
    }
    return currentCreditLabel;
}

#pragma maek - Life Cycle View

-(void)viewWillAppear:(BOOL)animated {
    [super viewWillAppear:animated];
    
    NSUserDefaults *defaults = [NSUserDefaults standardUserDefaults];
    int current = [[defaults valueForKey:@"ekcredit"] intValue];
    [self.currentCreditLabel.prixLabel setText:[NSString stringWithFormat:@"%d",current]];
    
    
    /*
    if (([UIApplication sharedApplication].statusBarOrientation == UIDeviceOrientationLandscapeLeft) ||
        ([UIApplication sharedApplication].statusBarOrientation == UIDeviceOrientationLandscapeRight))
    {
        self.storeViewLayout.numberOfColumns = 5;
        [self.storeViewLayout invalidateLayout];
    }
    else {
        if (isPad()) {
            self.storeViewLayout.numberOfColumns = 4;
        }
        else {
            self.storeViewLayout.numberOfColumns = 3;
        }
        
        [self.storeViewLayout invalidateLayout];
    }*/
}
-(void)viewDidAppear:(BOOL)animated {
    [super viewDidAppear:animated];
    collectionViewLayout.headerReferenceSize = CGSizeMake(storeCollectionView.frame.size.width, storeCollectionView.frame.size.width*ratioPubTop);
    collectionViewLayout.footerReferenceSize = CGSizeMake(storeCollectionView.frame.size.width, storeCollectionView.frame.size.width*ratioPubBottom);
    [storeCollectionView setCollectionViewLayout:collectionViewLayout];
}

-(void)viewWillDisappear:(BOOL)animated {
    [super viewWillDisappear:animated];
}
-(void)didRotateFromInterfaceOrientation:(UIInterfaceOrientation)fromInterfaceOrientation {
    [super didRotateFromInterfaceOrientation:fromInterfaceOrientation];
    
    collectionViewLayout.headerReferenceSize = CGSizeMake(storeCollectionView.frame.size.width, storeCollectionView.frame.size.width*ratioPubTop);
    collectionViewLayout.footerReferenceSize = CGSizeMake(storeCollectionView.frame.size.width, storeCollectionView.frame.size.width*ratioPubBottom);
    [storeCollectionView setCollectionViewLayout:collectionViewLayout];
    //[collectionViewLayout invalidateLayout];
    //[storeCollectionView reloadData];
    
    //[storeCollectionView reloadSections:[NSIndexSet indexSetWithIndexesInRange:NSMakeRange(0, 1)]];
    //[storeCollectionView.collectionViewLayout invalidateLayout];
}
- (void)willRotateToInterfaceOrientation:(UIInterfaceOrientation)toInterfaceOrientation duration:(NSTimeInterval)duration {
    //self.issueAlbumLayout.numberOfColumns = 3;
    //[self.issueAlbumLayout invalidateLayout];
    
    /*
    if (UIInterfaceOrientationIsLandscape(toInterfaceOrientation)) {
        self.storeViewLayout.numberOfColumns = 5;
        
        // handle insets for iPhone 4 or 5
        //CGFloat sideInset = [UIScreen mainScreen].preferredMode.size.width == 1136.0f ? 45.0f : 25.0f;
        
        //self.issueAlbumLayout.itemInsets = UIEdgeInsetsMake(22.0f, sideInset, 13.0f, sideInset);
        
    } else {
        self.storeViewLayout.numberOfColumns = 4;
        //self.issueAlbumLayout.itemInsets = UIEdgeInsetsMake(22.0f, 22.0f, 13.0f, 22.0f);
    }
    */
}

-(void)ChangementDeStatusDuCompte:(NSNotification*)notif {
    
    [dataArray removeAllObjects];
    [storeCollectionView performSelectorOnMainThread:@selector(reloadData) withObject:nil waitUntilDone:YES];
    
    [self getDataFromServeur];
    
}

-(void)onTouchCategorie:(id)sender {
    UIActionSheet *test = [[UIActionSheet alloc] initWithTitle:nil
                                                      delegate:self
                                             cancelButtonTitle:@"Annuler"
                                        destructiveButtonTitle:nil
                                             otherButtonTitles:
                           @"Tous",
                           @"Quotidien",
                           @"Hebdomadaire",
                           @"Mensuel",
                           @"Magazine",
                           @"Livre",
                           nil];
    [test setTag:101];
    if (isPad()) {
        [test showFromRect:self.categorieButton.frame inView:self.view animated:YES];
    }
    else {
        [test showFromTabBar:self.tabBar];
    }
    
}
-(void)actionSheet:(UIActionSheet *)actionSheet clickedButtonAtIndex:(NSInteger)buttonIndex {
    NSArray *array = @[@"Tous",
                       @"Quotidien",
                       @"Hebdomadaire",
                       @"Mensuel",
                       @"Magazine",
                       @"Livre"];
    
    if (actionSheet.tag != 101 || (actionSheet.tag == 101 && buttonIndex == [array count])) {
        return;
    }
    
    
    [UIView setAnimationsEnabled:NO];
    [self.categorieButton setTitle:[array objectAtIndex:buttonIndex] forState:UIControlStateNormal];
    [UIView setAnimationsEnabled:YES];
    
    [dataArray removeAllObjects];
    [storeCollectionView performSelectorOnMainThread:@selector(reloadData) withObject:nil waitUntilDone:YES];
    
    [self getDataFromServeur];
    
}

-(void)archiveTouched:(id)sender {
    ArchivesJournauxViewController *VC = [[ArchivesJournauxViewController alloc] init];
    
    [self.navigationController pushViewController:VC animated:YES];

}

-(IBAction)switchValueChanged:(id)sender
{
    [dataArray removeAllObjects];
    [storeCollectionView performSelectorOnMainThread:@selector(reloadData) withObject:nil waitUntilDone:YES];
    
    [self getDataFromServeur];
}

-(void)alertView:(UIAlertView *)alertView clickedButtonAtIndex:(NSInteger)buttonIndex {
    if (buttonIndex == 0) {
        NSLog(@"%@",self.tempDictionary);
        [self insertEditionInCoreData:[NSNumber numberWithFloat:0.0f]];
    }
    
    
    //[self performSelector:@selector(dismissViewController:) withObject:nil afterDelay:1];
    //[self dismissViewControllerAnimated:YES completion:nil];
}

-(void)getDataFromServeur
{
    [self.loadingAnimation startAnimating];
    NSUserDefaults *defaults = [NSUserDefaults standardUserDefaults];
    NSURL *url = [NSURL URLWithString:[NSString stringWithFormat:@"%@/getRecentsParCategorie.php?categorie=%@&username=%@&password=%@&abonnement=%d",kAppBaseURL, self.categorieButton.titleLabel.text, [defaults objectForKey:@"username"], [defaults objectForKey:@"password"],self.abonnementSwitch.isOn]];
 
    NSURLRequest *request = [NSURLRequest requestWithURL:url];
    GTMHTTPFetcher* myFetcher = [GTMHTTPFetcher fetcherWithRequest:request];
    [myFetcher beginFetchWithCompletionHandler:^(NSData *retrievedData, NSError *error) {
        if (error != nil) {
            // status code or network error
            NSLog(@"error getdatafromserveur");
            [self.loadingAnimation stopAnimating];
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
                [self.loadingAnimation stopAnimating];
                return;
            }
            
            NSLog(@"%@",publicTimeline);
            if ([[publicTimeline valueForKey:@"resultat"] isEqualToString:@"true"]) {
                [self setDataArray:[publicTimeline valueForKey:@"data"]];
                [self.loadingAnimation stopAnimating];
                [UIApplication sharedApplication].networkActivityIndicatorVisible = NO;
                [self performSelectorOnMainThread:@selector(reloadData) withObject:nil waitUntilDone:YES];
            }
            else {
                [[[UIAlertView alloc] initWithTitle:@"Erreur" message:[publicTimeline valueForKey:@"data"] delegate:nil cancelButtonTitle:@"Retour" otherButtonTitles:nil] show];
                [self.loadingAnimation stopAnimating];
            }
        }
    }];
}

-(void)reloadData {
    
    if (![[[dataArray valueForKey:@"topPub"] valueForKey:@"image"] isEqualToString:@""] && ![[[dataArray valueForKey:@"topPub"] valueForKey:@"url"] isEqualToString:@""]) {
        ratioPubTop = 0.13888;
    }
    else {
        ratioPubTop = 0;
    }
    
    if (![[[dataArray valueForKey:@"bottomPub"] valueForKey:@"image"] isEqualToString:@""] && ![[[dataArray valueForKey:@"bottomPub"] valueForKey:@"url"] isEqualToString:@""]) {
        ratioPubBottom = 0.13888;
    }
    else {
        ratioPubBottom = 0;
    }
    NSLog(@"%f", ratioPubTop);
    NSLog(@"%f", ratioPubBottom);
    collectionViewLayout.headerReferenceSize = CGSizeMake(storeCollectionView.frame.size.width, storeCollectionView.frame.size.width*ratioPubTop);
    collectionViewLayout.footerReferenceSize = CGSizeMake(storeCollectionView.frame.size.width, storeCollectionView.frame.size.width*ratioPubBottom);
    [storeCollectionView setCollectionViewLayout:collectionViewLayout];
    
    [storeCollectionView reloadData];
}

#pragma mark - DetailsStoreViewControllerDelegate 

-(void)didPurchaseItems:(NSDictionary *)data WithImage:(UIImage *)image AndFrame:(CGRect)frame {
    tempCoverAnimationView = [[UIImageView alloc] initWithFrame:frame];
    [tempCoverAnimationView setImage:image];
    tempDictionary = data;
    [self insertEditionInCoreData:[NSNumber numberWithFloat:0.0f]];
}
-(void)openBoughtItem:(NSDictionary *)data {
    //[self performSelector:@selector(dismissViewController:) withObject:nil afterDelay:0.8];
    NSLog(@"open call");
    [self dismissViewControllerAnimated:YES completion:^{
        [[NSNotificationCenter defaultCenter] postNotificationName:@"PushReaderWithEdition" object:data];
    }];
    
    
}
-(void)pushToAbonnementFromDetailView {
    [self.storeTabBarViewController goToAbonnement];
    //[self.storeTabBarViewController.tabBar setSelectedItem:[[self.storeTabBarViewController.tabBar items] objectAtIndex:1]];
}

#pragma mark - UICollectionViewController
-(NSInteger)numberOfSectionsInCollectionView:(UICollectionView *)collectionView {
    return 1;
}
-(NSInteger)collectionView:(UICollectionView *)collectionView numberOfItemsInSection:(NSInteger)section {
    return [[dataArray valueForKey:@"publications"] count];
}
-(UICollectionViewCell *)collectionView:(UICollectionView *)collectionView cellForItemAtIndexPath:(NSIndexPath *)indexPath {
    //static NSString *identifier = @"issueCell";
    EditionsStoreView *cell = (EditionsStoreView*)[collectionView dequeueReusableCellWithReuseIdentifier:storeCellIdentifier forIndexPath:indexPath];
    
    [cell setData:[[dataArray valueForKey:@"publications"] objectAtIndex:indexPath.row]];
    
    //if (indexPath.row > [self.storeViewLayout numberOfColumns]) {
    //    [cell.bordertop setHidden:NO];
    //}
    
    //[cell.borderright setHidden:NO];
    
    return cell;
}
-(void)collectionView:(UICollectionView *)collectionView didSelectItemAtIndexPath:(NSIndexPath *)indexPath {
    NSString *storyboardString = @"Main_iPhone";
    if (isPad()) {
        storyboardString = @"Main_iPad";
    }
    
    UIStoryboard *sb = [UIStoryboard storyboardWithName:storyboardString bundle:nil];
    
    DetailsStoreViewController* vc = (DetailsStoreViewController*)[sb instantiateViewControllerWithIdentifier:@"DetailsStoreViewController"];
    //[vc setModalPresentationStyle:UIModalPresentationFormSheet];
    
    
    [vc setDelegate:self];
    [vc setDataDictionary:[[dataArray valueForKey:@"publications"] objectAtIndex:indexPath.row]];
    //[self presentViewController:vc animated:YES completion:nil];
    [self.navigationController pushViewController:vc animated:YES];
}

-(UICollectionReusableView *)collectionView:(UICollectionView *)collectionView viewForSupplementaryElementOfKind:(NSString *)kind atIndexPath:(NSIndexPath *)indexPath {
    
    if ( kind == UICollectionElementKindSectionHeader ) {
        //[UIView setAnimationsEnabled:NO];
        AdsHeaderCollectionView *tempCellView = [collectionView dequeueReusableSupplementaryViewOfKind:UICollectionElementKindSectionHeader withReuseIdentifier:@"HeaderCell" forIndexPath:indexPath];
        
        if (dataArray != nil && [dataArray count] > 0) {
            if (![[[dataArray valueForKey:@"topPub"] valueForKey:@"image"] isEqualToString:@""]) {
                [tempCellView setImageUrl:[[dataArray valueForKey:@"topPub"] valueForKey:@"image"]];
                [tempCellView startDownload];
            }
            
            if (![[[dataArray valueForKey:@"topPub"] valueForKey:@"url"] isEqualToString:@""]) {
                [tempCellView setUrlToOpen:[[dataArray valueForKey:@"topPub"] valueForKey:@"url"]];
            }
        }
        
        
        return tempCellView;
        
    }
    else if ( kind == UICollectionElementKindSectionFooter ) {
        //[UIView setAnimationsEnabled:NO];
        AdsHeaderCollectionView *tempCellView = [collectionView dequeueReusableSupplementaryViewOfKind:UICollectionElementKindSectionFooter withReuseIdentifier:@"FooterCell" forIndexPath:indexPath];
        if (dataArray != nil && [dataArray count] > 0) {
            if (![[[dataArray valueForKey:@"bottomPub"] valueForKey:@"image"] isEqualToString:@""]) {
                [tempCellView setImageUrl:[[dataArray valueForKey:@"topPub"] valueForKey:@"image"]];
                [tempCellView startDownload];
            }
            
            if (![[[dataArray valueForKey:@"topPub"] valueForKey:@"url"] isEqualToString:@""]) {
                [tempCellView setUrlToOpen:[[dataArray valueForKey:@"topPub"] valueForKey:@"url"]];
            }
        }
        
        return tempCellView;
        
    }
    
    return nil;
    
}


#pragma mark - ajouter journal dans le coredata
/*-(NSManagedObjectContext *)insertionContext {
    if (insertionContext == nil) {
        insertionContext = [[NSManagedObjectContext alloc] init];
        [insertionContext setPersistentStoreCoordinator:[(AppDelegate*)[[UIApplication sharedApplication] delegate] persistentStoreCoordinator]];
    }
    return insertionContext;
}

-(NSEntityDescription *)editionEntityDescription {
    if (editionEntityDescription == nil) {
        editionEntityDescription = [NSEntityDescription entityForName:@"Editions" inManagedObjectContext:self.insertionContext];
    }
    return editionEntityDescription;
}
*/
-(void)insertEditionInCoreData:(NSNumber*)delay {
    
    insertionContext = [[NSManagedObjectContext alloc] init];
    [insertionContext setUndoManager:nil];
    [insertionContext setPersistentStoreCoordinator:[(AppDelegate*)[[UIApplication sharedApplication] delegate] persistentStoreCoordinator]];
    
    editionEntityDescription = [NSEntityDescription entityForName:@"Editions" inManagedObjectContext:insertionContext];
    
    Editions *currentEdition = [[Editions alloc] initWithEntity:editionEntityDescription
                                 insertIntoManagedObjectContext:insertionContext];
                               
    currentEdition.id = [NSNumber numberWithInt:[[tempDictionary valueForKey:@"id"] intValue]];
    currentEdition.idjournal = [NSNumber numberWithInt:[[tempDictionary valueForKey:@"id_journal"] intValue]];
    currentEdition.nom = [tempDictionary valueForKey:@"nom"];
    currentEdition.type = [tempDictionary valueForKey:@"type"];
    currentEdition.categorie = [tempDictionary valueForKey:@"categorie"];
    
    currentEdition.downloadpath = [tempDictionary valueForKey:@"downloadPath"];
    currentEdition.coverpath = [tempDictionary valueForKey:@"coverPath"];
    
    currentEdition.downloaddate = [[NSDate alloc] init];
    currentEdition.lu = [NSNumber numberWithBool:NO];
    currentEdition.favoris = [NSNumber numberWithBool:NO];
      currentEdition.isSubscription = [[tempDictionary valueForKey:@"isSubscription"] boolValue];
    NSDateFormatter *dateFormatter = [[NSDateFormatter alloc] init];
    
    //[dateFormatter setLocale:usLocale];
    [dateFormatter setDateFormat:@"yyyy-MM-dd"];
    
    currentEdition.publicationdate = [dateFormatter dateFromString:[tempDictionary valueForKey:@"datePublication"]];
    
    //FT_SAVE_MOC([self insertionContext])
    [insertionContext performSelectorOnMainThread:@selector(save:) withObject:nil waitUntilDone:YES];
    //[insertionContext save:nil];
    
    [self performSelectorOnMainThread:@selector(coverAnimation:) withObject:delay waitUntilDone:NO];
}

-(void)coverAnimation:(NSNumber*)delay {
    
    [self.view addSubview:tempCoverAnimationView];
    
    float dure = 0.6;
    dure = dure + (round(tempCoverAnimationView.frame.origin.y / 200)/10);
    
    [UIView beginAnimations:nil context:nil];
    [UIView setAnimationDuration:dure];
    [UIView setAnimationDelay:[delay floatValue]];
    [UIView setAnimationCurve:UIViewAnimationCurveEaseOut];
    [UIView setAnimationDelegate:self];
    
    tempCoverAnimationView.frame = CGRectMake(self.view.frame.size.width - 60, 40, 40, 80);
    tempCoverAnimationView.alpha = 0.4;
    
    [UIView commitAnimations];
    
}
-(void)animationDidStop:(CAAnimation *)anim finished:(BOOL)flag {
    NSLog(@"finished = %d",flag);
    [self performSelectorOnMainThread:@selector(coverAnimationComplete) withObject:nil waitUntilDone:YES];
}
-(void)coverAnimationComplete {
    self.tempDictionary = nil;
    [tempCoverAnimationView removeFromSuperview];
    tempCoverAnimationView = nil;
    [self dismissViewControllerAnimated:YES completion:nil];
    [[NSNotificationCenter defaultCenter] postNotificationName:@"ReloadNouveauxCollectionView" object:nil];
    //[self dismissViewControllerAnimated:YES completion:nil];
}

/*
#pragma mark - test in-ap purchase

-(void)testLoadProduct {
    _products = nil;
    
    [[SingleIssueIAPHelper sharedInstanceWithProductId:@"com.ngser.ekioskmobile.inapp.single.notrevoie"] requestProductsWithCompletionHandler:^(BOOL success, NSArray *products) {
        if (success) {
            _products = products;
            [self productLoaded];
        }
    }];
    
    
}

-(void)productLoaded {
    NSLog(@"product = %@",_products);
    SKProduct *product = _products[0];
    [[SingleIssueIAPHelper sharedInstance] buyProduct:product];
}
*/

@end
