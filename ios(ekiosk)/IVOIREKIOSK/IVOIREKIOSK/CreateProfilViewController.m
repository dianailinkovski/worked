//
//  CreateProfilViewController.m
//  IVOIREKIOSK
//
//  Created by Maxime Julien-Paquet on 2014-01-18.
//  Copyright (c) 2014 Maxime Julien-Paquet. All rights reserved.
//

#import "CreateProfilViewController.h"
#import <CommonCrypto/CommonDigest.h>

@interface CreateProfilViewController ()

@end

@implementation CreateProfilViewController

@synthesize webView, aiView, delegate;

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
    self.navigationItem.title = @"Créer un compte";
    
    [self initView];
}

-(void)initView {
    
    
    NSUserDefaults *defaults = [NSUserDefaults standardUserDefaults];
    int ekcredit = [[defaults valueForKey:@"ekcredit"] intValue];
    
    [self.webView loadRequest:[NSURLRequest requestWithURL:[NSURL URLWithString:[NSString stringWithFormat:@"http://ngser.gnetix.com/site/memberform?ekcredit=%d", ekcredit]]]];
    NSLog(@"http://ngser.gnetix.com/site/memberform?ekcredit=%d", ekcredit);
    self.aiView = [[UIActivityIndicatorView alloc] initWithActivityIndicatorStyle:UIActivityIndicatorViewStyleWhiteLarge];
    self.aiView.center = self.webView.center;
    self.aiView.tintColor = [UIColor blackColor];
    self.aiView.color = [UIColor blackColor];
    [self.aiView startAnimating];
    [self.view addSubview:self.aiView];
}

-(void)didReceiveMemoryWarning {
    [super didReceiveMemoryWarning];
    // Dispose of any resources that can be recreated.
}

#pragma mark - WebViewDelegate

-(void)webViewDidStartLoad:(UIWebView *)webView {
    [self.aiView startAnimating];
    
}

-(void)webViewDidFinishLoad:(UIWebView *)webView {
    [self.aiView stopAnimating];
    NSLog(@"webview end loading");
    [self verifValideInsciption];
}

-(void)verifValideInsciption {
    NSString *myText = [webView stringByEvaluatingJavaScriptFromString:@"document.getElementById('info').value"];
    NSLog(@"mytext = %@",myText);
    if ([myText isEqualToString:@""]) {
        return;
    }
    
    NSError *jsonParsingError = nil;
    NSArray *publicTimeline = [NSJSONSerialization JSONObjectWithData:[myText dataUsingEncoding:NSStringEncodingConversionAllowLossy] options:0 error:&jsonParsingError];
    NSLog(@"compte = %@", publicTimeline);
    
    NSString *usernameString = [publicTimeline valueForKey:@"email"];
    NSString *passwordString = [publicTimeline valueForKey:@"password"];
    NSString *prenomString = [publicTimeline valueForKey:@"first_name"];
    NSString *nomString = [publicTimeline valueForKey:@"last_name"];
    NSString *mobileString = [publicTimeline valueForKey:@"mobile"];
    NSString *ekcreditString = [publicTimeline valueForKey:@"ek_credit"];
    
    NSUserDefaults *defaults = [NSUserDefaults standardUserDefaults];
    [defaults setObject:usernameString forKey:@"username"];
    [defaults setObject:passwordString forKey:@"password"];
    [defaults setObject:prenomString forKey:@"prenom"];
    [defaults setObject:nomString forKey:@"nom"];
    [defaults setObject:mobileString forKey:@"mobile"];
    [defaults setObject:ekcreditString forKey:@"ekcredit"];
    
    [defaults setObject:nil forKey:@"lastSkipCompte"];
    
    [defaults synchronize];
    /*
    if (delegate && [delegate respondsToSelector:@selector(loginComplete)]) {
        [delegate loginComplete];
    }
    else {
        [self dismissViewControllerAnimated:YES completion:^{
            [[NSNotificationCenter defaultCenter] postNotificationName:@"ChangementDeStatusDuCompte" object:nil];
        }];
    }
    */
    
    
    
    
    NSString *storyboardString = @"Main_iPhone";
    if (isPad()) {
        storyboardString = @"Main_iPad";
    }
    
    UIStoryboard *sb = [UIStoryboard storyboardWithName:storyboardString bundle:nil];
    
    CompteNonActiverViewController * controller = (CompteNonActiverViewController*)[sb instantiateViewControllerWithIdentifier:@"CompteNonActiverViewController"];
    [controller setModalPresentationStyle:UIModalPresentationFormSheet];
    [controller setDelegate:self];
    [self.navigationController pushViewController:controller animated:YES];
    
    
    //[self.view removeFromSuperview];
    
}

-(void)dismissViewController {
    [self dismissViewControllerAnimated:YES completion:^{
        [[NSNotificationCenter defaultCenter] postNotificationName:@"ChangementDeStatusDuCompte" object:nil];
    }];
    //[self.view removeFromSuperview];
}

-(void)annuler:(id)sender {
    [self dismissViewControllerAnimated:YES completion:^{
        
    }];
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
        if (delegate && [delegate respondsToSelector:@selector(CompteCreateAndActivate)]) {
            [delegate CompteCreateAndActivate];
        }
    }];
}


@end
