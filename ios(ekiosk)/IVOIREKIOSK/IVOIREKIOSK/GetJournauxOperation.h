//
//  GetJournauxOperation.h
//  IVOIREKIOSK
//
//  Created by Maxime Julien-Paquet on 2014-01-17.
//  Copyright (c) 2014 Maxime Julien-Paquet. All rights reserved.
//

#import <Foundation/Foundation.h>

@protocol GetJournauxOperationDelegate <NSObject>

-(void)importerDidFinishParsingData:(NSMutableArray *)data;
-(void)importerDidFailedOrNoInternet;

@optional
-(void)importer:(NSOperation *)importer didFailWithError:(NSError *)error;

@end

@interface GetJournauxOperation : NSOperation

@property (nonatomic, weak) __weak id <GetJournauxOperationDelegate> delegate;

@end
