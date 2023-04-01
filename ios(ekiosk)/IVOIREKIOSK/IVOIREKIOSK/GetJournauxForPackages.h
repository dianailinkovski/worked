//
//  GetJournauxForPackages.h
//  IVOIREKIOSK
//
//  Created by Maxime Julien-Paquet on 2014-01-27.
//  Copyright (c) 2014 Maxime Julien-Paquet. All rights reserved.
//

#import <Foundation/Foundation.h>

@protocol GetJournauxForPackagesDelegate <NSObject>

-(void)importerDidFinishParsingData:(NSMutableArray *)data;
-(void)importerDidFailedOrNoInternet;

@optional
-(void)importer:(NSOperation *)importer didFailWithError:(NSError *)error;

@end

@interface GetJournauxForPackages : NSOperation

@property (nonatomic, strong) NSString *categorieString;
@property (nonatomic, weak) __weak id <GetJournauxForPackagesDelegate> delegate;

-(id)initWithCategorie:(NSString*)categorie;

@end
