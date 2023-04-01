//
//  IAPHelper.m
//  IVOIREKIOSK
//
//  Created by Maxime Julien-Paquet on 2014-01-29.
//  Copyright (c) 2014 Maxime Julien-Paquet. All rights reserved.
//

#import "IAPHelper.h"
//#import "NSStringAdditions.h"
//#import "NSDataAdditions.h"
#import <StoreKit/StoreKit.h>
#import "NSData+MKBase64.h"

#define kSharedSecret @"2e3013503b56480ba27122c398c9e74d"

//NSString *const IAPHelperProductPurchasedNotification = @"IAPHelperProductPurchasedNotification";
NSString *const IAPHelperPurchasedEndNotification = @"IAPHelperPurchasedEndNotification";
NSString *const IAPHelperProductPurchasedEditionNotification = @"IAPHelperProductPurchasedEditionNotification";
NSString *const IAPHelperProductPurchasedAbonnementNotification = @"IAPHelperProductPurchasedAbonnementNotification";

@interface IAPHelper () <SKProductsRequestDelegate, SKPaymentTransactionObserver>
@end

@implementation IAPHelper {
    SKProductsRequest * _productsRequest;
    
    RequestProductsCompletionHandler _completionHandler;
    BuyProductCompletionHandler _completionBuyHandler;
    NSSet * _productIdentifiers;
    NSMutableSet * _purchasedProductIdentifiers;
    
    NSMutableArray *_dataArray;
    
}

-(id)init {
    if (self = [super init]) {
        [[SKPaymentQueue defaultQueue] addTransactionObserver:self];
    }
    return self;
}

- (id)initWithProductIdentifiers:(NSSet *)productIdentifiers {
    NSLog(@"init");
    if ((self = [super init])) {
        [[SKPaymentQueue defaultQueue] addTransactionObserver:self];
        
        // Store product identifiers
        _productIdentifiers = productIdentifiers;
        
        // Check for previously purchased products
        _purchasedProductIdentifiers = [NSMutableSet set];
        for (NSString * productIdentifier in _productIdentifiers) {
            BOOL productPurchased = [[NSUserDefaults standardUserDefaults] boolForKey:productIdentifier];
            if (productPurchased) {
                [_purchasedProductIdentifiers addObject:productIdentifier];
                NSLog(@"Previously purchased: %@", productIdentifier);
            } else {
                NSLog(@"Not purchased: %@", productIdentifier);
            }
        }
        
    }
    return self;
}

- (void)requestProductsWithCompletionHandler:(RequestProductsCompletionHandler)completionHandler {
    
    // 1
    _completionHandler = [completionHandler copy];
    
    // 2
    _productsRequest = [[SKProductsRequest alloc] initWithProductIdentifiers:_productIdentifiers];
    _productsRequest.delegate = self;
    [_productsRequest start];
    
}

- (void)requestProductsWithCompletionHandler:(RequestProductsCompletionHandler)completionHandler ForProductIdentifier:(NSSet *)productIdentifiers {
    
    _productIdentifiers = productIdentifiers;
    
    // 1
    _completionHandler = [completionHandler copy];
    
    // 2
    _productsRequest = [[SKProductsRequest alloc] initWithProductIdentifiers:_productIdentifiers];
    _productsRequest.delegate = self;
    [_productsRequest start];
    
}

#pragma mark - SKProductsRequestDelegate

- (void)productsRequest:(SKProductsRequest *)request didReceiveResponse:(SKProductsResponse *)response {
    
    NSLog(@"Loaded list of products...");
    _productsRequest = nil;
    
    NSArray * skProducts = response.products;
    if (skProducts == nil) {
        NSLog(@"Loaded list = nil");
        _completionHandler(NO, nil);
        _completionHandler = nil;
        return;
    }
    int x = 0;
    for (SKProduct * skProduct in skProducts) {
        ++x;
        NSLog(@"Found product: %@ %@ %0.2f",
              skProduct.productIdentifier,
              skProduct.localizedTitle,
              skProduct.price.floatValue);
    }
    if (x == 0) {
        NSLog(@"Loaded list = 0");
        _completionHandler(NO, nil);
        _completionHandler = nil;
        return;
    }
    _completionHandler(YES, skProducts);
    _completionHandler = nil;
    
}

- (void)request:(SKRequest *)request didFailWithError:(NSError *)error {
    
    NSLog(@"Failed to load list of products.");
    _productsRequest = nil;
    
    _completionHandler(NO, nil);
    _completionHandler = nil;
    
}

- (BOOL)productPurchased:(NSString *)productIdentifier {
    return [_purchasedProductIdentifiers containsObject:productIdentifier];
}

- (void)buyProduct:(SKProduct *)product WithCompletionHandler:(BuyProductCompletionHandler)completionBuyHandler {
    _completionBuyHandler = [completionBuyHandler copy];
    NSLog(@"Buying %@...", product.productIdentifier);
    
    SKPayment * payment = [SKPayment paymentWithProduct:product];
    [[SKPaymentQueue defaultQueue] addPayment:payment];
    
}

- (void)buyProduct:(SKProduct *)product WithData:(NSMutableArray*)data {
    _dataArray = data;
    
    NSLog(@"Buying %@...", product.productIdentifier);
    
    SKPayment * payment = [SKPayment paymentWithProduct:product];
    [[SKPaymentQueue defaultQueue] addPayment:payment];
    
}

- (void)paymentQueue:(SKPaymentQueue *)queue updatedTransactions:(NSArray *)transactions {
    for (SKPaymentTransaction * transaction in transactions) {
        switch (transaction.transactionState)
        {
            case SKPaymentTransactionStatePurchased:
                [self completeTransaction:transaction];
                break;
            case SKPaymentTransactionStateFailed:
                [self failedTransaction:transaction];
                break;
            case SKPaymentTransactionStateRestored:
                [self restoreTransaction:transaction];
            default:
                break;
        }
        
    };
}

- (void)completeTransaction:(SKPaymentTransaction *)transaction {
    NSLog(@"completeTransaction...");
    
    //[self provideContentForProductIdentifier:transaction.payment.productIdentifier];
    
    if (_completionBuyHandler != nil) {
        _completionBuyHandler(YES, transaction);
        _completionBuyHandler = nil;
    }
    //else {
    //    [self validateReceiptForTransaction:transaction WithData:_dataArray];
    //}
    [[SKPaymentQueue defaultQueue] finishTransaction: transaction];
    //[[NSNotificationCenter defaultCenter] postNotificationName:IAPHelperPurchasedEndNotification object:@YES userInfo:nil];
    //[[SKPaymentQueue defaultQueue] removeTransactionObserver:self];
}

- (void)restoreTransaction:(SKPaymentTransaction *)transaction {
    NSLog(@"restoreTransaction...");
    
    //[self provideContentForProductIdentifier:transaction.originalTransaction.payment.productIdentifier];
    //[self validateReceiptForTransaction:transaction WithData:_dataArray];
    //[[SKPaymentQueue defaultQueue] finishTransaction:transaction];
}

- (void)failedTransaction:(SKPaymentTransaction *)transaction {
    
    NSLog(@"failedTransaction...");
    if (transaction.error.code != SKErrorPaymentCancelled)
    {
        NSLog(@"Transaction error: %@", transaction.error.localizedDescription);
    }
    NSLog(@"%@",transaction.error);
    [[SKPaymentQueue defaultQueue] finishTransaction: transaction];
    
    //[[NSNotificationCenter defaultCenter] postNotificationName:IAPHelperPurchasedEndNotification object:@NO userInfo:nil];
    
    if (_completionBuyHandler != nil) {
        _completionBuyHandler(NO, transaction);
        _completionBuyHandler = nil;
    }
    
}

- (void)provideContentForProductIdentifier:(NSString *)productIdentifier {
    NSLog(@"avant");
    NSLog(@"productIdentifier = %@",productIdentifier);
    [_purchasedProductIdentifiers addObject:productIdentifier];
    NSLog(@"apres");
    [[NSUserDefaults standardUserDefaults] setBool:YES forKey:productIdentifier];
    [[NSUserDefaults standardUserDefaults] synchronize];
    //[[NSNotificationCenter defaultCenter] postNotificationName:IAPHelperProductPurchasedNotification object:productIdentifier userInfo:nil];
    
    
}

- (void)restoreCompletedTransactions {
    [[SKPaymentQueue defaultQueue] restoreCompletedTransactions];
}



@end
