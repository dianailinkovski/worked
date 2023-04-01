//
//  ConsumableIAPHelper.m
//  eKiosk
//
//  Created by Maxime Julien-Paquet on 2014-02-19.
//  Copyright (c) 2014 Maxime Julien-Paquet. All rights reserved.
//

#import "ConsumableIAPHelper.h"

@implementation ConsumableIAPHelper

+ (ConsumableIAPHelper *)sharedInstance {
    static dispatch_once_t once;
    static ConsumableIAPHelper * sharedInstance;
    dispatch_once(&once, ^{
        sharedInstance = [[self alloc] init];
    });
    return sharedInstance;
}

+ (ConsumableIAPHelper *)sharedInstanceWithProduct:(NSSet*)productIdentifiers {
    static dispatch_once_t once;
    static ConsumableIAPHelper * sharedInstance;
    dispatch_once(&once, ^{
//        NSSet * productIdentifiers = [NSSet setWithObjects:
//                                      @"com.razeware.inapprage.drummerrage",
//                                      @"com.razeware.inapprage.itunesconnectrage",
//                                      @"com.razeware.inapprage.nightlyrage",
//                                      @"com.razeware.inapprage.studylikeaboss",
//                                      @"com.razeware.inapprage.updogsadness",
//                                      nil];
        sharedInstance = [[self alloc] initWithProductIdentifiers:productIdentifiers];
    });
    return sharedInstance;
}

@end
