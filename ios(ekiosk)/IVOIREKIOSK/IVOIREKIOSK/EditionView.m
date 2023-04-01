//
//  EditionView.m
//  IVOIREKIOSK
//
//  Created by Maxime Julien-Paquet on 2013-12-14.
//  Copyright (c) 2013 Maxime Julien-Paquet. All rights reserved.
//

#import "EditionView.h"
#import "EditionImageView.h"
#import "Editions.h"
#import "DetailEditionsViewController.h"
#import "ReaderDocument.h"
#import "MainPDFReaderViewController.h"
#import "AppDelegate.h"
#import "FFCircularProgressView.h"
#import <PDFTouch/PDFTouch.h>

@implementation EditionView

@synthesize coverImageView, edition, longPressGestureRecognizer, refViewController, tapGestureRecognizer, progressView, overImageView, bannerImageView;

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
    
    [self addSubview:[self coverImageView]];
    [self addGestureRecognizer:[self longPressGestureRecognizer]];
    [self addGestureRecognizer:[self tapGestureRecognizer]];
    [self addSubview:[self overImageView]];
    [self addSubview:[self bannerImageView]];
    [self addSubview:[self progressView]];
}

-(void)prepareForReuse {
    [super prepareForReuse];
    
    [coverImageView removeFromSuperview];
    [overImageView removeFromSuperview];
    [bannerImageView removeFromSuperview];
    [progressView removeFromSuperview];
    [self removeGestureRecognizer:longPressGestureRecognizer];
    [self removeGestureRecognizer:tapGestureRecognizer];
    
    overImageView = nil;
    coverImageView = nil;
    longPressGestureRecognizer = nil;
    tapGestureRecognizer = nil;
    progressView = nil;
    bannerImageView = nil;
    
    [self setup];
    
}

-(FFCircularProgressView *)progressView {
    if (progressView == nil) {
        if (isPad()) {
            progressView = [[FFCircularProgressView alloc] initWithFrame:CGRectMake(0, 0, 60, 60)];
            progressView.lineWidth = 2;
        }
        else {
            progressView = [[FFCircularProgressView alloc] initWithFrame:CGRectMake(0, 0, 40, 40)];
            progressView.lineWidth = 1;
        }
        progressView.center = coverImageView.center;
        progressView.tintColor = [UIColor colorWithRed:0.9412 green:0.4510 blue:0.0 alpha:1.0];
        
        progressView.hidden = YES;
    }
    return progressView;
}
-(UIImageView *)overImageView {
    if (overImageView == nil) {
        overImageView = [[UIImageView alloc] initWithFrame:self.coverImageView.frame];
        overImageView.backgroundColor = [UIColor whiteColor];
        overImageView.alpha = 0.6;
        overImageView.hidden = YES;
    }
    return overImageView;
}

-(EditionImageView *)coverImageView {
    if (coverImageView == nil) {
        if (isPad()) {
            coverImageView = [[EditionImageView alloc] initWithFrame:CGRectMake(0, 0, STATIC_EDITIONSIMAGEVIEW_WIDTH, STATIC_EDITIONSIMAGEVIEW_HEIGHT)];
        }
        else {
            if([UIScreen mainScreen].bounds.size.height == 568.0) {
                coverImageView = [[EditionImageView alloc] initWithFrame:CGRectMake(0, 0, STATIC_EDITIONSIMAGEVIEW_WIDTH * 0.7, STATIC_EDITIONSIMAGEVIEW_HEIGHT*0.7)];
            }
            else {
                coverImageView = [[EditionImageView alloc] initWithFrame:CGRectMake(0, 0, STATIC_EDITIONSIMAGEVIEW_WIDTH * 0.6, STATIC_EDITIONSIMAGEVIEW_HEIGHT*0.6)];
            }
        }
        NSLog(@"%@", NSStringFromCGRect(CGRectMake(0, 0, STATIC_EDITIONSIMAGEVIEW_WIDTH * 0.6, STATIC_EDITIONSIMAGEVIEW_HEIGHT*0.6)));
        coverImageView.autoresizingMask = UIViewAutoresizingFlexibleWidth | UIViewAutoresizingFlexibleHeight;
        [coverImageView addBorderAndDropShadow];
        [coverImageView addInnerShadow];
        refFrame = coverImageView.frame;
    }
    return coverImageView;
}

-(UIImageView *)bannerImageView {
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
        
        [bannerImageView setImage:[UIImage imageNamed:@"Bandeau_2_ALL_CAPS"]];
        [bannerImageView setBackgroundColor:[UIColor clearColor]];
        [bannerImageView setHidden:YES];
    }
    return bannerImageView;
}

-(UILongPressGestureRecognizer *)longPressGestureRecognizer {
    if (longPressGestureRecognizer == nil) {
        longPressGestureRecognizer = [[UILongPressGestureRecognizer alloc] initWithTarget:self action:@selector(handleLongPress:)];
        longPressGestureRecognizer.minimumPressDuration = 0.5;
    }
    return longPressGestureRecognizer;
}

-(UITapGestureRecognizer *)tapGestureRecognizer {
    if (tapGestureRecognizer == nil) {
        tapGestureRecognizer = [[UITapGestureRecognizer alloc] initWithTarget:self action:@selector(handleTap:)];
    }
    return tapGestureRecognizer;
}

-(void)setEditionInView:(Editions *)refEdition {
    
    [self setEdition:refEdition];
    
    [self.coverImageView setUrl:[NSURL URLWithString:[self.edition coverpath]]];
    [self.coverImageView startDownload];
    
    if (self.edition.openDate == nil) {
        [self.bannerImageView setHidden:NO];
    }
    else {
        [self.bannerImageView setHidden:YES];
    }
    
    if ([self.edition.favoris boolValue]) {
        [self.coverImageView showFav];
    }
    else {
        [self.coverImageView hideFav];
    }
    
    
}

-(void)handleLongPress:(UILongPressGestureRecognizer*)sender {
    if (sender.state == UIGestureRecognizerStateBegan){
        NSLog(@"UIGestureRecognizerStateBegan.");
        //Do Whatever You want on Began of Gesture
        //[self longPressSelectAnimation];
        [self pushDetailViewController];
    }
    else {
        //[self longPressUnselectAnimation];
    }
}
-(void)handleTap:(UITapGestureRecognizer*)sender {
    NSLog(@"open Reader");
    NSString *pdfUrl = [NSString stringWithFormat:@"%@/issue.pdf", edition.localpath];
    NSString *plistUrl = [NSString stringWithFormat:@"%@/issue.plist", edition.localpath];
    YLDocument *document = [[YLDocument alloc] initWithFilePath:pdfUrl];
    //ReaderDocument *document = [ReaderDocument withDocumentFilePath:pdfUrl password:nil];
    NSArray *tmpIssue = [NSArray arrayWithContentsOfFile:plistUrl];
    
    if (edition.openDate == nil) {
        [self updateReaded];
    }
    
    if (document != nil) // Must have a valid ReaderDocument object in order to proceed with things
	{
        //MainPDFReaderViewController *mainPDFReaderViewController = [[MainPDFReaderViewController alloc] initWithReaderDocument:document AndArray:tmpIssue];
        //[mainPDFReaderViewController setTitle:edition.nom];
		//[refViewController.navigationController pushViewController:mainPDFReaderViewController animated:YES];
        
        // 2. Create a YLPDFViewController instance and present it as a modal or child view controller.
        YLPDFViewController *v = [[YLPDFViewController alloc] initWithDocument:document];
        [v setDocumentMode:YLDocumentModeDouble];
        [v setDocumentLead:YLDocumentLeadRight];
        [v setAutoLayoutEnabled:YES];
        [v setPageCurlEnabled:YES];
        [v setModalPresentationStyle:UIModalPresentationFullScreen];
        [v setModalTransitionStyle:UIModalTransitionStyleCoverVertical];
        [refViewController.navigationController pushViewController:v animated:YES];
    }
    
}
-(void)longPressSelectAnimation {
    [UIView beginAnimations:nil context:nil];
    [UIView setAnimationDuration:0.2];
    [UIView setAnimationCurve:UIViewAnimationCurveEaseOut];
    self.coverImageView.frame = CGRectMake(self.coverImageView.frame.origin.x - 20,
                                           self.coverImageView.frame.origin.y - 20,
                                           self.coverImageView.frame.size.width + 40,
                                           self.coverImageView.frame.size.height + 40);
    [UIView commitAnimations];
}
-(void)longPressUnselectAnimation {
    [UIView beginAnimations:nil context:nil];
    [UIView setAnimationDuration:0.2];
    [UIView setAnimationCurve:UIViewAnimationCurveEaseOut];
    self.coverImageView.frame = refFrame;
    [UIView commitAnimations];
}

-(void)pushDetailViewController {
    
    NSString *storyboardString = @"Main_iPhone";
    if (isPad()) {
        storyboardString = @"Main_iPad";
    }
    
    UIStoryboard *sb = [UIStoryboard storyboardWithName:storyboardString bundle:nil];
    
    DetailEditionsViewController* vc = (DetailEditionsViewController*)[sb instantiateViewControllerWithIdentifier:@"DetailEditionsViewController"];
    [vc setViewController:refViewController];
    [vc setEdition:edition];
    //[self presentViewController:vc animated:YES completion:nil];
    
    [refViewController presentViewController:vc animated:YES completion:nil];
    
}

-(void)updateReaded {
    NSManagedObjectContext* managedObjectContext = [[NSManagedObjectContext alloc] init];
    [managedObjectContext setUndoManager:nil];
    [managedObjectContext setPersistentStoreCoordinator:[(AppDelegate*)[[UIApplication sharedApplication] delegate] persistentStoreCoordinator]];
    
    NSFetchRequest *request = [[NSFetchRequest alloc] init];
    [request setEntity:[NSEntityDescription entityForName:@"Editions" inManagedObjectContext:managedObjectContext]];
    
    NSError *error = nil;
    
    NSPredicate *predicate = [NSPredicate predicateWithFormat:@"id == %d", [self.edition.id intValue]];
    [request setPredicate:predicate];
    
    NSArray *results = [managedObjectContext executeFetchRequest:request error:&error];
    
    Editions *tempEdition = [results objectAtIndex:0];
    tempEdition.openDate = [NSDate new];
    tempEdition.lu = [NSNumber numberWithBool:YES];
    
    [managedObjectContext save:nil];
    
}

-(void)setDownloading:(BOOL)val {
    if (val) {
        [self.progressView setHidden:NO];
        [self.overImageView setHidden:NO];
        [self.progressView startSpinProgressBackgroundLayer];
        [self.progressView setProgress:0];
        
    }
    else {
        [self.progressView setHidden:YES];
        [self.overImageView setHidden:YES];
        [self.progressView stopSpinProgressBackgroundLayer];
    }
    
}

-(void)setProgression:(NSNumber*)progress {
    [self.progressView setProgress:[progress floatValue]];
}

-(BOOL)isDownloading {
    return self.progressView.hidden == YES;
}

@end
