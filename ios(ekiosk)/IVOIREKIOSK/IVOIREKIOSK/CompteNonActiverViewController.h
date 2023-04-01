//
//  CompteNonActiverViewController.h
//  eKiosk
//
//  Created by maxime on 2014-07-23.
//  Copyright (c) 2014 Maxime Julien-Paquet. All rights reserved.
//

#import <UIKit/UIKit.h>

@protocol CompteNonActiverViewControllerDelegate <NSObject>

-(void)dismissFromActivation;
-(void)compteActiver;

@end

@interface CompteNonActiverViewController : UIViewController

@property (nonatomic, weak) __weak id <CompteNonActiverViewControllerDelegate> delegate;

@property (nonatomic, strong) IBOutlet UIScrollView *scrollview;

@property (nonatomic, strong) IBOutlet UIActivityIndicatorView *activityIndicator;
@property (nonatomic, strong) IBOutlet UIButton *resendMailButton;
@property (nonatomic, strong) IBOutlet UIButton *validateActiviationButton;

-(IBAction)resendMail:(id)sender;
-(IBAction)validateActivation:(id)sender;

-(void)SetDimsissWhenEnded:(BOOL)dismiss;

@end
