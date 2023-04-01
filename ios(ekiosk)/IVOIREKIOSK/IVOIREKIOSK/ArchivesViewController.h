//
//  ArchivesViewController.h
//  IVOIREKIOSK
//
//  Created by Maxime Julien-Paquet on 2014-01-15.
//  Copyright (c) 2014 Maxime Julien-Paquet. All rights reserved.
//

#import <UIKit/UIKit.h>
#import "ArchivesMonthViewController.h"
#import "ArchivesNavBarView.h"
#import "MiniVCLabel.h"

@interface ArchivesViewController : UIViewController <ArchivesNavBarViewDelegate> {
    int currentMonth;
}

@property (nonatomic, strong) ArchivesMonthViewController *archivesMonthViewController;
@property (nonatomic, strong) ArchivesMonthViewController *nextArchivesMonthViewController;
@property (nonatomic, strong) ArchivesNavBarView *navbar;

@property (nonatomic, strong) NSString *idJournal;
@property (nonatomic, strong) NSString *nameJournal;

@property (nonatomic, strong) MiniVCLabel *currentCreditLabel;

-(id)initWithIdJournal:(NSString*)idjournal AndName:(NSString*)name;

@end
