//
//  LoginViewController.h
//  IVOIREKIOSK
//
//  Created by Maxime Julien-Paquet on 2014-01-09.
//  Copyright (c) 2014 Maxime Julien-Paquet. All rights reserved.
//

#import <UIKit/UIKit.h>
#import "CompteNonActiverViewController.h"

@protocol LoginViewControllerDelegate <NSObject>

-(void)loginComplete;
-(void)loginCompleteRequireActivation;

@end

@interface LoginViewController : UIViewController <UITextFieldDelegate, CompteNonActiverViewControllerDelegate>

@property (nonatomic, weak) __weak id <LoginViewControllerDelegate> delegate;

@property (nonatomic, strong) IBOutlet UITextField *usernameTextField;
@property (nonatomic, strong) IBOutlet UITextField *passwordTextField;
@property (nonatomic, strong) IBOutlet UIButton *loginButton;

-(IBAction)loginTouched:(id)sender;
-(void)dismissViewController;

@end
