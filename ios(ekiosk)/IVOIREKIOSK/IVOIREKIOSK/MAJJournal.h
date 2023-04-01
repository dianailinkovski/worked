//
//  MAJJournal.h
//  IVOIREKIOSK
//
//  Created by Maxime Julien-Paquet on 2013-12-11.
//  Copyright (c) 2013 Maxime Julien-Paquet. All rights reserved.
//

#import <UIKit/UIKit.h>

@class Editions;

@protocol MAJSaisonsDelegate <NSObject>

-(void)importerDidFinishParsingData:(NSMutableArray *)data;
-(void)importerDidFailedOrNoInternet;

@optional
-(void)importer:(NSOperation *)importer didFailWithError:(NSError *)error;

@end

@interface MAJJournal : NSOperation {
    NSManagedObjectContext *insertionContext;
    NSEntityDescription *journalEntityDescription;
    NSURL *journalsURL;
    
}

@property (nonatomic, retain) NSURL *journalsURL;
@property (nonatomic, assign) id <MAJSaisonsDelegate> delegate;
@property (nonatomic, retain, readonly) NSManagedObjectContext *insertionContext;
@property (nonatomic, retain, readonly) NSEntityDescription *editionEntityDescription;

@end
