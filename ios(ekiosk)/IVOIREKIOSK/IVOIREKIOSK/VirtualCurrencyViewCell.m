//
//  VirtualCurrencyViewCell.m
//  eKiosk
//
//  Created by Maxime Julien-Paquet on 2014-02-20.
//  Copyright (c) 2014 Maxime Julien-Paquet. All rights reserved.
//

#import "VirtualCurrencyViewCell.h"
#import <QuartzCore/QuartzCore.h>

@implementation VirtualCurrencyViewCell

@synthesize dataArray, vcLabel, newsImageView, firstLabel, escompteLabel, prixButtonBG, ligne1, ligne2, prixLabel;

- (id)initWithFrame:(CGRect)frame {
    self = [super initWithFrame:frame];
    if (self) {
        // Initialization code
        self.backgroundColor = [UIColor whiteColor];
        self.clipsToBounds = YES;
        self.layer.cornerRadius = 15;
        [self setup];
        UIImageView *bgFingerPrint;
        if (isPad()) {
            bgFingerPrint = [[UIImageView alloc] initWithFrame:CGRectMake(self.frame.size.width - 127, self.frame.size.height - 146, 127, 146)];
        }
        else {
            bgFingerPrint = [[UIImageView alloc] initWithFrame:CGRectMake(self.frame.size.width - 63, self.frame.size.height - 73, 63, 73)];
        }
        
        bgFingerPrint.image = [UIImage imageNamed:@"fond_fingerprint.png"];
        bgFingerPrint.alpha = 0.1;
        bgFingerPrint.autoresizingMask = UIViewAutoresizingFlexibleLeftMargin | UIViewAutoresizingFlexibleTopMargin;
        [self addSubview:bgFingerPrint];
        [self sendSubviewToBack:bgFingerPrint];
    }
    return self;
}

-(void)setup {
    [self addSubview:[self vcLabel]];
    [self addSubview:[self newsImageView]];
    [self addSubview:[self firstLabel]];
    //[self addSubview:[self secondLabel]];
    [self addSubview:[self escompteLabel]];
    [self addSubview:[self prixButtonBG]];
    [self addSubview:[self prixLabel]];
    
    
    
    [self addSubview:[self ligne1]];
    [self addSubview:[self ligne2]];
}

-(void)prepareForReuse {
    
    [self.vcLabel removeFromSuperview];
    [self.newsImageView removeFromSuperview];
    [self.firstLabel removeFromSuperview];
    //[self.secondLabel removeFromSuperview];
    [self.escompteLabel removeFromSuperview];
    [self.prixLabel removeFromSuperview];
    [self.prixButtonBG removeFromSuperview];
    
    self.vcLabel = nil;
    self.newsImageView = nil;
    self.firstLabel = nil;
    //self.secondLabel = nil;
    self.escompteLabel = nil;
    self.prixLabel = nil;
    self.prixButtonBG = nil;
    
    [self setup];
    
}

-(VCLabel *)vcLabel {
    if (vcLabel == nil) {
        if (isPad()) {
            vcLabel = [[VCLabel alloc] initWithFrame:CGRectMake(20, 20, self.frame.size.width - 40, 53)];
        }
        else {
            vcLabel = [[VCLabel alloc] initWithFrame:CGRectMake(5, 10, self.frame.size.width - 20, 32)];
        }
        
    }
    return vcLabel;
}

-(UIImageView *)newsImageView {
    if (newsImageView == nil) {
        if (isPad()) {
            newsImageView = [[UIImageView alloc] initWithFrame:CGRectMake(20, 109, 43, 35)];
        }
        else {
            newsImageView = [[UIImageView alloc] initWithFrame:CGRectMake(0, 0, 0, 0)];
        }
        
        newsImageView.image = [UIImage imageNamed:@"newspaper_icon.png"];
    }
    return newsImageView;
}

-(UILabel *)firstLabel {
    if (firstLabel == nil) {
        if (isPad()) {
            firstLabel = [[UILabel alloc] initWithFrame:CGRectMake(74, 96, self.frame.size.width - 99, 60)];
            firstLabel.font = [UIFont fontWithName:@"Helvetica" size:12];
            firstLabel.textAlignment = NSTextAlignmentCenter;
        }
        else {
            firstLabel = [[UILabel alloc] initWithFrame:CGRectMake(0, 50, self.frame.size.width, 60)];
            firstLabel.font = [UIFont fontWithName:@"Helvetica" size:10];
            firstLabel.textAlignment = NSTextAlignmentCenter;
        }
        firstLabel.numberOfLines = 4;
        firstLabel.text = @"";
    }
    return firstLabel;
}

/*
-(UILabel *)secondLabel {
    if (secondLabel == nil) {
        if (isPad()) {
            secondLabel = [[UILabel alloc] initWithFrame:CGRectMake(79, 125, self.frame.size.width - 104, 20)];
            secondLabel.font = [UIFont fontWithName:@"Helvetica" size:12];
        }
        else {
            secondLabel = [[UILabel alloc] initWithFrame:CGRectMake(0, 73, self.frame.size.width, 20)];
            secondLabel.font = [UIFont fontWithName:@"Helvetica" size:10];
            secondLabel.textAlignment = NSTextAlignmentCenter;
        }
        
        secondLabel.text = @"000 numéros à 200 EK";
    }
    return secondLabel;
}
*/

-(UILabel *)escompteLabel {
    if (escompteLabel == nil) {
        if (isPad()) {
            escompteLabel = [[UILabel alloc] initWithFrame:CGRectMake(20, 190, self.frame.size.width - 40, 20)];
            escompteLabel.font = [UIFont fontWithName:@"Helvetica" size:20];
        }
        else {
            escompteLabel = [[UILabel alloc] initWithFrame:CGRectMake(10, 115, self.frame.size.width - 20, 20)];
            escompteLabel.font = [UIFont fontWithName:@"Helvetica" size:12];
            escompteLabel.minimumScaleFactor = 0.5;
        }
        
        escompteLabel.textColor = [UIColor orangeColor];
        escompteLabel.textAlignment = NSTextAlignmentCenter;
        
        escompteLabel.text = @"00% de points bonis";
    }
    return escompteLabel;
}
-(UILabel *)prixLabel {
    if (prixLabel == nil) {
        prixLabel = [[UILabel alloc] initWithFrame:prixButtonBG.frame];
        prixLabel.backgroundColor = [UIColor clearColor];
        prixLabel.textColor = [UIColor whiteColor];
        prixLabel.textAlignment = NSTextAlignmentCenter;
        if (isPad()) {
            prixLabel.font = [UIFont fontWithName:@"Helvetica" size:24];
        }
        else {
            prixLabel.font = [UIFont fontWithName:@"Helvetica" size:16];
            prixLabel.minimumScaleFactor = 0.7;
        }
        
    }
    return prixLabel;
}
-(UIImageView *)prixButtonBG {
    if (prixButtonBG == nil) {
        if (isPad()) {
            prixButtonBG = [[UIImageView alloc] initWithFrame:CGRectMake(34, self.frame.size.height - 34 - 44, self.frame.size.width-(34*2), 44)];
        }
        else {
            prixButtonBG = [[UIImageView alloc] initWithFrame:CGRectMake(20, self.frame.size.height - 15 - 30, self.frame.size.width-(20*2), 30)];
        }
        
        [prixButtonBG setBackgroundColor:[UIColor colorWithRed:82.f/255.f green:182.f/255.f blue:21.f/255.f alpha:1]];
        prixButtonBG.layer.cornerRadius = 5;
    }
    return prixButtonBG;
}
-(UIImageView *)ligne1 {
    if (ligne1 == nil) {
        if (isPad()) {
            ligne1 = [[UIImageView alloc] initWithFrame:CGRectMake(20, 86, self.frame.size.width - 40, 2)];
        }
        else {
            ligne1 = [[UIImageView alloc] initWithFrame:CGRectMake(10, 50, self.frame.size.width - 20, 2)];
        }
        
        [ligne1 setBackgroundColor:[UIColor grayColor]];
    }
    return ligne1;
}
-(UIImageView *)ligne2 {
    if (ligne2 == nil) {
        if (isPad()) {
            ligne2 = [[UIImageView alloc] initWithFrame:CGRectMake(20, 165, self.frame.size.width - 40, 2)];
        }
        else {
            ligne2 = [[UIImageView alloc] initWithFrame:CGRectMake(10, 109, self.frame.size.width - 20, 2)];
        }
        
        [ligne2 setBackgroundColor:[UIColor grayColor]];
    }
    return ligne2;
}


-(void)setDataInView:(NSMutableArray *)data {
    self.dataArray = data;
    
    [self.vcLabel.prixLabel setText:[self.dataArray valueForKey:@"quantite"]];
    [self setPrix:[NSString stringWithFormat:@"%@ $USD", [self.dataArray valueForKey:@"prix_usd"]]];
    
    self.firstLabel.text = [self.dataArray valueForKey:@"equivalent"];
    self.escompteLabel.text = [self.dataArray valueForKey:@"bonis"];
    
}
-(void)setPrix:(NSString*)prixString {
    [self.prixLabel setText:prixString];
}

@end
