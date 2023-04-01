//
//  AppDelegate.m
//  IVOIREKIOSK
//
//  Created by Maxime Julien-Paquet on 2013-12-03.
//  Copyright (c) 2013 Maxime Julien-Paquet. All rights reserved.
//

#import "AppDelegate.h"
#import "Reachability.h"
#import "CleanOperation.h"
//#import "TestIAPHelper.h"
#import "VerifAbonnementOperation.h"

#import "Login2ViewController.h"
#import "CreateProfilViewController.h"


@implementation AppDelegate

@synthesize managedObjectContext, persistentStoreCoordinator, persistentStorePath;
@synthesize operationQueue;

void uncaughtExceptionHandler(NSException *exception) {
    NSLog(@"CRASH: %@", exception);
    NSLog(@"Stack Trace: %@", [exception callStackSymbols]);
    // Internal error reporting
}

- (BOOL)application:(UIApplication *)application didFinishLaunchingWithOptions:(NSDictionary *)launchOptions {
    // Override point for customization after application launch.
    NSSetUncaughtExceptionHandler(&uncaughtExceptionHandler);
    
    NSUserDefaults *defaults = [NSUserDefaults standardUserDefaults];
    if ([defaults objectForKey:@"nbMaximum"] == nil || [defaults objectForKey:@"deleteAfter"] == nil || [defaults objectForKey:@"excluFavoris"] == nil) {
        NSLog(@"default autoclean variable");
        [defaults setObject:[NSNumber numberWithInt:0] forKey:@"nbMaximum"];
        [defaults setObject:[NSNumber numberWithInt:0] forKey:@"tousAfter"];
        [defaults setObject:[NSNumber numberWithInt:1] forKey:@"deleteAfter"];
        [defaults setObject:[NSNumber numberWithBool:YES] forKey:@"excluFavoris"];
        [defaults setObject:[NSNumber numberWithBool:YES] forKey:@"showNoIssue"];
        
        [defaults setObject:[NSNumber numberWithBool:YES] forKey:@"showTutoriel"];
    }
    
    //[TestIAPHelper sharedInstance];
    //[testAbonnementX_IAPHelper sharedInstance];
    
    [[NSNotificationCenter defaultCenter] addObserver:self
                                             selector:@selector(ChangementDeStatusDuCompteVerifAbonnementDownload:)
                                                 name:@"ChangementDeStatusDuCompte"
                                               object:nil];
    
    
    
    
    /*
    if ([self connected]) {
        if ([[NSFileManager defaultManager] fileExistsAtPath:self.persistentStorePath isDirectory:NO]) {
            NSError *error = nil;
            BOOL oldStoreRemovalSuccess = [[NSFileManager defaultManager] removeItemAtPath:self.persistentStorePath error:&error];
            NSAssert3(oldStoreRemovalSuccess, @"Unhandled error adding persistent store in %s at line %d: %@", __FUNCTION__, __LINE__, [error localizedDescription]);
            self.managedObjectContext = nil;
            self.persistentStoreCoordinator = nil;
            
            self.persistentStoreCoordinator = [self persistentStoreCoordinator];
            self.managedObjectContext = [self managedObjectContext];
        }
        
        self.majJournal = [[MAJJournal alloc] init];
        // pass the coordinator so the importer can create its own managed object context
        majJournal.persistentStoreCoordinator = self.persistentStoreCoordinator;
        [UIApplication sharedApplication].networkActivityIndicatorVisible = YES;
        [self.operationQueue addOperation:majJournal];
    }
    else {
        [self performSelector:@selector(pushloadnotification) withObject:nil afterDelay:0.3];
    }
    */
    
    return YES;
}
							
- (void)applicationWillResignActive:(UIApplication *)application {
    // Sent when the application is about to move from active to inactive state. This can occur for certain types of temporary interruptions (such as an incoming phone call or SMS message) or when the user quits the application and it begins the transition to the background state.
    // Use this method to pause ongoing tasks, disable timers, and throttle down OpenGL ES frame rates. Games should use this method to pause the game.
    
}

- (void)applicationDidEnterBackground:(UIApplication *)application {
    // Use this method to release shared resources, save user data, invalidate timers, and store enough application state information to restore your application to its current state in case it is terminated later. 
    // If your application supports background execution, this method is called instead of applicationWillTerminate: when the user quits.
}

- (void)applicationWillEnterForeground:(UIApplication *)application {
    // Called as part of the transition from the background to the inactive state; here you can undo many of the changes made on entering the background.
    [self.operationQueue cancelAllOperations];
}

- (void)applicationDidBecomeActive:(UIApplication *)application {
    // Restart any tasks that were paused (or not yet started) while the application was inactive. If the application was previously in the background, optionally refresh the user interface.
    
    
    [[NSNotificationCenter defaultCenter] postNotificationName:@"ReloadCollectionView" object:nil];
    
    //if ([self.operationQueue operationCount] == 0) {
    
    //[self verifAbonnementDownload];
    
    
    
    
    
    
    CleanOperation *cleanOperation = [[CleanOperation alloc] init];
    [self.operationQueue addOperation:cleanOperation];
    
    //}
    
    
    
}

- (void)applicationWillTerminate:(UIApplication *)application {
    // Called when the application is about to terminate. Save data if appropriate. See also applicationDidEnterBackground:.
}

#pragma mark - init

-(NSOperationQueue *)operationQueue {
    if (operationQueue == nil) {
        operationQueue = [[NSOperationQueue alloc] init];
        operationQueue.maxConcurrentOperationCount = 1;
    }
    return operationQueue;
}
-(void)pushloadnotification {
    [[NSNotificationCenter defaultCenter] postNotificationName:@"CoreDataUpdated" object:nil];
}

#pragma mark - abonnement function

-(void)ChangementDeStatusDuCompteVerifAbonnementDownload:(NSNotification*)notif {
    [self performSelectorOnMainThread:@selector(verifAbonnementDownload) withObject:nil waitUntilDone:NO];
}
-(void )verifAbonnementDownload {
    if ([self connected]) {
        NSLog(@"ChangementDeStatusDuCompteVerifAbonnementDownload");
        [UIApplication sharedApplication].networkActivityIndicatorVisible = YES;
        VerifAbonnementOperation *verifAbonnementOperation = [[VerifAbonnementOperation alloc] init];
        [self.operationQueue addOperation:verifAbonnementOperation];
    }
}

# pragma mark - Reachability

- (BOOL)connected {
    Reachability *reachability = [Reachability reachabilityForInternetConnection];
    NetworkStatus networkStatus = [reachability currentReachabilityStatus];
    return !(networkStatus == NotReachable);
}

#pragma mark - DataStore Function
-(NSManagedObjectContext *)managedObjectContext {
    if (managedObjectContext == nil) {
        managedObjectContext = [[NSManagedObjectContext alloc] init];
        [managedObjectContext setPersistentStoreCoordinator:self.persistentStoreCoordinator];
    }
    return managedObjectContext;
}

-(NSPersistentStoreCoordinator *)persistentStoreCoordinator {
    if (persistentStoreCoordinator == nil) {
        NSURL *storeUrl = [NSURL fileURLWithPath:self.persistentStorePath];
        persistentStoreCoordinator = [[NSPersistentStoreCoordinator alloc] initWithManagedObjectModel:[NSManagedObjectModel mergedModelFromBundles:nil]];
        NSError *error = nil;
        //NSPersistentStore *persistentStore = [persistentStoreCoordinator addPersistentStoreWithType:NSSQLiteStoreType configuration:nil URL:storeUrl options:nil error:&error];
        
        
        if (![persistentStoreCoordinator addPersistentStoreWithType:NSSQLiteStoreType configuration:nil URL:storeUrl options:@{NSMigratePersistentStoresAutomaticallyOption:@YES, NSInferMappingModelAutomaticallyOption:@YES} error:&error]) {
            [[NSFileManager defaultManager] removeItemAtURL:storeUrl error:nil];
            NSLog(@"Deleted old database %@, %@", error, [error userInfo]);
            [persistentStoreCoordinator addPersistentStoreWithType:NSSQLiteStoreType configuration:nil URL:storeUrl options:@{NSMigratePersistentStoresAutomaticallyOption:@YES} error:&error];
        }
        
        NSAssert3(persistentStoreCoordinator != nil, @"Unhandled error adding persistent store in %s at line %d: %@", __FUNCTION__, __LINE__, [error localizedDescription]);
    }
    return persistentStoreCoordinator;
}

-(NSString *)persistentStorePath {
    if (persistentStorePath == nil) {
        NSArray *paths = NSSearchPathForDirectoriesInDomains(NSDocumentDirectory, NSUserDomainMask, YES);
        NSString *documentsDirectory = [paths lastObject];
        persistentStorePath = [documentsDirectory stringByAppendingPathComponent:@"IVOIREKIOSK.sqlite"];
    }
    return persistentStorePath;
}

@end
