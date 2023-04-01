//
//  VCLabel.h
//  eKiosk
//
//  Created by Maxime Julien-Paquet on 2014-02-20.
//  Copyright (c) 2014 Maxime Julien-Paquet. All rights reserved.
//

#import <UIKit/UIKit.h>

@interface VCLabel : UIView

@property (nonatomic, strong) UILabel *prixLabel;
@property (nonatomic, strong) UIImageView *ekImageView;

-(void)setup;

@end
