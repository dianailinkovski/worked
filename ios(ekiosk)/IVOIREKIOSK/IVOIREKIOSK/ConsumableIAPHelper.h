//
//  ConsumableIAPHelper.h
//  eKiosk
//
//  Created by Maxime Julien-Paquet on 2014-02-19.
//  Copyright (c) 2014 Maxime Julien-Paquet. All rights reserved.
//

#import "IAPHelper.h"

@interface ConsumableIAPHelper : IAPHelper

+ (ConsumableIAPHelper *)sharedInstance;
+ (ConsumableIAPHelper *)sharedInstanceWithProduct:(NSSet*)productIdentifiers;

@end
