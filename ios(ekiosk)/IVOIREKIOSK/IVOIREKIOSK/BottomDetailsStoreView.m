//
//  BottomDetailsStoreView.m
//  IVOIREKIOSK
//
//  Created by Maxime Julien-Paquet on 2013-12-24.
//  Copyright (c) 2013 Maxime Julien-Paquet. All rights reserved.
//

#import "BottomDetailsStoreView.h"
#import "EditionImageView.h"
#import <QuartzCore/QuartzCore.h>


@implementation BottomDetailsStoreView

@synthesize imageView, dateLabel, data, delegate, edition;

- (id)initWithFrame:(CGRect)frame AndDictionary:(NSDictionary*)dictionary {
    self = [super initWithFrame:frame];
    if (self) {
        // Initialization code
        self.backgroundColor = [UIColor clearColor];
        
        [self setData:dictionary];
        [self addSubview:[self imageView]];
        [self addSubview:[self dateLabel]];
        
        
        
        UITapGestureRecognizer *singleTap = [[UITapGestureRecognizer alloc] initWithTarget:self action:@selector(singleTap:)];
        singleTap.numberOfTapsRequired = 1;
        singleTap.delegate = self;
        [self addGestureRecognizer:singleTap];
    }
    return self;
}

-(id)initWithFrame:(CGRect)frame AndEdition:(Editions*)refEdition {
    self = [super initWithFrame:frame];
    if (self) {
        // Initialization code
        self.backgroundColor = [UIColor clearColor];
        
        [self setEdition:refEdition];
        [self addSubview:[self imageView]];
        [self addSubview:[self dateLabel]];
        
        
        
        UITapGestureRecognizer *singleTap = [[UITapGestureRecognizer alloc] initWithTarget:self action:@selector(singleTap:)];
        singleTap.numberOfTapsRequired = 1;
        singleTap.delegate = self;
        [self addGestureRecognizer:singleTap];
    }
    return self;
}

-(EditionImageView *)imageView {
    if (imageView == nil) {
        imageView = [[EditionImageView alloc] initWithFrame:CGRectMake(15,
                                                                       15,
                                                                       STATIC_EDITIONSIMAGEVIEW_WIDTH,
                                                                       STATIC_EDITIONSIMAGEVIEW_HEIGHT)];
        NSString *urlString;
        if (self.edition != nil) {
            urlString = self.edition.coverpath;
        }
        else {
            urlString = [self.data valueForKey:@"coverPath"];
        }
        
        [imageView setUrl:[NSURL URLWithString:urlString]];
        [imageView startDownload];
        [imageView addBorderAndDropShadow];
    }
    return imageView;
}
-(UILabel *)dateLabel {
    if (dateLabel == nil) {
        dateLabel = [[UILabel alloc] initWithFrame:CGRectMake(15, imageView.frame.origin.y + imageView.frame.size.height + 5, self.frame.size.width - 30, 20)];
        [dateLabel setTextAlignment:NSTextAlignmentCenter];
        NSString *dateString;
        if (self.edition != nil) {
            NSDateFormatter *dateFormatter = [[NSDateFormatter alloc] init];
            [dateFormatter setDateFormat:@"yyyy-MM-dd"];
            dateString = [dateFormatter stringFromDate:self.edition.publicationdate];
        }
        else {
            dateString = [self.data valueForKey:@"datePublication"];
        }
        [dateLabel performSelectorOnMainThread:@selector(setText:) withObject:dateString waitUntilDone:NO];
        
    }
    return dateLabel;
}

-(void)setSelected {
    self.layer.cornerRadius = 5;
    self.backgroundColor = [UIColor whiteColor];
}
-(void)setUnselected {
    self.layer.cornerRadius = 0;
    self.backgroundColor = [UIColor clearColor];
}
-(BOOL)isSelected {
    if (self.backgroundColor != [UIColor clearColor]) {
        return YES;
    }
    return NO;
}

- (void)singleTap:(UITapGestureRecognizer *)gesture {
    [self setSelected];
    if (delegate && [delegate respondsToSelector:@selector(BottomDetailsStoreViewTouched:)]) {
        [delegate BottomDetailsStoreViewTouched:self];
    }
}

@end
