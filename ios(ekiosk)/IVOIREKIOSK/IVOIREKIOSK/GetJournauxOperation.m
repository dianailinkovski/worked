//
//  GetJournauxOperation.m
//  IVOIREKIOSK
//
//  Created by Maxime Julien-Paquet on 2014-01-17.
//  Copyright (c) 2014 Maxime Julien-Paquet. All rights reserved.
//

#import "GetJournauxOperation.h"

@implementation GetJournauxOperation

@synthesize delegate;

-(void)main {
    NSUserDefaults *defaults = [NSUserDefaults standardUserDefaults];
    NSURLRequest *request = [NSURLRequest requestWithURL:[NSURL URLWithString:[NSString stringWithFormat:@"%@/getJournauxArchive.php?username=%@&password=%@", kAppBaseURL, [defaults objectForKey:@"username"], [defaults objectForKey:@"password"]]]];
    
    
    NSData *response = [NSURLConnection sendSynchronousRequest:request returningResponse:nil error:nil];
    
    if (response == nil) {
        if (delegate && [delegate respondsToSelector:@selector(importerDidFailedOrNoInternet)]) {
            [delegate importerDidFailedOrNoInternet];
        }
        return;
    }
    
    NSError *jsonParsingError = nil;
    NSMutableArray *publicTimeline = [NSJSONSerialization JSONObjectWithData:response options:NSJSONReadingMutableContainers error:&jsonParsingError];
    
    if (publicTimeline == nil) {
        NSString *dataString = [[NSString alloc] initWithData:response encoding:NSUTF8StringEncoding];
        NSLog(@"dataString = %@", dataString);
        UIAlertView *alert = [[UIAlertView alloc] initWithTitle:@"Erreur" message:dataString delegate:nil cancelButtonTitle:@"Retour" otherButtonTitles:nil];
        [alert performSelectorOnMainThread:@selector(show) withObject:nil waitUntilDone:YES];
        if (delegate && [delegate respondsToSelector:@selector(importerDidFailedOrNoInternet)]) {
            [delegate importerDidFailedOrNoInternet];
        }
        return;
    }
    
    if (delegate && [delegate respondsToSelector:@selector(importerDidFinishParsingData:)]) {
        [delegate importerDidFinishParsingData:[publicTimeline valueForKey:@"data"]];
    }
    
    
}

@end
