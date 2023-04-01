//
//  DetailHeaderViewCell.m
//  eKiosk
//
//  Created by Maxime Julien-Paquet on 2014-03-03.
//  Copyright (c) 2014 Maxime Julien-Paquet. All rights reserved.
//

#import "DetailHeaderViewCell.h"

@implementation DetailHeaderViewCell

@synthesize imageView, nomLabel, dateLabel, categorieLabel, prixButton, prixStringLabel, creditwarningLabel, firstLine, secondLine, otherIssuesLabel, rightView, noteButtonView, noteButtonLabel, verifAccountValideAI, adsView, detailView, downloadedImage;

-(id)init {
    self = [super init];
    if (self) {
        [self setup];
    }
    return self;
}
-(id)initWithFrame:(CGRect)frame {
    self = [super initWithFrame:frame];
    if (self) {
        // Initialization code
        [self setup];
    }
    return self;
}

-(void)dealloc {
    [[NSNotificationCenter defaultCenter] removeObserver:self name:@"HeaderSwitchToLandscape" object:nil];
    [[NSNotificationCenter defaultCenter] removeObserver:self name:@"HeaderSwitchToPortrait" object:nil];
}

-(void)prepareForReuse {
    [super prepareForReuse];
    
    [[NSNotificationCenter defaultCenter] removeObserver:self name:@"HeaderSwitchToLandscape" object:nil];
    [[NSNotificationCenter defaultCenter] removeObserver:self name:@"HeaderSwitchToPortrait" object:nil];
    
    [self setDownloadedImage:[self.adsView downloadedImage]];
    
    [self.imageView removeFromSuperview];
    [self.nomLabel removeFromSuperview];
    [self.dateLabel removeFromSuperview];
    [self.categorieLabel removeFromSuperview];
    [self.prixButton removeFromSuperview];
    [self.prixStringLabel removeFromSuperview];
    [self.creditwarningLabel removeFromSuperview];
    [self.firstLine removeFromSuperview];
    [self.secondLine removeFromSuperview];
    [self.otherIssuesLabel removeFromSuperview];
    [self.rightView removeFromSuperview];
    [self.noteButtonView removeFromSuperview];
    [self.noteButtonLabel removeFromSuperview];
    [self.verifAccountValideAI removeFromSuperview];
    [self.adsView removeFromSuperview];
    [self.detailView removeFromSuperview];
    
    self.imageView = nil;
    self.nomLabel = nil;
    self.dateLabel = nil;
    self.categorieLabel = nil;
    self.prixButton = nil;
    self.prixStringLabel = nil;
    self.creditwarningLabel = nil;
    self.firstLine = nil;
    self.secondLine = nil;
    self.otherIssuesLabel = nil;
    self.rightView = nil;
    self.noteButtonView = nil;
    self.noteButtonLabel = nil;
    self.verifAccountValideAI = nil;
    self.adsView = nil;
    self.detailView = nil;
    
    [self setup];
}

-(void)setup {
    [[NSNotificationCenter defaultCenter] addObserver:self selector:@selector(AnimationToLandscape:) name:@"HeaderSwitchToLandscape" object:nil];
    [[NSNotificationCenter defaultCenter] addObserver:self selector:@selector(AnimationToPortrait:) name:@"HeaderSwitchToPortrait" object:nil];
    
    self.autoresizingMask = UIViewAutoresizingFlexibleWidth | UIViewAutoresizingFlexibleHeight;
    self.backgroundColor = [UIColor clearColor];
    
    [self addSubview:[self adsView]];
    
    [self addSubview:[self detailView]];
    [self.detailView addSubview:[self imageView]];
    [self.detailView addSubview:[self rightView]];
    
    [self.rightView addSubview:[self nomLabel]];
    [self.rightView addSubview:[self dateLabel]];
    [self.rightView addSubview:[self categorieLabel]];
    [self.rightView addSubview:[self prixButton]];
    [self.rightView addSubview:[self noteButtonView]];
    [self.rightView addSubview:[self noteButtonLabel]];
    [self.rightView bringSubviewToFront:prixButton];
    [self.rightView addSubview:[self verifAccountValideAI]];
    [self.rightView addSubview:[self prixStringLabel]];
    [self.rightView addSubview:[self creditwarningLabel]];
    [self.rightView addSubview:[self firstLine]];
    [self.rightView addSubview:[self secondLine]];
    [self.detailView addSubview:[self otherIssuesLabel]];
    
    
   // [self.rightView setBackgroundColor:[UIColor redColor]];
   // [self.detailView setBackgroundColor:[UIColor greenColor]];

    if (downloadedImage != nil) {
        [self.adsView setDownloadedImage:downloadedImage];
    }
    
}

-(UIView *)rightView {
    if (rightView == nil) {
        if (isPad()) {
            rightView = [[UIView alloc] initWithFrame:CGRectMake(348, 34, 400, 360)];
        }
        else {
            rightView = [[UIView alloc] initWithFrame:CGRectMake(149, -3, 160, 200)];
        }

        rightView.backgroundColor = [UIColor clearColor];
    }
    return rightView;
}

-(EditionImageView *)imageView {
    if (imageView == nil) {
        if (isPad()) {
            imageView = [[EditionImageView alloc] initWithFrame:CGRectMake(44, 34, 250, 350)];
        }
        else {
            imageView = [[EditionImageView alloc] initWithFrame:CGRectMake(11, 5, 125, 175)];
        }
        
        [imageView addBorderAndDropShadow];
    }
    return imageView;
}
-(UILabel *)nomLabel {
    if (nomLabel == nil) {
        if (isPad()) {
            nomLabel = [[UILabel alloc] initWithFrame:CGRectMake(0, 0, 400, 40)];
            nomLabel.font = [UIFont fontWithName:@"Helvetica-Bold" size:32];
            
        }
        else {
            nomLabel = [[UILabel alloc] initWithFrame:CGRectMake(0, 5, 160, 30)];
            nomLabel.font = [UIFont fontWithName:@"Helvetica-Bold" size:18];
            
        }
        
        nomLabel.textAlignment = NSTextAlignmentCenter;
        nomLabel.textColor = [UIColor darkGrayColor];
        nomLabel.adjustsFontSizeToFitWidth = YES;
        nomLabel.minimumScaleFactor = 0.5;
    }
    return nomLabel;
}
-(UILabel *)categorieLabel {
    if (categorieLabel == nil) {
        if (isPad()) {
            categorieLabel = [[UILabel alloc] initWithFrame:CGRectMake(0, 48, 400, 30)];
            categorieLabel.font = [UIFont fontWithName:@"Helvetica" size:24];
        }
        else {
            categorieLabel = [[UILabel alloc] initWithFrame:CGRectMake(0, 29, 160, 25)];
            categorieLabel.font = [UIFont fontWithName:@"Helvetica" size:15];
        }
        
        categorieLabel.textAlignment = NSTextAlignmentCenter;
        categorieLabel.textColor = [UIColor darkGrayColor];
    }
    return categorieLabel;
}
-(UILabel *)dateLabel {
    if (dateLabel == nil) {
        if (isPad()) {
            dateLabel = [[UILabel alloc] initWithFrame:CGRectMake(0, 138, 400, 30)];
            dateLabel.font = [UIFont fontWithName:@"Helvetica" size:24];
            dateLabel.numberOfLines = 1;
        }
        else {
            dateLabel = [[UILabel alloc] initWithFrame:CGRectMake(0, 57, 160, 35)];
            dateLabel.font = [UIFont fontWithName:@"Helvetica" size:13];
            dateLabel.numberOfLines = 2;
        }
        
        dateLabel.textAlignment = NSTextAlignmentCenter;
        dateLabel.textColor = [UIColor darkGrayColor];
    }
    return dateLabel;
}
-(VCLabel *)prixStringLabel {
    if (prixStringLabel == nil) {
        if (isPad()) {
            prixStringLabel = [[VCLabel alloc] initWithFrame:CGRectMake(122, 200, 156, 50)];
        }
        else {
            prixStringLabel = [[VCLabel alloc] initWithFrame:CGRectMake(23, 106, 100, 30)];
        }
    }
    return prixStringLabel;
}
-(UILabel *)creditwarningLabel {
    if (creditwarningLabel == nil) {
        if (isPad()) {
            creditwarningLabel = [[UILabel alloc] initWithFrame:CGRectMake(0, 249, 400, 21)];
            creditwarningLabel.font = [UIFont fontWithName:@"Helvetica" size:13];
        }
        else {
            creditwarningLabel = [[UILabel alloc] initWithFrame:CGRectMake(0, 138, 160, 21)];
            creditwarningLabel.font = [UIFont fontWithName:@"Helvetica" size:10];
        }
        
        creditwarningLabel.textAlignment = NSTextAlignmentCenter;
        creditwarningLabel.textColor = [UIColor darkGrayColor];
        creditwarningLabel.text = @"Crédits ekiosk nécessaires";
    }
    return creditwarningLabel;
}
-(UIButton *)prixButton {
    if (prixButton == nil) {
        prixButton = [UIButton buttonWithType:UIButtonTypeCustom];
        
        if (isPad()) {
            prixButton.frame = CGRectMake(50, 280, 300, 50);
            [prixButton.titleLabel setFont:[UIFont fontWithName:@"Helvetica" size:30]];
        }
        else {
            prixButton.frame = CGRectMake(9, 159, 140, 30);
            [prixButton.titleLabel setFont:[UIFont fontWithName:@"Helvetica" size:16]];
        }
        
        [prixButton setBackgroundColor:[UIColor colorWithRed:82.f/255.f green:182.f/255.f blue:21.f/255.f alpha:1]];
        [prixButton.layer setCornerRadius:5];
        
        prixButton.tag = 8912;
        
        
    }
    return prixButton;
}
-(UIView *)detailView {
    if (detailView == nil) {
        if (isPad()) {
            detailView = [[UIView alloc] initWithFrame:CGRectMake(0, adsView.frame.origin.y + adsView.frame.size.height, self.bounds.size.width, self.bounds.size.height - adsView.frame.size.height)];
        }
        else {
            detailView = [[UIView alloc] initWithFrame:CGRectMake(0, adsView.frame.origin.y + adsView.frame.size.height + 8, self.bounds.size.width, self.bounds.size.height - adsView.frame.size.height)];
        }
        detailView.backgroundColor = [UIColor clearColor];
        detailView.autoresizingMask =   UIViewAutoresizingFlexibleWidth|UIViewAutoresizingFlexibleTopMargin|UIViewAutoresizingFlexibleBottomMargin|UIViewAutoresizingFlexibleHeight;
        [detailView setClipsToBounds:NO];
    }
    return detailView;
}
-(AdsHeaderCollectionView *)adsView {
    if (adsView == nil) {
        float ratiopub = 0.13888;
        
        adsView = [[AdsHeaderCollectionView alloc] initWithFrame:CGRectMake(0, 0, self.bounds.size.width, self.bounds.size.width*ratiopub)];
        adsView.autoresizingMask = UIViewAutoresizingFlexibleWidth;
        
    }
    return adsView;
}

-(UIActivityIndicatorView *)verifAccountValideAI {
    if (verifAccountValideAI == nil) {
        //verifAccountValideAI = [[UIActivityIndicatorView alloc] initWithActivityIndicatorStyle:UIActivityIndicatorViewStyleWhiteLarge];
        if (isPad()) {
            verifAccountValideAI = [[UIActivityIndicatorView alloc] initWithActivityIndicatorStyle:UIActivityIndicatorViewStyleWhiteLarge];
            verifAccountValideAI.frame = CGRectMake((prixButton.frame.size.width-40)/2 + prixButton.frame.origin.x, (prixButton.frame.size.height-40)/2 + prixButton.frame.origin.y, 40, 40);
        }
        else {
            verifAccountValideAI = [[UIActivityIndicatorView alloc] initWithActivityIndicatorStyle:UIActivityIndicatorViewStyleWhite];
            verifAccountValideAI.frame = CGRectMake((prixButton.frame.size.width-30)/2 + prixButton.frame.origin.x, (prixButton.frame.size.height-30)/2 + prixButton.frame.origin.y, 30, 30);
        }
        verifAccountValideAI.color = [UIColor blackColor];
        verifAccountValideAI.hidesWhenStopped = YES;
        
    }
    return verifAccountValideAI;
}

-(UIView *)noteButtonView {
    if (noteButtonView == nil) {
        if (isPad()) {
            noteButtonView = [[UIView alloc] initWithFrame:CGRectMake(prixButton.frame.origin.x + 10, prixButton.frame.origin.y + prixButton.frame.size.height - 10, prixButton.frame.size.width - 20, 46)];
        }
        else {
            noteButtonView = [[UIView alloc] initWithFrame:CGRectMake(prixButton.frame.origin.x + 5, prixButton.frame.origin.y + prixButton.frame.size.height - 10, prixButton.frame.size.width - 10, 30)];
        }
        noteButtonView.backgroundColor = [UIColor yellowColor];
        noteButtonView.layer.cornerRadius = 5;
        
        noteButtonView.layer.shadowColor = [UIColor blackColor].CGColor;
        noteButtonView.layer.shadowOpacity = 0.5;
        noteButtonView.layer.shadowRadius = 2;
        noteButtonView.layer.shadowOffset = CGSizeMake(1.0f, 1.0f);
    }
    return noteButtonView;
}

-(UILabel *)noteButtonLabel {
    if (noteButtonLabel == nil) {
        if (isPad()) {
            noteButtonLabel = [[UILabel alloc] initWithFrame:CGRectMake(noteButtonView.frame.origin.x + 5, prixButton.frame.origin.y + prixButton.frame.size.height, noteButtonView.frame.size.width - 10, 35)];
            noteButtonLabel.font = [UIFont fontWithName:@"Helvetica" size:15];
        }
        else {
            noteButtonLabel = [[UILabel alloc] initWithFrame:CGRectMake(noteButtonView.frame.origin.x + 5, prixButton.frame.origin.y + prixButton.frame.size.height, noteButtonView.frame.size.width - 10, 20)];
            noteButtonLabel.font = [UIFont fontWithName:@"Helvetica" size:8];
        }
        noteButtonLabel.text = @"3 téléchargements par achat";
        noteButtonLabel.numberOfLines = 0;
        noteButtonLabel.textColor = [UIColor darkGrayColor];
        noteButtonLabel.textAlignment = NSTextAlignmentCenter;
        
    }
    return noteButtonLabel;
}

-(UIImageView *)firstLine {
    if (firstLine == nil) {
        if (isPad()) {
            firstLine = [[UIImageView alloc] initWithFrame:CGRectMake(0, 104, 400, 3)];
        }
        else {
            firstLine = [[UIImageView alloc] initWithFrame:CGRectMake(0, 51, 160, 1)];
        }
        
        firstLine.backgroundColor = [UIColor lightGrayColor];
    }
    return firstLine;
}
-(UIImageView *)secondLine {
    if (secondLine == nil) {
        if (isPad()) {
            secondLine = [[UIImageView alloc] initWithFrame:CGRectMake(0, 0, 0, 0)];
        }
        else {
            secondLine = [[UIImageView alloc] initWithFrame:CGRectMake(0, 95, 160, 1)];
        }
        
        secondLine.backgroundColor = [UIColor lightGrayColor];
    }
    return secondLine;
}

-(UILabel *)otherIssuesLabel {
    if (otherIssuesLabel == nil) {
        if (isPad()) {
            
            otherIssuesLabel = [[UILabel alloc] initWithFrame:CGRectMake(40,425, 200, 31)];
            otherIssuesLabel.font = [UIFont fontWithName:@"Helvetica" size:26];
        }
        else {
            
            otherIssuesLabel = [[UILabel alloc] initWithFrame:CGRectMake(15, 210, 200, 26)];
            otherIssuesLabel.font = [UIFont fontWithName:@"Helvetica" size:20];
        }
        otherIssuesLabel.autoresizingMask = UIViewAutoresizingFlexibleLeftMargin | UIViewAutoresizingFlexibleBottomMargin;
        otherIssuesLabel.textColor = [UIColor darkGrayColor];
        otherIssuesLabel.text = @"Autres éditions";
        otherIssuesLabel.userInteractionEnabled = NO;
        [otherIssuesLabel setClipsToBounds:YES];
    }
    return otherIssuesLabel;
}

-(void)movePrixButtonBought:(BOOL)bought {
    
    CGRect frameButton = self.prixButton.frame;
    
    if (bought) {
        if (isPad()) {
            frameButton.origin.y = 234;
        }
        else {
            frameButton.origin.y = 110;
        }
    }
    else {
        
        if (isPad()) {
            frameButton.origin.y = 314;
        }
        else {
            frameButton.origin.y = 162;
        }
    }
    
    [self.prixButton setFrame:frameButton];
    
    self.noteButtonView.frame = CGRectMake(self.noteButtonView.frame.origin.x, prixButton.frame.origin.y + prixButton.frame.size.height - 10, self.noteButtonView.frame.size.width, self.noteButtonView.frame.size.height);
    
    self.noteButtonLabel.frame = CGRectMake(self.noteButtonLabel.frame.origin.x, prixButton.frame.origin.y + prixButton.frame.size.height, self.noteButtonLabel.frame.size.width, self.noteButtonLabel.frame.size.height);
    
}

-(void)AnimationToLandscape:(float)duration {
    CGRect frame = self.rightView.frame;
    frame.origin.x = 448;
    self.rightView.frame = frame;
    /*
    CGRect frame = self.nomLabel.frame;
    frame.origin.x = 448;
    self.nomLabel.frame = frame;
    
    frame = self.categorieLabel.frame;
    frame.origin.x = 448;
    self.categorieLabel.frame = frame;
    
    frame = self.firstLine.frame;
    frame.origin.x = 448;
    self.firstLine.frame = frame;
    
    frame = self.dateLabel.frame;
    frame.origin.x = 448;
    self.dateLabel.frame = frame;
    
    frame = self.prixStringLabel.frame;
    frame.origin.x = 570;
    self.prixStringLabel.frame = frame;
    
    frame = self.creditwarningLabel.frame;
    frame.origin.x = 448;
    self.creditwarningLabel.frame = frame;
    
    frame = self.prixButton.frame;
    frame.origin.x = 498;
    self.prixButton.frame = frame;
    */
    
}
-(void)AnimationToPortrait:(float)duration {
    CGRect frame = self.rightView.frame;
    frame.origin.x = 348;
    self.rightView.frame = frame;
    /*
    CGRect frame = self.nomLabel.frame;
    frame.origin.x = 348;
    self.nomLabel.frame = frame;
    
    frame = self.categorieLabel.frame;
    frame.origin.x = 348;
    self.categorieLabel.frame = frame;
    
    frame = self.firstLine.frame;
    frame.origin.x = 348;
    self.firstLine.frame = frame;
    
    frame = self.dateLabel.frame;
    frame.origin.x = 348;
    self.dateLabel.frame = frame;
    
    frame = self.prixStringLabel.frame;
    frame.origin.x = 470;
    self.prixStringLabel.frame = frame;
    
    frame = self.creditwarningLabel.frame;
    frame.origin.x = 348;
    self.creditwarningLabel.frame = frame;
    
    frame = self.prixButton.frame;
    frame.origin.x = 398;
    self.prixButton.frame = frame;
    */
}

-(void)PubModOff {
    [self.adsView setHidden:YES];
    
    CGRect tempFrame = self.detailView.frame;
    tempFrame.origin.y = 0;
    self.detailView.frame = tempFrame;
}
-(void)PubModOn {
    [self.adsView setHidden:NO];
    
    CGRect tempFrame = self.detailView.frame;
    tempFrame.origin.y = adsView.frame.origin.y + adsView.frame.size.height;
    self.detailView.frame = tempFrame;
}

@end
