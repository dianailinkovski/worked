//
//  VirtualCurrencyViewCell.h
//  eKiosk
//
//  Created by Maxime Julien-Paquet on 2014-02-20.
//  Copyright (c) 2014 Maxime Julien-Paquet. All rights reserved.
//

#import <UIKit/UIKit.h>
#import "VCLabel.h"

@interface VirtualCurrencyViewCell : UICollectionViewCell

@property (nonatomic, strong) NSMutableArray *dataArray;

@property (nonatomic, strong) VCLabel *vcLabel;
@property (nonatomic, strong) UIImageView *newsImageView;
@property (nonatomic, strong) UILabel *firstLabel;
//@property (nonatomic, strong) UILabel *secondLabel;
@property (nonatomic, strong) UILabel *escompteLabel;

@property (nonatomic, strong) UILabel *prixLabel;
@property (nonatomic, strong) UIImageView *prixButtonBG;

@property (nonatomic, strong) UIImageView *ligne1;
@property (nonatomic, strong) UIImageView *ligne2;

-(void)setDataInView:(NSMutableArray *)data;
-(void)setPrix:(NSString*)prixString;

@end
