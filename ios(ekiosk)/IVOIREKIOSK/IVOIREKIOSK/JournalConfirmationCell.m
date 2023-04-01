//
//  JournalConfirmationCell.m
//  IVOIREKIOSK
//
//  Created by Maxime Julien-Paquet on 2014-01-28.
//  Copyright (c) 2014 Maxime Julien-Paquet. All rights reserved.
//

#import "JournalConfirmationCell.h"
#import "FTWCache.h"
#import "NSString+MD5.h"

@implementation JournalConfirmationCell

@synthesize originalImageView, dataDictionary, activityIndicator;

- (id)initWithFrame:(CGRect)frame {
    self = [super initWithFrame:frame];
    if (self) {
        // Initialization code
        
        [self setup];
    }
    return self;
}

-(void)setup {
    [self addSubview:[self originalImageView]];
}

-(void)prepareForReuse {
    [originalImageView removeFromSuperview];
    
    originalImageView = nil;
    
    [self setup];
}

-(UIImageView *)originalImageView {
    if (originalImageView == nil) {
        originalImageView = [[UIImageView alloc] initWithFrame:CGRectMake(0, 0, 120, 75)];
        originalImageView.backgroundColor = [UIColor whiteColor];
        originalImageView.layer.borderColor = [UIColor lightGrayColor].CGColor;
        originalImageView.layer.borderWidth = 1;
    }
    return originalImageView;
}

-(UIActivityIndicatorView *)activityIndicator {
    if (activityIndicator == nil) {
        activityIndicator = [[UIActivityIndicatorView alloc] initWithActivityIndicatorStyle:UIActivityIndicatorViewStyleWhiteLarge];
        activityIndicator.frame = CGRectMake((self.frame.size.width - 40)/2, (self.frame.size.height - 40)/2, 40, 40);
        //activityIndicator.tintColor = [UIColor lightGrayColor];
        activityIndicator.color = [UIColor lightGrayColor];
        activityIndicator.hidesWhenStopped = YES;
        [activityIndicator startAnimating];
    }
    return activityIndicator;
}

-(void)setDataInView:(NSDictionary*)dic {
    self.dataDictionary = dic;
    
    NSString *imagePath = [NSString stringWithFormat:@"%@", [self getFullSizeImageName:[dataDictionary valueForKey:@"image"]]];
    
    [self startDownload:[NSURL URLWithString:imagePath]];
    
}

-(NSString*)getFullSizeImageName:(NSString*)name {
    
    NSString *originalName = [name substringWithRange:NSMakeRange(0, name.length - 4)];
    
    originalName = [originalName stringByAppendingString:@"_a"];
    originalName = [originalName stringByAppendingString:[name substringWithRange:NSMakeRange(name.length - 4, 4)]];
    
    return originalName;
}

-(void)startDownload:(NSURL*)url {
    
	NSString *key = [[url path] MD5Hash];
	NSData *data = [FTWCache objectForKey:key];
	if (data) {
		UIImage *image = [UIImage imageWithData:data];
        [self.originalImageView performSelectorOnMainThread:@selector(setImage:) withObject:image waitUntilDone:YES];
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
                    [self.originalImageView performSelectorOnMainThread:@selector(setImage:) withObject:imageFromData waitUntilDone:YES];
                    [self.activityIndicator stopAnimating];
                });
            }
        });
    }
    
}

@end
