//
//  NBPublicationViewController.m
//  IVOIREKIOSK
//
//  Created by Maxime Julien-Paquet on 2014-01-08.
//  Copyright (c) 2014 Maxime Julien-Paquet. All rights reserved.
//

#import "NBPublicationViewController.h"

@interface NBPublicationViewController () {
    int selected;
}

@end

@implementation NBPublicationViewController

@synthesize tableView, dataArray;

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
    
    dataArray = @[@"30 publications", @"60 publications", @"90 publications", @"120 publications", @"illimités"];
    NSUserDefaults *defaults = [NSUserDefaults standardUserDefaults];
    selected = [[defaults objectForKey:@"nbMaximum"] intValue];
}

- (void)didReceiveMemoryWarning {
    [super didReceiveMemoryWarning];
    // Dispose of any resources that can be recreated.
}

-(NSInteger)tableView:(UITableView *)tableView numberOfRowsInSection:(NSInteger)section {
    return 5;
}

-(UITableViewCell *)tableView:(UITableView *)tableView cellForRowAtIndexPath:(NSIndexPath *)indexPath {
    static NSString *CellIdentifier = @"Cell";
    UITableViewCell *cell = [tableView dequeueReusableCellWithIdentifier:CellIdentifier];
    
    if(cell == nil)
    {
        cell = [[UITableViewCell alloc] initWithStyle:UITableViewCellStyleDefault reuseIdentifier:CellIdentifier];
    }
    
    NSString *string = [dataArray objectAtIndex:indexPath.row];
    cell.textLabel.text = string;
    
    if (indexPath.row == selected) {
        cell.accessoryType = UITableViewCellAccessoryCheckmark;
    }
    else {
        cell.accessoryType = UITableViewCellAccessoryNone;
    }
    
    return cell;
}

-(NSString *)tableView:(UITableView *)tableView titleForFooterInSection:(NSInteger)section {
    return @"Une fois ce nombre de publication atteint, les anciennes publication seront supprimées de votre appareil.\n\nVous pourrez toujours les télécharger à nouveaux via le Kiosque.";
}

-(void)tableView:(UITableView *)tableView didSelectRowAtIndexPath:(NSIndexPath *)indexPath {
    [tableView deselectRowAtIndexPath:indexPath animated:YES];
    UITableViewCell *cell;

    cell = [tableView cellForRowAtIndexPath:[NSIndexPath indexPathForItem:selected inSection:0]];
    cell.accessoryType = UITableViewCellAccessoryNone;
    
    selected = indexPath.row;
    NSUserDefaults *defaults = [NSUserDefaults standardUserDefaults];
    [defaults setObject:[NSNumber numberWithInt:selected] forKey:@"nbMaximum"];
    
    
    cell = [tableView cellForRowAtIndexPath:[NSIndexPath indexPathForItem:selected inSection:0]];
    cell.accessoryType = UITableViewCellAccessoryCheckmark;
    [self.navigationController performSelector:@selector(popViewControllerAnimated:) withObject:@YES afterDelay:0.3];
}

@end
