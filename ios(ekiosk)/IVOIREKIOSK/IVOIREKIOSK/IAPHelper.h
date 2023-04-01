//
//  IAPHelper.h
//  IVOIREKIOSK
//
//  Created by Maxime Julien-Paquet on 2014-01-29.
//  Copyright (c) 2014 Maxime Julien-Paquet. All rights reserved.
//

#import <Foundation/Foundation.h>
#import <StoreKit/StoreKit.h>

UIKIT_EXTERN NSString *const IAPHelperProductPurchasedEditionNotification;
UIKIT_EXTERN NSString *const IAPHelperProductPurchasedAbonnementNotification;
UIKIT_EXTERN NSString *const IAPHelperPurchasedEndNotification;

typedef void (^RequestProductsCompletionHandler)(BOOL success, NSArray * products);
typedef void (^BuyProductCompletionHandler)(BOOL success, SKPaymentTransaction * transaction);

@interface IAPHelper : NSObject

- (id)initWithProductIdentifiers:(NSSet *)productIdentifiers;
- (void)requestProductsWithCompletionHandler:(RequestProductsCompletionHandler)completionHandler;
- (void)requestProductsWithCompletionHandler:(RequestProductsCompletionHandler)completionHandler ForProductIdentifier:(NSSet *)productIdentifiers;

// Add two new method declarations
- (void)buyProduct:(SKProduct *)product WithCompletionHandler:(BuyProductCompletionHandler)completionBuyHandler;
- (void)buyProduct:(SKProduct *)product WithData:(NSMutableArray*)data;
- (BOOL)productPurchased:(NSString *)productIdentifier;


- (void)restoreCompletedTransactions;
- (void)validateReceiptForTransaction:(SKPaymentTransaction *)transaction WithData:(NSMutableArray*)dataArray;

@end
