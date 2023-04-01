//
//  CreateProfil2ViewController.m
//  eKiosk
//
//  Created by maxime on 2014-07-30.
//  Copyright (c) 2014 Maxime Julien-Paquet. All rights reserved.
//

#import "CreateProfil2ViewController.h"

@interface CreateProfil2ViewController ()

@end

@implementation CreateProfil2ViewController

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
    
    UIBarButtonItem *temp =[[UIBarButtonItem alloc] initWithTitle:@"Retour" style:UIBarButtonItemStyleDone target:self action:@selector(annulerButton:)];
    self.navigationItem.leftBarButtonItem = temp;
    //self.navigationItem.title = @"Créer un compte";
    //[self initView];
    
    //[self.webView setDelegate:self];
}

- (void)didReceiveMemoryWarning
{
    [super didReceiveMemoryWarning];
    // Dispose of any resources that can be recreated.
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


-(void)annulerButton:(id)sender {
    //[self.view removeFromSuperview];
    //[self removeFromParentViewController];
    [self dismissViewControllerAnimated:YES completion:^{
        //[[NSNotificationCenter defaultCenter] postNotificationName:@"ChangementDeStatusDuCompte" object:nil];
    }];
}

@end
