//
//  JournalPickerViewCell.h
//  IVOIREKIOSK
//
//  Created by Maxime Julien-Paquet on 2014-01-11.
//  Copyright (c) 2014 Maxime Julien-Paquet. All rights reserved.
//

#import <UIKit/UIKit.h>
#import "FTWCache.h"
#import "NSString+MD5.h"

@interface JournalPickerViewCell : UICollectionViewCell {
    BOOL isSelected;
}

@property (nonatomic, strong) UIView *subView;
@property (nonatomic, strong) UIImageView *originalImageView;
@property (nonatomic, strong) UIImageView *grayImageView;
@property (nonatomic, strong) UIImageView *checkmarkImageView,*bannerImageView;
@property (nonatomic, strong) UIActivityIndicatorView *activityIndicator;
@property (nonatomic, strong) UILabel *titleLabel;
@property (nonatomic, strong) UILabel *titleSwitchLabel;
@property (nonatomic, strong) NSDictionary *dataDictionary;
@property (nonatomic, strong) UISwitch *subscriptionSwitch;
-(void)setDataInView:(NSDictionary*)dic;
-(void)setArchivesDataInView:(NSDictionary*)dic;
-(void)flipImageView;

-(BOOL)getIsSelected;

@end
