//
//  DownloadOperation.m
//  IVOIREKIOSK
//
//  Created by Maxime Julien-Paquet on 2014-01-02.
//  Copyright (c) 2014 Maxime Julien-Paquet. All rights reserved.
//

#import "DownloadOperation.h"
#import "AppDelegate.h"
#import "Editions.h"
#import "SSZipArchive.h"

@implementation DownloadOperation

@synthesize managedObjectContext, delegate;

-(id)initWithEdition:(Editions*)data AtIndexPath:(NSIndexPath*)indexPath {
    self = [super init];
    if (self) {
        // Custom initialization
        self.edition = data;
        self.indexPath = indexPath;
    }
    return self;
}
/*
- (void)mergeChanges:(NSNotification *)notification;
{
    //AppDelegate *appDelegate = [[NSApplication sharedApplication] delegate];
    NSManagedObjectContext *mainContext = [(AppDelegate*)[[UIApplication sharedApplication] delegate] managedObjectContext];
    
    // Merge changes into the main context on the main thread
    [mainContext performSelectorOnMainThread:@selector(mergeChangesFromContextDidSaveNotification:)
                                  withObject:notification
                               waitUntilDone:YES];
}
*/
-(void)main {
    // Register context with the notification center
    NSUserDefaults *defaults = [NSUserDefaults standardUserDefaults];
    NSString *username = [defaults objectForKey:@"username"];
    NSString *password = [defaults objectForKey:@"password"];
    
    if (username == nil || password == nil) {
        username = @"";
        password = @"";
    }
    
    done = NO;
    
    managedObjectContext = [[NSManagedObjectContext alloc] init];
    [managedObjectContext setUndoManager:nil];
    [managedObjectContext setPersistentStoreCoordinator:[(AppDelegate*)[[UIApplication sharedApplication] delegate] persistentStoreCoordinator]];
    
    /*
    NSNotificationCenter *nc = [NSNotificationCenter defaultCenter];
    [nc addObserver:self
           selector:@selector(mergeChanges:)
               name:NSManagedObjectContextDidSaveNotification
             object:managedObjectContext];
    */
    
    
    //NSURL *contentDownloadURL = [self contentURL];
    
    // ***
    if (self.isCancelled) return;
    // ***
    
    
    
    //NSURLRequest *request = [NSURLRequest requestWithURL:[NSURL URLWithString:self.edition.downloadpath]];
    
    NSURLRequest *request;
    if (self.edition.isSubscription)
    {
        request= [NSURLRequest requestWithURL:
                  [NSURL URLWithString:
                   [NSString stringWithFormat:@"%@/getEditionDownload.php?username=%@&password=%@&editionid=%d&subscription=1",kAppBaseURL, username, password, [self.edition.id intValue]]]];
    }
    else
    {
        request= [NSURLRequest requestWithURL:
                  [NSURL URLWithString:
                   [NSString stringWithFormat:@"%@/getEditionDownload.php?username=%@&password=%@&editionid=%d",kAppBaseURL, username, password, [self.edition.id intValue]]]];
    
        NSLog(@"%@/getEditionDownload.php?username=%@&password=%@&editionid=%d",kAppBaseURL, username, password, [self.edition.id intValue]);

    }
    
    
    urlconnection = [[NSURLConnection alloc] initWithRequest:request delegate:self startImmediately:NO];
    [urlconnection scheduleInRunLoop:[NSRunLoop mainRunLoop]
                          forMode:NSDefaultRunLoopMode];
    [urlconnection start];
    //[urlconnection start];
    //NSLog(@"%@",urlconnection);
    //NSData *response = [NSURLConnection sendSynchronousRequest:request returningResponse:nil error:nil];
    
    // ***
    if (self.isCancelled) return;
    // ***
    
    NSRunLoop* currentRunLoop = [NSRunLoop currentRunLoop];
    if ( currentRunLoop ) {
        
        while ( !done && ![self isCancelled] ) {
            // Run the RunLoop!
            NSDate* dateLimit = [[NSDate date] dateByAddingTimeInterval:0.1];
            [currentRunLoop runUntilDate:dateLimit];
        }
        
    }
    
    //NSLog(@"fin main");
    
}

-(NSURL *)contentURL {
    NSURL *theURL;

    theURL = [NSURL fileURLWithPath:[CacheDirectory stringByAppendingPathComponent:[NSString stringWithFormat:@"%d",[self.edition.id intValue]]]];
    
    if([[NSFileManager defaultManager] fileExistsAtPath:[theURL path]]==NO) {
        //NSLog(@"Creating content directory: %@",[theURL path]);
        NSError *error=nil;
        if([[NSFileManager defaultManager] createDirectoryAtPath:[theURL path] withIntermediateDirectories:NO attributes:nil error:&error]==NO) {
            NSLog(@"There was an error in creating the directory: %@",error);
        }
        
    }
    // returns the url
    return theURL;
}

-(void)updateCoreData:(NSString*)finalPath {
    
    NSFetchRequest *request = [[NSFetchRequest alloc] init];
    [request setEntity:[NSEntityDescription entityForName:@"Editions" inManagedObjectContext:managedObjectContext]];
    
    NSError *error = nil;
    
    
    NSLog(@"id == %d",[self.edition.id intValue]);
    NSPredicate *predicate = [NSPredicate predicateWithFormat:@"id == %d", [self.edition.id intValue]];
    [request setPredicate:predicate];
    
    NSArray *results = [managedObjectContext executeFetchRequest:request error:&error];
    
    Editions *tempEdition = [results objectAtIndex:0];
    tempEdition.downloaddate = [NSDate new];
    tempEdition.localpath = finalPath;
    [managedObjectContext save:nil];
    
    
    //NSLog(@"error = %@",error);
    
}


#pragma mark NSURLConnection Delegate Methods

- (void)connection:(NSURLConnection *)connection didReceiveResponse:(NSURLResponse *)response {
    // A response has been received, this is where we initialize the instance var you created
    // so that we can append data to it in the didReceiveData method
    // Furthermore, this method is called each time there is a redirect so reinitializing it
    // also serves to clear it
    _responseData = [[NSMutableData alloc] init];
    expectedLength = [response expectedContentLength];
    //NSLog(@"expectedLength = %lld",expectedLength);
}

- (void)connection:(NSURLConnection *)connection didReceiveData:(NSData *)data {
    
    
    if (self.isCancelled) [connection cancel];
    
    // Append the new data to the instance variable you declared
    [_responseData appendData:data];
    long long downloaded = [_responseData length];
    float pourcentage = (double)((double)downloaded / (double)expectedLength);
    
    //NSLog(@"pourcentage = %f",pourcentage);
    
    if (delegate && [delegate respondsToSelector:@selector(downloadProgress:AtIndexPath:)]) {
        //[delegate downloadComplete];
        [delegate downloadProgress:pourcentage AtIndexPath:self.indexPath];
    }
}

- (NSCachedURLResponse *)connection:(NSURLConnection *)connection
                  willCacheResponse:(NSCachedURLResponse*)cachedResponse {
    // Return nil to indicate not necessary to store a cached response for this connection
    return nil;
}

- (void)connectionDidFinishLoading:(NSURLConnection *)connection {
    // The request is complete and data has been received
    // You can parse the stuff in your instance variable now
    
    
    NSString *zipPath = [NSString stringWithFormat:@"%@/issues-%d.zip",[[self contentURL] path], [[self.edition id] intValue]];
    NSLog(@"%@", zipPath);
    [_responseData writeToFile:zipPath atomically:NO];
    
    NSURL *finalURL = [self contentURL];
    
    [SSZipArchive unzipFileAtPath:zipPath toDestination:[finalURL path]];
    [[NSFileManager defaultManager] removeItemAtPath:zipPath error:NULL];
    
    [self updateCoreData:[finalURL path]];
    
    if (delegate && [delegate respondsToSelector:@selector(downloadCompleteAtIndexPath:)]) {
        [delegate downloadCompleteAtIndexPath:self.indexPath];
    }
    done = YES;
}

- (void)connection:(NSURLConnection *)connection didFailWithError:(NSError *)error {
    // The request has failed for some reason!
    // Check the error var
    done = YES;
}

@end
