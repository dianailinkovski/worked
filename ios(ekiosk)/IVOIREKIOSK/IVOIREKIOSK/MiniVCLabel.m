//
//  MiniVCLabel.m
//  eKiosk
//
//  Created by Maxime Julien-Paquet on 2014-02-23.
//  Copyright (c) 2014 Maxime Julien-Paquet. All rights reserved.
//

#import "MiniVCLabel.h"

#define IMAGE_WIDTH 27
#define IMAGE_HEIGHT 27
#define IMAGE_SPACE 5

#define IMAGE_WIDTH_IPHONE 20
#define IMAGE_HEIGHT_IPHONE 20
#define IMAGE_SPACE_IPHONE 5

@implementation MiniVCLabel

@synthesize prixLabel, ekImageView;

- (id)initWithFrame:(CGRect)frame {
    self = [super initWithFrame:frame];
    if (self) {
        // Initialization code
        [self setup];
        [[NSNotificationCenter defaultCenter] addObserver:self
                                                 selector:@selector(UpdateCreditCount)
                                                     name:@"UpdateCreditCount"
                                                   object:nil];
        [[NSNotificationCenter defaultCenter] addObserver:self
                                                 selector:@selector(UpdateCreditCount)
                                                     name:@"ChangementDeStatusDuCompte"
                                                   object:nil];
    }
    return self;
}

-(void)dealloc {
    [[NSNotificationCenter defaultCenter] removeObserver:self name:@"UpdateCreditCount" object:nil];
    [[NSNotificationCenter defaultCenter] removeObserver:self name:@"ChangementDeStatusDuCompte" object:nil];
}

-(void)setup {
    NSLog(@"setup");
    self.backgroundColor = [UIColor clearColor];
    [self addSubview:[self prixLabel]];
    [self addSubview:[self ekImageView]];

    [self.prixLabel setFont:[UIFont fontWithName:@"Helvetica" size:20]];
    
    /*CGRect frame = self.ekImageView.frame;
    CGPoint tempPoint = self.ekImageView.center;
    frame.size.width = frame.size.width / 1.5;
    frame.size.height = frame.size.height / 1.5;
    
    self.ekImageView.frame = frame;
    self.ekImageView.center = tempPoint;*/
}

-(void)UpdateCreditCount {
    NSUserDefaults *defaults = [NSUserDefaults standardUserDefaults];
    int current = [[defaults valueForKey:@"ekcredit"] intValue];
    //[self.prixLabel setText:[NSString stringWithFormat:@"%d",current]];
    [self.prixLabel performSelectorOnMainThread:@selector(setText:) withObject:[NSString stringWithFormat:@"%d",current] waitUntilDone:YES];
}

-(UILabel *)prixLabel {
    if (prixLabel == nil) {
        if (isPad()) {
            prixLabel = [[UILabel alloc] initWithFrame:CGRectMake(0, 0, self.frame.size.width - IMAGE_SPACE - IMAGE_WIDTH - IMAGE_SPACE, self.frame.size.height)];
            prixLabel.font = [UIFont fontWithName:@"Helvetica" size:36];
        }
        else {
            prixLabel = [[UILabel alloc] initWithFrame:CGRectMake(0, 0, self.frame.size.width - IMAGE_SPACE_IPHONE - IMAGE_WIDTH_IPHONE - IMAGE_SPACE_IPHONE, self.frame.size.height)];
            prixLabel.font = [UIFont fontWithName:@"Helvetica" size:24];
        }
        
        prixLabel.textAlignment = NSTextAlignmentRight;
        prixLabel.backgroundColor = [UIColor clearColor];
        prixLabel.minimumScaleFactor = 0.5;
    }
    return prixLabel;
}

-(UIImageView *)ekImageView {
    if (ekImageView == nil) {
        if (isPad()) {
            ekImageView = [[UIImageView alloc] initWithFrame:CGRectMake(self.frame.size.width - IMAGE_WIDTH - IMAGE_SPACE, (self.frame.size.height - IMAGE_HEIGHT) / 2, IMAGE_WIDTH, IMAGE_HEIGHT)];
        }
        else {
            ekImageView = [[UIImageView alloc] initWithFrame:CGRectMake(self.frame.size.width - IMAGE_WIDTH_IPHONE - IMAGE_SPACE_IPHONE, (self.frame.size.height - IMAGE_HEIGHT_IPHONE) / 2, IMAGE_WIDTH_IPHONE, IMAGE_HEIGHT_IPHONE)];
        }
        
        ekImageView.image = [UIImage imageNamed:@"ek-coin-icon.png"];
    }
    return ekImageView;
}

@end
