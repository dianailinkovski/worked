//
//  StoreNavController.m
//  IVOIREKIOSK
//
//  Created by Maxime Julien-Paquet on 2014-01-13.
//  Copyright (c) 2014 Maxime Julien-Paquet. All rights reserved.
//

#import "StoreNavController.h"

@interface StoreNavController ()

@end

@implementation StoreNavController

- (id)initWithNibName:(NSString *)nibNameOrNil bundle:(NSBundle *)nibBundleOrNil
{
    self = [super initWithNibName:nibNameOrNil bundle:nibBundleOrNil];
    if (self) {
        // Custom initialization
    }
    return self;
}

- (void)viewDidLoad
{
    [super viewDidLoad];
	// Do any additional setup after loading the view.
    self.view.autoresizesSubviews = YES;
    self.view.autoresizingMask = UIViewAutoresizingFlexibleHeight | UIViewAutoresizingFlexibleWidth;
}

- (void)didReceiveMemoryWarning
{
    [super didReceiveMemoryWarning];
    // Dispose of any resources that can be recreated.
}

@end
