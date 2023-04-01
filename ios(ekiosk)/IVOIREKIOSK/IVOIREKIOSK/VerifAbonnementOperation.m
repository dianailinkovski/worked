//
//  VerifAbonnementOperation.m
//  e-Kiosk
//
//  Created by Maxime Julien-Paquet on 2014-02-06.
//  Copyright (c) 2014 Maxime Julien-Paquet. All rights reserved.
//

#import "VerifAbonnementOperation.h"
#import "AppDelegate.h"
#import "Editions.h"

@implementation VerifAbonnementOperation

@synthesize insertionContext, editionEntityDescription;
//@synthesize delegate;

-(void)main {
    NSUserDefaults *defaults = [NSUserDefaults standardUserDefaults];
    NSString *username = [defaults objectForKey:@"username"];
    NSString *password = [defaults objectForKey:@"password"];
    
    if (username == nil || [username isEqualToString:@""] ||
        password == nil || [password isEqualToString:@""]) {
        return;
    }
    
    NSURLRequest *request = [NSURLRequest requestWithURL:[NSURL URLWithString:[NSString stringWithFormat:@"%@/getEditionAbonnementDownload.php?username=%@&password=%@", kAppBaseURL, username, password]]];
    
    NSData *response = [NSURLConnection sendSynchronousRequest:request returningResponse:nil error:nil];
    
    if (response == nil) {
        return;
    }
    
    NSError *jsonParsingError = nil;
    NSArray *publicTimeline = [NSJSONSerialization JSONObjectWithData:response options:0 error:&jsonParsingError];
    
    if (publicTimeline == nil) {
        NSString *dataString = [[NSString alloc] initWithData:response encoding:NSUTF8StringEncoding];
        NSLog(@"dataString = %@", dataString);
        
        UIAlertView *alert = [[UIAlertView alloc] initWithTitle:@"Erreur" message:dataString delegate:nil cancelButtonTitle:@"Retour" otherButtonTitles:nil];
        [alert performSelectorOnMainThread:@selector(show) withObject:nil waitUntilDone:YES];
        //if (delegate && [delegate respondsToSelector:@selector(importerDidFailedOrNoInternet)]) {
        //    [delegate importerDidFailedOrNoInternet];
        //}
        [UIApplication sharedApplication].networkActivityIndicatorVisible = NO;
        return;
    }
    
    insertionContext = [[NSManagedObjectContext alloc] init];
    [insertionContext setUndoManager:nil];
    [insertionContext setPersistentStoreCoordinator:[(AppDelegate*)[[UIApplication sharedApplication] delegate] persistentStoreCoordinator]];
    
    
    
    
    NSDictionary *tempDictionary;
    for(int i=0; i<[[publicTimeline valueForKey:@"data"] count]; ++i) {
        tempDictionary = [[publicTimeline valueForKey:@"data"] objectAtIndex:i];
        
        if ([self verifIfAlreadyInCoreData:insertionContext ForId:[tempDictionary valueForKey:@"id"]]) {
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
            

        }
        
        
        
    }
    
    //if (delegate && [delegate respondsToSelector:@selector(importerDidFinishParsingData:)]) {
        //FT_SAVE_MOC([self insertionContext])
    [insertionContext performSelectorOnMainThread:@selector(save:) withObject:nil waitUntilDone:YES];
        //[delegate importerDidFinishParsingData:data];
    //}
    
    [UIApplication sharedApplication].networkActivityIndicatorVisible = NO;
    [[NSNotificationCenter defaultCenter] postNotificationName:@"ReloadNouveauxCollectionView" object:nil];
}

-(BOOL)verifIfAlreadyInCoreData:(NSManagedObjectContext *)managedObjectContext ForId:(NSString*)idString {
    
    
    NSFetchRequest *request = [[NSFetchRequest alloc] init];
    [request setEntity:[NSEntityDescription entityForName:@"Editions" inManagedObjectContext:managedObjectContext]];
    
    NSError *error = nil;
    
    
    NSPredicate *predicate = [NSPredicate predicateWithFormat:@"id == %@", idString];
    [request setPredicate:predicate];
    
    NSArray *results = [managedObjectContext executeFetchRequest:request error:&error];
    if ([results count] != 0) {
        return NO;
    }
    
    return YES;
}

@end
