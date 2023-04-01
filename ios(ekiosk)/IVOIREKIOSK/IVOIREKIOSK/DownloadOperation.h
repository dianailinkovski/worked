//
//  DownloadOperation.h
//  IVOIREKIOSK
//
//  Created by Maxime Julien-Paquet on 2014-01-02.
//  Copyright (c) 2014 Maxime Julien-Paquet. All rights reserved.
//

#import <UIKit/UIKit.h>

@class Editions;

@protocol DownloadOperationDelegate;

@interface DownloadOperation : NSOperation <NSURLConnectionDelegate> {
    NSManagedObjectContext *managedObjectContext;
    NSMutableData *_responseData;
    long long expectedLength;
    NSURLConnection *urlconnection;
    BOOL done;
}

@property (nonatomic, weak) __weak id <DownloadOperationDelegate> delegate;
@property (nonatomic, strong) Editions *edition;
@property (nonatomic, retain, readonly) NSManagedObjectContext *managedObjectContext;
@property (nonatomic, strong) NSIndexPath *indexPath;

-(id)initWithEdition:(Editions*)data AtIndexPath:(NSIndexPath*)indexPath;

@end

@protocol DownloadOperationDelegate <NSObject>

-(void)downloadProgress:(float)progression AtIndexPath:(NSIndexPath*)indexPath;
-(void)downloadCompleteAtIndexPath:(NSIndexPath *)indexPath;

@end