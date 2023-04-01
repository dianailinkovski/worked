//
//  VerifAbonnementOperation.h
//  e-Kiosk
//
//  Created by Maxime Julien-Paquet on 2014-02-06.
//  Copyright (c) 2014 Maxime Julien-Paquet. All rights reserved.
//

#import <Foundation/Foundation.h>

/*
@protocol VerifAbonnementOperationDelegate <NSObject>

-(void)importerDidFinishParsingData:(NSMutableArray *)data;
-(void)importerDidFailedOrNoInternet;

@optional
-(void)importer:(NSOperation *)importer didFailWithError:(NSError *)error;

@end
*/

@interface VerifAbonnementOperation : NSOperation

//@property (nonatomic, strong) NSString *categorieString;
//@property (nonatomic, weak) __weak id <VerifAbonnementOperationDelegate> delegate;
@property (nonatomic, retain, readonly) NSManagedObjectContext *insertionContext;
@property (nonatomic, retain, readonly) NSEntityDescription *editionEntityDescription;

//-(id)initWithCategorie:(NSString*)categorie;

@end
