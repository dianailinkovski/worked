//
//  VirtualCurrencyHeaderViewCell.m
//  eKiosk
//
//  Created by Maxime Julien-Paquet on 2014-02-20.
//  Copyright (c) 2014 Maxime Julien-Paquet. All rights reserved.
//

#import "VirtualCurrencyHeaderViewCell.h"

@implementation VirtualCurrencyHeaderViewCell

@synthesize headerImageView, descriptionLabel, descriptionBG;

- (id)initWithFrame:(CGRect)frame {
    self = [super initWithFrame:frame];
    if (self) {
        // Initialization code
        [self setup];
    }
    return self;
}

-(void)setup {
    [self addSubview:[self headerImageView]];
    [self addSubview:[self descriptionBG]];
    [self addSubview:[self descriptionLabel]];
}

-(void)prepareForReuse {
    [self.headerImageView removeFromSuperview];
    [self.descriptionLabel removeFromSuperview];
    [self.descriptionBG removeFromSuperview];
    
    self.headerImageView = nil;
    self.descriptionLabel = nil;
    self.descriptionBG = nil;
    
    [self setup];
}

-(UIImageView *)headerImageView {
    if (headerImageView == nil) {
        if (isPad()) {
            headerImageView = [[UIImageView alloc] initWithFrame:CGRectMake((self.frame.size.width - 341) / 2, 20, 341, 130)];
        }
        else {
            headerImageView = [[UIImageView alloc] initWithFrame:CGRectMake((self.frame.size.width - 170) / 2, 10, 170, 65)];
        }
        headerImageView.image = [UIImage imageNamed:@"logo_ekiosk.png"];
        headerImageView.autoresizingMask = UIViewAutoresizingFlexibleLeftMargin | UIViewAutoresizingFlexibleRightMargin;
    }
    return headerImageView;
}

-(UILabel *)descriptionLabel {
    if (descriptionLabel == nil) {
        if (isPad()) {
            descriptionLabel = [[UILabel alloc] initWithFrame:CGRectMake(30, 170, self.frame.size.width - 60, 40)];
            descriptionLabel.font = [UIFont fontWithName:@"Helvetica" size:14];
            descriptionLabel.numberOfLines = 2;
        }
        else {
            descriptionLabel = [[UILabel alloc] initWithFrame:CGRectMake(20, 90, self.frame.size.width - 40, 60)];
            descriptionLabel.font = [UIFont fontWithName:@"Helvetica" size:12];
            descriptionLabel.numberOfLines = 4;
        }
        
        descriptionLabel.autoresizingMask = UIViewAutoresizingFlexibleWidth;
        descriptionLabel.textAlignment = NSTextAlignmentCenter;
        descriptionLabel.textColor = [UIColor colorWithWhite:0.3 alpha:1];
        
        
        descriptionLabel.text = @"Achetez des crédits ekiosk pour débloquer le téléchargement des journaux et magazines.\nObtenez des points bonis et économisez gros en achetant une plus grande quantité de points";
    }
    return descriptionLabel;
}

-(UIImageView *)descriptionBG {
    if (descriptionBG == nil) {
        if (isPad()) {
            descriptionBG = [[UIImageView alloc] initWithFrame:CGRectMake(20, 160, self.frame.size.width - 40, 60)];
        }
        else {
            descriptionBG = [[UIImageView alloc] initWithFrame:CGRectMake(10, 80, self.frame.size.width - 20, 80)];
        }
        
        descriptionBG.autoresizingMask = UIViewAutoresizingFlexibleWidth;
        descriptionBG.backgroundColor = [UIColor colorWithWhite:1 alpha:0.5];
    }
    return descriptionBG;
}

@end