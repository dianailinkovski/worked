//
//  CreateProfilViewController.h
//  IVOIREKIOSK
//
//  Created by Maxime Julien-Paquet on 2014-01-18.
//  Copyright (c) 2014 Maxime Julien-Paquet. All rights reserved.
//

#import <UIKit/UIKit.h>
#import "CompteNonActiverViewController.h"

@protocol CreateProfilViewControllerDelegate <NSObject>

-(void)CompteCreateAndActivate;

-(void)cancelActivationView;

@end

@interface CreateProfilViewController : UIViewController <UIWebViewDelegate, CompteNonActiverViewControllerDelegate>

@property (nonatomic, weak) __weak id <CreateProfilViewControllerDelegate> delegate;

@property (nonatomic, strong) UIActivityIndicatorView *aiView;

@property (nonatomic, strong) IBOutlet UIWebView *webView;

-(IBAction)annuler:(id)sender;
-(void)dismissViewController;

-(void)initView;
-(void)verifValideInsciption;

@end
