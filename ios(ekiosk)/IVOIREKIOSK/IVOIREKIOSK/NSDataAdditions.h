//
//  NSDataAdditions.h
//  IVOIREKIOSK
//
//  Created by Maxime Julien-Paquet on 2014-01-30.
//  Copyright (c) 2014 Maxime Julien-Paquet. All rights reserved.
//

#import <Foundation/Foundation.h>

@interface NSData (NSDataAdditions)

+ (NSData *) base64DataFromString:(NSString *)string;

@end
