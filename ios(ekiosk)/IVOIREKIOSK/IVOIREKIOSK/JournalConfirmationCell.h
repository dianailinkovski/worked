//
//  JournalConfirmationCell.h
//  IVOIREKIOSK
//
//  Created by Maxime Julien-Paquet on 2014-01-28.
//  Copyright (c) 2014 Maxime Julien-Paquet. All rights reserved.
//

#import <UIKit/UIKit.h>

@interface JournalConfirmationCell : UICollectionViewCell

@property (nonatomic, strong) UIImageView *originalImageView;
@property (nonatomic, strong) UIActivityIndicatorView *activityIndicator;
@property (nonatomic, strong) NSDictionary *dataDictionary;

-(void)setDataInView:(NSDictionary*)dic;

@end
