//
//  GetSameEditeurs.h
//  IVOIREKIOSK
//
//  Created by Maxime Julien-Paquet on 2013-12-24.
//  Copyright (c) 2013 Maxime Julien-Paquet. All rights reserved.
//

#import <UIKit/UIKit.h>

@protocol GetSameEditeursDelegate <NSObject>

-(void)importerDidFinishParsingData:(NSMutableArray *)data;
-(void)importerDidFailedOrNoInternet;

@optional
-(void)importer:(NSOperation *)importer didFailWithError:(NSError *)error;

@end

@interface GetSameEditeurs : NSOperation

@property (nonatomic, strong) NSString *idEditeur;
@property (nonatomic, weak) __weak id <GetSameEditeursDelegate> delegate;

-(id)initWithIdEditeur:(NSString*)idEditeurRef;

@end
