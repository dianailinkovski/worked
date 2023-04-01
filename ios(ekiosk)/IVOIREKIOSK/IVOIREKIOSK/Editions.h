//
//  Editions.h
//  IVOIREKIOSK
//
//  Created by Maxime Julien-Paquet on 2014-01-16.
//  Copyright (c) 2014 Maxime Julien-Paquet. All rights reserved.
//

#import <Foundation/Foundation.h>
#import <CoreData/CoreData.h>


@interface Editions : NSManagedObject

@property (nonatomic, retain) NSString * categorie;
@property (nonatomic, retain) NSData * coverImage;
@property (nonatomic, retain) NSString * coverpath;
@property (nonatomic, retain) NSDate * downloaddate;
@property (nonatomic, retain) NSString * downloadpath;
@property (nonatomic, retain) NSNumber * favoris;
@property (nonatomic, retain) NSNumber * id;
@property (nonatomic, retain) NSNumber * idjournal;
@property (nonatomic, retain) NSString * localpath;
@property (nonatomic, retain) NSNumber * lu;
@property (nonatomic, retain) NSString * nom;
@property (nonatomic, retain) NSDate * publicationdate;
@property (nonatomic, retain) NSString * type;
@property (nonatomic, retain) NSDate * openDate;
@property (nonatomic, assign) BOOL  isSubscription;

@end
