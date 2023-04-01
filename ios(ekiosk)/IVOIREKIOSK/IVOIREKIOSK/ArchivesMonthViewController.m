//
//  ArchivesMonthViewController.m
//  IVOIREKIOSK
//
//  Created by Maxime Julien-Paquet on 2014-01-15.
//  Copyright (c) 2014 Maxime Julien-Paquet. All rights reserved.
//

#import "ArchivesMonthViewController.h"
#import "EditionsStoreView.h"
#import "AppDelegate.h"
#import "Editions.h"


static NSString * const archivesCellIdentifier = @"archivesCell";

@interface ArchivesMonthViewController ()

@end

@implementation ArchivesMonthViewController

@synthesize monthCollectionView, dataArray, idJournalString, dateString, tempCoverAnimationView, tempDictionary, editionEntityDescription, insertionContext, loadingAnimation;

-(id)initWithNibName:(NSString *)nibNameOrNil bundle:(NSBundle *)nibBundleOrNil {
    self = [super initWithNibName:nibNameOrNil bundle:nibBundleOrNil];
    if (self) {
        // Custom initialization
    }
    return self;
}

-(id)initWithIdJournal:(NSString*)idjournal  AndDate:(NSString *)date {
    self = [super init];
    if (self) {
        self.idJournalString = idjournal;
        self.dateString = date;
    }
    return self;
}

-(void)viewDidLoad {
    [super viewDidLoad];
	// Do any additional setup after loading the view.
    
    [[NSNotificationCenter defaultCenter] addObserver:self
                                             selector:@selector(ChangementDeStatusDuCompte:)
                                                 name:@"ChangementDeStatusDuCompte"
                                               object:nil];
    
    UICollectionViewFlowLayout *collectionViewLayout = [[UICollectionViewFlowLayout alloc] init];
    if (isPad()) {
        collectionViewLayout.sectionInset = UIEdgeInsetsMake(20, 40, 20, 40);
        collectionViewLayout.minimumLineSpacing = 20;
        collectionViewLayout.itemSize = CGSizeMake(130.0f, 210.0f);
    }
    else {
        collectionViewLayout.sectionInset = UIEdgeInsetsMake(10, 20, 10, 20);
        collectionViewLayout.minimumLineSpacing = 20;
        collectionViewLayout.itemSize = CGSizeMake(130.0f*0.6f, 210.0f*0.6f);
    }
    
    
    
    monthCollectionView = [[UICollectionView alloc]initWithFrame:self.view.bounds collectionViewLayout:collectionViewLayout];
    monthCollectionView.autoresizingMask = UIViewAutoresizingFlexibleWidth | UIViewAutoresizingFlexibleHeight;
    
    monthCollectionView.contentInset = UIEdgeInsetsMake(108, 0, 0, 0);
    monthCollectionView.backgroundColor = [UIColor clearColor];
    monthCollectionView.delegate = self;
    monthCollectionView.dataSource = self;
    [monthCollectionView registerClass:[EditionsStoreView class] forCellWithReuseIdentifier:archivesCellIdentifier];
    
    [self.view addSubview:monthCollectionView];
    
    [self.view addSubview:[self loadingAnimation]];
    [self getDataFromServeur];
    
}

-(void)didReceiveMemoryWarning {
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
        loadingAnimation.center = monthCollectionView.center;
        loadingAnimation.color = [UIColor blackColor];
        loadingAnimation.hidesWhenStopped = YES;
    }
    return loadingAnimation;
}

-(void)ChangementDeStatusDuCompte:(NSNotification*)notif {
    
    [dataArray removeAllObjects];
    [monthCollectionView performSelectorOnMainThread:@selector(reloadData) withObject:nil waitUntilDone:YES];
    
    [self getDataFromServeur];
    
}

-(void)getDataFromServeur {
    [self.loadingAnimation startAnimating];
    NSUserDefaults *defaults = [NSUserDefaults standardUserDefaults];
    
    // self.idJournalString
    // self.dateString
    
    NSURL *url = [NSURL URLWithString:[NSString stringWithFormat:@"%@/getPublicationsArchive.php?id=%@&month=%@&username=%@&password=%@",kAppBaseURL, self.idJournalString, self.dateString, [defaults objectForKey:@"username"], [defaults objectForKey:@"password"]]];
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
                UIAlertView *alert = [[UIAlertView alloc] initWithTitle:@"Erreur" message:dataString delegate:nil cancelButtonTitle:@"Retour" otherButtonTitles:nil];
                [alert show];
                [self.loadingAnimation stopAnimating];
                return;
            }
            
            if ([[publicTimeline valueForKey:@"resultat"] isEqualToString:@"true"]) {
                [self.loadingAnimation stopAnimating];
                [UIApplication sharedApplication].networkActivityIndicatorVisible = NO;
                
                
                
                [self setDataArray:[publicTimeline valueForKey:@"data"]];
                if ([[publicTimeline valueForKey:@"data"] count] == 0) {
                    NSLog(@"count == 0");
                    
                    [self performSelectorOnMainThread:@selector(addNoPublicationsLabel) withObject:nil waitUntilDone:YES];
                }
                else {
                    [self.monthCollectionView performSelectorOnMainThread:@selector(reloadData) withObject:nil waitUntilDone:YES];
                }
                
            }
            else {
                [[[UIAlertView alloc] initWithTitle:@"Erreur" message:[publicTimeline valueForKey:@"data"] delegate:nil cancelButtonTitle:@"Retour" otherButtonTitles:nil] show];
                [self.loadingAnimation stopAnimating];
            }
        }
    }];
}

#pragma mark - UICollectionViewController

-(NSInteger)numberOfSectionsInCollectionView:(UICollectionView *)collectionView {
    return 1;
}
-(NSInteger)collectionView:(UICollectionView *)collectionView numberOfItemsInSection:(NSInteger)section {
    return [dataArray count];
}
-(UICollectionViewCell *)collectionView:(UICollectionView *)collectionView cellForItemAtIndexPath:(NSIndexPath *)indexPath {
    //static NSString *identifier = @"issueCell";
    EditionsStoreView *cell = (EditionsStoreView*)[collectionView dequeueReusableCellWithReuseIdentifier:archivesCellIdentifier forIndexPath:indexPath];
    
    [cell setArchivesData:[dataArray objectAtIndex:indexPath.row]];
    
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
    [vc setDataDictionary:[dataArray objectAtIndex:indexPath.row]];
    //[vc removeAbonnementButton];
    [self.navigationController pushViewController:vc animated:YES];
}

#pragma mark - OperationThread Delegate
-(void)addNoPublicationsLabel {
    UILabel *tempLabel;
    if (isPad()) {
        tempLabel = [[UILabel alloc] initWithFrame:CGRectMake(0, 140, self.view.frame.size.width, 40)];
        tempLabel.font = [UIFont fontWithName:@"Helvetica-Bold" size:30];
    }
    else {
        tempLabel = [[UILabel alloc] initWithFrame:CGRectMake(20, 104, self.view.frame.size.width - 40, 80)];
        tempLabel.font = [UIFont fontWithName:@"Helvetica-Bold" size:18];
        tempLabel.numberOfLines = 2;
    }
    
    
    tempLabel.autoresizingMask = UIViewAutoresizingFlexibleWidth | UIViewAutoresizingFlexibleBottomMargin;
    tempLabel.textAlignment = NSTextAlignmentCenter;
    tempLabel.textColor = [UIColor colorWithWhite:0.3 alpha:1];
    
    tempLabel.text = @"Aucunes publications trouvées pour ce mois";
    [self.view addSubview:tempLabel];
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
    [self dismissViewControllerAnimated:YES completion:^{
        [[NSNotificationCenter defaultCenter] postNotificationName:@"PushReaderWithEdition" object:data];
    }];
}

#pragma mark - ajouter journal dans le coredata
-(NSManagedObjectContext *)insertionContext {
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

-(void)insertEditionInCoreData:(NSNumber*)delay {
    
    Editions *currentEdition = [[Editions alloc] initWithEntity:self.editionEntityDescription
                                 insertIntoManagedObjectContext:self.insertionContext];
    
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
    
    FT_SAVE_MOC([self insertionContext])
    
    [[NSNotificationCenter defaultCenter] postNotificationName:@"ReloadNouveauxCollectionView" object:nil];
    
    [self performSelectorOnMainThread:@selector(coverAnimation:) withObject:delay waitUntilDone:NO];
}

-(void)coverAnimation:(NSNumber*)delay {
    
    [self.view addSubview:tempCoverAnimationView];
    
    float dure = 0.2;
    dure = dure + (round(tempCoverAnimationView.frame.origin.y / 200)/10);
    
    [UIView beginAnimations:nil context:nil];
    [UIView setAnimationDuration:dure];
    [UIView setAnimationDelay:[delay floatValue]];
    [UIView setAnimationCurve:UIViewAnimationCurveEaseOut];
    [UIView setAnimationDelegate:self];
    
    tempCoverAnimationView.frame = CGRectMake(20, 40, 40, 80);
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
    //[self dismissViewControllerAnimated:YES completion:nil];
}

@end
