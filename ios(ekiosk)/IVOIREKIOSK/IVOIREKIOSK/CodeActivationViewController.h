//
//  CodeActivationViewController.h
//  eKiosk
//
//  Created by Maxime Julien-Paquet on 2014-02-23.
//  Copyright (c) 2014 Maxime Julien-Paquet. All rights reserved.
//

#import <UIKit/UIKit.h>

@interface CodeActivationViewController : UIViewController <UITextFieldDelegate>

@property (nonatomic, strong) IBOutlet UITextField *codeTextField;
@property (nonatomic, strong) UIActivityIndicatorView *loadingCodeActivation;
@property (nonatomic, strong) IBOutlet UIButton *submitButton;

-(IBAction)submitTouched;

@end
