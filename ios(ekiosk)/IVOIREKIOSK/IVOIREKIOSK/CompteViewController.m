//
//  CompteViewController.m
//  eKiosk
//
//  Created by Maxime Julien-Paquet on 2014-03-13.
//  Copyright (c) 2014 Maxime Julien-Paquet. All rights reserved.
//

#import "CompteViewController.h"

@interface CompteViewController ()

@end

@implementation CompteViewController

@synthesize delegate, scrollview;

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
    
    UIBarButtonItem *temp =[[UIBarButtonItem alloc] initWithTitle:@"Retour" style:UIBarButtonItemStyleDone target:self action:@selector(retour:)];
    self.navigationItem.leftBarButtonItem = temp;
    self.navigationItem.title = @"Compte ekiosk";
    
    
    
}

-(void)viewDidAppear:(BOOL)animated {
    [super viewDidAppear:animated];
    if (!isPad()) {
        NSLog(@"frame = %@", NSStringFromCGRect(self.scrollview.frame));
        
        if([UIScreen mainScreen].bounds.size.height != 568.0) {
            
            self.scrollview.frame = CGRectMake(0, 0, 320, 480);
            [self.scrollview setContentSize:CGSizeMake(self.view.frame.size.width, 490)];
            
            NSLog(@"frame = %@", NSStringFromCGRect(self.view.frame));
        }
    }
}

-(void)didReceiveMemoryWarning {
    [super didReceiveMemoryWarning];
    // Dispose of any resources that can be recreated.
}

-(void)connecterCompte:(id)sender {
    NSString *storyboardString = @"Main_iPhone";
    if (isPad()) {
        storyboardString = @"Main_iPad";
    }
    
    UIStoryboard *sb = [UIStoryboard storyboardWithName:storyboardString bundle:nil];
    
    Login2ViewController* controller = (Login2ViewController*)[sb instantiateViewControllerWithIdentifier:@"Login2ViewController"];
    [controller setModalPresentationStyle:UIModalPresentationFormSheet];
    [controller setDelegate:self];
    [self.navigationController pushViewController:controller animated:YES];
    
}

-(void)creerCompte:(id)sender {
    NSString *storyboardString = @"Main_iPhone";
    if (isPad()) {
        storyboardString = @"Main_iPad";
    }
    
    UIStoryboard *sb = [UIStoryboard storyboardWithName:storyboardString bundle:nil];
    
    CreateProfilViewController* controller = (CreateProfilViewController*)[sb instantiateViewControllerWithIdentifier:@"CreateProfilViewController"];
    [controller setModalPresentationStyle:UIModalPresentationFormSheet];
    [controller setDelegate:self];
    [self.navigationController pushViewController:controller animated:YES];
    
}

-(void)skipCompte:(id)sender {
    [self dismissViewControllerAnimated:YES completion:^{
        NSUserDefaults *defaults = [NSUserDefaults standardUserDefaults];
        [defaults setObject:[NSDate date] forKey:@"lastSkipCompte"];
        if (delegate && [delegate respondsToSelector:@selector(compteConnecter)]) {
            [delegate compteSkip];
        }
    }];
}

-(void)retour:(id)sender {
    [self dismissViewControllerAnimated:YES completion:nil];
}

#pragma mark - LoginDelegate 

-(void)loginComplete {
    
    [self dismissViewControllerAnimated:YES completion:^{
        [[NSNotificationCenter defaultCenter] postNotificationName:@"ChangementDeStatusDuCompte" object:nil];
        if (delegate && [delegate respondsToSelector:@selector(compteConnecter)]) {
            [delegate compteConnecter];
        }
    }];
    
}

-(void)loginCompleteRequireActivation {
    [self.navigationController popToRootViewControllerAnimated:NO];
    
    NSString *storyboardString = @"Main_iPhone";
    if (isPad()) {
        storyboardString = @"Main_iPad";
    }
    
    UIStoryboard *sb = [UIStoryboard storyboardWithName:storyboardString bundle:nil];
    
    CompteNonActiverViewController * controller = (CompteNonActiverViewController*)[sb instantiateViewControllerWithIdentifier:@"CompteNonActiverViewController"];
    [controller setModalPresentationStyle:UIModalPresentationFormSheet];
    [controller setDelegate:self];
    [self.navigationController pushViewController:controller animated:YES];
}

#pragma mark - CompteNonActiverDelegate

-(void)dismissFromActivation {
    NSLog(@"retour2");
    //[self dismissViewControllerAnimated:YES completion:^{
        NSLog(@"retour2 - completion");
        [[NSNotificationCenter defaultCenter] postNotificationName:@"ChangementDeStatusDuCompte" object:nil];
        if (delegate && [delegate respondsToSelector:@selector(cancelActivationView)]) {
            NSLog(@"retour2 - delegate");
            [delegate cancelActivationView];
        }
    //}];
}

-(void)compteActiver {
    [self dismissViewControllerAnimated:YES completion:^{
        [[NSNotificationCenter defaultCenter] postNotificationName:@"ChangementDeStatusDuCompte" object:nil];
        if (delegate && [delegate respondsToSelector:@selector(compteConnecter)]) {
            [delegate compteConnecter];
        }
    }];
}

#pragma mark - CreateProfilViewControllerDelegate

-(void)CompteCreateAndActivate {
    //[self dismissViewControllerAnimated:YES completion:^{
        [[NSNotificationCenter defaultCenter] postNotificationName:@"ChangementDeStatusDuCompte" object:nil];
        if (delegate && [delegate respondsToSelector:@selector(compteConnecter)]) {
            [delegate compteConnecter];
        }
    //}];
}

-(void)cancelActivationView {
    NSLog(@"retour2 - completion");
    [[NSNotificationCenter defaultCenter] postNotificationName:@"ChangementDeStatusDuCompte" object:nil];
    if (delegate && [delegate respondsToSelector:@selector(cancelActivationView)]) {
        NSLog(@"retour2 - delegate");
        [delegate cancelActivationView];
    }
}

@end
