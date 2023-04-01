//
//  Login2ViewController.m
//  IVOIREKIOSK
//
//  Created by Maxime Julien-Paquet on 2014-02-06.
//  Copyright (c) 2014 Maxime Julien-Paquet. All rights reserved.
//

#import "Login2ViewController.h"

@interface Login2ViewController ()

@end

@implementation Login2ViewController

-(id)initWithNibName:(NSString *)nibNameOrNil bundle:(NSBundle *)nibBundleOrNil {
    self = [super initWithNibName:nibNameOrNil bundle:nibBundleOrNil];
    if (self) {
        // Custom initialization
    }
    return self;
}

-(void)viewDidLoad {
    [super viewDidLoad];
	// Do any additional setup after loading the view.
    UIBarButtonItem *temp =[[UIBarButtonItem alloc] initWithTitle:@"Retour" style:UIBarButtonItemStyleDone target:self action:@selector(annulerButton:)];
    self.navigationItem.leftBarButtonItem = temp;
    self.navigationItem.title = @"Me connecter";
}

-(void)didReceiveMemoryWarning {
    [super didReceiveMemoryWarning];
    // Dispose of any resources that can be recreated.
}

-(void)annulerButton:(id)sender {
    //[self.view removeFromSuperview];
    //[self removeFromParentViewController];
    [self dismissViewControllerAnimated:YES completion:^{
        //[[NSNotificationCenter defaultCenter] postNotificationName:@"ChangementDeStatusDuCompte" object:nil];
    }];
}

-(void)dismissViewController {
    [self dismissViewControllerAnimated:YES completion:^{
        [[NSNotificationCenter defaultCenter] postNotificationName:@"ChangementDeStatusDuCompte" object:nil];
    }];
    
}



@end
