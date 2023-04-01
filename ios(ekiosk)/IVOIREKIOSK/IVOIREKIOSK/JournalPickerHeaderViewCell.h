//
//  JournalPickerHeaderViewCell.h
//  IVOIREKIOSK
//
//  Created by Maxime Julien-Paquet on 2014-01-11.
//  Copyright (c) 2014 Maxime Julien-Paquet. All rights reserved.
//

#import <UIKit/UIKit.h>

@interface JournalPickerHeaderViewCell : UICollectionViewCell

@property (nonatomic, strong) UILabel *titleLabel;
@property (nonatomic, strong) UILabel *compteurLabel;
@property (nonatomic, strong) NSIndexPath *indexPathLocal;
@property (nonatomic, strong) UILabel *titleSwitchLabel;
@property (nonatomic, strong) UISwitch *subscriptionSwitch;

@end
