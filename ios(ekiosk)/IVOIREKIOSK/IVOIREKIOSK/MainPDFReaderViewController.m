//
//  MainPDFReaderViewController.m
//  NGSER
//
//  Created by Maxime Julien-Paquet on 2013-10-17.
//
//

#import "MainPDFReaderViewController.h"
#import "TextReaderViewController.h"


@interface MainPDFReaderViewController ()

@end

@implementation MainPDFReaderViewController {
    ReaderDocument *document;
    NSArray *text;
}

#pragma mark Constants

#define ZOOM_LEVELS 4
#define CONTENT_INSET 2.0f
#define PAGE_THUMB_LARGE 240
#define PAGE_THUMB_SMALL 144

static inline CGFloat ZoomScaleThatFits(CGSize target, CGSize source) {
	CGFloat w_scale = (target.width / source.width);
	CGFloat h_scale = (target.height / source.height);
    
	return ((w_scale < h_scale) ? w_scale : h_scale);
}

@synthesize scrollView, readerPageVC, bottomBar;

-(id)initWithReaderDocument:(ReaderDocument *)object AndArray:(NSArray*)textArray {
    
    if ((object != nil) && ([object isKindOfClass:[ReaderDocument class]])) {
		if ((self = [super init])) {
			document = object;
            text = textArray;
            return self;
		}
	}
    return nil;
}

- (void)viewDidLoad {
    [super viewDidLoad];
	// Do any additional setup after loading the view.
    [self.view addSubview:[self scrollView]];
    [self addChildViewController:[self readerPageVC]];
    [[self scrollView] addSubview:[self readerPageVC].view];
    [self.view addSubview:[self bottomBar]];
    [self updateMinimumMaximumZoom];
    
    
    [[NSNotificationCenter defaultCenter] addObserver:self selector:@selector(showTextOver:) name:@"OpenTexVersion" object:nil];
    
}

-(void)viewDidUnload {
    [[NSNotificationCenter defaultCenter] removeObserver:self name:@"OpenTexVersion" object:nil];
}

- (void)didReceiveMemoryWarning {
    [super didReceiveMemoryWarning];
    // Dispose of any resources that can be recreated.
}

-(UIScrollView *)scrollView {
    if (scrollView ==nil) {
        scrollView = [[UIScrollView alloc] initWithFrame:self.view.bounds];
        scrollView.scrollsToTop = NO;
		scrollView.delaysContentTouches = NO;
		scrollView.showsVerticalScrollIndicator = NO;
		scrollView.showsHorizontalScrollIndicator = NO;
		scrollView.contentMode = UIViewContentModeRedraw;
		scrollView.autoresizingMask = UIViewAutoresizingFlexibleWidth | UIViewAutoresizingFlexibleHeight;
		scrollView.backgroundColor = [UIColor clearColor];
		scrollView.userInteractionEnabled = YES;
		scrollView.bouncesZoom = YES;
		scrollView.delegate = self;
    }
    return scrollView;
}
-(ReaderPageViewController *)readerPageVC {
    if (readerPageVC == nil) {
        readerPageVC = [[ReaderPageViewController alloc] initWithReaderDocument:document];
        [readerPageVC setDelegate:self];
    }
    return readerPageVC;
}
-(ReaderMainPagebar *)bottomBar {
    if (bottomBar == nil) {
        bottomBar = [[ReaderMainPagebar alloc] initWithFrame:CGRectMake(0, self.view.frame.size.height-44, self.view.frame.size.width, 44) document:document];
        [bottomBar setDelegate:self];
    }
    return bottomBar;
}

- (UIView *)viewForZoomingInScrollView:(UIScrollView *)scrollView {
	return [self readerPageVC].view;
}

- (void)updateMinimumMaximumZoom {
	CGRect targetRect = CGRectInset(self.view.bounds, CONTENT_INSET, CONTENT_INSET);
    
	CGFloat zoomScale = ZoomScaleThatFits(targetRect.size, readerPageVC.view.bounds.size);
    
	scrollView.minimumZoomScale = zoomScale; // Set the minimum and maximum zoom scales
    
	scrollView.maximumZoomScale = (zoomScale * ZOOM_LEVELS); // Max number of zoom levels
    
	zoomAmount = ((scrollView.maximumZoomScale - scrollView.minimumZoomScale) / ZOOM_LEVELS);
}

- (BOOL)shouldAutorotateToInterfaceOrientation:(UIInterfaceOrientation)toInterfaceOrientation {
    return ((toInterfaceOrientation == UIInterfaceOrientationPortrait) || (toInterfaceOrientation == UIInterfaceOrientationLandscapeLeft) || (toInterfaceOrientation == UIInterfaceOrientationLandscapeRight));
}

- (NSUInteger)supportedInterfaceOrientations {
    return (UIInterfaceOrientationMaskPortrait | UIInterfaceOrientationMaskLandscapeLeft | UIInterfaceOrientationMaskLandscapeRight);
}
/*
- (NSUInteger)supportedInterfaceOrientations {
    return UIInterfaceOrientationMaskPortrait;
}
// pre-iOS 6 support
- (BOOL)shouldAutorotateToInterfaceOrientation:(UIInterfaceOrientation)toInterfaceOrientation {
    return (toInterfaceOrientation == UIInterfaceOrientationPortrait);
}
*/

-(void) showTextOver:(NSNotification *)not {
    
    int pageId = [[not object] intValue];
    
    NSArray *articlesArray = [self getContentFromPage:pageId];
    NSLog(@"articlesArray = %@", articlesArray);
    if (articlesArray == nil) {
        return;
    }
    
    //[self.readerPageVC hideNavBar];
    
    NSString *title = [self getTitleFromPage:pageId];
    
    TextReaderViewController *textReader = [[TextReaderViewController alloc] initWithArticles:articlesArray AndTitleNavBar:title];
    [textReader.view setFrame:self.view.bounds];
    [self addChildViewController:textReader];
    [self.view addSubview:textReader.view];
    
}
-(NSString*)getTitleFromPage:(int)pageX {
    
    for (int x = 0; x < [text count]; ++x) {
        if ([[[text objectAtIndex:x] valueForKey:@"Page"] intValue] == pageX) {
            return [NSString stringWithFormat:@"Page %@",[[text objectAtIndex:x] valueForKey:@"Page"]];
        }
    }
    
    return @"";
}
-(NSArray*)getContentFromPage:(int)pageX {
    
    for (int x = 0; x < [text count]; ++x) {
        if ([[[text objectAtIndex:x] valueForKey:@"Page"] intValue] == pageX) {
            return [[text objectAtIndex:x] valueForKey:@"articles"];
        }
    }
    
    return nil;
}

#pragma mark - ReaderPageViewControllerDelegate

-(void)ReaderPageViewController:(ReaderPageViewController *)readerPageViewController RefreshZoom:(BOOL)refresh {
    [scrollView setZoomScale:1];
}

-(void)ReaderPageViewController:(ReaderPageViewController *)readerPageViewController CurrentPage:(int)page {
    [self.bottomBar updatePagebar];
}

#pragma mark - ReaderMainPagebarDelegate

- (void)pagebar:(ReaderMainPagebar *)pagebar gotoPage:(NSInteger)page {
    NSLog(@"page = %d",page);
    [self.readerPageVC toPage:page];
}

@end
