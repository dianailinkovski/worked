//
//  RecentsDurantViewController.m
//  eKiosk
//
//  Created by maxime on 2014-04-08.
//  Copyright (c) 2014 Maxime Julien-Paquet. All rights reserved.
//

#import "RecentsDurantViewController.h"

@interface RecentsDurantViewController () {
    int selected;
}
@end

@implementation RecentsDurantViewController

@synthesize tableView, dataArray;

- (id)initWithNibName:(NSString *)nibNameOrNil bundle:(NSBundle *)nibBundleOrNil
{
    self = [super initWithNibName:nibNameOrNil bundle:nibBundleOrNil];
    if (self) {
        // Custom initialization
    }
    return self;
}

- (void)viewDidLoad {
    [super viewDidLoad];
	// Do any additional setup after loading the view.
    dataArray = @[@"7 jours", @"15 jours", @"30 jours", @"Toujours"];
    NSUserDefaults *defaults = [NSUserDefaults standardUserDefaults];
    selected = [[defaults objectForKey:@"tousAfter"] intValue];
}

- (void)didReceiveMemoryWarning {
    [super didReceiveMemoryWarning];
    // Dispose of any resources that can be recreated.
}

-(NSInteger)tableView:(UITableView *)tableView numberOfRowsInSection:(NSInteger)section {
    return 4;
}

- (UITableViewCell *)tableView:(UITableView *)tableView cellForRowAtIndexPath:(NSIndexPath *)indexPath {
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
    return @"Après ce délai, les éditions ne s'afficheront plus dans la section récents mais seront disponible dans la section tous de votre bibliothèque.";
}

-(void)tableView:(UITableView *)tableView didSelectRowAtIndexPath:(NSIndexPath *)indexPath {
    [tableView deselectRowAtIndexPath:indexPath animated:YES];
    UITableViewCell *cell;
    
    cell = [tableView cellForRowAtIndexPath:[NSIndexPath indexPathForItem:selected inSection:0]];
    cell.accessoryType = UITableViewCellAccessoryNone;
    
    selected = indexPath.row;
    NSUserDefaults *defaults = [NSUserDefaults standardUserDefaults];
    [defaults setObject:[NSNumber numberWithInt:selected] forKey:@"tousAfter"];
    
    
    cell = [tableView cellForRowAtIndexPath:[NSIndexPath indexPathForItem:selected inSection:0]];
    cell.accessoryType = UITableViewCellAccessoryCheckmark;
    [[NSNotificationCenter defaultCenter] postNotificationName:@"ReloadCollectionView" object:nil];
    [self.navigationController performSelector:@selector(popViewControllerAnimated:) withObject:@YES afterDelay:0.3];
}

@end
