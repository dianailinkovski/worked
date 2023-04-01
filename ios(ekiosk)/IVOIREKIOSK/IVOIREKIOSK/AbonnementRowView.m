//
//  AbonnementRowView.m
//  NGSER
//
//  Created by Maxime Julien-Paquet on 2013-11-06.
//
//

#import "AbonnementRowView.h"
#import <QuartzCore/QuartzCore.h>

@implementation AbonnementRowView

@synthesize abonnementLabel, titleLabel, prixLabel, touchButton;

-(id)initWithFrame:(CGRect)frame {
    self = [super initWithFrame:frame];
    if (self) {
        // Initialization code
        
        [self addSubview:[self abonnementLabel]];
        [self addSubview:[self titleLabel]];
        [self addSubview:[self prixLabel]];
        [self addSubview:[self touchButton]];
    }
    return self;
}

-(UILabel *)abonnementLabel {
    if (abonnementLabel == nil) {
        
        abonnementLabel = [[UILabel alloc] initWithFrame:CGRectMake(20, 20, self.frame.size.width - 20, 30)];
        abonnementLabel.font = [UIFont fontWithName:@"Helvetica-Light" size:20];
        abonnementLabel.backgroundColor = [UIColor clearColor];
        abonnementLabel.textColor = [UIColor colorWithRed:0.1960f green:0.2f blue:0.2156f alpha:1];
        //abonnementLabel.textAlignment = UITextAlignmentCenter;
        abonnementLabel.text = @"ABONNEMENT";
    }
    return abonnementLabel;
}

-(UILabel *)titleLabel {
    if (titleLabel == nil) {
        
        titleLabel = [[UILabel alloc] initWithFrame:CGRectMake(20, 55, self.frame.size.width - 20, 40)];
        titleLabel.font = [UIFont fontWithName:@"Helvetica" size:26];
        titleLabel.backgroundColor = [UIColor clearColor];
        titleLabel.textColor = [UIColor colorWithRed:0.1960f green:0.2f blue:0.2156f alpha:1];
        //titleLabel.textAlignment = UITextAlignmentCenter;
        
    }
    return titleLabel;
}

-(UILabel *)prixLabel {
    if (prixLabel == nil) {
        
        prixLabel = [[UILabel alloc] initWithFrame:CGRectMake(self.frame.size.width - 120, 55, 100, 40)];
        prixLabel.font = [UIFont fontWithName:@"Arial" size:22];
        prixLabel.textColor = [UIColor colorWithWhite:0 alpha:0.9];
        prixLabel.textAlignment = NSTextAlignmentCenter;
        
        prixLabel.backgroundColor = [UIColor colorWithRed:0.1960f green:0.2f blue:0.2156f alpha:0.1];
        [prixLabel.layer setCornerRadius:10];
        [prixLabel.layer setBorderWidth:2];
        [prixLabel.layer setBorderColor:[UIColor colorWithRed:0.2196 green:0.8196 blue:0.3373 alpha:0.5].CGColor];
        
    }
    return prixLabel;
}

-(void)addBottomSeparator {
    UIImageView *borderbottom = [[UIImageView alloc] initWithFrame:CGRectMake(30, self.frame.size.height - 1, self.frame.size.width - 60, 1)];
    borderbottom.backgroundColor = [UIColor colorWithWhite:0 alpha:0.1];
    [self addSubview:borderbottom];
}

-(void)addLeftSeparator {
    UIImageView *borderbottom = [[UIImageView alloc] initWithFrame:CGRectMake(0, 15, 1, self.frame.size.height - 30)];
    borderbottom.backgroundColor = [UIColor colorWithWhite:0 alpha:0.1];
    [self addSubview:borderbottom];
}

-(UIButton *)touchButton {
    if (touchButton ==  nil) {
        touchButton = [UIButton buttonWithType:UIButtonTypeRoundedRect];
        touchButton.frame = CGRectMake(titleLabel.frame.origin.x + titleLabel.frame.size.width, 10, 150, self.frame.size.height - 20);
        touchButton.autoresizingMask = UIViewAutoresizingFlexibleBottomMargin | UIViewAutoresizingFlexibleLeftMargin;
        //[actionButton setTitleColor:[UIColor whiteColor] forState:UIControlStateNormal];
        //[actionButton.titleLabel setFont:[UIFont fontWithName:@"Helvetica-Light" size:26]];
        
    }
    return touchButton;
}


@end
