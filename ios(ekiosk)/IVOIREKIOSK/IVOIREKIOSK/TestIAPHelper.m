//
//  test.m
//  IVOIREKIOSK
//
//  Created by Maxime Julien-Paquet on 2014-02-05.
//  Copyright (c) 2014 Maxime Julien-Paquet. All rights reserved.
//

#import "TestIAPHelper.h"
#import "NSData+MKBase64.h"

@implementation TestIAPHelper

+ (TestIAPHelper *)sharedInstance {
    static dispatch_once_t once;
    static TestIAPHelper * sharedInstance;
    dispatch_once(&once, ^{
        sharedInstance = [[self alloc] init];
    });
    return sharedInstance;
}

- (void)validateReceiptForTransaction:(SKPaymentTransaction *)transaction WithData:(NSMutableArray*)dataArray {
    NSLog(@"SKPaymentTransaction = %@",transaction.payment.productIdentifier);
    NSString *productIdentifier = transaction.payment.productIdentifier;
    
    if ([productIdentifier rangeOfString:@"subscription"].location == NSNotFound) {
        [self validateReceiptForTransactionSingle:transaction WithData:dataArray];
    }
    else {
        [self validateReceiptForTransactionAbonnement:transaction WithData:dataArray];
    }
}

- (void)validateReceiptForTransactionSingle:(SKPaymentTransaction *)transaction WithData:(NSMutableArray*)dataArray {
    NSLog(@"Single transaction");
    NSURL *url = [NSURL URLWithString:[NSString stringWithFormat:@"http://gnetix.com/iphone/ngser/api/in-app/verifyProduct.php"]];
	
	NSMutableURLRequest *theRequest = [NSMutableURLRequest requestWithURL:url
                                                              cachePolicy:NSURLRequestReloadIgnoringCacheData
                                                          timeoutInterval:60];
	
	[theRequest setHTTPMethod:@"POST"];
	[theRequest setValue:@"application/x-www-form-urlencoded" forHTTPHeaderField:@"Content-Type"];
	
	NSString *receiptDataString = [transaction.transactionReceipt base64EncodedString];
    NSLog(@"dataArray IAP = %@", dataArray);
	NSString *postData = [NSString stringWithFormat:@"receiptdata=%@", receiptDataString];
    if (dataArray != nil) {
        NSString * test = [[NSString alloc] initWithData:[NSJSONSerialization dataWithJSONObject:dataArray options:0 error:nil] encoding:NSUTF8StringEncoding];
        postData = [postData stringByAppendingFormat:@"&data=%@", test];
    }
    else {
        NSUserDefaults *defaults = [NSUserDefaults standardUserDefaults];
        NSString * test = [[NSString alloc] initWithData:[NSJSONSerialization dataWithJSONObject:[NSMutableArray arrayWithObjects:[defaults valueForKey:@"username"], [defaults valueForKey:@"password"], nil] options:0 error:nil] encoding:NSUTF8StringEncoding];
        postData = [postData stringByAppendingFormat:@"&data=%@", test];
    }
    
    
    
	NSString *length = [NSString stringWithFormat:@"%d", [postData length]];
	[theRequest setValue:length forHTTPHeaderField:@"Content-Length"];
	
	[theRequest setHTTPBody:[postData dataUsingEncoding:NSASCIIStringEncoding]];
    
    NSError        *error = nil;
    NSURLResponse  *response = nil;
    
    NSData *data = [NSURLConnection sendSynchronousRequest:theRequest returningResponse:&response error:&error];
    NSString *responseString = [[NSString alloc] initWithData:data encoding:NSUTF8StringEncoding];
    NSLog(@"data = %@", responseString);
    
    if([responseString isEqualToString:@"YES"]) {
        [[SKPaymentQueue defaultQueue] finishTransaction:transaction];
        if (dataArray != nil && [dataArray count] > 0) {
            //[[NSNotificationCenter defaultCenter] postNotificationName:IAPHelperProductPurchasedEditionNotification object:transaction.payment.productIdentifier userInfo:nil];
            [[NSNotificationCenter defaultCenter] postNotificationName:IAPHelperProductPurchasedEditionNotification object:@YES userInfo:nil];
        }
        
    }
    else {
        [[SKPaymentQueue defaultQueue] finishTransaction:transaction];
        [[[UIAlertView alloc] initWithTitle:@"Erreur lors de l'achat" message:responseString delegate:nil cancelButtonTitle:@"Retour" otherButtonTitles:nil] show];
    }
    
}

- (void)validateReceiptForTransactionAbonnement:(SKPaymentTransaction *)transaction WithData:(NSMutableArray*)dataArray {
    NSLog(@"Abonnement transaction");
    NSLog(@"transaction = %@",transaction.transactionIdentifier);
    NSURL *url = [NSURL URLWithString:[NSString stringWithFormat:@"http://gnetix.com/iphone/ngser/api/in-app/verifyProductAbonnement.php"]];
	
	NSMutableURLRequest *theRequest = [NSMutableURLRequest requestWithURL:url
                                                              cachePolicy:NSURLRequestReloadIgnoringCacheData
                                                          timeoutInterval:60];
	
	[theRequest setHTTPMethod:@"POST"];
	[theRequest setValue:@"application/x-www-form-urlencoded" forHTTPHeaderField:@"Content-Type"];
	
	NSString *receiptDataString = [transaction.transactionReceipt base64EncodedString];
    NSLog(@"dataArray IAP = %@", dataArray);
	NSString *postData = [NSString stringWithFormat:@"receiptdata=%@", receiptDataString];
    if (dataArray != nil) {
        NSString * test = [[NSString alloc] initWithData:[NSJSONSerialization dataWithJSONObject:dataArray options:0 error:nil] encoding:NSUTF8StringEncoding];
        postData = [postData stringByAppendingFormat:@"&data=%@", test];
    }
    else {
        NSUserDefaults *defaults = [NSUserDefaults standardUserDefaults];
        NSString * test = [[NSString alloc] initWithData:[NSJSONSerialization dataWithJSONObject:[NSMutableArray arrayWithObjects:[defaults valueForKey:@"username"], [defaults valueForKey:@"password"], nil] options:0 error:nil] encoding:NSUTF8StringEncoding];
        postData = [postData stringByAppendingFormat:@"&data=%@", test];
    }
    
    
    
	NSString *length = [NSString stringWithFormat:@"%d", [postData length]];
	[theRequest setValue:length forHTTPHeaderField:@"Content-Length"];
	
	[theRequest setHTTPBody:[postData dataUsingEncoding:NSASCIIStringEncoding]];
    
    NSError        *error = nil;
    NSURLResponse  *response = nil;
    
    NSData *data = [NSURLConnection sendSynchronousRequest:theRequest returningResponse:&response error:&error];
    NSString *responseString = [[NSString alloc] initWithData:data encoding:NSUTF8StringEncoding];
    NSLog(@"data = %@", responseString);
    
    if([responseString isEqualToString:@"YES"]) {
        [[SKPaymentQueue defaultQueue] finishTransaction:transaction];
        if (dataArray != nil && [dataArray count] > 0) {
            //[[NSNotificationCenter defaultCenter] postNotificationName:IAPHelperProductPurchasedNotification object:transaction.payment.productIdentifier userInfo:nil];
            [[NSNotificationCenter defaultCenter] postNotificationName:IAPHelperProductPurchasedAbonnementNotification object:@YES userInfo:nil];
        }
        
    }
    else if([responseString isEqualToString:@"NO iTunesCode=21006"]) {
        [[SKPaymentQueue defaultQueue] finishTransaction:transaction];
    }
    else {
        
        [[SKPaymentQueue defaultQueue] finishTransaction:transaction];
        [[[UIAlertView alloc] initWithTitle:@"Erreur lors de l'achat" message:responseString delegate:nil cancelButtonTitle:@"Retour" otherButtonTitles:nil] show];
    }
    
}



@end
