//
//  AbonnementConfirmationViewController.m
//  IVOIREKIOSK
//
//  Created by Maxime Julien-Paquet on 2014-01-13.
//  Copyright (c) 2014 Maxime Julien-Paquet. All rights reserved.
//

#import "AbonnementConfirmationViewController.h"
#import "JournalPickerHeaderViewCell.h"
#import "JournalConfirmationCell.h"
//#import "AbonnementIAPHelper.h"
#import "TestIAPHelper.h"

static NSString * const JournalViewLayoutConfirmationCellKind = @"journalConfirmationViewCell";
static NSString * const JournalViewHeaderLayoutKind = @"journalPickerHeaderViewCell";

@interface AbonnementConfirmationViewController () {
    NSArray *_products;
}

@end

@implementation AbonnementConfirmationViewController

@synthesize abonnementLabel, dataArray, delegate, segmentedControl, escompteLabel, packageArray, backgroundImageView, mainCollectionView, journauxArray;

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
    
    [self.abonnementLabel setText:[self.packageArray valueForKey:@"nom"]];
    
    
    [self.segmentedControl setTitle:[NSString stringWithFormat:@"%@$ USD / 1 mois",[[self.packageArray valueForKey:@"prix_1"] valueForKey:@"prix"]] forSegmentAtIndex:0];
    
    [self.segmentedControl setTitle:[NSString stringWithFormat:@"%@$ USD / 3 mois",[[self.packageArray valueForKey:@"prix_3"] valueForKey:@"prix"]] forSegmentAtIndex:1];
    
    float escompte = (([[[self.packageArray valueForKey:@"prix_1"] valueForKey:@"prix"] floatValue] * 12) - ([[[self.packageArray valueForKey:@"prix_3"] valueForKey:@"prix"] floatValue] * 4));
    [self.escompteLabel setText:[NSString stringWithFormat:@"Vous économisez %.2f$ par année",escompte]];
    
    self.backgroundImageView.layer.shadowColor = [UIColor blackColor].CGColor;
    self.backgroundImageView.layer.shadowOpacity = 0.5;
    self.backgroundImageView.layer.shadowRadius = 2;
    self.backgroundImageView.layer.shadowOffset = CGSizeMake(2.0f, 2.0f);
    
    
    
    
    
    self.journauxArray = [[NSMutableArray alloc] init];
    
    for (int x = 0; x < [self.dataArray count]; ++x) {
        
        NSMutableArray *tempArray = [[NSMutableArray alloc] init];
        
        for (int y = 0; y < [[[self.dataArray objectAtIndex:x] valueForKey:@"journaux"] count]; ++y) {
            
            if ([[[[[self.dataArray objectAtIndex:x] valueForKey:@"journaux"] objectAtIndex:y] valueForKey:@"selected"] intValue] == 1) {
                [tempArray addObject:[[[self.dataArray objectAtIndex:x] valueForKey:@"journaux"] objectAtIndex:y]];
            }
        }
        
        [self.journauxArray addObject:[NSDictionary dictionaryWithObjectsAndKeys:
                                       [[self.dataArray objectAtIndex:x] valueForKey:@"title"], @"title",
                                       tempArray, @"journaux",
                                       nil]];
    }
    
    UICollectionViewFlowLayout *collectionViewLayout = [[UICollectionViewFlowLayout alloc] init];
    collectionViewLayout.sectionInset = UIEdgeInsetsMake(20, 40, 20, 40);
    collectionViewLayout.minimumLineSpacing = 20;
    collectionViewLayout.itemSize = CGSizeMake(120.0f, 75.0f);
    collectionViewLayout.headerReferenceSize = CGSizeMake(540, 50);
    
    
    mainCollectionView = [[UICollectionView alloc] initWithFrame:self.view.bounds collectionViewLayout:collectionViewLayout];
    mainCollectionView.autoresizingMask = UIViewAutoresizingFlexibleWidth | UIViewAutoresizingFlexibleHeight;
    mainCollectionView.contentInset = UIEdgeInsetsMake(222, 0, 0, 0);
    mainCollectionView.backgroundColor = [UIColor clearColor];
    mainCollectionView.delegate = self;
    mainCollectionView.dataSource = self;
    
    [mainCollectionView registerClass:[JournalConfirmationCell class]
       forCellWithReuseIdentifier:JournalViewLayoutConfirmationCellKind];
    
    [mainCollectionView registerClass:[JournalPickerHeaderViewCell class]
       forSupplementaryViewOfKind:UICollectionElementKindSectionHeader
              withReuseIdentifier:JournalViewHeaderLayoutKind];
    
    [self.view addSubview:mainCollectionView];
    [self.view sendSubviewToBack:mainCollectionView];
    
    
}

-(void)didReceiveMemoryWarning {
    [super didReceiveMemoryWarning];
    // Dispose of any resources that can be recreated.
}

-(void)modifierButton:(id)sender {
    [self dismissViewControllerAnimated:YES completion:nil];
}
-(void)confirmerButton:(id)sender {
    
    [self testLoadProduct];
    
}

-(void)segmentedChange:(id)sender {
    UISegmentedControl *segmented = (UISegmentedControl*)sender;
    
    if ([segmented selectedSegmentIndex] == 1) {
        [self.escompteLabel setHidden:NO];
    }
    else {
        [self.escompteLabel setHidden:YES];
    }
    
}

#pragma mark - UICollectionViewController
-(NSInteger)numberOfSectionsInCollectionView:(UICollectionView *)collectionView {
    return [journauxArray count];
}
-(NSInteger)collectionView:(UICollectionView *)collectionView numberOfItemsInSection:(NSInteger)section {
    return [[[journauxArray objectAtIndex:section] valueForKey:@"journaux"] count];
}
-(UICollectionViewCell *)collectionView:(UICollectionView *)collectionView cellForItemAtIndexPath:(NSIndexPath *)indexPath {
    JournalConfirmationCell *cell = (JournalConfirmationCell*)[collectionView dequeueReusableCellWithReuseIdentifier:JournalViewLayoutConfirmationCellKind forIndexPath:indexPath];
    
    [cell setDataInView:[[[journauxArray objectAtIndex:indexPath.section] valueForKey:@"journaux"] objectAtIndex:indexPath.row]];
    
    return cell;
}
-(UICollectionReusableView *)collectionView:(UICollectionView *)collectionView viewForSupplementaryElementOfKind:(NSString *)kind atIndexPath:(NSIndexPath *)indexPath {
    
    UICollectionReusableView *reusableview = nil;
    
    if (kind == UICollectionElementKindSectionHeader) {
        JournalPickerHeaderViewCell *headerView = [collectionView dequeueReusableSupplementaryViewOfKind:UICollectionElementKindSectionHeader withReuseIdentifier:JournalViewHeaderLayoutKind forIndexPath:indexPath];
        headerView.indexPathLocal = indexPath;
        headerView.titleLabel.text = [[dataArray objectAtIndex:indexPath.section] valueForKey:@"title"];
        
        
        reusableview = headerView;
    }
    
    return reusableview;
}

#pragma mark - IAP
-(void)testLoadProduct {
    _products = nil;
    
    NSString *iapString = @"";
    if ([segmentedControl selectedSegmentIndex] == 1) {
        iapString = [[self.packageArray valueForKey:@"prix_3"] valueForKey:@"itunes"];
    }
    else {
        iapString = [[self.packageArray valueForKey:@"prix_1"] valueForKey:@"itunes"];
    }
    
    [[TestIAPHelper sharedInstance] requestProductsWithCompletionHandler:^(BOOL success, NSArray *products) {
        if (success) {
            _products = products;
            [self productLoaded:iapString];
        }
        else {
            [[[UIAlertView alloc] initWithTitle:@"Erreur" message:@"Erreur de communication entre votre appareil et itunes." delegate:nil cancelButtonTitle:@"Retour" otherButtonTitles:nil] show];
        }
    } ForProductIdentifier:[NSSet setWithObjects:iapString, nil]];
    
}

-(void)productLoaded:(NSString*)iapString {
    
    NSMutableArray *tempArray = [[NSMutableArray alloc] init];
    for (NSArray *tempDataArray in [dataArray valueForKey:@"journaux"]) {
        for (NSDictionary *tempDic in tempDataArray) {
            //NSLog(@"tempDic = %@", tempDic);
            
            if ([[tempDic valueForKey:@"selected"] intValue] == 1) {
                [tempArray addObject:tempDic];
            }
            
            
        }
    }
    
    
    NSUserDefaults *defaults = [NSUserDefaults standardUserDefaults];
    
    NSLog(@"test = %@",[NSMutableArray arrayWithObjects:[defaults valueForKey:@"username"], [defaults valueForKey:@"password"], tempArray, packageArray, nil]);
    
    SKProduct *product = _products[0];
    [[NSNotificationCenter defaultCenter] addObserver:self selector:@selector(productComplete:) name:IAPHelperProductPurchasedAbonnementNotification object:nil];
    //[[AbonnementIAPHelper sharedInstanceWithProductId:iapString] buyProduct:product WithData:[NSMutableArray arrayWithObjects:[defaults valueForKey:@"username"], [defaults valueForKey:@"password"], tempArray, packageArray, nil]];
    
    [[TestIAPHelper sharedInstance] buyProduct:product WithData:[NSMutableArray arrayWithObjects:[defaults valueForKey:@"username"], [defaults valueForKey:@"password"], tempArray, packageArray, nil]];
    
}

-(void)productComplete:(NSNotification*)notif {
    [[NSNotificationCenter defaultCenter] removeObserver:self name:IAPHelperProductPurchasedAbonnementNotification object:nil];
    NSLog(@"-(void)productComplete:(NSNotification*)notif = %@", notif.object);
    if (notif.object) {
        [[NSNotificationCenter defaultCenter] postNotificationName:@"ChangementDeStatusDuCompte" object:nil];
        [self performSelectorOnMainThread:@selector(backOnMainThread) withObject:nil waitUntilDone:NO];
    }
    else {
        [[[UIAlertView alloc] initWithTitle:@"Erreur" message:@"Une erreur s'est produit lors de votre achat." delegate:nil cancelButtonTitle:@"Retour" otherButtonTitles:nil] show];
    }
    
}

-(void)backOnMainThread {
    [self dismissViewControllerAnimated:YES completion:^{
        if (delegate && [delegate respondsToSelector:@selector(didDismissAbonnementConfirmationViewController)]) {
            [delegate didDismissAbonnementConfirmationViewController];
        }
    }];
}

@end
