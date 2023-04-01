//
//  VCLabel.m
//  eKiosk
//
//  Created by Maxime Julien-Paquet on 2014-02-20.
//  Copyright (c) 2014 Maxime Julien-Paquet. All rights reserved.
//

#import "VCLabel.h"

#define IMAGE_WIDTH 44
#define IMAGE_HEIGHT 43
#define IMAGE_SPACE 10

#define IMAGE_WIDTH_IPHONE 25
#define IMAGE_HEIGHT_IPHONE 24
#define IMAGE_SPACE_IPHONE 5

@implementation VCLabel

@synthesize prixLabel, ekImageView;

- (id)initWithFrame:(CGRect)frame {
    self = [super initWithFrame:frame];
    if (self) {
        // Initialization code
        [self setup];
    }
    return self;
}
-(void)setup {
    self.backgroundColor = [UIColor clearColor];
    [self addSubview:[self prixLabel]];
    [self addSubview:[self ekImageView]];
}

-(UILabel *)prixLabel {
    if (prixLabel == nil) {
        if (isPad()) {
            prixLabel = [[UILabel alloc] initWithFrame:CGRectMake(0, 0, self.frame.size.width - IMAGE_SPACE - IMAGE_WIDTH - IMAGE_SPACE, self.frame.size.height)];
            prixLabel.font = [UIFont fontWithName:@"Helvetica" size:36];
        }
        else {
            prixLabel = [[UILabel alloc] initWithFrame:CGRectMake(0, 0, self.frame.size.width - IMAGE_SPACE_IPHONE - IMAGE_WIDTH_IPHONE - IMAGE_SPACE_IPHONE, self.frame.size.height)];
            prixLabel.font = [UIFont fontWithName:@"Helvetica" size:22
                              ];
        }
        
        prixLabel.textAlignment = NSTextAlignmentRight;
        
        prixLabel.backgroundColor = [UIColor clearColor];
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
