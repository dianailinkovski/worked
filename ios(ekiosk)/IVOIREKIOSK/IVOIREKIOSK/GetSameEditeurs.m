//
//  GetSameEditeurs.m
//  IVOIREKIOSK
//
//  Created by Maxime Julien-Paquet on 2013-12-24.
//  Copyright (c) 2013 Maxime Julien-Paquet. All rights reserved.
//

#import "GetSameEditeurs.h"

@implementation GetSameEditeurs


@synthesize idEditeur, delegate;

-(id)initWithIdEditeur:(NSString*)idEditeurRef {
    self = [super init];
    if (self) {
        // Custom initialization
        self.idEditeur = idEditeurRef;
    }
    return self;
}

-(void)main {
    NSUserDefaults *defaults = [NSUserDefaults standardUserDefaults];
    NSURLRequest *request = [NSURLRequest requestWithURL:[NSURL URLWithString:[NSString stringWithFormat:@"%@/getMemeEditeurs.php?id=%@&username=%@&password=%@", kAppBaseURL, self.idEditeur, [defaults objectForKey:@"username"], [defaults objectForKey:@"password"]]]];
    
    NSLog(@"%@/getMemeEditeurs.php?id=%@&username=%@&password=%@", kAppBaseURL, self.idEditeur, [defaults objectForKey:@"username"], [defaults objectForKey:@"password"]);
    NSData *response = [NSURLConnection sendSynchronousRequest:request returningResponse:nil error:nil];
    
    if (response == nil) {
        if (delegate && [delegate respondsToSelector:@selector(importerDidFailedOrNoInternet)]) {
            [delegate importerDidFailedOrNoInternet];
        }
        return;
    }
    
    NSError *jsonParsingError = nil;
    NSArray *publicTimeline = [NSJSONSerialization JSONObjectWithData:response options:0 error:&jsonParsingError];
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
    //NSLog(@"memeediteur = %@",publicTimeline);
    
    NSMutableArray *data = [[NSMutableArray alloc] init];
    
    NSDictionary *tempDic;
    
    for(int i=0; i<[[publicTimeline valueForKey:@"data"] count]; ++i) {
        tempDic = [[publicTimeline valueForKey:@"data"] objectAtIndex:i];
        
        [data addObject:tempDic];
        
    }
    
    if (delegate && [delegate respondsToSelector:@selector(importerDidFinishParsingData:)]) {
        [delegate importerDidFinishParsingData:data];
    }
    
    
}

@end
