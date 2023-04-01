//
//  CleanOperation.m
//  IVOIREKIOSK
//
//  Created by Maxime Julien-Paquet on 2014-01-19.
//  Copyright (c) 2014 Maxime Julien-Paquet. All rights reserved.
//

#import "CleanOperation.h"
#import "AppDelegate.h"

@implementation CleanOperation

@synthesize managedObjectContext;

-(void)main {
    NSLog(@"CleanOperation Start");
    NSUserDefaults *defaults = [NSUserDefaults standardUserDefaults];
    int nbMaximum = [[defaults objectForKey:@"nbMaximum"] intValue];
    int deleteAfter = [[defaults objectForKey:@"deleteAfter"] intValue];
    BOOL exclureFavoris = [[defaults objectForKey:@"excluFavoris"] boolValue];
    
    if (nbMaximum == 4 && deleteAfter == 4) {
        return;
    } // Illimité sur le device. canceller l'operation
    
    managedObjectContext = [[NSManagedObjectContext alloc] init];
    [managedObjectContext setUndoManager:nil];
    [managedObjectContext setPersistentStoreCoordinator:[(AppDelegate*)[[UIApplication sharedApplication] delegate] persistentStoreCoordinator]];
    
    
    if (deleteAfter != 4) {
        [self clearByDeleteAfter:deleteAfter AndFavoris:exclureFavoris];
    }
    if (self.isCancelled) return;
    managedObjectContext = [[NSManagedObjectContext alloc] init];
    [managedObjectContext setUndoManager:nil];
    [managedObjectContext setPersistentStoreCoordinator:[(AppDelegate*)[[UIApplication sharedApplication] delegate] persistentStoreCoordinator]];
    
    if (nbMaximum != 4) {
        [self clearByNbMaximum:nbMaximum AndFavoris:exclureFavoris];
    }
    if (self.isCancelled) return;
}

-(void)clearByDeleteAfter:(int)deleteAfter AndFavoris:(BOOL)exclureFavoris {
    NSFetchRequest *fetchRequest = [[NSFetchRequest alloc] init];
    NSEntityDescription *entity = [NSEntityDescription entityForName:@"Editions" inManagedObjectContext:self.managedObjectContext];
    [fetchRequest setEntity:entity];
    
    NSSortDescriptor *sortDescriptor = [[NSSortDescriptor alloc] initWithKey:@"publicationdate" ascending:YES];
    NSArray *sortDescriptors = [[NSArray alloc] initWithObjects:sortDescriptor, nil];
    [fetchRequest setSortDescriptors:sortDescriptors];
    
    
    NSDate *lastDate = [self getLastDownloadDate];
    
    if (lastDate == nil) {
        return;
    }
    
    NSDateComponents* componentsToSubtract = [[NSDateComponents alloc] init];
    [componentsToSubtract setDay:0-[self GetDeleteAfterNbJour:deleteAfter]];
    
    NSDate *recentDate = [[NSCalendar currentCalendar] dateByAddingComponents:componentsToSubtract toDate:lastDate options:0];
    NSLog(@"recentDate = %@",recentDate);
    
    
    

    
    
    NSMutableArray *predicateArray = [[NSMutableArray alloc] init];
    
    NSPredicate *predicateDate = [NSPredicate predicateWithFormat:@"downloaddate <= %@", recentDate];
    [predicateArray addObject:predicateDate];
    
    //componentsToSubtract = [[NSDateComponents alloc] init];
    //[componentsToSubtract setDay:-2];
    
    //NSDate *yesterday = [[NSCalendar currentCalendar] dateByAddingComponents:componentsToSubtract toDate:[NSDate date] options:0];
    
    NSPredicate *predicate;
    //predicate = [NSPredicate predicateWithFormat:@"openDate < %@", yesterday];
    //[predicateArray addObject:predicate];
    predicate = [NSPredicate predicateWithFormat:@"openDate != nil"];
    [predicateArray addObject:predicate];
    
    if (exclureFavoris) {
        NSPredicate *predicateFavoris = [NSPredicate predicateWithFormat:@"favoris != 1"];
        [predicateArray addObject:predicateFavoris];
    }
    
    NSPredicate *compoundPredicate = [NSCompoundPredicate andPredicateWithSubpredicates:predicateArray];
    
    [fetchRequest setPredicate:compoundPredicate];
    
    NSError *error;
    NSArray *items = [managedObjectContext executeFetchRequest:fetchRequest error:&error];
    
    //NSLog(@"item = %@",items);
    
    for (Editions *tempEdition in items) {
        if (self.isCancelled) return;
        NSLog(@"%@",tempEdition.publicationdate);
        if (![self deletePublication:tempEdition]) {
            [[[UIAlertView alloc] initWithTitle:@"Erreur" message:@"Une erreur s'est produit lors du nettoyage automatique des publications. Consulter les développeurs si l'erreur persiste." delegate:nil cancelButtonTitle:@"Retour" otherButtonTitles:nil] show];
            [self cancel];
            return;
        }
    }
    
//    [managedObjectContext deleteObject:tempEdition];
//
//    if([managedObjectContext save:nil]) {
//        [[NSFileManager defaultManager] removeItemAtPath:localPath error:NULL];
//        [self dismissViewController:nil];
//        [[NSNotificationCenter defaultCenter] postNotificationName:@"CoreDataUpdated" object:nil];
//    }
    
}

-(void)clearByNbMaximum:(int)nbMaximum AndFavoris:(BOOL)exclureFavoris {
    
    
    NSFetchRequest *fetchRequest = [[NSFetchRequest alloc] init];
    NSEntityDescription *entity = [NSEntityDescription entityForName:@"Editions" inManagedObjectContext:self.managedObjectContext];
    [fetchRequest setEntity:entity];
    
    NSSortDescriptor *sortDescriptor = [[NSSortDescriptor alloc] initWithKey:@"publicationdate" ascending:YES];
    NSArray *sortDescriptors = [[NSArray alloc] initWithObjects:sortDescriptor, nil];
    [fetchRequest setSortDescriptors:sortDescriptors];
    
    
    if (exclureFavoris) {
        NSPredicate *predicateFavoris = [NSPredicate predicateWithFormat:@"favoris != 1"];
        [fetchRequest setPredicate:predicateFavoris];
    }
    
    
    
    NSError *error;
    NSArray *items = [managedObjectContext executeFetchRequest:fetchRequest error:&error];
    
    NSLog(@"%d, max = %d",items.count, [self GetNbMaximum:nbMaximum]);
    
    NSInteger nbToDelete = (items.count - [self GetNbMaximum:nbMaximum]);
    if (nbToDelete <= 0) {
        return;
    }
    
    for (int x = 0; x < nbToDelete; ++x) {
        if (self.isCancelled) return;
        Editions *tempEdition = [items objectAtIndex:x];
        NSLog(@"%@",tempEdition.publicationdate);
        if (![self deletePublication:tempEdition]) {
            [[[UIAlertView alloc] initWithTitle:@"Erreur" message:@"Une erreur s'est produit lors du nettoyage automatique des publications. Consulter les développeurs si l'erreur persiste." delegate:nil cancelButtonTitle:@"Retour" otherButtonTitles:nil] show];
            [self cancel];
            return;
        }
    }
    
    //for (Editions *tempEdition in items) {
    //    NSLog(@"%@",tempEdition.publicationdate);
    //}
    
    
    
    //    [managedObjectContext deleteObject:tempEdition];
    //
    //    if([managedObjectContext save:nil]) {
    //        [[NSFileManager defaultManager] removeItemAtPath:localPath error:NULL];
    //        [self dismissViewController:nil];
    //        [[NSNotificationCenter defaultCenter] postNotificationName:@"CoreDataUpdated" object:nil];
    //    }
    
}

-(int)GetDeleteAfterNbJour:(int)deleteAfter {
    switch (deleteAfter) {
        case 0:
            return 15;
            break;
        case 1:
            return 30;
            break;
        case 2:
            return 60;
            break;
        case 3:
            return 120;
            break;
        case 4:
            return 0;
            break;
            
        default:
            return 0;
            break;
    }
}

-(int)GetNbMaximum:(int)nbMaximum {
    switch (nbMaximum) {
        case 0:
            return 30;
            break;
        case 1:
            return 60;
            break;
        case 2:
            return 90;
            break;
        case 3:
            return 120;
            break;
        case 4:
            return 0;
            break;
            
        default:
            return 0;
            break;
    }
}

-(BOOL)deletePublication:(Editions*)edition {
    NSManagedObjectContext *managedObjectContext2 = [[NSManagedObjectContext alloc] init];
    [managedObjectContext2 setUndoManager:nil];
    [managedObjectContext2 setPersistentStoreCoordinator:[(AppDelegate*)[[UIApplication sharedApplication] delegate] persistentStoreCoordinator]];
    
    NSFetchRequest *request = [[NSFetchRequest alloc] init];
    [request setEntity:[NSEntityDescription entityForName:@"Editions" inManagedObjectContext:managedObjectContext2]];
    
    NSError *error = nil;
    
    NSPredicate *predicate = [NSPredicate predicateWithFormat:@"id == %d", [edition.id intValue]];
    [request setPredicate:predicate];
    
    NSArray *results = [managedObjectContext2 executeFetchRequest:request error:&error];
    
    Editions *tempEdition = [results objectAtIndex:0];
    NSString *localPath = tempEdition.localpath;
    [managedObjectContext2 deleteObject:tempEdition];
    
    if([managedObjectContext2 save:nil]) {
        [[NSFileManager defaultManager] removeItemAtPath:localPath error:NULL];
        [[NSNotificationCenter defaultCenter] postNotificationName:@"CoreDataUpdated" object:nil];
    }
    else {
        return NO;
    }
    
    return YES;
}

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

@end
