//
//  ViewController.m
//  IVOIREKIOSK
//
//  Created by Maxime Julien-Paquet on 2013-12-03.
//  Copyright (c) 2013 Maxime Julien-Paquet. All rights reserved.
//

#import "ViewController.h"
#import "AppDelegate.h"
#import "Editions.h"
#import "EditionView.h"
#import "EditionImageView.h"

//#import "IssuesViewCell.h"
#import "EditionsViewLayout.h"
#import "FPPopoverController.h"

#import "ReglagesViewController.h"
#import "Reachability.h"

#import "Login2ViewController.h"
#import "CreateProfilViewController.h"

#import "ReglagesIphoneViewController.h"


#import "UIView+Toast.h"

static NSString * const issueCellIdentifier = @"issueCell";

@interface ViewController () {
    NSMutableArray *editionsViewArray;
    NSMutableArray *downloadingEditionsViewArray;
}

@property (nonatomic, strong) EditionsViewLayout *issueAlbumLayout;

@end

@implementation ViewController

@synthesize issueAlbumLayout;

@synthesize managedObjectContext, operationQueue, progressView, countLabel, backgroundProgressImageView, filtreSegmented, kioskButtonItem, noIssuesImageView, overVC;
//@synthesize mainScrollView;

-(void)viewDidLoad {
    [super viewDidLoad];
    
    [[NSNotificationCenter defaultCenter] addObserver:self selector:@selector(_mocDidSaveNotification:) name:NSManagedObjectContextDidSaveNotification object:nil];
	// Do any additional setup after loading the view, typically from a nib.
    
    [[NSNotificationCenter defaultCenter] addObserver:self selector:@selector(updateIssues) name:@"CoreDataUpdated" object:nil];
    [[NSNotificationCenter defaultCenter] addObserver:self selector:@selector(reloadCollectionView:) name:@"ReloadCollectionView" object:nil];
    [[NSNotificationCenter defaultCenter] addObserver:self selector:@selector(reloadNouveauxCollectionView) name:@"ReloadNouveauxCollectionView" object:nil];
    [[NSNotificationCenter defaultCenter] addObserver:self selector:@selector(CloseMenuPopupAndPushViewController:) name:@"CloseMenuPopupAndPushViewController" object:nil];
    
    [[NSNotificationCenter defaultCenter] addObserver:self selector:@selector(pushReaderWithEdition:) name:@"PushReaderWithEdition" object:nil];
    
    
    //[[NSNotificationCenter defaultCenter] addObserver:self selector:@selector(show) name:@"SideMenuShow" object:nil];
    [[NSNotificationCenter defaultCenter] addObserver:self selector:@selector(CloseMenuPopupAndPushViewController:) name:@"SideMenuHide" object:nil];
    [[NSNotificationCenter defaultCenter] addObserver:self selector:@selector(reglages:) name:@"SideMenuShow" object:nil];
    
    
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
    
    
    editionsViewArray = [[NSMutableArray alloc] init];
    downloadingEditionsViewArray = [[NSMutableArray alloc] init];
    
    CGRect frame;
    UIImage *image;
    frame = self.view.bounds;
    if (isPad()) {
        image = [UIImage imageNamed:@"bg-tablette-ipad.jpg"];
    }
    else {
        
        //frame = CGRectMake(0, 0, 320, 568);
        
        if([UIScreen mainScreen].bounds.size.height == 568.0) {
            image = [UIImage imageNamed:@"bg-tablette-iphone5.jpg"];
        }
        else {
            image = [UIImage imageNamed:@"bg-tablette-iphone.jpg"];
        }
        
    }
    
    UIImageView *imageView = [[UIImageView alloc] initWithFrame:frame];
    [imageView setAutoresizingMask:UIViewAutoresizingFlexibleWidth];
    [imageView setImage:image];
    [self.view addSubview:imageView];
    
    
//    UICollectionViewFlowLayout *collectionViewLayout = [[UICollectionViewFlowLayout alloc] init];
//    collectionViewLayout.sectionInset = UIEdgeInsetsMake(53.0f, 30.0f, 10.0f, 10.0f);
//    collectionViewLayout.minimumLineSpacing = 55;
//    collectionViewLayout.itemSize = CGSizeMake(130.0f, 170.0f);
    
    
    self.issueAlbumLayout= [[EditionsViewLayout alloc]init];
    issuesCollectionView = [[UICollectionView alloc]initWithFrame:self.view.bounds
                                             collectionViewLayout:self.issueAlbumLayout];
    issuesCollectionView.autoresizingMask = UIViewAutoresizingFlexibleWidth | UIViewAutoresizingFlexibleHeight;
    
    issuesCollectionView.contentInset = UIEdgeInsetsMake(74, 0, 0, 0);
    issuesCollectionView.backgroundColor = [UIColor clearColor];
    issuesCollectionView.delegate = self;
    issuesCollectionView.dataSource = self;
    issuesCollectionView.pagingEnabled = YES;
    [issuesCollectionView registerClass:[EditionView class] forCellWithReuseIdentifier:issueCellIdentifier];
    
    [self.view addSubview:issuesCollectionView];
    [self.view sendSubviewToBack:issuesCollectionView];
    [self.view sendSubviewToBack:imageView];
    
    //[self.view addSubview:[self mainScrollView]];
    //[self.view addSubview:[self backgroundProgressImageView]];
    //[self.view addSubview:[self progressView]];
    //[self.view addSubview:[self countLabel]];
    
    downloadCount = 0;
    downloadTotal = 0;
    
    [self.view addSubview:[self noIssuesImageView]];
    
    
    
    NSUserDefaults *defaults = [NSUserDefaults standardUserDefaults];
    if ([defaults objectForKey:@"showTutoriel"] == nil || [[defaults objectForKey:@"showTutoriel"] boolValue] == YES) {
        overVC = [[OverTutorielViewController alloc] initWithNibName:nil bundle:nil];
        [self.navigationController.view addSubview:overVC.view];
    }
    
    //[[[[UIApplication sharedApplication] delegate] window] performSelector:@selector(addSubview:) withObject:overVC.view afterDelay:0.1];
    //[[[[UIApplication sharedApplication] delegate] window] addSubview:overVC.view];
    
}

-(void)dealloc {
    [[NSNotificationCenter defaultCenter] removeObserver:self name:@"CoreDataUpdated" object:nil];
    [[NSNotificationCenter defaultCenter] removeObserver:self name:@"ReloadCollectionView" object:nil];
    [[NSNotificationCenter defaultCenter] removeObserver:self name:@"ReloadNouveauxCollectionView" object:nil];
    //[[NSNotificationCenter defaultCenter] removeObserver:self name:@"CloseMenuPopupAndPushViewController" object:nil];
    [[NSNotificationCenter defaultCenter] removeObserver:self name:@"PushReaderWithEdition" object:nil];
    
    //[[NSNotificationCenter defaultCenter] removeObserver:self name:@"SideMenuShow" object:nil];
    [[NSNotificationCenter defaultCenter] removeObserver:self name:@"SideMenuHide" object:nil];
    [[NSNotificationCenter defaultCenter] removeObserver:self name:@"SideMenuShow" object:nil];
    
    //KEYBOARD OBSERVERS
    [[NSNotificationCenter defaultCenter] removeObserver:self name:UIKeyboardWillShowNotification object:nil];
    [[NSNotificationCenter defaultCenter] removeObserver:self name:UIKeyboardWillHideNotification object:nil];
}

-(void)didReceiveMemoryWarning {
    [super didReceiveMemoryWarning];
    // Dispose of any resources that can be recreated.
}

#pragma mark - Property define

-(NSManagedObjectContext *)managedObjectContext {
    if (managedObjectContext == nil) {
        AppDelegate *appDelegate = (AppDelegate *)[[UIApplication sharedApplication] delegate];
        managedObjectContext = [appDelegate managedObjectContext];
    }
    return managedObjectContext;
}

/*
-(UIScrollView *)mainScrollView {
    if (mainScrollView == nil) {
        mainScrollView = [[UIScrollView alloc] initWithFrame:CGRectMake(0, 0, self.view.frame.size.width, self.view.frame.size.height)];
        mainScrollView.pagingEnabled = YES;
        mainScrollView.backgroundColor = [UIColor clearColor];
    }
    return mainScrollView;
}
*/

-(NSOperationQueue *)operationQueue {
    if (operationQueue == nil) {
        operationQueue = [[NSOperationQueue alloc] init];
        operationQueue.maxConcurrentOperationCount = 1;
    }
    return operationQueue;
}

-(UIImageView *)backgroundProgressImageView {
    if (backgroundProgressImageView == nil) {
        backgroundProgressImageView = [[UIImageView alloc] initWithFrame:CGRectMake(0,
                                                                                    self.view.frame.size.height - 42,
                                                                                    self.view.frame.size.width,
                                                                                    42)];
        backgroundProgressImageView.backgroundColor = [UIColor colorWithWhite:1 alpha:0.5];
        backgroundProgressImageView.autoresizingMask = UIViewAutoresizingFlexibleTopMargin | UIViewAutoresizingFlexibleWidth;
        backgroundProgressImageView.hidden = YES;
    }
    return backgroundProgressImageView;
}

-(UIProgressView *)progressView {
    if (progressView == nil) {
        progressView = [[UIProgressView alloc] initWithProgressViewStyle:UIProgressViewStyleBar];
        progressView.frame = CGRectMake((self.view.frame.size.width - 200) / 2,
                                        self.view.frame.size.height - 30,
                                        200,
                                        30);
        progressView.autoresizingMask = UIViewAutoresizingFlexibleTopMargin | UIViewAutoresizingFlexibleRightMargin | UIViewAutoresizingFlexibleLeftMargin;
        progressView.hidden = YES;
    }
    return progressView;
}

-(UILabel *)countLabel {
    if (countLabel == nil) {
        countLabel = [[UILabel alloc] initWithFrame:CGRectMake((self.view.frame.size.width - 200) / 2,
                                                               self.view.frame.size.height - 32,
                                                               200,
                                                               30)];
        countLabel.autoresizingMask = UIViewAutoresizingFlexibleTopMargin | UIViewAutoresizingFlexibleRightMargin | UIViewAutoresizingFlexibleLeftMargin;
        countLabel.textAlignment = NSTextAlignmentCenter;
        countLabel.font = [UIFont fontWithName:@"Helvetica" size:18];
        countLabel.hidden = YES;
    }
    return countLabel;
}

-(UIImageView *)noIssuesImageView {
    if (noIssuesImageView == nil) {
        if (isPad()) {
            noIssuesImageView = [[UIImageView alloc] initWithFrame:CGRectMake(self.view.frame.size.width - 305, 60, 305, 215)];
        }
        else {
            noIssuesImageView = [[UIImageView alloc] initWithFrame:CGRectMake(self.view.frame.size.width - 244, 60, 244, 172)];
        }
        noIssuesImageView.image = [UIImage imageNamed:@"empty_library_dialog.png"];
        noIssuesImageView.autoresizingMask = UIViewAutoresizingFlexibleLeftMargin | UIViewAutoresizingFlexibleBottomMargin;
        noIssuesImageView.hidden = YES;
    }
    return noIssuesImageView;
}

#pragma mark - view life cycle

-(void)viewWillAppear:(BOOL)animated {
    [super viewWillAppear:animated];
    [[NSNotificationCenter defaultCenter] addObserver:self selector:@selector(pushActionSheetAccount) name:@"pushActionSheetAccount" object:nil];
    
    NSUserDefaults *defaults = [NSUserDefaults standardUserDefaults];
    BOOL showNoIssue = [[defaults objectForKey:@"showNoIssue"] boolValue];
    if (showNoIssue) {
        [self.noIssuesImageView setHidden:NO];
    }
    else {
        [self.noIssuesImageView setHidden:YES];
    }
    
    
    if (([UIApplication sharedApplication].statusBarOrientation == UIDeviceOrientationLandscapeLeft) ||
        ([UIApplication sharedApplication].statusBarOrientation == UIDeviceOrientationLandscapeRight))
    {
        
        self.issueAlbumLayout.numberOfColumns = 7;
        self.issueAlbumLayout.numberOfRow = 3;
        [self.issueAlbumLayout invalidateLayout];
    }
    else {
        if (isPad()) {
            self.issueAlbumLayout.numberOfColumns = 5;
            self.issueAlbumLayout.numberOfRow = 4;
        }
        else {
            if([UIScreen mainScreen].bounds.size.height == 568.0) {
                self.issueAlbumLayout.numberOfColumns = 3;
                self.issueAlbumLayout.numberOfRow = 3;
            }
            else {
                self.issueAlbumLayout.numberOfColumns = 4;
                self.issueAlbumLayout.numberOfRow = 3;
            }
        }
        [self.issueAlbumLayout invalidateLayout];
            
            
    }
    //[self updateIssues];
    [self segmentedSelectionChanged:self.filtreSegmented];
    
    //[self issuesDownloadVerification];
}

-(void)viewWillDisappear:(BOOL)animated {
    [super viewWillDisappear:animated];
    [[NSNotificationCenter defaultCenter] removeObserver:self name:@"pushActionSheetAccount" object:nil];
    //[self.operationQueue cancelAllOperations];
    //NSLog(@"cancel All Operation");
}

-(void)willRotateToInterfaceOrientation:(UIInterfaceOrientation)toInterfaceOrientation duration:(NSTimeInterval)duration {
    //self.issueAlbumLayout.numberOfColumns = 3;
    //[self.issueAlbumLayout invalidateLayout];
    [[NSNotificationCenter defaultCenter]
     postNotificationName:@"willrotate"
     object:@[
              [NSNumber numberWithInt:toInterfaceOrientation],
              [NSNumber numberWithFloat:duration]]];
    
    if (UIInterfaceOrientationIsLandscape(toInterfaceOrientation)) {
        self.issueAlbumLayout.numberOfColumns = 7;
        self.issueAlbumLayout.numberOfRow = 3;
        // handle insets for iPhone 4 or 5
        //CGFloat sideInset = [UIScreen mainScreen].preferredMode.size.width == 1136.0f ? 45.0f : 25.0f;
        
        //self.issueAlbumLayout.itemInsets = UIEdgeInsetsMake(22.0f, sideInset, 13.0f, sideInset);
        
    } else {
        self.issueAlbumLayout.numberOfColumns = 5;
        self.issueAlbumLayout.numberOfRow = 4;
        //self.issueAlbumLayout.itemInsets = UIEdgeInsetsMake(22.0f, 22.0f, 13.0f, 22.0f);
    }
    
}

-(void)didRotateFromInterfaceOrientation:(UIInterfaceOrientation)fromInterfaceOrientation {
    [super didRotateFromInterfaceOrientation:fromInterfaceOrientation];
    [[NSNotificationCenter defaultCenter] postNotificationName:@"didrotate" object:[NSNumber numberWithInt:fromInterfaceOrientation]];
}

#pragma mark - StoryBoard defined function

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
    
    if (overVC != nil) {
        [self.navigationController.view bringSubviewToFront:overVC.view];
    }
    
}

-(BOOL)connected {
    Reachability *reachability = [Reachability reachabilityForInternetConnection];
    NetworkStatus networkStatus = [reachability currentReachabilityStatus];
    return !(networkStatus == NotReachable);
}

-(BOOL)shouldPerformSegueWithIdentifier:(NSString *)identifier sender:(id)sender {
    if ([identifier isEqualToString:@"pushStoreSegue"]) {
        if (![self connected]) {
            [[[UIAlertView alloc] initWithTitle:@"Informations" message:@"Vous avez besoin d'une connexion internet pour ouvrir le Kiosk" delegate:nil cancelButtonTitle:@"Retour" otherButtonTitles:nil] show];
            return NO;
        }
        /*else {
            NSUserDefaults *defaults = [NSUserDefaults standardUserDefaults];
            NSString *username = [defaults objectForKey:@"username"];
            NSString *password = [defaults objectForKey:@"password"];
            
            if (username == nil || [username isEqualToString:@""] ||
                password == nil || [password isEqualToString:@""]) {
                [self.noIssuesImageView setHidden:YES];
                fromActionSheet = YES;
                [[[UIActionSheet alloc] initWithTitle:@"Vous avez besoin d'un compte pour ouvrir le Kiosk." delegate:self cancelButtonTitle:@"Retour" destructiveButtonTitle:nil otherButtonTitles:@"Me connecter", @"Créer mon compte", nil] showFromBarButtonItem:kioskButtonItem animated:YES];
                return NO;
            }
        }*/
    }
    return YES;
}

-(void)pushActionSheetAccount {
    [[[UIActionSheet alloc] initWithTitle:@"Vous avez besoin d'un compte pour ouvrir le Kiosk." delegate:self cancelButtonTitle:@"Retour" destructiveButtonTitle:nil otherButtonTitles:@"Me connecter", @"Créer mon compte", nil] showInView:self.view];
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
            [self presentViewController:controller animated:YES completion:nil];
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
            [self presentViewController:controller animated:YES completion:nil];
        }
            break;
            
        default:
            break;
    }
}

-(void)segmentedSelectionChanged:(id)sender {
    UISegmentedControl *segmented = (UISegmentedControl*)sender;
    switch ([segmented selectedSegmentIndex]) {
        case 0:
            [self performSelectorOnMainThread:@selector(getRecentIssues) withObject:nil waitUntilDone:NO];
            //[self updateIssues];
            break;
        case 1:
            [self performSelectorOnMainThread:@selector(getAll) withObject:nil waitUntilDone:NO];
            //[self getAll];
            break;
        case 2:
            [self performSelectorOnMainThread:@selector(getFavoris) withObject:nil waitUntilDone:NO];
            //[self getFavoris];
            break;
        case 3:
            [self performSelectorOnMainThread:@selector(getSubscription) withObject:nil waitUntilDone:NO];
            //[self getFavoris];
            break;
        default:
            break;
    }
    
}

-(void)reloadCollectionView:(NSNotification*)notif {
    NSLog(@"reloadCollectionView");
    if (notif.object != nil) {
        NSLog(@"notif.object != nil");
        NSString *tempString = (NSString*)notif.object;
        if ([tempString isEqualToString:@"deleted"]) {
            [self.view makeToast:@"Publication supprimée de votre appareil."
                        duration:3.0
                        position:@"bottom"];
        }
    }
    [self segmentedSelectionChanged:self.filtreSegmented];
    
}

-(void)reloadNouveauxCollectionView {
    [self.filtreSegmented setSelectedSegmentIndex:0];
    [self segmentedSelectionChanged:self.filtreSegmented];
}

#pragma mark - download and load data in view

-(NSDate*)getLastDownloadDate {
    NSFetchRequest *fetchRequest = [[NSFetchRequest alloc] init];
    NSEntityDescription *entity = [NSEntityDescription entityForName:@"Editions" inManagedObjectContext:self.managedObjectContext];
    [fetchRequest setEntity:entity];
    
    NSSortDescriptor *sortDescriptor = [[NSSortDescriptor alloc] initWithKey:@"downloaddate" ascending:NO];
    NSArray *sortDescriptors = [[NSArray alloc] initWithObjects:sortDescriptor, nil];
    [fetchRequest setSortDescriptors:sortDescriptors];
    
    [fetchRequest setFetchLimit:1];
    
    NSError *error;
    NSArray *items = [managedObjectContext executeFetchRequest:fetchRequest error:&error];
    
    NSLog(@"item = %@",items);
    
    NSDate *tempDate = nil;
    
    
    for (Editions *managedObject in items) {
        tempDate = managedObject.downloaddate;
    }
    
    NSLog(@"date = %@", tempDate);
    return tempDate;
}

-(void)getRecentIssues {
    
    [editionsViewArray removeAllObjects];
    [downloadingEditionsViewArray removeAllObjects];
    
    
    NSDate *lastDate = [self getLastDownloadDate];
    
    if (lastDate == nil) {
        [issuesCollectionView reloadData];
        return;
    }
    
    
    NSFetchRequest *fetchRequest = [[NSFetchRequest alloc] init];
    NSEntityDescription *entity = [NSEntityDescription entityForName:@"Editions" inManagedObjectContext:self.managedObjectContext];
    [fetchRequest setEntity:entity];
    
    
    
    NSUserDefaults *defaults = [NSUserDefaults standardUserDefaults];
    int tousAfter = [self GetRecentsDurantNbJour:[[defaults objectForKey:@"tousAfter"] intValue]];
    
    if (tousAfter != 0) {
        NSDateComponents* componentsToSubtract = [[NSDateComponents alloc] init];
        [componentsToSubtract setDay:0-tousAfter];
        
        NSDate *recentDate = [[NSCalendar currentCalendar] dateByAddingComponents:componentsToSubtract toDate:lastDate options:0];
        NSLog(@"recentDate = %@",recentDate);
        
        NSPredicate *predicate = [NSPredicate predicateWithFormat:@"downloaddate >= %@ OR openDate == nil", recentDate];
        [fetchRequest setPredicate:predicate];
    }
    
    NSSortDescriptor *sortDescriptor = [[NSSortDescriptor alloc] initWithKey:@"publicationdate" ascending:NO];
    NSArray *sortDescriptors = [[NSArray alloc] initWithObjects:sortDescriptor, nil];
    [fetchRequest setSortDescriptors:sortDescriptors];
    
    
    
    
    
    NSError *error;
    NSArray *items = [managedObjectContext executeFetchRequest:fetchRequest error:&error];
    
    int x = 0;
    for (Editions *managedObject in items) {
        [editionsViewArray addObject:managedObject];
        if (managedObject.localpath == nil) {
            [downloadingEditionsViewArray addObject:[NSArray arrayWithObjects:[NSIndexPath indexPathForRow:x inSection:0], managedObject, nil]];
        }
        ++x;
    }
    
    
    
    
    [issuesCollectionView reloadData];
    [self performSelector:@selector(verifForDownloadIssues) withObject:nil afterDelay:1];
    
}

-(void)updateIssues {
    [UIApplication sharedApplication].networkActivityIndicatorVisible = NO;
    
    NSLog(@"updateIssues");
    [editionsViewArray removeAllObjects];
    [downloadingEditionsViewArray removeAllObjects];
    
    NSFetchRequest *fetchRequest = [[NSFetchRequest alloc] init];
    NSEntityDescription *entity = [NSEntityDescription entityForName:@"Editions" inManagedObjectContext:self.managedObjectContext];
    [fetchRequest setEntity:entity];
    
    NSSortDescriptor *sortDescriptor = [[NSSortDescriptor alloc] initWithKey:@"publicationdate" ascending:NO];
    NSArray *sortDescriptors = [[NSArray alloc] initWithObjects:sortDescriptor, nil];
    [fetchRequest setSortDescriptors:sortDescriptors];
    
    NSDateComponents *componentsToSubtract = [[NSDateComponents alloc] init];
    [componentsToSubtract setDay:-2];
    
    NSDate *yesterday = [[NSCalendar currentCalendar] dateByAddingComponents:componentsToSubtract toDate:[NSDate date] options:0];
    
    NSPredicate *predicate = [NSPredicate predicateWithFormat:@"openDate >= %@ OR openDate == nil", yesterday];
    [fetchRequest setPredicate:predicate];
    
    NSError *error;
    NSArray *items = [managedObjectContext executeFetchRequest:fetchRequest error:&error];
    
    int x = 0;
    for (Editions *managedObject in items) {
        [editionsViewArray addObject:managedObject];
        if (managedObject.localpath == nil) {
            [downloadingEditionsViewArray addObject:[NSArray arrayWithObjects:[NSIndexPath indexPathForRow:x inSection:0], managedObject, nil]];
        }
        ++x;
    }
    
    
    [issuesCollectionView reloadData];
    [self performSelector:@selector(verifForDownloadIssues) withObject:nil afterDelay:1];
    //[self verifForDownloadIssues];
    
}

-(void)getAll {
    [UIApplication sharedApplication].networkActivityIndicatorVisible = NO;
    
    NSLog(@"updateIssues");
    [editionsViewArray removeAllObjects];
    [downloadingEditionsViewArray removeAllObjects];
    
    NSFetchRequest *fetchRequest = [[NSFetchRequest alloc] init];
    NSEntityDescription *entity = [NSEntityDescription entityForName:@"Editions" inManagedObjectContext:self.managedObjectContext];
    [fetchRequest setEntity:entity];
    
    NSSortDescriptor *sortDescriptor = [[NSSortDescriptor alloc] initWithKey:@"publicationdate" ascending:NO];
    NSArray *sortDescriptors = [[NSArray alloc] initWithObjects:sortDescriptor, nil];
    [fetchRequest setSortDescriptors:sortDescriptors];
    
    NSError *error;
    NSArray *items = [managedObjectContext executeFetchRequest:fetchRequest error:&error];
    
    int x = 0;
    for (Editions *managedObject in items) {
        [editionsViewArray addObject:managedObject];
        if (managedObject.localpath == nil) {
            [downloadingEditionsViewArray addObject:[NSArray arrayWithObjects:[NSIndexPath indexPathForRow:x inSection:0], managedObject, nil]];
        }
        ++x;
    }
    
    [issuesCollectionView reloadData];
    
    [self performSelector:@selector(verifForDownloadIssues) withObject:nil afterDelay:1];
    //[self verifForDownloadIssues];
    
}


-(void)getSubscription {
    
    [editionsViewArray removeAllObjects];
    [downloadingEditionsViewArray removeAllObjects];
    
    NSFetchRequest *fetchRequest = [[NSFetchRequest alloc] init];
    NSEntityDescription *entity = [NSEntityDescription entityForName:@"Editions" inManagedObjectContext:self.managedObjectContext];
    [fetchRequest setEntity:entity];
    
    NSSortDescriptor *sortDescriptor = [[NSSortDescriptor alloc] initWithKey:@"publicationdate" ascending:NO];
    NSArray *sortDescriptors = [[NSArray alloc] initWithObjects:sortDescriptor, nil];
    [fetchRequest setSortDescriptors:sortDescriptors];
    
    NSPredicate *predicate = [NSPredicate predicateWithFormat:@"isSubscription == 1"];
    [fetchRequest setPredicate:predicate];
    
    NSError *error;
    NSArray *items = [managedObjectContext executeFetchRequest:fetchRequest error:&error];
    
    int x = 0;
    for (Editions *managedObject in items) {
        [editionsViewArray addObject:managedObject];
        if (managedObject.localpath == nil) {
            [downloadingEditionsViewArray addObject:[NSArray arrayWithObjects:[NSIndexPath indexPathForRow:x inSection:0], managedObject, nil]];
        }
        ++x;
    }
    
    [issuesCollectionView reloadData];
    
    [self performSelector:@selector(verifForDownloadIssues) withObject:nil afterDelay:1];
    
}

-(void)getFavoris {
    
    [editionsViewArray removeAllObjects];
    [downloadingEditionsViewArray removeAllObjects];
    
    NSFetchRequest *fetchRequest = [[NSFetchRequest alloc] init];
    NSEntityDescription *entity = [NSEntityDescription entityForName:@"Editions" inManagedObjectContext:self.managedObjectContext];
    [fetchRequest setEntity:entity];
    
    NSSortDescriptor *sortDescriptor = [[NSSortDescriptor alloc] initWithKey:@"publicationdate" ascending:NO];
    NSArray *sortDescriptors = [[NSArray alloc] initWithObjects:sortDescriptor, nil];
    [fetchRequest setSortDescriptors:sortDescriptors];
    
    NSPredicate *predicate = [NSPredicate predicateWithFormat:@"favoris == 1"];
    [fetchRequest setPredicate:predicate];
    
    NSError *error;
    NSArray *items = [managedObjectContext executeFetchRequest:fetchRequest error:&error];
    
    int x = 0;
    for (Editions *managedObject in items) {
        [editionsViewArray addObject:managedObject];
        if (managedObject.localpath == nil) {
            [downloadingEditionsViewArray addObject:[NSArray arrayWithObjects:[NSIndexPath indexPathForRow:x inSection:0], managedObject, nil]];
        }
        ++x;
    }
    
    [issuesCollectionView reloadData];
    
    [self performSelector:@selector(verifForDownloadIssues) withObject:nil afterDelay:1];
    
}

-(void)verifForDownloadIssues {
    NSLog(@"total queue début = %d",self.operationQueue.operationCount);
    if ([downloadingEditionsViewArray count] > 0) {
        if ([operationQueue operationCount] != [downloadingEditionsViewArray count]) {
            
            for (NSArray *tempArray in downloadingEditionsViewArray) {
                
                NSIndexPath *indexPath = [tempArray objectAtIndex:0];
                Editions *tempEdition = [tempArray objectAtIndex:1];
                
                BOOL trouve = NO;
                //NSArray *tempOperationArray = self.operationQueue.operations;
                for(NSOperation *op in [self.operationQueue operations]) {
                    DownloadOperation *downloadOp = (DownloadOperation*)op;
                    if ([downloadOp.edition.id intValue] == [tempEdition.id intValue]) {
                        trouve = YES;
                        [downloadOp setIndexPath:indexPath];
                    }
                }
                
                if (!trouve) {
                    DownloadOperation *tempOperation = [[DownloadOperation alloc] initWithEdition:tempEdition AtIndexPath:indexPath];
                    [tempOperation setDelegate:self];
                    [self.operationQueue addOperation:tempOperation];
                }
                
                
            }
            
        }
    }
    NSLog(@"total queue fin = %d",self.operationQueue.operationCount);
}

-(void)downloadProgress:(float)progression AtIndexPath:(NSIndexPath *)indexPath {
    EditionView *cell = (EditionView*)[issuesCollectionView cellForItemAtIndexPath:indexPath];
    [cell performSelectorOnMainThread:@selector(setProgression:) withObject:[NSNumber numberWithFloat:progression] waitUntilDone:YES];
}

-(void)downloadCompleteAtIndexPath:(NSIndexPath *)indexPath {
    [self performSelector:@selector(animationDownloadCompleteAtIndexPath:) withObject:indexPath afterDelay:1.5];
    
}

-(void)animationDownloadCompleteAtIndexPath:(NSIndexPath *)indexPath {
    //EditionView *cell = (EditionView*)[issuesCollectionView cellForItemAtIndexPath:indexPath];
    //[cell performSelectorOnMainThread:@selector(setDownloading:) withObject:@NO waitUntilDone:YES];
    
    [self performSelectorOnMainThread:@selector(reloadCollectionView:) withObject:nil waitUntilDone:YES];
}

/*
-(void)issuesDownloadVerification {
    downloadCount = 0;
    downloadTotal = 0;
    
    NSFetchRequest *fetchRequest = [[NSFetchRequest alloc] init];
    NSEntityDescription *entity = [NSEntityDescription entityForName:@"Editions" inManagedObjectContext:self.managedObjectContext];
    [fetchRequest setEntity:entity];
    
    NSError *error;
    NSArray *items = [managedObjectContext executeFetchRequest:fetchRequest error:&error];
    NSLog(@"error = %@",error);
    
    for (Editions *managedObject in items) {
        if (managedObject.localpath == nil) {
            ++downloadTotal;
            DownloadOperation *tempOperation = [[DownloadOperation alloc] initWithEdition:managedObject];
            [tempOperation setDelegate:self];
            [self.operationQueue addOperation:tempOperation];
        }
    }
    if (downloadTotal != 0) {
        [[self countLabel] setText:[NSString stringWithFormat:@"%d de %d",downloadCount, downloadTotal]];
        [[self progressView] setProgress:((float)downloadCount/(float)downloadTotal)];
        [[self backgroundProgressImageView] setHidden:NO];
        [[self countLabel] setHidden:NO];
        [[self progressView] setHidden:NO];
    }
    
}

-(void)downloadComplete {
    ++downloadCount;
    [[self countLabel] setText:[NSString stringWithFormat:@"%d de %d",downloadCount, downloadTotal]];
    [[self progressView] setProgress:((float)downloadCount/(float)downloadTotal)];
    if (downloadCount == downloadTotal) {
        [self performSelector:@selector(hideProgressView) withObject:nil afterDelay:1];
    }
    [self updateIssues];
}

-(void)hideProgressView {
    [[self backgroundProgressImageView] setHidden:YES];
    [[self countLabel] setHidden:YES];
    [[self progressView] setHidden:YES];
}
*/

#pragma mark - CoreData multi thread Merging

- (void)_mocDidSaveNotification:(NSNotification *)notification {
    
    NSManagedObjectContext *savedContext = [notification object];
    
    // ignore change notifications for the main MOC
    if (self.managedObjectContext == savedContext)
    {
        return;
    }
    
    if (managedObjectContext.persistentStoreCoordinator != savedContext.persistentStoreCoordinator)
    {
        // that's another database
        NSLog(@"error possible = persistentStoreCoordinator a pas le meme path pour le merge");
        return;
    }
    
    //dispatch_sync(dispatch_get_main_queue(), ^{
        //[self.managedObjectContext mergeChangesFromContextDidSaveNotification:notification];
    [[self managedObjectContext] performSelectorOnMainThread:@selector(mergeChangesFromContextDidSaveNotification:) withObject:notification waitUntilDone:YES];
    //});
}

#pragma mark - UICollectionViewController
-(void)changeNumberOfColumns:(int)nb {
    //[self.issueAlbumLayout setNumberOfColumns:nb];
}
-(NSInteger)numberOfSectionsInCollectionView:(UICollectionView *)collectionView {
    return 1;
}
-(NSInteger)collectionView:(UICollectionView *)collectionView numberOfItemsInSection:(NSInteger)section {
    return [editionsViewArray count];
}
-(UICollectionViewCell *)collectionView:(UICollectionView *)collectionView cellForItemAtIndexPath:(NSIndexPath *)indexPath {
    //static NSString *identifier = @"issueCell";
    EditionView *cell = (EditionView*)[collectionView dequeueReusableCellWithReuseIdentifier:issueCellIdentifier forIndexPath:indexPath];
    
    [cell setEditionInView:[editionsViewArray objectAtIndex:indexPath.row]];
    [cell setRefViewController:self];
    
    [cell setDownloading:([(Editions*)[editionsViewArray objectAtIndex:indexPath.row] localpath] == nil)];
    
    return cell;
}

-(void)pushReaderWithEdition:(NSNotification*)notification {
    NSLog(@"testasjhjkhdjksahdjkashdjkashdjkahdjkasjkdahsdajksdjk");
    Editions *refEdition = nil;
    
    NSMutableArray *array = [notification object];
    
    NSManagedObjectContext* managedObjectContext2 = [[NSManagedObjectContext alloc] init];
    [managedObjectContext2 setUndoManager:nil];
    [managedObjectContext2 setPersistentStoreCoordinator:[(AppDelegate*)[[UIApplication sharedApplication] delegate] persistentStoreCoordinator]];
    
    NSFetchRequest *request = [[NSFetchRequest alloc] init];
    [request setEntity:[NSEntityDescription entityForName:@"Editions" inManagedObjectContext:managedObjectContext2]];
    
    NSError *error = nil;
    
    NSPredicate *predicate = [NSPredicate predicateWithFormat:@"id == %d", [[array valueForKey:@"id"] intValue]];
    [request setPredicate:predicate];
    
    NSArray *results = [managedObjectContext executeFetchRequest:request error:&error];
    
    refEdition = [results objectAtIndex:0];
    
    
    
    if (refEdition != nil) {
        EditionView *cell = [[EditionView alloc] initWithFrame:CGRectZero];
        
        [cell setEditionInView:refEdition];
        [cell setRefViewController:self];
        
        [cell handleTap:nil];
    }
    
}

/*
-(void)collectionView:(UICollectionView *)collectionView didSelectItemAtIndexPath:(NSIndexPath *)indexPath {
    
    if([[_store issueAtIndex:indexPath.row] isIssueAvailableForRead]) {
        [self readIssue:[[[_store issueAtIndex:indexPath.row] contentURL] path] WithTitle:[[_store issueAtIndex:indexPath.row] title]];
        //[self readIssue:[_store issueAtIndex:indexPath.row] forIndice:indexPath.row];
    }
    else {
        [self downloadIssueAtIndex:indexPath.row];
    }
    
}
*/

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


-(int)GetRecentsDurantNbJour:(int)deleteAfter {
    switch (deleteAfter) {
        case 0:
            return 7;
            break;
        case 1:
            return 15;
            break;
        case 2:
            return 30;
            break;
        case 3:
            return 0;
            break;

        default:
            return 0;
            break;
    }
}

@end
