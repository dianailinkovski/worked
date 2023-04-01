//
//  OverTutorielViewController.m
//  eKiosk
//
//  Created by maxime on 2014-04-10.
//  Copyright (c) 2014 Maxime Julien-Paquet. All rights reserved.
//

#import "OverTutorielViewController.h"
#import <QuartzCore/QuartzCore.h>

@interface OverTutorielViewController () {
    int step;
}

@end

@implementation OverTutorielViewController

@synthesize imageView, nextButton, prevButton;

- (id)initWithNibName:(NSString *)nibNameOrNil bundle:(NSBundle *)nibBundleOrNil {
    self = [super initWithNibName:nibNameOrNil bundle:nibBundleOrNil];
    if (self) {
        // Custom initialization
    }
    return self;
}

- (void)viewDidLoad {
    [super viewDidLoad];
    // Do any additional setup after loading the view.
    [[NSNotificationCenter defaultCenter] addObserver:self selector:@selector(willrotate:) name:@"willrotate" object:nil];
    [[NSNotificationCenter defaultCenter] addObserver:self selector:@selector(didrotate:) name:@"didrotate" object:nil];
    
    step = 0;
    
    [self.view addSubview:[self imageView]];
    [self setupButton:-1 switchOrientation:-1];
}

- (void)didReceiveMemoryWarning {
    [super didReceiveMemoryWarning];
    // Dispose of any resources that can be recreated.
}

-(void)willrotate:(NSNotification*)notif {
    NSArray *temparray = notif.object;
    
    int orientation = [[temparray objectAtIndex:0] intValue];
    float dure = [[temparray objectAtIndex:1] floatValue];
    NSLog(@"orientation = %d", orientation);
    NSLog(@"dure = %f", dure);
    
    [self changePhoto:dure switchOrientation:orientation];
    
    [prevButton removeFromSuperview];
    [nextButton removeFromSuperview];
    
    prevButton = nil;
    nextButton = nil;
    //[self setupButton:dure switchOrientation:orientation];
}

-(void)didrotate:(NSNotification*)notif {
    [self setupButton:0.1f switchOrientation:-1];
    //[self setupButton:-1];
    
}

-(UIImageView *)imageView {
    if (imageView == nil) {
        
        imageView = [[UIImageView alloc] initWithFrame:self.view.frame];
        imageView.autoresizingMask = UIViewAutoresizingFlexibleWidth | UIViewAutoresizingFlexibleHeight;
        imageView.backgroundColor = [UIColor clearColor];
        imageView.image = [UIImage imageNamed:@"tutoriel_etape_1.png"];
    }
    return imageView;
}

-(void)changePhoto:(float)duree switchOrientation:(int)fromOrientation {
    NSString *deviceString = @"";
    NSString *detailString = @"";
    
    UIInterfaceOrientation interfaceOrientation;
    if (fromOrientation != -1) {
        if (fromOrientation == 4 || fromOrientation == 3) {
            interfaceOrientation = UIInterfaceOrientationLandscapeLeft;
        }
        else if(fromOrientation == 1) {
            interfaceOrientation = UIInterfaceOrientationPortrait;
        }
    }
    else {
        interfaceOrientation = self.interfaceOrientation;
    }
    
    
    if (isPad()) {
        deviceString = @"ipad";
        if (UIInterfaceOrientationIsLandscape(interfaceOrientation)) {
            detailString = @"landscape";
        }
        else {
            detailString = @"portrait";
        }
        
    }
    else {
        deviceString = @"iphone";
    }
    
    switch (step) {
        case 0: {
            imageView.image = [UIImage imageNamed:[NSString stringWithFormat:@"tutoriel_%@_%@_etape_1.png", deviceString, detailString]];
        }
            break;
        case 1: {
            imageView.image = [UIImage imageNamed:[NSString stringWithFormat:@"tutoriel_%@_%@_etape_2.png", deviceString, detailString]];
        }
            break;
        case 2: {
            [[NSNotificationCenter defaultCenter] postNotificationName:@"SideMenuHide" object:nil];
            imageView.image = [UIImage imageNamed:[NSString stringWithFormat:@"tutoriel_%@_%@_etape_3.png", deviceString, detailString]];
        }
            break;
        case 3: {
            imageView.image = [UIImage imageNamed:[NSString stringWithFormat:@"tutoriel_%@_%@_etape_4.png", deviceString, detailString]];
        }
            break;
    }
    
    /*
    switch (step) {
        case 0: {
            
            [UIView transitionWithView:self.view
                              duration:duree
                               options:UIViewAnimationOptionTransitionCrossDissolve
                            animations:^{
                                imageView.image = [UIImage imageNamed:[NSString stringWithFormat:@"tutoriel_%@_%@_etape_1.png", deviceString, detailString]];
                            } completion:NULL];
        }
            break;
        case 1: {
            
            [UIView transitionWithView:self.view
                              duration:duree
                               options:UIViewAnimationOptionTransitionCrossDissolve
                            animations:^{
                                imageView.image = [UIImage imageNamed:[NSString stringWithFormat:@"tutoriel_%@_%@_etape_2.png", deviceString, detailString]];
                            } completion:NULL];
        }
            break;
        case 2: {
            
            [UIView transitionWithView:self.view
                              duration:duree
                               options:UIViewAnimationOptionTransitionCrossDissolve
                            animations:^{
                                imageView.image = [UIImage imageNamed:[NSString stringWithFormat:@"tutoriel_%@_%@_etape_3.png", deviceString, detailString]];
                            } completion:NULL];
        }
            break;
        case 3: {
            
            [UIView transitionWithView:self.view
                              duration:duree
                               options:UIViewAnimationOptionTransitionCrossDissolve
                            animations:^{
                                imageView.image = [UIImage imageNamed:[NSString stringWithFormat:@"tutoriel_%@_%@_etape_4.png", deviceString, detailString]];
                            } completion:NULL];
        }
            break;
    }
    */
    
}

-(void)setupButton:(float)duree switchOrientation:(int)fromOrientation {
    
    if (nextButton != nil) {
        [nextButton removeFromSuperview];
        nextButton = nil;
    }
    if (prevButton != nil) {
        [prevButton removeFromSuperview];
        prevButton = nil;
    }
    
    if (duree == -1) {
        duree = 0.4;
    }
    
    UIInterfaceOrientation interfaceOrientation;
    if (fromOrientation != -1) {
        if(fromOrientation == 1) {
            interfaceOrientation = UIInterfaceOrientationPortrait;
        }
        else {
            interfaceOrientation = UIInterfaceOrientationLandscapeLeft;
        }
    }
    else {
        interfaceOrientation = self.interfaceOrientation;
    }
    
    
    if (isPad()) {
        [self ipadSetup:duree with:interfaceOrientation];
        
    }
    else {
        [self iphoneSetup:duree with:interfaceOrientation];
        
    }
    
    
}

-(void)ipadSetup:(float)duree with:(UIInterfaceOrientation)interfaceOrientation {
    
    NSString *deviceString = @"";
    NSString *detailString = @"";
    
    deviceString = @"ipad";
    if (UIInterfaceOrientationIsLandscape(interfaceOrientation)) {
        detailString = @"landscape";
    }
    else {
        detailString = @"portrait";
    }
    
    switch (step) {
        case 0: {
            
            prevButton = [self nonmerciButton];
            nextButton = [self debuterButton];
            
            
            [nextButton addTarget:self action:@selector(nextStep) forControlEvents:UIControlEventTouchUpInside];
            [prevButton addTarget:self action:@selector(skipTuto) forControlEvents:UIControlEventTouchUpInside];
            
            [UIView transitionWithView:self.view
                              duration:duree
                               options:UIViewAnimationOptionTransitionCrossDissolve
                            animations:^{
                                imageView.image = [UIImage imageNamed:[NSString stringWithFormat:@"tutoriel_%@_%@_etape_1.png", deviceString, detailString]];
                                [self.view addSubview:prevButton];
                                [self.view addSubview:nextButton];
                            } completion:NULL];
        }
            break;
            
        case 1:
        {
            [[NSNotificationCenter defaultCenter] postNotificationName:@"SideMenuHide" object:nil];
            
            
            prevButton = [self SideButton:0];
            nextButton = [self SideButton:1];
            
            
            
            [nextButton addTarget:self action:@selector(nextStep) forControlEvents:UIControlEventTouchUpInside];
            [prevButton addTarget:self action:@selector(prevStep) forControlEvents:UIControlEventTouchUpInside];
            
            
            
            [UIView transitionWithView:self.view
                              duration:duree
                               options:UIViewAnimationOptionTransitionCrossDissolve
                            animations:^{
                                imageView.image = [UIImage imageNamed:[NSString stringWithFormat:@"tutoriel_%@_%@_etape_2.png", deviceString, detailString]];
                                [self.view addSubview:prevButton];
                                [self.view addSubview:nextButton];
                            } completion:NULL];
        }
            break;
        case 2: {
            
            prevButton = [self SideButton:0];
            nextButton = [self SideButton:1];
            
            //nextButton.frame = CGRectMake((self.view.frame.size.width - 192), (self.view.frame.size.height-104), 192, 46);
            //prevButton.frame = CGRectMake(0, (self.view.frame.size.height-104), 192, 46);
            
            [nextButton addTarget:self action:@selector(nextStep) forControlEvents:UIControlEventTouchUpInside];
            [prevButton addTarget:self action:@selector(prevStep) forControlEvents:UIControlEventTouchUpInside];
            
            [UIView transitionWithView:self.view
                              duration:duree
                               options:UIViewAnimationOptionTransitionCrossDissolve
                            animations:^{
                                imageView.image = [UIImage imageNamed:[NSString stringWithFormat:@"tutoriel_%@_%@_etape_3.png", deviceString, detailString]];
                                [self.view addSubview:prevButton];
                                [self.view addSubview:nextButton];
                            } completion:NULL];
            
            [[NSNotificationCenter defaultCenter] postNotificationName:@"SideMenuShow" object:nil];
        }
            break;
        case 3: {
            
            [[NSNotificationCenter defaultCenter] postNotificationName:@"SideMenuHide" object:nil];
            
            prevButton = [self SideButton:0];
            nextButton = [self debuterButton];
            
            //nextButton.frame = CGRectMake((self.view.frame.size.width - 210) / 2, (self.view.frame.size.height-240), 210, 50);
            //prevButton.frame = CGRectMake(0, (self.view.frame.size.height-104), 192, 46);
            
            [nextButton addTarget:self action:@selector(skipTuto) forControlEvents:UIControlEventTouchUpInside];
            [prevButton addTarget:self action:@selector(prevStep) forControlEvents:UIControlEventTouchUpInside];
            
            [UIView transitionWithView:self.view
                              duration:duree
                               options:UIViewAnimationOptionTransitionCrossDissolve
                            animations:^{
                                imageView.image = [UIImage imageNamed:[NSString stringWithFormat:@"tutoriel_%@_%@_etape_4.png", deviceString, detailString]];
                                [self.view addSubview:prevButton];
                                [self.view addSubview:nextButton];
                            } completion:NULL];
        }
            break;
            
        default:
            break;
    }
}

-(void)iphoneSetup:(float)duree with:(UIInterfaceOrientation)interfaceOrientation {
    
    NSString *deviceString = @"";
    NSString *detailString = @"";
    
    deviceString = @"iphone";
    if([UIScreen mainScreen].bounds.size.height == 568.0) {
        detailString = @"5";
    }
    else {
        detailString = @"4";
    }
    
    switch (step) {
        case 0: {
            
            prevButton = [self nonmerciButton];
            nextButton = [self debuterButton];
            
            
            [nextButton addTarget:self action:@selector(nextStep) forControlEvents:UIControlEventTouchUpInside];
            [prevButton addTarget:self action:@selector(skipTuto) forControlEvents:UIControlEventTouchUpInside];
            
            [UIView transitionWithView:self.view
                              duration:duree
                               options:UIViewAnimationOptionTransitionCrossDissolve
                            animations:^{
                                imageView.image = [UIImage imageNamed:[NSString stringWithFormat:@"tutoriel_%@_%@_etape_1.png", deviceString, detailString]];
                                [self.view addSubview:prevButton];
                                [self.view addSubview:nextButton];
                            } completion:NULL];
        }
            break;
            
        case 1:
        {
            [[NSNotificationCenter defaultCenter] postNotificationName:@"SideMenuHide" object:nil];
            
            
            prevButton = [self SideButton:0];
            nextButton = [self SideButton:1];
            
            
            
            [nextButton addTarget:self action:@selector(nextStep) forControlEvents:UIControlEventTouchUpInside];
            [prevButton addTarget:self action:@selector(prevStep) forControlEvents:UIControlEventTouchUpInside];
            
            
            
            [UIView transitionWithView:self.view
                              duration:duree
                               options:UIViewAnimationOptionTransitionCrossDissolve
                            animations:^{
                                imageView.image = [UIImage imageNamed:[NSString stringWithFormat:@"tutoriel_%@_%@_etape_2.png", deviceString, detailString]];
                                [self.view addSubview:prevButton];
                                [self.view addSubview:nextButton];
                            } completion:NULL];
        }
            break;
        case 2: {
            
            prevButton = [self SideButton:0];
            nextButton = [self SideButton:1];
            
            //nextButton.frame = CGRectMake((self.view.frame.size.width - 192), (self.view.frame.size.height-104), 192, 46);
            //prevButton.frame = CGRectMake(0, (self.view.frame.size.height-104), 192, 46);
            
            [nextButton addTarget:self action:@selector(nextStep) forControlEvents:UIControlEventTouchUpInside];
            [prevButton addTarget:self action:@selector(prevStep) forControlEvents:UIControlEventTouchUpInside];
            
            [UIView transitionWithView:self.view
                              duration:duree
                               options:UIViewAnimationOptionTransitionCrossDissolve
                            animations:^{
                                imageView.image = [UIImage imageNamed:[NSString stringWithFormat:@"tutoriel_%@_%@_etape_3.png", deviceString, detailString]];
                                [self.view addSubview:prevButton];
                                [self.view addSubview:nextButton];
                            } completion:NULL];
            
            [[NSNotificationCenter defaultCenter] postNotificationName:@"SideMenuShow" object:nil];
        }
            break;
        case 3: {
            
            prevButton = [self SideButton:0];
            nextButton = [self SideButton:1];
            
            //nextButton.frame = CGRectMake((self.view.frame.size.width - 192), (self.view.frame.size.height-104), 192, 46);
            //prevButton.frame = CGRectMake(0, (self.view.frame.size.height-104), 192, 46);
            
            [nextButton addTarget:self action:@selector(nextStep) forControlEvents:UIControlEventTouchUpInside];
            [prevButton addTarget:self action:@selector(prevStep) forControlEvents:UIControlEventTouchUpInside];
            
            [UIView transitionWithView:self.view
                              duration:duree
                               options:UIViewAnimationOptionTransitionCrossDissolve
                            animations:^{
                                imageView.image = [UIImage imageNamed:[NSString stringWithFormat:@"tutoriel_%@_%@_etape_4.png", deviceString, detailString]];
                                [self.view addSubview:prevButton];
                                [self.view addSubview:nextButton];
                            } completion:NULL];
            
            [[NSNotificationCenter defaultCenter] postNotificationName:@"SideMenuShow" object:nil];
        }
            break;
        case 4: {
            
            [[NSNotificationCenter defaultCenter] postNotificationName:@"SideMenuHide" object:nil];
            
            prevButton = [self SideButton:0];
            nextButton = [self debuterButton];
            
            //nextButton.frame = CGRectMake((self.view.frame.size.width - 210) / 2, (self.view.frame.size.height-240), 210, 50);
            //prevButton.frame = CGRectMake(0, (self.view.frame.size.height-104), 192, 46);
            
            [nextButton addTarget:self action:@selector(skipTuto) forControlEvents:UIControlEventTouchUpInside];
            [prevButton addTarget:self action:@selector(prevStep) forControlEvents:UIControlEventTouchUpInside];
            
            [UIView transitionWithView:self.view
                              duration:duree
                               options:UIViewAnimationOptionTransitionCrossDissolve
                            animations:^{
                                imageView.image = [UIImage imageNamed:[NSString stringWithFormat:@"tutoriel_%@_%@_etape_5.png", deviceString, detailString]];
                                [self.view addSubview:prevButton];
                                [self.view addSubview:nextButton];
                            } completion:NULL];
        }
            break;
            
        default:
            break;
    }
}

-(UIButton*)SideButton:(int)side {
    UIButton *tempButton = [UIButton buttonWithType:UIButtonTypeCustom];
    
    if (side == 0) {
        [tempButton setBackgroundImage:[UIImage imageNamed:@"left-tutoriel-button"] forState:UIControlStateNormal];
        [tempButton setTitle:@"Précédent" forState:UIControlStateNormal];
        [tempButton setTitleColor:[UIColor whiteColor] forState:UIControlStateNormal];
        [tempButton.titleLabel setFont:[UIFont fontWithName:@"Helvetica-Bold" size:20]];
        
        if (isPad()) {
            UIInterfaceOrientation interfaceOrientation = self.interfaceOrientation;
            if (UIInterfaceOrientationIsLandscape(interfaceOrientation)) {
                tempButton.frame = CGRectMake(0, (768 - 104), 192, 46);
            }
            else {
                tempButton.frame = CGRectMake(0, (1024 - 124), 192, 46);
            }
        }
        else {
            [tempButton.titleLabel setFont:[UIFont fontWithName:@"Helvetica" size:16]];
            if([UIScreen mainScreen].bounds.size.height == 568.0) {
                if (step == 4) {
                    tempButton.frame = CGRectMake(0, (568 - 62), 125, 30);
                }
                else {
                    tempButton.frame = CGRectMake(0, (568 - 122), 125, 30);
                }
            }
            else {
                if (step == 4) {
                    tempButton.frame = CGRectMake(0, (480 - 62), 125, 30);
                }
                else {
                    tempButton.frame = CGRectMake(0, (480 - 102), 125, 30);
                }
            }
        }
        
        //[tempButton setAutoresizingMask:UIViewAutoresizingFlexibleRightMargin | UIViewAutoresizingFlexibleTopMargin | UIViewAutoresizingFlexibleBottomMargin];
        
        
    }
    else if(side == 1) {
        
        [tempButton setBackgroundImage:[UIImage imageNamed:@"right-tutoriel-button"] forState:UIControlStateNormal];
        [tempButton setTitle:@"Suivant" forState:UIControlStateNormal];
        [tempButton setTitleColor:[UIColor whiteColor] forState:UIControlStateNormal];
        [tempButton.titleLabel setFont:[UIFont fontWithName:@"Helvetica-Bold" size:20]];
        
        if (isPad()) {
            UIInterfaceOrientation interfaceOrientation = self.interfaceOrientation;
            if (UIInterfaceOrientationIsLandscape(interfaceOrientation)) {
                tempButton.frame = CGRectMake((1024 - 192), (768 - 104), 192, 46);
            }
            else {
                tempButton.frame = CGRectMake((768 - 192), (1024 - 124), 192, 46);
            }
        }
        else {
            [tempButton.titleLabel setFont:[UIFont fontWithName:@"Helvetica" size:16]];
            if([UIScreen mainScreen].bounds.size.height == 568.0) {
                tempButton.frame = CGRectMake((320 - 125), (568 - 122), 125, 30);
            }
            else {
                tempButton.frame = CGRectMake((320 - 125), (480 - 102), 125, 30);
            }
        }
        
        //[tempButton setAutoresizingMask:UIViewAutoresizingFlexibleLeftMargin | UIViewAutoresizingFlexibleTopMargin | UIViewAutoresizingFlexibleBottomMargin];
        
        
    }
    
    return tempButton;
}

-(UIButton*)debuterButton {
    UIButton *tempButton = [UIButton buttonWithType:UIButtonTypeCustom];
    
    [tempButton setTitle:@"Débuter" forState:UIControlStateNormal];
    [tempButton setTitleColor:[UIColor whiteColor] forState:UIControlStateNormal];
    [tempButton.titleLabel setFont:[UIFont fontWithName:@"Helvetica-Bold" size:24]];
    [tempButton setBackgroundColor:[UIColor colorWithRed:10.0f/255.0f green:175.0f/255.0f blue:95.0f/255.0f alpha:1]];
    [tempButton.layer setCornerRadius:5];
    
    if (isPad()) {
        UIInterfaceOrientation interfaceOrientation = self.interfaceOrientation;
        if (UIInterfaceOrientationIsLandscape(interfaceOrientation)) {
            if (step == 3) {
                tempButton.frame = CGRectMake((1024 - 210) / 2, (768 - 130), 210, 50);
            }
            else {
                tempButton.frame = CGRectMake((1024 - 210) / 2, (768 - 220), 210, 50);
            }
        }
        else {
            if (step == 3) {
                tempButton.frame = CGRectMake((768 - 210) / 2, (1024 - 250), 210, 50);
            }
            else {
                tempButton.frame = CGRectMake((768 - 210) / 2, (1024 - 330), 210, 50);
            }
        }
    }
    else {
        [tempButton.titleLabel setFont:[UIFont fontWithName:@"Helvetica-Bold" size:20]];
        
        if([UIScreen mainScreen].bounds.size.height == 568.0) {
            if (step == 4) {
                [tempButton.titleLabel setFont:[UIFont fontWithName:@"Helvetica-Bold" size:16]];
                tempButton.frame = CGRectMake((320 - 160), (568 - 62), 150, 30);
            }
            else {
                tempButton.frame = CGRectMake((320 - 170) / 2, (568 - 190), 170, 36);
            }
            
        }
        else {
            if (step == 4) {
                [tempButton.titleLabel setFont:[UIFont fontWithName:@"Helvetica-Bold" size:16]];
                tempButton.frame = CGRectMake((320 - 160), (480 - 62), 150, 30);
            }
            else {
                tempButton.frame = CGRectMake((320 - 170) / 2, (480 - 170), 170, 36);
            }
        }
    }
    
    //[tempButton setAutoresizingMask:UIViewAutoresizingFlexibleLeftMargin | UIViewAutoresizingFlexibleRightMargin];
    
    
    
    return tempButton;
}

-(UIButton*)nonmerciButton {
    UIButton *tempButton = [UIButton buttonWithType:UIButtonTypeCustom];
    
    [tempButton setTitle:@"Non merci\nPasser le tutoriel" forState:UIControlStateNormal];
    [tempButton setTitleColor:[UIColor whiteColor] forState:UIControlStateNormal];
    [tempButton.titleLabel setFont:[UIFont fontWithName:@"Helvetica" size:20]];
    [tempButton.titleLabel setNumberOfLines:0];
    [tempButton.titleLabel setTextAlignment:NSTextAlignmentCenter];
    
    if (isPad()) {
        UIInterfaceOrientation interfaceOrientation = self.interfaceOrientation;
        if (UIInterfaceOrientationIsLandscape(interfaceOrientation)) {
            tempButton.frame = CGRectMake((1024 - 280) / 2, (768 - 144), 280, 40);
        }
        else {
            tempButton.frame = CGRectMake((768 - 280) / 2, (1024 - 244), 280, 40);
        }
    }
    else {
        [tempButton.titleLabel setFont:[UIFont fontWithName:@"Helvetica" size:14]];
        if([UIScreen mainScreen].bounds.size.height == 568.0) {
            tempButton.frame = CGRectMake((320 - 240) / 2, (568 - 134), 240, 30);
        }
        else {
            tempButton.frame = CGRectMake((320 - 240) / 2, (480 - 114), 240, 30);
        }
    }
    
    //[tempButton setAutoresizingMask:UIViewAutoresizingFlexibleLeftMargin | UIViewAutoresizingFlexibleRightMargin];
    
    
    return tempButton;
}

-(void)prevStep {
    --step;
    [self setupButton:-1 switchOrientation:-1];
}

-(void)nextStep {
    ++step;
    [self setupButton:-1 switchOrientation:-1];
}

-(void)skipTuto {
    NSUserDefaults *defaults = [NSUserDefaults standardUserDefaults];
    [defaults setObject:[NSNumber numberWithBool:NO] forKey:@"showTutoriel"];
    [defaults synchronize];
    [UIView transitionWithView:self.view
                      duration:0.4f
                       options:UIViewAnimationOptionTransitionCrossDissolve
                    animations:^{
                        [self.view setAlpha:0];
                    } completion:^(BOOL finished) {
                        [self.view removeFromSuperview];
                    }];
    
}

/*
#pragma mark - Navigation

// In a storyboard-based application, you will often want to do a little preparation before navigation
- (void)prepareForSegue:(UIStoryboardSegue *)segue sender:(id)sender
{
    // Get the new view controller using [segue destinationViewController].
    // Pass the selected object to the new view controller.
}
*/

@end
