//
//  ArchivesNavBarView.m
//  IVOIREKIOSK
//
//  Created by Maxime Julien-Paquet on 2014-01-17.
//  Copyright (c) 2014 Maxime Julien-Paquet. All rights reserved.
//

#import "ArchivesNavBarView.h"

@implementation ArchivesNavBarView

@synthesize leftLabel, centerLabel, rightLabel, monthArray, leftButton, rightButton, delegate;

-(id)initWithFrame:(CGRect)frame {
    self = [super initWithFrame:frame];
    if (self) {
        // Initialization code
        
        self.autoresizingMask = UIViewAutoresizingFlexibleWidth;
        
        UINavigationBar *navBar = [[UINavigationBar alloc] initWithFrame:CGRectMake(0, 0, self.frame.size.width, 44)];
        navBar.autoresizingMask = UIViewAutoresizingFlexibleWidth;
        [self addSubview:navBar];
        
        [self addSubview:[self leftButton]];
        [self addSubview:[self rightButton]];
        
        //[self addSubview:[self leftLabel]];
        //[self addSubview:[self centerLabel]];
        //[self addSubview:[self rightLabel]];
    }
    return self;
}

-(void)clean {
    [leftLabel removeFromSuperview];
    [centerLabel removeFromSuperview];
    [rightLabel removeFromSuperview];
    
    leftLabel = nil;
    centerLabel = nil;
    rightLabel = nil;
}

-(void)setup {
    [self addSubview:[self leftLabel]];
    [self addSubview:[self centerLabel]];
    [self addSubview:[self rightLabel]];
    
    if (currentMonth > 0) {
        [self.leftLabel setText:[self.monthArray objectAtIndex:currentMonth-1]];
    }
    if (currentMonth < 11) {
        [self.rightLabel setText:[self.monthArray objectAtIndex:currentMonth+1]];
    }
    [self.centerLabel setText:[self.monthArray objectAtIndex:currentMonth]];
    
    [self bringSubviewToFront:[self leftButton]];
    [self bringSubviewToFront:[self rightButton]];
}

-(void)setcurrentmonth:(int)currentmonth {
    
    currentMonth = currentmonth;
    
}

-(UILabel *)leftLabel {
    if (leftLabel == nil) {
        if (isPad()) {
            leftLabel = [[UILabel alloc] initWithFrame:CGRectMake(20, 0, 200, 44)];
            leftLabel.font = [UIFont fontWithName:@"Helvetica-Bold" size:22];
        }
        else {
            leftLabel = [[UILabel alloc] initWithFrame:CGRectMake(0, 0, 100, 44)];
            leftLabel.font = [UIFont fontWithName:@"Helvetica-Bold" size:14];
            leftLabel.adjustsFontSizeToFitWidth = YES;
            leftLabel.minimumScaleFactor = 0.5;
        }
        
        leftLabel.autoresizingMask = UIViewAutoresizingFlexibleRightMargin;
        leftLabel.textColor = [UIColor colorWithWhite:0.3 alpha:1];
        leftLabel.textAlignment = NSTextAlignmentCenter;
        leftLabel.transform = CGAffineTransformScale(leftLabel.transform, 0.75, 0.75);
    }
    return leftLabel;
}

-(UILabel *)rightLabel {
    if (rightLabel == nil) {
        if (isPad()) {
            rightLabel = [[UILabel alloc] initWithFrame:CGRectMake(self.frame.size.width - 220, 0, 200, 44)];
            rightLabel.font = [UIFont fontWithName:@"Helvetica-Bold" size:22];
        }
        else {
            rightLabel = [[UILabel alloc] initWithFrame:CGRectMake(self.frame.size.width - 100, 0, 100, 44)];
            rightLabel.font = [UIFont fontWithName:@"Helvetica-Bold" size:14];
            rightLabel.adjustsFontSizeToFitWidth = YES;
            rightLabel.minimumScaleFactor = 0.5;
        }
        
        rightLabel.autoresizingMask = UIViewAutoresizingFlexibleLeftMargin;
        rightLabel.textColor = [UIColor colorWithWhite:0.3 alpha:1];
        rightLabel.textAlignment = NSTextAlignmentCenter;
        rightLabel.transform = CGAffineTransformScale(rightLabel.transform, 0.75, 0.75);
    }
    return rightLabel;
}

-(UILabel *)centerLabel {
    if (centerLabel == nil) {
        if (isPad()) {
            centerLabel = [[UILabel alloc] initWithFrame:CGRectMake((self.frame.size.width/2) - 100, 0, 200, 44)];
            centerLabel.font = [UIFont fontWithName:@"Helvetica-Bold" size:22];
        }
        else {
            centerLabel = [[UILabel alloc] initWithFrame:CGRectMake((self.frame.size.width/2) - 60, 0, 120, 44)];
            centerLabel.font = [UIFont fontWithName:@"Helvetica-Bold" size:18];
            centerLabel.adjustsFontSizeToFitWidth = YES;
            centerLabel.minimumScaleFactor = 0.5;
        }
        
        centerLabel.autoresizingMask = UIViewAutoresizingFlexibleLeftMargin | UIViewAutoresizingFlexibleRightMargin;
        centerLabel.textColor = [UIColor colorWithWhite:0.3 alpha:1];
        centerLabel.textAlignment = NSTextAlignmentCenter;
    }
    return centerLabel;
}

-(UIButton *)leftButton {
    if (leftButton == nil) {
        leftButton = [UIButton buttonWithType:UIButtonTypeCustom];
        if (isPad()) {
            leftButton.frame = CGRectMake(20, 0, 200, 44);
        }
        else {
            leftButton.frame = CGRectMake(0, 0, 100, 44);
        }
        
        [leftButton addTarget:self action:@selector(leftTouched:) forControlEvents:UIControlEventTouchUpInside];
    }
    return leftButton;
}

-(UIButton *)rightButton {
    if (rightButton == nil) {
        rightButton = [UIButton buttonWithType:UIButtonTypeCustom];
        if (isPad()) {
            rightButton.frame = CGRectMake(self.frame.size.width - 220, 0, 200, 44);
        }
        else {
            rightButton.frame = CGRectMake(self.frame.size.width - 100, 0, 100, 44);
        }
        
        [rightButton addTarget:self action:@selector(rightTouched:) forControlEvents:UIControlEventTouchUpInside];
    }
    return rightButton;
}

-(void)animationLeft {
    nextSide = centerLabel.center;
    nextCenter = leftLabel.center;
    
    [UIView animateWithDuration:0.4
                     animations:^{
                         //self.rightLabel.frame = CGRectMake(self.frame.size.width - 420, 0, 200, 44);
                         //self.centerLabel.frame = CGRectMake((self.frame.size.width/2) - 400, 0, 200, 44);
                         //self.leftLabel.frame = CGRectMake(-220, 0, 200, 44);
                         
                         [centerLabel setCenter:nextCenter];
                         [rightLabel setCenter:nextSide];
                         
                         self.rightLabel.transform = CGAffineTransformScale(rightLabel.transform, 1.25, 1.25);
                         self.centerLabel.transform = CGAffineTransformScale(centerLabel.transform, 0.75, 0.75);
                         self.leftLabel.transform = CGAffineTransformScale(centerLabel.transform, 0.5, 0.5);
                         self.leftLabel.alpha = 0.5;
                     }
                     completion:^(BOOL finished){
                         
                         ++currentMonth;
                         [self clean];
                         [self setup];
                         touched = NO;
                     }];
}

-(void)animationRight {
    nextSide = centerLabel.center;
    nextCenter = rightLabel.center;
    
    [UIView animateWithDuration:0.4
                     animations:^{
                         //self.leftLabel.frame = CGRectMake(220, 0, 200, 44);
                         //self.centerLabel.frame = CGRectMake((self.frame.size.width/2) + 200, 0, 200, 44);
                         //self.rightLabel.frame = CGRectMake(self.frame.size.width + 180, 0, 200, 44);
                         
                         [centerLabel setCenter:nextCenter];
                         [leftLabel setCenter:nextSide];
                         
                         self.leftLabel.transform = CGAffineTransformScale(leftLabel.transform, 1.25, 1.25);
                         self.centerLabel.transform = CGAffineTransformScale(centerLabel.transform, 0.75, 0.75);
                         self.rightLabel.transform = CGAffineTransformScale(centerLabel.transform, 0.5, 0.5);
                         self.rightLabel.alpha = 0.5;
                         
                     }
                     completion:^(BOOL finished){
                         --currentMonth;
                         [self clean];
                         [self setup];
                         touched = NO;
                     }];
    
}

-(void)leftTouched:(id)sender {
    if (currentMonth == 0) {
        return;
    }
    if (touched) {
        return;
    }
    if (delegate && [delegate respondsToSelector:@selector(leftGetTouched)]) {
        touched = YES;
        [delegate rightGetTouched];
    }
}

-(void)rightTouched:(id)sender {
    if (currentMonth == 11) {
        return;
    }
    if (touched) {
        return;
    }
    if (delegate && [delegate respondsToSelector:@selector(rightGetTouched)]) {
        touched = YES;
        [delegate leftGetTouched];
    }
}

@end
