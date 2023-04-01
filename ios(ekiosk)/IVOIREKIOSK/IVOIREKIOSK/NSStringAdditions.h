//
//  NSStringAdditions.h
//  IVOIREKIOSK
//
//  Created by Maxime Julien-Paquet on 2014-01-30.
//  Copyright (c) 2014 Maxime Julien-Paquet. All rights reserved.
//

#import <Foundation/Foundation.h>

@interface NSString (NSStringAdditions)

+ (NSString *) base64StringFromData:(NSData *)data length:(int)length;

@end
