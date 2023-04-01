//
//  ArchivesJournauxViewController.m
//  IVOIREKIOSK
//
//  Created by Maxime Julien-Paquet on 2014-01-17.
//  Copyright (c) 2014 Maxime Julien-Paquet. All rights reserved.
//

#import "ArchivesJournauxViewController.h"
#import "JournalPickerHeaderViewCell.h"
#import "JournalPickerViewCell.h"
#import "GTMHTTPFetcher.h"
#import "ArchivesViewController.h"

static NSString * const JournalViewLayoutJournalCellKind = @"journalViewCell";
static NSString * const JournalViewHeaderLayoutKind = @"journalHeaderViewCell";

@interface ArchivesJournauxViewController ()

@end

@implementation ArchivesJournauxViewController

@synthesize journauxCollectionView, dataArray, loadingAnimation;

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
    
    
    UICollectionViewFlowLayout *collectionViewLayout = [[UICollectionViewFlowLayout alloc] init];
    if (isPad()) {
        collectionViewLayout.sectionInset = UIEdgeInsetsMake(20, 40, 20, 40);
        collectionViewLayout.minimumLineSpacing = 20;
        collectionViewLayout.itemSize = CGSizeMake(200.0f, 180.0f);
        collectionViewLayout.headerReferenceSize = CGSizeMake(self.view.frame.size.width, 50);
    }
    else {
        collectionViewLayout.sectionInset = UIEdgeInsetsMake(20, 10, 20, 10);
        collectionViewLayout.minimumLineSpacing = 20;
        collectionViewLayout.itemSize = CGSizeMake(200.0f*0.7f, 180.0f*0.7f);
        collectionViewLayout.headerReferenceSize = CGSizeMake(self.view.frame.size.width, 30);
    }
    
    
    journauxCollectionView = [[UICollectionView alloc]initWithFrame:self.view.bounds collectionViewLayout:collectionViewLayout];
    journauxCollectionView.autoresizingMask = UIViewAutoresizingFlexibleWidth | UIViewAutoresizingFlexibleHeight;
    
    //journauxCollectionView.contentInset = UIEdgeInsetsMake(64, 0, 0, 0);
    journauxCollectionView.backgroundColor = [UIColor clearColor];
    journauxCollectionView.delegate = self;
    journauxCollectionView.dataSource = self;
    [journauxCollectionView registerClass:[JournalPickerViewCell class] forCellWithReuseIdentifier:JournalViewLayoutJournalCellKind];
    
    [journauxCollectionView registerClass:[JournalPickerHeaderViewCell class] forSupplementaryViewOfKind:UICollectionElementKindSectionHeader withReuseIdentifier:JournalViewHeaderLayoutKind];
    
    [self.view addSubview:journauxCollectionView];
    
    [self.view addSubview:[self loadingAnimation]];
    
    [self getDataFromServeur];
}

- (void)didReceiveMemoryWarning {
    [super didReceiveMemoryWarning];
    // Dispose of any resources that can be recreated.
}

-(UIActivityIndicatorView *)loadingAnimation {
    if (loadingAnimation == nil) {
        loadingAnimation = [[UIActivityIndicatorView alloc] initWithActivityIndicatorStyle:UIActivityIndicatorViewStyleWhiteLarge];
        loadingAnimation.autoresizingMask = UIViewAutoresizingFlexibleBottomMargin | UIViewAutoresizingFlexibleTopMargin | UIViewAutoresizingFlexibleLeftMargin | UIViewAutoresizingFlexibleRightMargin;
        loadingAnimation.frame = CGRectMake(0, 0, 40, 40);
        loadingAnimation.center = journauxCollectionView.center;
        loadingAnimation.color = [UIColor blackColor];
        loadingAnimation.hidesWhenStopped = YES;
    }
    return loadingAnimation;
}

#pragma maek - Life Cycle View

-(void)viewWillAppear:(BOOL)animated {
    [super viewWillAppear:animated];
    
    [[NSNotificationCenter defaultCenter] addObserver:self
                                             selector:@selector(ChangementDeStatusDuCompte:)
                                                 name:@"ChangementDeStatusDuCompte"
                                               object:nil];
    
}

-(void)viewWillDisappear:(BOOL)animated {
    [super viewWillDisappear:animated];
    [[NSNotificationCenter defaultCenter] removeObserver:self
                                                    name:@"ChangementDeStatusDuCompte"
                                                  object:nil];
}

-(void)ChangementDeStatusDuCompte:(NSNotification*)notif {
    
    [dataArray removeAllObjects];
    [journauxCollectionView performSelectorOnMainThread:@selector(reloadData) withObject:nil waitUntilDone:YES];
    
    [self getDataFromServeur];
    
}

#pragma mark - UICollectionViewController

-(NSInteger)numberOfSectionsInCollectionView:(UICollectionView *)collectionView {
    return [dataArray count];
}
-(NSInteger)collectionView:(UICollectionView *)collectionView numberOfItemsInSection:(NSInteger)section
{
    return [[[dataArray objectAtIndex:section] valueForKey:@"journaux"] count];

}
-(UICollectionViewCell *)collectionView:(UICollectionView *)collectionView cellForItemAtIndexPath:(NSIndexPath *)indexPath {
    JournalPickerViewCell *cell = (JournalPickerViewCell*)[collectionView dequeueReusableCellWithReuseIdentifier:JournalViewLayoutJournalCellKind forIndexPath:indexPath];
    
    [cell setArchivesDataInView:[[[dataArray objectAtIndex:indexPath.section] valueForKey:@"journaux"] objectAtIndex:indexPath.row]];
    
    return cell;
}
-(UICollectionReusableView *)collectionView:(UICollectionView *)collectionView viewForSupplementaryElementOfKind:(NSString *)kind atIndexPath:(NSIndexPath *)indexPath {
    
    UICollectionReusableView *reusableview = nil;
    
    if (kind == UICollectionElementKindSectionHeader) {
        JournalPickerHeaderViewCell *headerView = [collectionView dequeueReusableSupplementaryViewOfKind:UICollectionElementKindSectionHeader withReuseIdentifier:JournalViewHeaderLayoutKind forIndexPath:indexPath];
        headerView.indexPathLocal = indexPath;
        headerView.titleLabel.text = [[dataArray objectAtIndex:indexPath.section] valueForKey:@"categorie"];
        /*if (isPad())
        {
            headerView.subscriptionSwitch.tag = indexPath.section;
            [headerView.subscriptionSwitch addTarget:self action:@selector(switchValueChanged:) forControlEvents:UIControlEventValueChanged];
        }
        */
        reusableview = headerView;
    }
    
    return reusableview;
}

-(void)switchValueChanged:(id)sender
{
    UISwitch * switcher = (UISwitch*)sender;
    [self.journauxCollectionView reloadSections:[NSIndexSet indexSetWithIndex:switcher.tag]];
    
}

-(void)collectionView:(UICollectionView *)collectionView didSelectItemAtIndexPath:(NSIndexPath *)indexPath {
    ArchivesViewController *archivesViewController = [[ArchivesViewController alloc]
                                                      initWithIdJournal:[[[[dataArray objectAtIndex:indexPath.section] valueForKey:@"journaux"] objectAtIndex:indexPath.row] valueForKey:@"id"]
                                                                AndName:[[[[dataArray objectAtIndex:indexPath.section] valueForKey:@"journaux"] objectAtIndex:indexPath.row] valueForKey:@"nom"]];
    
    //[archivesViewController setData:];
    
    [self.navigationController pushViewController:archivesViewController animated:YES];
}

#pragma mark - OperationThread Delegate

-(void)getDataFromServeur {
    [self.loadingAnimation startAnimating];
    NSUserDefaults *defaults = [NSUserDefaults standardUserDefaults];
    NSURL *url = [NSURL URLWithString:[NSString stringWithFormat:@"%@/getJournauxArchive.php?username=%@&password=%@",kAppBaseURL, [defaults objectForKey:@"username"], [defaults objectForKey:@"password"]]];
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
                [self.loadingAnimation stopAnimating];
                [UIApplication sharedApplication].networkActivityIndicatorVisible = NO;
                
                [self setDataArray:[publicTimeline valueForKey:@"data"]];
                [self.journauxCollectionView performSelectorOnMainThread:@selector(reloadData) withObject:nil waitUntilDone:YES];
            }
            else {
                [[[UIAlertView alloc] initWithTitle:@"Erreur" message:[publicTimeline valueForKey:@"data"] delegate:nil cancelButtonTitle:@"Retour" otherButtonTitles:nil] show];
                [self.loadingAnimation stopAnimating];
            }
        }
    }];
}

@end
