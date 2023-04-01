//
//  PackagesViewController.m
//  IVOIREKIOSK
//
//  Created by Maxime Julien-Paquet on 2014-01-10.
//  Copyright (c) 2014 Maxime Julien-Paquet. All rights reserved.
//

#import "PackagesViewController.h"
#import "AbonnementViewCell.h"
#import "AbonnementViewController.h"

@interface PackagesViewController () {
    NSArray *dataArray;
    
}

@end

@implementation PackagesViewController

@synthesize abonnementCollectionView, operationQueue;

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
    /*
    dataArray = @[
                  @{@"title": @"Premium",
                    @"prix": @"1000 F CFA",
                    @"quotidien": @"3",
                    @"hebdo": @"2",
                    @"mensuel": @"0",
                    @"total": @"5"
                    },
                  @{@"title": @"Premium +",
                    @"prix": @"1500 F CFA",
                    @"quotidien": @"7",
                    @"hebdo": @"2",
                    @"mensuel": @"1",
                    @"total": @"10"
                    },
                  @{@"title": @"Premium + +",
                    @"prix": @"2000 F CFA",
                    @"quotidien": @"7",
                    @"hebdo": @"6",
                    @"mensuel": @"2",
                    @"total": @"15"
                    }
                  ];
    */
    
    
    UICollectionViewFlowLayout *collectionViewLayout = [[UICollectionViewFlowLayout alloc] init];
    collectionViewLayout.sectionInset = UIEdgeInsetsMake(25, 15, 25, 15);
    collectionViewLayout.minimumLineSpacing = 25;
    collectionViewLayout.itemSize = CGSizeMake(487.0f, 260.0f);
    
    
    abonnementCollectionView = [[UICollectionView alloc]initWithFrame:self.view.bounds collectionViewLayout:collectionViewLayout];
    abonnementCollectionView.backgroundColor = [UIColor clearColor];
    abonnementCollectionView.opaque = NO;
    abonnementCollectionView.autoresizesSubviews = YES;
    abonnementCollectionView.autoresizingMask = UIViewAutoresizingFlexibleWidth | UIViewAutoresizingFlexibleHeight;
    
    [abonnementCollectionView registerClass:[AbonnementViewCell class] forCellWithReuseIdentifier:@"abonnementViewCell"];
    [abonnementCollectionView setDelegate:self];
    [abonnementCollectionView setDataSource:self];
    [self.view addSubview:abonnementCollectionView];
    
    GetPackagesOperation *getPackagesOperation = [[GetPackagesOperation alloc] init];
    getPackagesOperation.delegate = self;
    [UIApplication sharedApplication].networkActivityIndicatorVisible = YES;
    [self.operationQueue addOperation:getPackagesOperation];
    
}

- (void)didReceiveMemoryWarning {
    [super didReceiveMemoryWarning];
    // Dispose of any resources that can be recreated.
}

-(NSOperationQueue *)operationQueue {
    if (operationQueue == nil) {
        operationQueue = [[NSOperationQueue alloc] init];
        operationQueue.maxConcurrentOperationCount = 1;
    }
    return operationQueue;
}

-(void)importerDidFinishParsingData:(NSMutableArray *)data {
    //NSLog(@"%@",data);
    
    [UIApplication sharedApplication].networkActivityIndicatorVisible = NO;
    dataArray = nil;
    dataArray = [[NSMutableArray alloc] initWithArray:data];
    [abonnementCollectionView performSelectorOnMainThread:@selector(reloadData) withObject:nil waitUntilDone:YES];
    
}

-(void)importerDidFailedOrNoInternet {
    [[[UIAlertView alloc] initWithTitle:@"Erreur" message:@"Le Kiosk n'a pu récurérer les informations. Vérifier votre connexion internet et réessayer." delegate:nil cancelButtonTitle:@"Retour" otherButtonTitles:nil] show];
}

#pragma mark - UICollectionViewController
-(NSInteger)numberOfSectionsInCollectionView:(UICollectionView *)collectionView {
    return 1;
}
-(NSInteger)collectionView:(UICollectionView *)collectionView numberOfItemsInSection:(NSInteger)section {
    //NSLog(@"number of issues = %d", [editionsViewArray count]);
    //return [editionsViewArray count];
    return [dataArray count];
}
-(UICollectionViewCell *)collectionView:(UICollectionView *)collectionView cellForItemAtIndexPath:(NSIndexPath *)indexPath {
    //static NSString *identifier = @"issueCell";
    AbonnementViewCell *cell = (AbonnementViewCell*)[collectionView dequeueReusableCellWithReuseIdentifier:@"abonnementViewCell" forIndexPath:indexPath];
    [cell setDataArray:[dataArray objectAtIndex:indexPath.row]];
    [cell setItemsInView];
    
    [cell.nomLabel setText:[[dataArray objectAtIndex:indexPath.row] valueForKey:@"nom"]];
    NSLog(@"%@",[dataArray objectAtIndex:indexPath.row]);
    if ([[[dataArray objectAtIndex:indexPath.row] valueForKey:@"bought"] intValue] == 1) {
        [cell.prixLabel setText:[NSString stringWithFormat:@"Abonné"]];
    }
    else {
    [cell.prixLabel setText:[NSString stringWithFormat:@"%@$ USD",[[[dataArray objectAtIndex:indexPath.row] valueForKey:@"prix_1"] valueForKey:@"prix"]]];
    }
    
    cell.selectLabel.text = @"Selectionner";
    cell.selectLabel.textColor = [UIColor colorWithRed:0.1098 green:0.5333 blue:1 alpha:1];
    cell.selectLabel.font = [UIFont fontWithName:@"Helvetica" size:22];
    
    return cell;
}

-(void)collectionView:(UICollectionView *)collectionView didSelectItemAtIndexPath:(NSIndexPath *)indexPath {
    
    [collectionView deselectItemAtIndexPath:indexPath animated:YES];
    
    AbonnementViewController *VC = [[AbonnementViewController alloc] initWithPackage:[dataArray objectAtIndex:indexPath.row]];
    
    [self.navigationController pushViewController:VC animated:YES];
    
}

@end
