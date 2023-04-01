//
//  GetJournauxForPackages.m
//  IVOIREKIOSK
//
//  Created by Maxime Julien-Paquet on 2014-01-27.
//  Copyright (c) 2014 Maxime Julien-Paquet. All rights reserved.
//

#import "GetJournauxForPackages.h"

@implementation GetJournauxForPackages

@synthesize categorieString, delegate;

-(id)initWithCategorie:(NSString*)categorie {
    self = [super init];
    if (self) {
        // Custom initialization
        self.categorieString = categorie;
    }
    return self;
}

-(void)main {
    NSString *urlText = [NSString stringWithFormat:@"%@/getJournauxParAbonnements.php?categorie=%@",kAppBaseURL ,self.categorieString];
    NSString* urlTextEscaped = [urlText stringByAddingPercentEscapesUsingEncoding:NSUTF8StringEncoding];
    
    NSURLRequest *request = [NSURLRequest requestWithURL:[NSURL URLWithString:urlTextEscaped]];
    NSData *response = [NSURLConnection sendSynchronousRequest:request returningResponse:nil error:nil];

    if (response == nil) {
        if (delegate && [delegate respondsToSelector:@selector(importerDidFailedOrNoInternet)]) {
            [delegate importerDidFailedOrNoInternet];
        }
        return;
    }
    
    NSError *jsonParsingError = nil;
    NSArray *publicTimeline = [NSJSONSerialization JSONObjectWithData:response options:NSJSONReadingMutableContainers error:&jsonParsingError];
    
    NSMutableArray *data = [[NSMutableArray alloc] init];
    
    NSMutableDictionary *tempDic;
    
    for(int i=0; i<[[publicTimeline valueForKey:@"data"] count]; ++i) {
        tempDic = [[publicTimeline valueForKey:@"data"] objectAtIndex:i];
        
        [data addObject:tempDic];
        
    }
    
    if (delegate && [delegate respondsToSelector:@selector(importerDidFinishParsingData:)]) {
        [delegate importerDidFinishParsingData:data];
    }
    
}

@end
