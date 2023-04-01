//
//  MAJJournal.m
//  IVOIREKIOSK
//
//  Created by Maxime Julien-Paquet on 2013-12-11.
//  Copyright (c) 2013 Maxime Julien-Paquet. All rights reserved.
//

#import "MAJJournal.h"
#import "AppDelegate.h"
#import "Editions.h"

@implementation MAJJournal

@synthesize editionEntityDescription, journalsURL, insertionContext;

-(void)main {
    
    NSURLRequest *request = [NSURLRequest requestWithURL:[NSURL URLWithString:[NSString stringWithFormat:@"%@/getRecents-old.php", kAppBaseURL]]];
    
    NSData *response = [NSURLConnection sendSynchronousRequest:request returningResponse:nil error:nil];
    
    if (response == nil) {
        return;
    }
    
    NSError *jsonParsingError = nil;
    NSArray *publicTimeline = [NSJSONSerialization JSONObjectWithData:response options:0 error:&jsonParsingError];
    
    NSLog(@"%@",[publicTimeline valueForKey:@"data"]);
    
    NSDictionary *tempDic;
    
    for(int i=0; i<[[publicTimeline valueForKey:@"data"] count]; ++i) {
        
        tempDic = [[publicTimeline valueForKey:@"data"] objectAtIndex:i];
        
//        Journal *currentJournal = [[Journal alloc] initWithEntity:self.journalEntityDescription
//                                   insertIntoManagedObjectContext:self.insertionContext];
//        
//        currentJournal.id = [NSNumber numberWithInt:[[tempDic valueForKey:@"id"] intValue]];
//        currentJournal.nom = [tempDic valueForKey:@"nom"];
//        currentJournal.type = [tempDic valueForKey:@"type"];
//        currentJournal.categorie = [tempDic valueForKey:@"categorie"];
//        
//        
//        for(int j=0; j<[[tempDic valueForKey:@"editions"] count]; ++j) {
//            
//            Editions *currentEdition = [[Editions alloc] initWithEntity:self.editionEntityDescription
//                                       insertIntoManagedObjectContext:self.insertionContext];
//            
//            currentEdition.id = [NSNumber numberWithInt:[[[[tempDic valueForKey:@"editions"] objectAtIndex:j] valueForKey:@"id"] intValue]];
//            
//            currentEdition.downloadpath = [[[tempDic valueForKey:@"editions"] objectAtIndex:j] valueForKey:@"downloadPath"];
//            currentEdition.coverpath = [[[tempDic valueForKey:@"editions"] objectAtIndex:j] valueForKey:@"coverPath"];
//            currentEdition.publicationdate = [[NSDate alloc] init];
//            currentEdition.editeur = currentJournal;
//            currentEdition.lu = [NSNumber numberWithBool:NO];
//            [currentJournal addPublicationsObject:currentEdition];
//            
//            //NSSet *test = [NSSet setWithObject:currentEdition];
//            //[currentJournal addPublicationsObject:test];
//            
//        }
        
    }
    //NSError *saveError = nil;
    FT_SAVE_MOC([self insertionContext])
    //NSAssert1([insertionContext save:&saveError], @"Unhandled error saving managed object context in import thread: %@", saveError);
    
    [[NSNotificationCenter defaultCenter] postNotificationName:@"CoreDataUpdated" object:nil];
}

- (NSManagedObjectContext *)insertionContext {
    if (insertionContext == nil) {
        insertionContext = [[NSManagedObjectContext alloc] init];
        [insertionContext setPersistentStoreCoordinator:[(AppDelegate*)[[UIApplication sharedApplication] delegate] persistentStoreCoordinator]];
    }
    return insertionContext;
}

- (NSEntityDescription *)editionEntityDescription {
    if (editionEntityDescription == nil) {
        editionEntityDescription = [NSEntityDescription entityForName:@"Editions" inManagedObjectContext:self.insertionContext];
    }
    return editionEntityDescription;
}


@end
