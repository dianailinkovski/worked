//
//  AbonnementViewController.m
//  IVOIREKIOSK
//
//  Created by Maxime Julien-Paquet on 2014-01-11.
//  Copyright (c) 2014 Maxime Julien-Paquet. All rights reserved.
//

#import "AbonnementViewController.h"
#import "JournalPickerViewCell.h"
#import "JournalPickerHeaderViewCell.h"


static NSString * const JournalViewLayoutJournalPickerCellKind = @"journalPickerViewCell";
static NSString * const JournalViewHeaderLayoutKind = @"journalPickerHeaderViewCell";

@interface AbonnementViewController () {
    NSMutableArray *dataArray;
    
}

@end

@implementation AbonnementViewController

@synthesize mainCollectionView, packageArray, operationQueue, selectedCountArray;

-(id)initWithPackage:(NSMutableArray*)package {
    self = [super init];
    if (self) {
        
        self.packageArray = package;
        
    }
    return self;
}

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
    self.title = @"Faites votre sélection";
    self.view.opaque = NO;
    UIImageView *bg;
    
    bg = [[UIImageView alloc] initWithFrame:self.view.bounds];
    bg.autoresizingMask = UIViewAutoresizingFlexibleHeight | UIViewAutoresizingFlexibleWidth;
    bg.backgroundColor = [UIColor whiteColor];
    bg.alpha = 0.3;
    [self.view addSubview:bg];
    [self.view sendSubviewToBack:bg];
    
    bg = [[UIImageView alloc] initWithFrame:self.view.bounds];
    bg.autoresizingMask = UIViewAutoresizingFlexibleHeight | UIViewAutoresizingFlexibleWidth;
    [bg setImage:[UIImage imageNamed:@"bg-street.jpg"]];
    [self.view addSubview:bg];
    [self.view sendSubviewToBack:bg];
    
    UIBarButtonItem *temp =[[UIBarButtonItem alloc] initWithTitle:@"Étape suivante" style:UIBarButtonItemStyleDone target:self action:@selector(continuerTouched:)];
    self.navigationItem.rightBarButtonItem = temp;
    
    
    
    //packageArray = @[@[@"0",@"3"], @[@"0",@"2"], @[@"0",@"0"]];
    
    
    /*
    dataArray = [[NSMutableArray alloc] initWithObjects:
                 [NSMutableDictionary dictionaryWithObjectsAndKeys:
                  @"Quotidien",
                  @"section",
                  [NSMutableArray arrayWithObjects:
                   [NSMutableDictionary dictionaryWithObjectsAndKeys:
                    @"0", @"id",
                    @"Notre Voie", @"nom",
                    @"notrevoie.jpg", @"imagePath",
                    @"0", @"selected",
                    nil],
                   [NSMutableDictionary dictionaryWithObjectsAndKeys:
                    @"0", @"id",
                    @"Le Temps", @"nom",
                    @"letemps.jpg", @"imagePath",
                    @"0", @"selected",
                    nil],
                   [NSMutableDictionary dictionaryWithObjectsAndKeys:
                    @"0", @"id",
                    @"L'intelligent d'Abidjan", @"nom",
                    @"intelligentabidjan.jpg", @"imagePath",
                    @"0", @"selected",
                    nil],
                   [NSMutableDictionary dictionaryWithObjectsAndKeys:
                    @"0", @"id",
                    @"Le Dialogue", @"nom",
                    @"dialogue.jpg", @"imagePath",
                    @"0", @"selected",
                    nil],
                   [NSMutableDictionary dictionaryWithObjectsAndKeys:
                    @"0", @"id",
                    @"Le Quotidien", @"nom",
                    @"lequotidien.jpg", @"imagePath",
                    @"0", @"selected",
                    nil],
                   [NSMutableDictionary dictionaryWithObjectsAndKeys:
                    @"0", @"id",
                    @"Nord Sud", @"nom",
                    @"nordsudquotidien.jpg", @"imagePath",
                    @"0", @"selected",
                    nil],
                   [NSMutableDictionary dictionaryWithObjectsAndKeys:
                    @"0", @"id",
                    @"Le Patriote", @"nom",
                    @"patriote.jpg", @"imagePath",
                    @"0", @"selected",
                    nil],
                   [NSMutableDictionary dictionaryWithObjectsAndKeys:
                    @"0", @"id",
                    @"LG Infos", @"nom",
                    @"lginfos.jpg", @"imagePath",
                    @"0", @"selected",
                    nil],
                   [NSMutableDictionary dictionaryWithObjectsAndKeys:
                    @"0", @"id",
                    @"L'Expression", @"nom",
                    @"lexpressionquotidien.jpg", @"imagePath",
                    @"0", @"selected",
                    nil],
                   [NSMutableDictionary dictionaryWithObjectsAndKeys:
                    @"0", @"id",
                    @"Le Nouveau Réveil", @"nom",
                    @"lenouveaureveil.jpg", @"imagePath",
                    @"0", @"selected",
                    nil],
                   
                   nil],
                  @"journaux",
                  nil],
                 [NSMutableDictionary dictionaryWithObjectsAndKeys:
                  @"Hebdomadaire",
                  @"section",
                  [NSMutableArray arrayWithObjects:
                   
                   [NSMutableDictionary dictionaryWithObjectsAndKeys:
                    @"0", @"id",
                    @"Super Sport", @"nom",
                    @"supersport.jpg", @"imagePath",
                    @"0", @"selected",
                    nil],
                   [NSMutableDictionary dictionaryWithObjectsAndKeys:
                    @"0", @"id",
                    @"Abidjan Sport", @"nom",
                    @"abidjansports.jpg", @"imagePath",
                    @"0", @"selected",
                    nil],
                   nil],
                  @"journaux",
                  nil],
                 [NSMutableDictionary dictionaryWithObjectsAndKeys:
                  @"Mensuel",
                  @"section",
                  [NSMutableArray arrayWithObjects:
                   [NSMutableDictionary dictionaryWithObjectsAndKeys:
                    @"0", @"id",
                    @"Nord Sud", @"nom",
                    @"nordsudquotidien.jpg", @"imagePath",
                    @"0", @"selected",
                    nil],
                   [NSMutableDictionary dictionaryWithObjectsAndKeys:
                    @"0", @"id",
                    @"Nord Sud", @"nom",
                    @"nordsudquotidien.jpg", @"imagePath",
                    @"0", @"selected",
                    nil],
                   [NSMutableDictionary dictionaryWithObjectsAndKeys:
                    @"0", @"id",
                    @"Nord Sud", @"nom",
                    @"nordsudquotidien.jpg", @"imagePath",
                    @"0", @"selected",
                    nil],
                   [NSMutableDictionary dictionaryWithObjectsAndKeys:
                    @"0", @"id",
                    @"Nord Sud", @"nom",
                    @"nordsudquotidien.jpg", @"imagePath",
                    @"0", @"selected",
                    nil],
                   nil],
                  @"journaux",
                  nil],
                 nil];
    */
    
    
    
    
    
    
    
    
    
    UICollectionViewFlowLayout *collectionViewLayout = [[UICollectionViewFlowLayout alloc] init];
    collectionViewLayout.sectionInset = UIEdgeInsetsMake(20, 40, 20, 40);
    collectionViewLayout.minimumLineSpacing = 20;
    collectionViewLayout.itemSize = CGSizeMake(200.0f, 180.0f);
    collectionViewLayout.headerReferenceSize = CGSizeMake(self.view.frame.size.width, 50);

    
    mainCollectionView = [[UICollectionView alloc]initWithFrame:self.view.bounds collectionViewLayout:collectionViewLayout];
    mainCollectionView.autoresizingMask = UIViewAutoresizingFlexibleWidth | UIViewAutoresizingFlexibleHeight;
    
    mainCollectionView.contentInset = UIEdgeInsetsMake(64, 0, 0, 0);
    mainCollectionView.backgroundColor = [UIColor clearColor];
    mainCollectionView.delegate = self;
    mainCollectionView.dataSource = self;
    [mainCollectionView registerClass:[JournalPickerViewCell class] forCellWithReuseIdentifier:JournalViewLayoutJournalPickerCellKind];
    [mainCollectionView registerClass:[JournalPickerHeaderViewCell class] forSupplementaryViewOfKind:UICollectionElementKindSectionHeader withReuseIdentifier:JournalViewHeaderLayoutKind];
    
    [self.view addSubview:mainCollectionView];
    
    
    self.selectedCountArray = [[NSMutableArray alloc] init];
    /*
    selectedCountArray = [[NSMutableArray alloc] initWithObjects:
                          [NSMutableArray arrayWithObjects:@"0", @"3", nil],
                          [NSMutableArray arrayWithObjects:@"0", @"2", nil],
                          [NSMutableArray arrayWithObjects:@"0", @"0", nil],
                          nil];
    */
    //[[[[self.dataArray valueForKey:@"items"] objectAtIndex:x] valueForKey:@"amount"] intValue]
    
    
    NSString *tempString = @"";
    for (int x = 0; x < [[self.packageArray valueForKey:@"items"] count]; ++x) {
        tempString = [tempString stringByAppendingFormat:@"%@-,-",[[[self.packageArray valueForKey:@"items"] objectAtIndex:x] valueForKey:@"type"]];
        //NSMutableArray *temp = [[NSMutableArray alloc] initWithObjects:@"0", [[[self.packageArray valueForKey:@"items"] objectAtIndex:x] valueForKey:@"amount"], nil];
        //[self.selectedCountArray addObject:temp];
        [self.selectedCountArray addObject:[[NSMutableArray alloc] initWithObjects:@"0", [[[self.packageArray valueForKey:@"items"] objectAtIndex:x] valueForKey:@"amount"], nil]];
        
        //[selectedCountArray addObjectsFromArray:[[NSMutableArray alloc] initWithObjects:@"", [[[[self.packageArray valueForKey:@"items"] objectAtIndex:x] valueForKey:@"amount"] intValue], nil]];
    }
    NSLog(@"selectedarray = %@",selectedCountArray);
    tempString = [tempString substringWithRange:NSMakeRange(0, tempString.length-3)];
    
    GetJournauxForPackages *getJournauxForPackages = [[GetJournauxForPackages alloc] initWithCategorie:tempString];
    getJournauxForPackages.delegate = self;
    [UIApplication sharedApplication].networkActivityIndicatorVisible = YES;
    [self.operationQueue addOperation:getJournauxForPackages];
    
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
    [UIApplication sharedApplication].networkActivityIndicatorVisible = NO;
    dataArray = nil;
    dataArray = [[NSMutableArray alloc] initWithArray:data];
    [mainCollectionView performSelectorOnMainThread:@selector(reloadData) withObject:nil waitUntilDone:YES];
}

-(void)importerDidFailedOrNoInternet {
    [[[UIAlertView alloc] initWithTitle:@"Erreur" message:@"Le Kiosk n'a pu récurérer les informations. Vérifier votre connexion internet et réessayer." delegate:nil cancelButtonTitle:@"Retour" otherButtonTitles:nil] show];
}

#pragma mark - UICollectionViewController
-(NSInteger)numberOfSectionsInCollectionView:(UICollectionView *)collectionView {
    return [dataArray count];
}
-(NSInteger)collectionView:(UICollectionView *)collectionView numberOfItemsInSection:(NSInteger)section {
    return [[[dataArray objectAtIndex:section] valueForKey:@"journaux"] count];
}
-(UICollectionViewCell *)collectionView:(UICollectionView *)collectionView cellForItemAtIndexPath:(NSIndexPath *)indexPath {
    JournalPickerViewCell *cell = (JournalPickerViewCell*)[collectionView dequeueReusableCellWithReuseIdentifier:JournalViewLayoutJournalPickerCellKind forIndexPath:indexPath];
    
    [cell setDataInView:[[[dataArray objectAtIndex:indexPath.section] valueForKey:@"journaux"] objectAtIndex:indexPath.row]];
    
    return cell;
}
-(UICollectionReusableView *)collectionView:(UICollectionView *)collectionView viewForSupplementaryElementOfKind:(NSString *)kind atIndexPath:(NSIndexPath *)indexPath {
    
    UICollectionReusableView *reusableview = nil;
    
    if (kind == UICollectionElementKindSectionHeader) {
        JournalPickerHeaderViewCell *headerView = [collectionView dequeueReusableSupplementaryViewOfKind:UICollectionElementKindSectionHeader withReuseIdentifier:JournalViewHeaderLayoutKind forIndexPath:indexPath];
        headerView.indexPathLocal = indexPath;
        headerView.titleLabel.text = [[dataArray objectAtIndex:indexPath.section] valueForKey:@"title"];
        int total = [[[selectedCountArray objectAtIndex:indexPath.section] objectAtIndex:1] intValue];
        int choisi = [[[selectedCountArray objectAtIndex:indexPath.section] objectAtIndex:0] intValue];
        
        if (total == 0) {
            //headerView.compteurLabel.textColor = [UIColor colorWithRed:0.0 green:0.6196 blue:0.3804 alpha:1.0];
            headerView.compteurLabel.text = [NSString stringWithFormat:@"Aucun disponible avec l'abonnement sélectionné"];
        }
        else if (total - choisi > 0) {
            //headerView.compteurLabel.textColor = [UIColor colorWithRed:0.0 green:0.6196 blue:0.3804 alpha:1.0];
            headerView.compteurLabel.text = [NSString stringWithFormat:@"Il vous reste %d choix", total - choisi];
        }
        else if (total - choisi < 0) {
            //headerView.compteurLabel.textColor = [UIColor redColor];
            headerView.compteurLabel.text = [NSString stringWithFormat:@"Vous avez %d choix de trop, %d maximum", abs(total - choisi), total];
        }
        else {
            //headerView.compteurLabel.textColor = [UIColor colorWithRed:0.0 green:0.6196 blue:0.3804 alpha:1.0];
            headerView.compteurLabel.text = [NSString stringWithFormat:@"Sélection complète"];
        }
        
        reusableview = headerView;
    }
    /*
    if (kind == UICollectionElementKindSectionFooter) {
        UICollectionReusableView *footerview = [collectionView dequeueReusableSupplementaryViewOfKind:UICollectionElementKindSectionFooter withReuseIdentifier:@"FooterView" forIndexPath:indexPath];
        
        reusableview = footerview;
    }
    */
    return reusableview;
}

-(void)collectionView:(UICollectionView *)collectionView didSelectItemAtIndexPath:(NSIndexPath *)indexPath {
    if ([[[selectedCountArray objectAtIndex:indexPath.section] objectAtIndex:1] intValue] == 0) {
        return;
    }
    
    JournalPickerViewCell *cell = (JournalPickerViewCell*)[collectionView cellForItemAtIndexPath:indexPath];
    
    int choisi = [[[selectedCountArray objectAtIndex:indexPath.section] objectAtIndex:0] intValue];
    
    
    if ([cell getIsSelected]) {
        --choisi;
        [[[[dataArray objectAtIndex:indexPath.section] valueForKey:@"journaux"] objectAtIndex:indexPath.row] setObject:@"0" forKey:@"selected"];
    }
    else {
        ++choisi;
        [[[[dataArray objectAtIndex:indexPath.section] valueForKey:@"journaux"] objectAtIndex:indexPath.row] setObject:@"1" forKey:@"selected"];
    }
    
    [[selectedCountArray objectAtIndex:indexPath.section] replaceObjectAtIndex:0 withObject:[NSString stringWithFormat:@"%d",choisi]];
    
    [cell flipImageView];
    
    [[NSNotificationCenter defaultCenter] postNotificationName:@"ReloadHeaderSection" object:@[indexPath, selectedCountArray]];
    
    
    
    [self validateSelection:NO];
    
}

-(BOOL)validateSelection:(BOOL)showAlert {
    
    for (int x = 0; x < [selectedCountArray count]; ++x) {
        int total = [[[selectedCountArray objectAtIndex:x] objectAtIndex:1] intValue];
        int choisi = [[[selectedCountArray objectAtIndex:x] objectAtIndex:0] intValue];
        if (choisi < total) {
            if (!showAlert) return NO;
            NSString *tempString = [NSString stringWithFormat:@"La catégorie '%@' n'est pas complète",[[dataArray objectAtIndex:x] valueForKey:@"title"]];
            [[[UIAlertView alloc] initWithTitle:@"Erreur" message:tempString delegate:nil cancelButtonTitle:@"Retour" otherButtonTitles:nil] show];
            return NO;
        }
        else if (choisi > total) {
            if (!showAlert) return NO;
            NSString *tempString = [NSString stringWithFormat:@"Vous avez %d de trop dans la catégorie '%@'", abs(choisi - total),[[dataArray objectAtIndex:x] valueForKey:@"title"]];
            [[[UIAlertView alloc] initWithTitle:@"Erreur" message:tempString delegate:nil cancelButtonTitle:@"Retour" otherButtonTitles:nil] show];
            return NO;
        }
    }
    
    if (!showAlert) {
        [self performSelector:@selector(pushAlertCompleted) withObject:Nil afterDelay:0.3];
        return NO;
    }
    return YES;
}

-(void)pushAlertCompleted {
    
    UIAlertView *alertView = [[UIAlertView alloc] initWithTitle:@"Informations" message:@"Vos sélections sont complètes.\n\nVoulez-vous passer à l'étape suivant ?" delegate:self cancelButtonTitle:@"Modifier" otherButtonTitles:@"Étape suivant", nil];
    [alertView setBounds:CGRectMake(0, 0, 400, 400)];
    [alertView setTag:1001];
    [alertView show];
    
}

-(void)alertView:(UIAlertView *)alertView clickedButtonAtIndex:(NSInteger)buttonIndex {
    if (alertView.tag == 1001 && buttonIndex == 1) {
        [self pushConfirmationView];
    }
}

-(void)continuerTouched:(id)sender {
    if ([self validateSelection:YES]) {
        [self pushConfirmationView];
    }
}

-(void)pushConfirmationView {
    
    if (![self validateSelection:YES]) {
        return;
    }
    
    
    NSString *storyboardString = @"Main_iPhone";
    if (isPad()) {
        storyboardString = @"Main_iPad";
    }
    
    UIStoryboard *sb = [UIStoryboard storyboardWithName:storyboardString bundle:nil];
    
    AbonnementConfirmationViewController* vc = (AbonnementConfirmationViewController*)[sb instantiateViewControllerWithIdentifier:@"AbonnementConfirmationViewController"];
    [vc setModalPresentationStyle:UIModalPresentationFormSheet];
    
    [vc setDataArray:dataArray];
    [vc setPackageArray:packageArray];
    [vc setDelegate:self];
    //[vc setDelegate:self];
    //[vc setDataDictionary:[dataArray objectAtIndex:indexPath.row]];
    [self presentViewController:vc animated:YES completion:nil];
}

-(void)didDismissAbonnementConfirmationViewController {
    //[self.navigationController popToRootViewControllerAnimated:YES];
    [self dismissViewControllerAnimated:YES completion:nil];
}

@end
