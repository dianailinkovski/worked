//
//  JournalPickerViewCell.m
//  IVOIREKIOSK
//
//  Created by Maxime Julien-Paquet on 2014-01-11.
//  Copyright (c) 2014 Maxime Julien-Paquet. All rights reserved.
//

#import "JournalPickerViewCell.h"
#import "GrayScaleImage.h"

@implementation JournalPickerViewCell

@synthesize subView, originalImageView, grayImageView, titleLabel, dataDictionary, activityIndicator, checkmarkImageView,bannerImageView;

- (id)initWithFrame:(CGRect)frame {
    self = [super initWithFrame:frame];
    if (self) {
        // Initialization code
        self.opaque = NO;
        self.backgroundColor = [UIColor whiteColor];
        self.layer.shadowColor = [UIColor blackColor].CGColor;
        self.layer.shadowOpacity = 0.5;
        self.layer.shadowRadius = 2;
        self.layer.shadowOffset = CGSizeMake(1.0f, 1.0f);
        
        self.layer.cornerRadius = 10;
        
        [self setup];
        
    }
    return self;
}

-(void)setup {
    isSelected = NO;
    [[self subView] addSubview:[self originalImageView]];
    [[self subView] addSubview:[self grayImageView]];
    [self addSubview:[self subView]];
    [self addSubview:[self checkmarkImageView]];
    [self addSubview:[self activityIndicator]];
    [self addSubview:[self titleLabel]];
    [self addSubview:[self bannerImageView]];

}

-(void)prepareForReuse {
    [activityIndicator removeFromSuperview];
    [subView removeFromSuperview];
    [originalImageView removeFromSuperview];
    [grayImageView removeFromSuperview];
    [titleLabel removeFromSuperview];
    [checkmarkImageView removeFromSuperview];
    [bannerImageView removeFromSuperview];


    activityIndicator = nil;
    subView = nil;
    originalImageView = nil;
    grayImageView = nil;
    titleLabel = nil;
    checkmarkImageView = nil;
    bannerImageView = nil;
    
    [self setup];
}

-(UIView *)subView {
    if (subView == nil) {
        if (isPad()) {
            subView = [[UIView alloc] initWithFrame:CGRectMake(20, 20, 160, 100)];
        }
        else {
            subView = [[UIView alloc] initWithFrame:CGRectMake(14, 14, 160*0.7, 100*0.7)];
        }
        
        subView.backgroundColor = [UIColor clearColor];
        subView.layer.borderColor = [UIColor lightGrayColor].CGColor;
        subView.layer.borderWidth = 1;
    }
    return subView;
}

-(UIImageView *)originalImageView {
    if (originalImageView == nil) {
        originalImageView = [[UIImageView alloc] initWithFrame:subView.bounds];
        originalImageView.backgroundColor = [UIColor whiteColor];
    }
    return originalImageView;
}

-(UIImageView *)grayImageView {
    if (grayImageView == nil) {
        grayImageView = [[UIImageView alloc] initWithFrame:subView.bounds];
        grayImageView.backgroundColor = [UIColor whiteColor];
        
    }
    return grayImageView;
}

-(UIImageView *)checkmarkImageView {
    if (checkmarkImageView == nil) {
        checkmarkImageView = [[UIImageView alloc] initWithFrame:CGRectMake(self.subView.frame.size.width + self.subView.frame.origin.x -15,
                                                                           self.subView.frame.origin.y -15,
                                                                           30,
                                                                           30)];
        checkmarkImageView.backgroundColor = [UIColor clearColor];
        checkmarkImageView.image = [UIImage imageNamed:@"checkmark-trans.png"];
        //checkmarkImageView.hidden = YES;
        checkmarkImageView.alpha = 0;
        
    }
    return checkmarkImageView;
}

-(UIActivityIndicatorView *)activityIndicator {
    if (activityIndicator == nil) {
        activityIndicator = [[UIActivityIndicatorView alloc] initWithActivityIndicatorStyle:UIActivityIndicatorViewStyleWhiteLarge];
        if (isPad()) {
            activityIndicator.frame = CGRectMake((self.frame.size.width - 40)/2, ((100 - 40)/2) + 20, 40, 40);
        }
        else {
            activityIndicator.frame = CGRectMake((self.frame.size.width - 40)/2, (((100*0.7) - 40)/2) + 14, 40, 40);
        }
        
        //activityIndicator.tintColor = [UIColor lightGrayColor];
        activityIndicator.color = [UIColor lightGrayColor];
        activityIndicator.hidesWhenStopped = YES;
        [activityIndicator startAnimating];
    }
    return activityIndicator;
}

-(UILabel *)titleLabel {
    if (titleLabel == nil) {
        if (isPad()) {
            titleLabel = [[ UILabel alloc] initWithFrame:CGRectMake(10, self.frame.size.height - 55, self.frame.size.width-20, 50)];
            titleLabel.font = [UIFont fontWithName:@"Helvetica" size:20];
        }
        else {
            titleLabel = [[ UILabel alloc] initWithFrame:CGRectMake(10, self.frame.size.height - 40, self.frame.size.width-20, 35)];
            titleLabel.font = [UIFont fontWithName:@"Helvetica" size:16];
        }
        
        
        titleLabel.textAlignment = NSTextAlignmentCenter;
        titleLabel.backgroundColor = [UIColor clearColor];
        titleLabel.textColor = [UIColor blackColor];
        titleLabel.numberOfLines = 2;
        titleLabel.adjustsFontSizeToFitWidth = YES;
        //titleLabel.text = [dataDictionary valueForKey:@"title"];
        
    }
    return titleLabel;
}

-(UIImageView *)bannerImageView
{
    if (bannerImageView == nil) {
        if (isPad()) {
            bannerImageView = [[UIImageView alloc] initWithFrame:CGRectMake(-2, -2, 80, 80)];
        }
        else {
            if([UIScreen mainScreen].bounds.size.height == 568.0) {
                bannerImageView = [[UIImageView alloc] initWithFrame:CGRectMake(-2, -2, 55, 55)];
            }
            else {
                bannerImageView = [[UIImageView alloc] initWithFrame:CGRectMake(-2, -2, 55, 55)];
            }
        }
        
        [bannerImageView setImage:[UIImage imageNamed:@"Header_subscription"]];
        [bannerImageView setBackgroundColor:[UIColor clearColor]];
        [bannerImageView setHidden:YES];
    }
    return bannerImageView;
}




-(NSString*)getFullSizeImageName:(NSString*)name {
    
    NSString *originalName = [name substringWithRange:NSMakeRange(0, name.length - 4)];
    
    originalName = [originalName stringByAppendingString:@"_a"];
    originalName = [originalName stringByAppendingString:[name substringWithRange:NSMakeRange(name.length - 4, 4)]];
    
    return originalName;
}

-(void)setDataInView:(NSDictionary*)dic {
    self.dataDictionary = dic;
    [self.titleLabel setText:[dataDictionary valueForKey:@"nom"]];
    
    if ([[dataDictionary valueForKey:@"selected"] intValue] == 1) {
        isSelected = YES;
        [grayImageView removeFromSuperview];
        [[self subView] addSubview:[self originalImageView]];
        titleLabel.font = [UIFont fontWithName:@"Helvetica-Bold" size:20];
        [self.checkmarkImageView setAlpha:1];
        
    }
    
    NSString *imagePath = [NSString stringWithFormat:@"%@", [self getFullSizeImageName:[dataDictionary valueForKey:@"image"]]];
    //NSLog(@"url = %@",imagePath);
    
    [self startDownload:[NSURL URLWithString:imagePath]];
    
}

-(void)setArchivesDataInView:(NSDictionary*)dic {
    self.dataDictionary = dic;
    [self.titleLabel setText:[dataDictionary valueForKey:@"nom"]];
    
    isSelected = YES;
    [grayImageView removeFromSuperview];
    [[self subView] addSubview:[self originalImageView]];
    //titleLabel.font = [UIFont fontWithName:@"Helvetica-Bold" size:20];
    //[self.checkmarkImageView setAlpha:1];
    
    NSString *imagePath = [NSString stringWithFormat:@"%@", [self getFullSizeImageName:[dataDictionary valueForKey:@"image"]]];
    //NSLog(@"url = %@",imagePath);
    if ([[dataDictionary valueForKey:@"isSubscription"] intValue] == 1)
    {
        self.bannerImageView.hidden = NO;
    }
    else
    {
        self.bannerImageView.hidden = YES;

    }
    
    [self startDownload:[NSURL URLWithString:imagePath]];
}

-(void)startDownload:(NSURL*)url {
    
	NSString *key = [[url path] MD5Hash];
	NSData *data = [FTWCache objectForKey:key];
	if (data) {
		UIImage *image = [UIImage imageWithData:data];
        [self performSelectorOnMainThread:@selector(setImage:) withObject:image waitUntilDone:YES];
        [self.activityIndicator stopAnimating];
	} else {
        dispatch_queue_t queue = dispatch_get_global_queue(DISPATCH_QUEUE_PRIORITY_DEFAULT, 0ul);
        dispatch_async(queue, ^{
            NSData *data = [NSData dataWithContentsOfURL:url];
            
            UIImage *imageFromData = [UIImage imageWithData:data];
            
            [FTWCache setObject:UIImagePNGRepresentation(imageFromData) forKey:key];
            UIImage *imageToSet = imageFromData;
            if (imageToSet) {
                dispatch_async(dispatch_get_main_queue(), ^{
                    [self performSelectorOnMainThread:@selector(setImage:) withObject:imageFromData waitUntilDone:YES];
                    [self.activityIndicator stopAnimating];
                });
            }
        });
    }
    
}

-(void)setImage:(UIImage*)image {
    [self.originalImageView setImage:image];
    GrayScaleImage *grayImage = [[GrayScaleImage alloc] initWithCGImage:[image CGImage]];
    [self.grayImageView setImage:[grayImage toGrayscale]];
}

-(void)flipImageView {
    
    if (isSelected) {
        isSelected = NO;
        [UIView beginAnimations:nil context:NULL];
        [UIView setAnimationDuration:0.5];
        [UIView setAnimationTransition:UIViewAnimationTransitionFlipFromRight forView:self.subView cache:NO];
        //[self.divisionView removeFromSuperview];
        //[self.subView addSubview:self.conferenceView];
        [originalImageView removeFromSuperview];
        [[self subView] addSubview:[self grayImageView]];
        titleLabel.font = [UIFont fontWithName:@"Helvetica" size:20];
        [self.checkmarkImageView setAlpha:0];
        [UIView commitAnimations];
    }
    else {
        isSelected = YES;
        [UIView beginAnimations:nil context:NULL];
        [UIView setAnimationDuration:0.5];
        [UIView setAnimationTransition:UIViewAnimationTransitionFlipFromLeft forView:self.subView cache:NO];
        //[self.conferenceView removeFromSuperview];
        //[self.subView addSubview:self.divisionView];
        [grayImageView removeFromSuperview];
        [[self subView] addSubview:[self originalImageView]];
        titleLabel.font = [UIFont fontWithName:@"Helvetica-Bold" size:20];
        [self.checkmarkImageView setAlpha:1];
        [UIView commitAnimations];
    }
    
}

-(BOOL)getIsSelected {
    return isSelected;
}

@end
