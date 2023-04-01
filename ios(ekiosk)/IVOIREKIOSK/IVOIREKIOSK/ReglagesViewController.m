//
//  ReglagesViewController.m
//  IVOIREKIOSK
//
//  Created by Maxime Julien-Paquet on 2014-01-08.
//  Copyright (c) 2014 Maxime Julien-Paquet. All rights reserved.
//

#import "ReglagesViewController.h"

@interface ReglagesViewController ()

@end

@implementation ReglagesViewController

@synthesize favLabel, profilLabel;

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
    [[NSNotificationCenter defaultCenter] addObserver:self selector:@selector(FavorisSwitchChangedValue:) name:@"FavorisSwitchChanged" object:nil];
    [[NSNotificationCenter defaultCenter] addObserver:self
                                             selector:@selector(ChangementDeStatusDuCompte:)
                                                 name:@"ChangementDeStatusDuCompte"
                                               object:nil];
    
    //[self.view addSubview:[self currentCreditLabel]];
    
}

-(void)didReceiveMemoryWarning {
    [super didReceiveMemoryWarning];
    // Dispose of any resources that can be recreated.
}

-(void)viewWillAppear:(BOOL)animated {
    [super viewWillAppear:animated];
    
    [self refreshInterface];
    
}

-(void)viewWillDisappear:(BOOL)animated {
    [super viewWillDisappear:animated];
    [self.navigationController setNavigationBarHidden:NO animated:YES];
}

-(void)dealloc {
    [[NSNotificationCenter defaultCenter] removeObserver:self name:@"FavorisSwitchChanged" object:nil];
    [[NSNotificationCenter defaultCenter] removeObserver:self name:@"ChangementDeStatusDuCompte" object:nil];
}

-(void)ChangementDeStatusDuCompte:(NSNotification*)notif {
    [self refreshInterface];
}

-(void)refreshInterface {
    [self.navigationController setNavigationBarHidden:YES animated:YES];
    
    NSUserDefaults *defaults = [NSUserDefaults standardUserDefaults];
    
    //int current = [[defaults valueForKey:@"ekcredit"] intValue];
    //[self.currentCreditLabel.prixLabel setText:[NSString stringWithFormat:@"%d",current]];
    
    //NSString *prenom = [defaults objectForKey:@"prenom"];
    //NSString *nom = [defaults objectForKey:@"nom"];
    
    NSString *username = [defaults objectForKey:@"username"];
    
    if (username != nil && ![username isEqualToString:@""]) {
        [profilLabel setText:[NSString stringWithFormat:@"%@",username]];
    }
    else {
        [profilLabel setText:@"Profil"];
    }
    
    
    BOOL exclureFavoris = [[defaults objectForKey:@"excluFavoris"] boolValue];
    [self setFavorisText:exclureFavoris];

}

/*
-(MiniVCLabel *)currentCreditLabel {
    if (currentCreditLabel == nil) {
        currentCreditLabel = [[MiniVCLabel alloc] initWithFrame:CGRectMake(280-120, 5, 110, 40)];
    }
    return currentCreditLabel;
}
*/

-(void)FavorisSwitchChangedValue:(NSNotification*)notif {
    NSLog(@"FavorisSwitchChanged:");
    NSLog(@"%@",notif.object);
    [self setFavorisText:[(NSNumber*)notif.object boolValue]];
    NSUserDefaults *defaults = [NSUserDefaults standardUserDefaults];
    [defaults setObject:[NSNumber numberWithBool:[(NSNumber*)notif.object boolValue]] forKey:@"excluFavoris"];
}

-(void)setFavorisText:(BOOL)value {
    if (value) {
        [favLabel setText:@"Vos Favoris ne seront pas supprimés automatiquement."];
        
        //favLabel.frame = CGRectMake(20, 315, 240, 38);
    }
    else {
        [favLabel setText:@"Vos Favoris seront supprimés automatiquement par le nettoyage automatique."];
        
        //favLabel.frame = CGRectMake(20, 315, 240, 55);
    }
}

@end
