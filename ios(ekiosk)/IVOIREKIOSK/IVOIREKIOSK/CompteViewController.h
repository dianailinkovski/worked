//
//  CompteViewController.h
//  eKiosk
//
//  Created by Maxime Julien-Paquet on 2014-03-13.
//  Copyright (c) 2014 Maxime Julien-Paquet. All rights reserved.
//

#import <UIKit/UIKit.h>
#import "Login2ViewController.h"
#import "CreateProfilViewController.h"
#import "CompteNonActiverViewController.h"

@protocol CompteViewControllerDelegate <NSObject>

-(void)compteConnecter;
-(void)compteSkip;

-(void)cancelActivationView;

@end

@interface CompteViewController : UIViewController <LoginViewControllerDelegate, CreateProfilViewControllerDelegate, CompteNonActiverViewControllerDelegate>

@property (nonatomic, weak) __weak id <CompteViewControllerDelegate> delegate;
@property (nonatomic, strong) IBOutlet UIScrollView *scrollview;

-(IBAction)connecterCompte:(id)sender;
-(IBAction)creerCompte:(id)sender;
-(IBAction)skipCompte:(id)sender;
-(IBAction)retour:(id)sender;

@end
