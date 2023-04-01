//
//  DetailVirtualCurrencyViewController.h
//  eKiosk
//
//  Created by Maxime Julien-Paquet on 2014-02-21.
//  Copyright (c) 2014 Maxime Julien-Paquet. All rights reserved.
//

#import <UIKit/UIKit.h>
#import <StoreKit/StoreKit.h>
#import "VCLabel.h"
#import "GTMHTTPFetcher.h"

@protocol DetailVirtualCurrencyViewControllerDelegate <NSObject>

-(void)EndBuyingCredit;

@end

@interface DetailVirtualCurrencyViewController : UIViewController {
    BOOL Buying;
}

@property (nonatomic, weak) __weak id <DetailVirtualCurrencyViewControllerDelegate> delegate;

@property (nonatomic, strong) NSMutableArray *dataArray;
@property (nonatomic, strong) SKProduct *itunesProduct;

@property (nonatomic, strong) VCLabel *currentLabel;
@property (nonatomic, strong) VCLabel *buyingLabel;
@property (nonatomic, strong) VCLabel *totalLabel;

@property (nonatomic, strong) UILabel *currentTextLabel;
@property (nonatomic, strong) UILabel *buyingTextLabel;
@property (nonatomic, strong) UILabel *totalTextLabel;

@property (nonatomic, strong) UIButton *confirmButton;
@property (nonatomic, strong) UIButton *cancelButton;
@property (nonatomic, strong) UIBarButtonItem *barButtonItem;


@property (nonatomic, strong) NSString *idTransaction;
@property (nonatomic, strong) SKPaymentTransaction *pendingTransaction;

@property (nonatomic, strong) UIActivityIndicatorView *loadingCurrentEK;
@property (nonatomic, strong) UIActivityIndicatorView *loadingPurchase;

-(void)setCurrentBuyingState:(BOOL)buyingState;

@end
