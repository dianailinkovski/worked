//
//  EditionImageView.m
//  IVOIREKIOSK
//
//  Created by Maxime Julien-Paquet on 2013-12-15.
//  Copyright (c) 2013 Maxime Julien-Paquet. All rights reserved.
//

#import "EditionImageView.h"


@interface EditionImageView () <NSURLConnectionDownloadDelegate> {
    NSMutableData *receivedData;
}

@end

@implementation EditionImageView

@synthesize url, activityIndicator, favImageView;

- (id)initWithFrame:(CGRect)frame {
    self = [super initWithFrame:frame];
    if (self) {
        // Initialization code
        self.backgroundColor = [UIColor whiteColor];
        [self addSubview:[self activityIndicator]];
        [self addSubview:[self favImageView]];
    }
    return self;
}

-(UIActivityIndicatorView *)activityIndicator {
    if (activityIndicator == nil) {
        activityIndicator = [[UIActivityIndicatorView alloc] initWithActivityIndicatorStyle:UIActivityIndicatorViewStyleWhiteLarge];
        activityIndicator.frame = CGRectMake((self.frame.size.width - 30) / 2, (self.frame.size.height - 30) / 2, 30, 30);
        activityIndicator.color = [UIColor blackColor];
        activityIndicator.hidesWhenStopped = YES;
        [activityIndicator startAnimating];
    }
    return activityIndicator;
}

-(UIImageView *)favImageView {
    if (favImageView == nil) {
        favImageView = [[UIImageView alloc] initWithFrame:CGRectMake(self.frame.size.width-20, 0, 15, 0)];
        favImageView.image = [UIImage imageNamed:@"favico-over.png"];
        favImageView.hidden = YES;
        
        favImageView.layer.shadowColor = [UIColor blackColor].CGColor;
        favImageView.layer.shadowOpacity = 0.8;
        favImageView.layer.shadowRadius = 2;
        favImageView.layer.shadowOffset = CGSizeMake(2.0f, 2.0f);
    }
    return favImageView;
}

-(void)startDownload {
    
	NSString *key = [[self.url path] MD5Hash];
	NSData *data = [FTWCache objectForKey:key];
	if (data) {
		UIImage *image = [UIImage imageWithData:data];
		//self.image = image;
        [self performSelectorOnMainThread:@selector(setImage:) withObject:image waitUntilDone:YES];
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
                    [self performSelectorOnMainThread:@selector(setImage:) withObject:imageFromData waitUntilDone:YES];
                    [self.activityIndicator stopAnimating];
                });
            }
        });
    }
    
}

-(void)addBorder {
    [self.layer setBorderColor:[UIColor colorWithWhite:0.2 alpha:0.5].CGColor];
    [self.layer setBorderWidth:1];
}

-(void)addBorderAndDropShadow {
    [self addBorder];
    
    self.layer.shadowColor = [UIColor blackColor].CGColor;
    self.layer.shadowOpacity = 0.5;
    self.layer.shadowRadius = 2;
    self.layer.shadowOffset = CGSizeMake(1.0f, 1.0f);
}

-(void)addInnerShadow {
    CAShapeLayer* shadowLayer = [CAShapeLayer layer];
    [shadowLayer setFrame:[self bounds]];
    
    // Standard shadow stuff
    [shadowLayer setShadowColor:[[UIColor colorWithWhite:0 alpha:1] CGColor]];
    [shadowLayer setShadowOffset:CGSizeMake(-2.0f, -2.0f)];
    [shadowLayer setShadowOpacity:1.0f];
    [shadowLayer setShadowRadius:5];
    
    // Causes the inner region in this example to NOT be filled.
    [shadowLayer setFillRule:kCAFillRuleEvenOdd];
    
    // Create the larger rectangle path.
    CGMutablePathRef path = CGPathCreateMutable();
    CGRect test = CGRectInset(self.bounds, 0, -10);
    test.origin.y += 10;
    CGPathAddRect(path, NULL, test);
    
    
    // Add the inner path so it's subtracted from the outer path.
    // someInnerPath could be a simple bounds rect, or maybe
    // a rounded one for some extra fanciness.
    CGPathAddPath(path, NULL, [[UIBezierPath bezierPathWithRect:[shadowLayer bounds]] CGPath]);
    CGPathCloseSubpath(path);
    
    [shadowLayer setPath:path];
    CGPathRelease(path);
    
    [[self layer] addSublayer:shadowLayer];
    
    CAShapeLayer* maskLayer = [CAShapeLayer layer];
    [maskLayer setPath:[[UIBezierPath bezierPathWithRect:[shadowLayer bounds]] CGPath]];
    [shadowLayer setMask:maskLayer];
}

-(void)showFav {
    favImageView.hidden = NO;
    if (self.frame.size.width > 200) {
        favImageView.frame = CGRectMake(self.frame.size.width-40, 0, 30, 60);
    }
    else if (self.frame.size.width > 100) {
        favImageView.frame = CGRectMake(self.frame.size.width-25, 0, 20, 40);
    }
    else {
        favImageView.frame = CGRectMake(self.frame.size.width-15, 0, 10, 20);
    }
    
    NSLog(@"%@", NSStringFromCGRect(self.frame));
}

-(void)hideFav {
    favImageView.hidden = YES;
    favImageView.frame = CGRectMake(self.frame.size.width-15, 0, 15, 0);
}

-(void)showFavAnimated {
    favImageView.hidden = NO;
    [UIView beginAnimations:nil context:nil];
    [UIView setAnimationDuration:0.2];
    [UIView setAnimationCurve:UIViewAnimationCurveEaseOut];
    favImageView.frame = CGRectMake(self.frame.size.width-15, 0, 15, 30);
    [UIView commitAnimations];
}

-(void)hideFavAnimated {
    
    [UIView beginAnimations:nil context:nil];
    [UIView setAnimationDuration:0.2];
    [UIView setAnimationCurve:UIViewAnimationCurveEaseOut];
    favImageView.frame = CGRectMake(self.frame.size.width-15, 0, 15, 0);
    [UIView commitAnimations];
    
    [favImageView performSelector:@selector(setHidden:) withObject:@YES afterDelay:0.2];
}

@end
