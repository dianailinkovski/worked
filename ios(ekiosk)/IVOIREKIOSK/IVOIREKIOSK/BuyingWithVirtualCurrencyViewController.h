//
//  BuyingWithVirtualCurrencyViewController.h
//  eKiosk
//
//  Created by Maxime Julien-Paquet on 2014-02-22.
//  Copyright (c) 2014 Maxime Julien-Paquet. All rights reserved.
//

#import "DetailVirtualCurrencyViewController.h"

@protocol BuyingWithVirtualCurrencyViewControllerDelegate <NSObject>

-(void)CreditSelection;
-(void)AchatComplete;

@end

@interface BuyingWithVirtualCurrencyViewController : DetailVirtualCurrencyViewController

@property (nonatomic, strong) NSDictionary *achatData;
@property (nonatomic, weak) __weak id <BuyingWithVirtualCurrencyViewControllerDelegate> delegate;

@end
