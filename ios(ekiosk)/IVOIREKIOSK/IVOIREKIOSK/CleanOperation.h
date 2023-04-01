//
//  CleanOperation.h
//  IVOIREKIOSK
//
//  Created by Maxime Julien-Paquet on 2014-01-19.
//  Copyright (c) 2014 Maxime Julien-Paquet. All rights reserved.
//

#import <Foundation/Foundation.h>
#import "Editions.h"

@interface CleanOperation : NSOperation

//@property (nonatomic, strong) Editions *edition;
@property (nonatomic, retain, readonly) NSManagedObjectContext *managedObjectContext;

@end
