//
//  AdsHeaderCollectionView.m
//  eKiosk
//
//  Created by maxime on 2014-07-31.
//  Copyright (c) 2014 Maxime Julien-Paquet. All rights reserved.
//

#import "AdsHeaderCollectionView.h"

@implementation AdsHeaderCollectionView

@synthesize url, activityIndicator, imageView, overButton, urlToOpen, downloadedImage;

-(id)init {
    self = [super init];
    if (self) {
        // Initialization code
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

-(void)setup {
    self.backgroundColor = [UIColor clearColor];
    [self addSubview:[self imageView]];
    [self addSubview:[self activityIndicator]];
    [self addSubview:[self overButton]];
}

-(void)prepareForReuse {
    [super prepareForReuse];
    
    [self.imageView removeFromSuperview];
    [self.activityIndicator removeFromSuperview];
    [self.overButton removeFromSuperview];
    
    self.imageView = nil;
    self.activityIndicator = nil;
    self.overButton = nil;
    
    [self setup];
}

-(UIActivityIndicatorView *)activityIndicator {
    if (activityIndicator == nil) {
        activityIndicator = [[UIActivityIndicatorView alloc] initWithActivityIndicatorStyle:UIActivityIndicatorViewStyleWhiteLarge];
        activityIndicator.frame = CGRectMake((self.frame.size.width - 30) / 2, (self.frame.size.height - 30) / 2, 30, 30);
        activityIndicator.autoresizingMask = UIViewAutoresizingFlexibleBottomMargin|UIViewAutoresizingFlexibleLeftMargin|UIViewAutoresizingFlexibleRightMargin|UIViewAutoresizingFlexibleTopMargin;
        activityIndicator.color = [UIColor blackColor];
        activityIndicator.hidesWhenStopped = YES;
        [activityIndicator stopAnimating];
    }
    return activityIndicator;
}

-(UIImageView *)imageView {
    if (imageView == nil) {
        imageView = [[UIImageView alloc] initWithFrame:self.bounds];
        imageView.autoresizingMask = UIViewAutoresizingFlexibleHeight | UIViewAutoresizingFlexibleWidth;
        [imageView setBackgroundColor:[UIColor clearColor]];
        
    }
    return imageView;
}

-(UIButton *)overButton {
    if (overButton == nil) {
        overButton = [UIButton buttonWithType:UIButtonTypeCustom];
        overButton.frame = self.bounds;
        [overButton addTarget:self action:@selector(openUrlOnTouch) forControlEvents:UIControlEventTouchUpInside];
    }
    return overButton;
}
/*
-(void)startDownload {
    
    [activityIndicator startAnimating];
    
    NSString *key = [[self.url path] MD5Hash];
	NSData *data = [FTWCache objectForKey:key];
	if (data) {
		UIImage *image = [UIImage imageWithData:data];
		//self.image = image;
        [self.imageView performSelectorOnMainThread:@selector(setImage:) withObject:image waitUntilDone:YES];
        [self.activityIndicator stopAnimating];
	} else {
        dispatch_queue_t queue = dispatch_get_global_queue(DISPATCH_QUEUE_PRIORITY_DEFAULT, 0ul);
        dispatch_async(queue, ^{
            NSData *data = [NSData dataWithContentsOfURL:self.url];
            
            UIImage *imageFromData = [UIImage imageWithData:data];
            
            [FTWCache setObject:UIImagePNGRepresentation(imageFromData) forKey:key];
            UIImage *imageToSet = imageFromData;
            if (imageToSet) {
                dispatch_async(dispatch_get_main_queue(), ^{
                    //self.image = imageFromData;
                    [self.imageView performSelectorOnMainThread:@selector(setImage:) withObject:imageFromData waitUntilDone:YES];
                    [self.activityIndicator stopAnimating];
                });
            }
        });
    }
}
 */
-(void)startDownload {
    if (downloadedImage != nil) {
        [self.imageView performSelectorOnMainThread:@selector(setImage:) withObject:downloadedImage waitUntilDone:YES];
    }
    else {
    [activityIndicator startAnimating];
    
        dispatch_queue_t queue = dispatch_get_global_queue(DISPATCH_QUEUE_PRIORITY_DEFAULT, 0ul);
        dispatch_async(queue, ^{
            NSData *data = [NSData dataWithContentsOfURL:self.url];
            
            downloadedImage = [UIImage imageWithData:data];
            if (downloadedImage) {
                dispatch_async(dispatch_get_main_queue(), ^{
                    //self.image = imageFromData;
                    [self.imageView performSelectorOnMainThread:@selector(setImage:) withObject:downloadedImage waitUntilDone:YES];
                    [self.activityIndicator stopAnimating];
                });
            }
        });
    }
}


-(void)setImageUrl:(NSString*)tempUrl {
    NSUserDefaults *defaults = [NSUserDefaults standardUserDefaults];
    NSString *username = [defaults objectForKey:@"username"];
    NSString *password = [defaults objectForKey:@"password"];
    if (username == nil || password == nil || [username isEqualToString:@""] || [password isEqualToString:@""]) {
        [self setUrl:[NSURL URLWithString:tempUrl]];
    }
    else {
        tempUrl = [tempUrl stringByAppendingFormat:@"&username=%@&password=%@", username, password];
        [self setUrl:[NSURL URLWithString:tempUrl]];
    }
    
}

-(void)openUrlOnTouch {
    if (![urlToOpen isEqualToString:@""]) {
        NSUserDefaults *defaults = [NSUserDefaults standardUserDefaults];
        NSString *username = [defaults objectForKey:@"username"];
        NSString *password = [defaults objectForKey:@"password"];
        NSString *tempUrl = urlToOpen;
        if (username == nil || password == nil || [username isEqualToString:@""] || [password isEqualToString:@""]) {
            [[UIApplication sharedApplication] openURL:[NSURL URLWithString: tempUrl]];
        }
        else {
            tempUrl = [tempUrl stringByAppendingFormat:@"&username=%@&password=%@", username, password];
            [[UIApplication sharedApplication] openURL:[NSURL URLWithString: tempUrl]];
        }
        
    }
}
-(void)clearView {
    [self prepareForReuse];
}

@end
