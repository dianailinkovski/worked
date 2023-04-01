//
//  DetailEditionsViewController.m
//  IVOIREKIOSK
//
//  Created by Maxime Julien-Paquet on 2013-12-29.
//  Copyright (c) 2013 Maxime Julien-Paquet. All rights reserved.
//

#import "DetailEditionsViewController.h"
#import "EditionImageView.h"
#import "Editions.h"
#import "AppDelegate.h"
#import "EditionsStoreView.h"
#import "DetailEditionsHeaderViewCell.h"

@interface DetailEditionsViewController ()

@end

@implementation DetailEditionsViewController

@synthesize edition, viewController, managedObjectContext, collectionView, bottomDataArray;

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
    UICollectionViewFlowLayout *collectionViewLayout = [[UICollectionViewFlowLayout alloc] init];
    if (isPad()) {
        collectionViewLayout.sectionInset = UIEdgeInsetsMake(20, 40, 20, 40);
        collectionViewLayout.minimumLineSpacing = 20;
        collectionViewLayout.itemSize = CGSizeMake(130.0f, 210.0f);
        collectionViewLayout.headerReferenceSize = CGSizeMake(768, 460);
    }
    else {
        //rendu la
        collectionViewLayout.sectionInset = UIEdgeInsetsMake(20, 20, 20, 20);
        collectionViewLayout.minimumLineSpacing = 20;
        collectionViewLayout.itemSize = CGSizeMake(77, 130);
        collectionViewLayout.headerReferenceSize = CGSizeMake(320, 215);
        
    }
    
    
    //if (isPad()) {
    //    bottomCollectionView = [[UICollectionView alloc]initWithFrame:CGRectMake(0, 524, 768, 500) collectionViewLayout:collectionViewLayout];
    //}
    //else {
    collectionView = [[UICollectionView alloc]initWithFrame:self.view.bounds collectionViewLayout:collectionViewLayout];
    //}
    
    collectionView.autoresizingMask = UIViewAutoresizingFlexibleWidth | UIViewAutoresizingFlexibleHeight;
    
    collectionView.backgroundColor = [UIColor clearColor];
    collectionView.delegate = self;
    collectionView.dataSource = self;
    collectionView.contentInset = UIEdgeInsetsMake(74, 0, 0, 0);
    [collectionView registerClass:[EditionsStoreView class] forCellWithReuseIdentifier:@"editionsStoreView"];
    [collectionView registerClass:[DetailEditionsHeaderViewCell class] forSupplementaryViewOfKind:UICollectionElementKindSectionHeader withReuseIdentifier:@"HeaderCell"];
    
    [self.view addSubview:collectionView];
    [self.view sendSubviewToBack:collectionView];
    
    
    UIImageView *bgFingerPrint = [[UIImageView alloc] initWithFrame:CGRectMake(self.view.frame.size.width - 508, self.view.frame.size.height - 584, 508, 584)];
    bgFingerPrint.image = [UIImage imageNamed:@"fond_fingerprint.png"];
    bgFingerPrint.alpha = 0.1;
    bgFingerPrint.autoresizingMask = UIViewAutoresizingFlexibleLeftMargin | UIViewAutoresizingFlexibleTopMargin;
    [self.view addSubview:bgFingerPrint];
    [self.view sendSubviewToBack:bgFingerPrint];
}

-(void)didReceiveMemoryWarning {
    [super didReceiveMemoryWarning];
    // Dispose of any resources that can be recreated.
}
/*
-(void)setup {
    NSLog(@"self.edition = %@",self.edition);
    [self.imageView setUrl:[NSURL URLWithString:[self.edition coverpath]]];
    [self.imageView startDownload];
    
    [self.navBar.topItem setTitle:[self.edition nom]];
    [self.categorieLabel setText:[self.edition categorie]];
    NSDateFormatter *dateFormatter = [[NSDateFormatter alloc] init];
    [dateFormatter setDateFormat:@"yyyy-MM-dd"];
    [self.dateLabel setText:[dateFormatter stringFromDate:[self.edition publicationdate]]];
    
    if (![self.edition.favoris boolValue]) {
        [favLabel.layer setBorderWidth:0];
        [self.imageView hideFavAnimated];
    }
    else {
        [favLabel.layer setBorderWidth:2];
        [self.imageView showFavAnimated];
    }
}
*/
-(void)viewWillAppear:(BOOL)animated {
    [super viewWillAppear:animated];
    
    //[self setup];
    
    //[self.prixLabel setText:[NSString stringWithFormat:@"%@$",[self.dataDictionary valueForKey:@"prix"]]];
    
    [self getSameEditeur];
    
//    GetSameEditeurs *getSameEditeurs = [[GetSameEditeurs alloc] initWithIdEditeur:[self.dataDictionary valueForKey:@"id_journal"]];
//    getSameEditeurs.delegate = self;
//    [UIApplication sharedApplication].networkActivityIndicatorVisible = YES;
//    [self.operationQueue addOperation:getSameEditeurs];
}

-(void)willRotateToInterfaceOrientation:(UIInterfaceOrientation)toInterfaceOrientation duration:(NSTimeInterval)duration {
    [super willRotateToInterfaceOrientation:toInterfaceOrientation duration:duration];
    //[self.collectionView reloadData];
    if (isPad()) {
        if (UIInterfaceOrientationIsLandscape(toInterfaceOrientation)) {
            [[NSNotificationCenter defaultCenter] postNotificationName:@"HeaderSwitchToLandscape" object:nil];
        }
        else {
            [[NSNotificationCenter defaultCenter] postNotificationName:@"HeaderSwitchToPortrait" object:nil];
        }
    }
}


-(void)dismissViewController:(id)sender {
    [self dismissViewControllerAnimated:YES completion:nil];
}

-(void)deletePublication:(id)sender {
    UIAlertView *alertView = [[UIAlertView alloc] initWithTitle:@"Avertissement" message:@"Voulez-vous vraiment supprimer cette publication de votre appareil ?\n\nVous pourrez la télécharger à nouveau dans le Kiosque." delegate:self cancelButtonTitle:@"Non" otherButtonTitles:@"Oui", nil];
    [alertView setTag:100];
    [alertView show];
}

-(void)favButtonTouch:(id)sender {
    if ([self.edition.favoris boolValue]) {
        [self updateCoreDataWithFavoris:NO];
        //[self.imageView hideFavAnimated];
    }
    else {
        [self updateCoreDataWithFavoris:YES];
        //[self.imageView showFavAnimated];
    }
    [self.collectionView reloadData];
}

-(NSManagedObjectContext *)managedObjectContext {
    if (managedObjectContext == nil) {
        managedObjectContext = [[NSManagedObjectContext alloc] init];
        [managedObjectContext setUndoManager:nil];
        [managedObjectContext setPersistentStoreCoordinator:[(AppDelegate*)[[UIApplication sharedApplication] delegate] persistentStoreCoordinator]];
    }
    return managedObjectContext;
}

-(void)updateCoreDataWithFavoris:(BOOL)favoris {
    
    NSManagedObjectContext *managedObjectContext2 = [[NSManagedObjectContext alloc] init];
    [managedObjectContext2 setUndoManager:nil];
    [managedObjectContext2 setPersistentStoreCoordinator:[(AppDelegate*)[[UIApplication sharedApplication] delegate] persistentStoreCoordinator]];
    
    NSFetchRequest *request = [[NSFetchRequest alloc] init];
    [request setEntity:[NSEntityDescription entityForName:@"Editions" inManagedObjectContext:managedObjectContext2]];
    
    NSError *error = nil;
    
    NSPredicate *predicate = [NSPredicate predicateWithFormat:@"id == %d", [self.edition.id intValue]];
    [request setPredicate:predicate];
    
    NSArray *results = [managedObjectContext2 executeFetchRequest:request error:&error];
    
    Editions *tempEdition = [results objectAtIndex:0];
    tempEdition.favoris = [NSNumber numberWithBool:favoris];
    
    if([managedObjectContext2 save:nil]) {
        //[self.viewController segmentedSelectionChanged:self.viewController.filtreSegmented];
        [self.viewController reloadCollectionView:nil];
        NSString *idString = [NSString stringWithFormat:@"%d",[self.edition.id intValue]];
        self.managedObjectContext = nil;
        [self setupFromId:idString];
        [self getSameEditeur];
        
    }
    else {
        [[[UIAlertView alloc] initWithTitle:@"Erreur" message:@"Erreur lors de la modification de l'édition." delegate:nil cancelButtonTitle:@"Ok" otherButtonTitles:nil] show];
    }
}

-(void)getSameEditeur {
    NSManagedObjectContext *managedObjectContext2 = [self managedObjectContext];
    
    NSFetchRequest *fetchRequest = [[NSFetchRequest alloc] init];
    NSEntityDescription *entity = [NSEntityDescription entityForName:@"Editions" inManagedObjectContext:managedObjectContext2];
    [fetchRequest setEntity:entity];
    
    NSSortDescriptor *sortDescriptor = [[NSSortDescriptor alloc] initWithKey:@"publicationdate" ascending:NO];
    NSArray *sortDescriptors = [[NSArray alloc] initWithObjects:sortDescriptor, nil];
    [fetchRequest setSortDescriptors:sortDescriptors];
    
    NSPredicate *predicate = [NSPredicate predicateWithFormat:@"idjournal == %@", self.edition.idjournal];
    [fetchRequest setPredicate:predicate];
    
    NSError *error;
    NSArray *items = [managedObjectContext2 executeFetchRequest:fetchRequest error:&error];
    
    NSMutableArray *editionsViewArray = [[NSMutableArray alloc] init];
    for (Editions *managedObject in items) {
        if (managedObject.localpath != nil) {
            [editionsViewArray addObject:managedObject];
        }
    }
    
    [self setBottomDataArray:editionsViewArray];
    
    [self.collectionView performSelectorOnMainThread:@selector(reloadData) withObject:nil waitUntilDone:YES];
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
    
    [cell setEditionsData:[bottomDataArray objectAtIndex:indexPath.row]];
    
    //if (indexPath.row > [self.storeViewLayout numberOfColumns]) {
    //    [cell.bordertop setHidden:NO];
    //}
    
    //[cell.borderright setHidden:NO];
    
    return cell;
}

-(UICollectionReusableView *)collectionView:(UICollectionView *)collectionView viewForSupplementaryElementOfKind:(NSString *)kind atIndexPath:(NSIndexPath *)indexPath {
    
    if ( kind == UICollectionElementKindSectionHeader ) {
        //[UIView setAnimationsEnabled:NO];
        DetailEditionsHeaderViewCell *tempCellView = [collectionView dequeueReusableSupplementaryViewOfKind:UICollectionElementKindSectionHeader withReuseIdentifier:@"HeaderCell" forIndexPath:indexPath];
        
        [tempCellView.imageView setUrl:[NSURL URLWithString:[self.edition coverpath]]];
        [tempCellView.imageView startDownload];
        
        //[self.navBar.topItem setTitle:[self.dataDictionary valueForKey:@"nom"]];
        [tempCellView.nomLabel setText:[self.edition nom]];
        self.navBar.topItem.title = [self.edition nom];
        [tempCellView.categorieLabel setText:[self.edition categorie]];
        NSDateFormatter *dateFormatter = [[NSDateFormatter alloc] init];
        [dateFormatter setDateFormat:@"yyyy-MM-dd"];
        NSDate *tempDate = [self.edition publicationdate];
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
        
        if ([[self.edition favoris] boolValue]) {
            [tempCellView.imageView showFav];
        }
        
        [tempCellView.favorisButton addTarget:self action:@selector(favButtonTouch:) forControlEvents:UIControlEventTouchUpInside];
        [tempCellView.deleteButton addTarget:self action:@selector(deletePublication:) forControlEvents:UIControlEventTouchUpInside];
        
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
        }
        //[UIView setAnimationsEnabled:YES];
        
        return tempCellView;
        
    }
    
    return nil;
    
}


-(void)collectionView:(UICollectionView *)collectionView didSelectItemAtIndexPath:(NSIndexPath *)indexPath {
    
    NSManagedObjectContext *managedObjectContext2 = [self managedObjectContext];
    
    NSFetchRequest *request = [[NSFetchRequest alloc] init];
    [request setEntity:[NSEntityDescription entityForName:@"Editions" inManagedObjectContext:managedObjectContext2]];
    
    NSError *error = nil;
    Editions *temp =[self.bottomDataArray objectAtIndex:indexPath.row];
    NSPredicate *predicate = [NSPredicate predicateWithFormat:@"id == %d", [temp.id intValue]];
    [request setPredicate:predicate];
    
    NSArray *results = [managedObjectContext2 executeFetchRequest:request error:&error];
    
    Editions *tempEdition = [results objectAtIndex:0];
    [self setEdition:tempEdition];
    
    [self.collectionView reloadData];
    
    [self.collectionView setContentOffset:CGPointMake(0, -74) animated:YES];
    
}

#pragma mark - BottomDetailsStoreViewDelegate

-(void)BottomDetailsStoreViewTouched:(BottomDetailsStoreView *)bottomDetailView {
    NSLog(@"edition = %@",bottomDetailView.edition.id);
    
    NSManagedObjectContext *managedObjectContext2 = [self managedObjectContext];
    
    NSFetchRequest *request = [[NSFetchRequest alloc] init];
    [request setEntity:[NSEntityDescription entityForName:@"Editions" inManagedObjectContext:managedObjectContext2]];
    
    NSError *error = nil;
    
    NSPredicate *predicate = [NSPredicate predicateWithFormat:@"id == %d", [bottomDetailView.edition.id intValue]];
    [request setPredicate:predicate];
    
    NSArray *results = [managedObjectContext2 executeFetchRequest:request error:&error];
    
    Editions *tempEdition = [results objectAtIndex:0];
    [self setEdition:tempEdition];
    
    [self.collectionView reloadData];
    
    
    /*
    [self setDataDictionary:[bottomDetailView data]];
    
    [self.imageView setUrl:[NSURL URLWithString:[self.dataDictionary valueForKey:@"coverPath"]]];
    [self.imageView startDownload];
    
    [self.navBar.topItem setTitle:[self.dataDictionary valueForKey:@"nom"]];
    [self.categorieLabel setText:[self.dataDictionary valueForKey:@"categorie"]];
    [self.dateLabel setText:[self.dataDictionary valueForKey:@"datePublication"]];
    [self.prixStringLabel setText:[NSString stringWithFormat:@"%@ F CFA",[self.dataDictionary valueForKey:@"prix"]]];
    
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
        [self.prixLabel setText:[NSString stringWithFormat:@"Ouvrir"]];
    }
    else {
        [self.prixLabel setText:@"Acheter cette édition"];
    }
    */
    
}

-(void)setupFromId:(NSString*)idString {
    NSLog(@"isString = %@",idString);
    NSManagedObjectContext *managedObjectContext2 = [self managedObjectContext];
    
    NSFetchRequest *request = [[NSFetchRequest alloc] init];
    [request setEntity:[NSEntityDescription entityForName:@"Editions" inManagedObjectContext:managedObjectContext2]];
    
    NSError *error = nil;
    
    NSPredicate *predicate = [NSPredicate predicateWithFormat:@"id == %d", [idString intValue]];
    [request setPredicate:predicate];
    
    NSArray *results = [managedObjectContext2 executeFetchRequest:request error:&error];
    
    Editions *tempEdition = [results objectAtIndex:0];
    [self setEdition:tempEdition];
    
    
}

#pragma mark - UIAlertViewDelegate pour la suppression

-(void)alertView:(UIAlertView *)alertView clickedButtonAtIndex:(NSInteger)buttonIndex {
    if ([alertView tag] == 100) { // popup de suppression
        if (buttonIndex == 1) {
            NSLog(@"supprimer la publication");
            [self deletePublication];
            
        }
    }
}

-(void)deletePublication {
    NSManagedObjectContext *managedObjectContext2 = [[NSManagedObjectContext alloc] init];
    [managedObjectContext2 setUndoManager:nil];
    [managedObjectContext2 setPersistentStoreCoordinator:[(AppDelegate*)[[UIApplication sharedApplication] delegate] persistentStoreCoordinator]];
    
    NSFetchRequest *request = [[NSFetchRequest alloc] init];
    [request setEntity:[NSEntityDescription entityForName:@"Editions" inManagedObjectContext:managedObjectContext2]];
    
    NSError *error = nil;
    
    NSPredicate *predicate = [NSPredicate predicateWithFormat:@"id == %d", [self.edition.id intValue]];
    [request setPredicate:predicate];
    
    NSArray *results = [managedObjectContext2 executeFetchRequest:request error:&error];
    
    Editions *tempEdition = [results objectAtIndex:0];
    NSString *localPath = tempEdition.localpath;
    [managedObjectContext2 deleteObject:tempEdition];
    
    if([managedObjectContext2 save:nil]) {
        [[NSFileManager defaultManager] removeItemAtPath:localPath error:NULL];
        [self dismissViewController:nil];
        [[NSNotificationCenter defaultCenter] postNotificationName:@"ReloadCollectionView" object:@"deleted"];
    }
    else {
        [[[UIAlertView alloc] initWithTitle:@"Erreur" message:@"Erreur lors de la suppresion de la publication." delegate:nil cancelButtonTitle:@"Ok" otherButtonTitles:nil] show];
    }
    
    
}

#pragma mark - Touch button animation
-(void)favorisTouched {
    [[[UIAlertView alloc] initWithTitle:@"Avertisement" message:@"" delegate:self cancelButtonTitle:@"Non" otherButtonTitles:@"Oui", nil] show];
}

-(void)deleteTouched {
    [self deletePublication];
}

/*
-(void)touchDownButton:(id)sender {
    UIButton *tempbutton = (UIButton*)sender;
    if (tempbutton.tag == 1) {
        favLabel.backgroundColor = [UIColor colorWithRed:0.1960f green:0.2f blue:0.2156f alpha:0.6];
    }
    else if (tempbutton.tag == 2) {
        deleteLabel.backgroundColor = [UIColor colorWithRed:0.1960f green:0.2f blue:0.2156f alpha:0.6];
    }
    
}

-(void)touchUpButton:(id)sender {
    UIButton *tempbutton = (UIButton*)sender;
    if (tempbutton.tag == 1) {
        favLabel.backgroundColor = [UIColor colorWithRed:0.1960f green:0.2f blue:0.2156f alpha:0.1];
    }
    else if (tempbutton.tag == 2) {
        deleteLabel.backgroundColor = [UIColor colorWithRed:0.1960f green:0.2f blue:0.2156f alpha:0.1];
    }
    
}
*/

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

@end
