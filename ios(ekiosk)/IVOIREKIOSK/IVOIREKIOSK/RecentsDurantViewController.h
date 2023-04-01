//
//  RecentsDurantViewController.h
//  eKiosk
//
//  Created by maxime on 2014-04-08.
//  Copyright (c) 2014 Maxime Julien-Paquet. All rights reserved.
//

#import <UIKit/UIKit.h>

@interface RecentsDurantViewController : UIViewController <UITableViewDelegate, UITableViewDataSource>

@property (nonatomic, strong) IBOutlet UITableView *tableView;
@property (nonatomic, strong) NSArray *dataArray;

@end
