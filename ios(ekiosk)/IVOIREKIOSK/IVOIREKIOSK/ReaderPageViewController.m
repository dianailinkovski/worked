//
//  ReaderPageViewController.m
//  NGSER
//
//  Created by Maxime Julien-Paquet on 2013-10-10.
//
//

#import "ReaderPageViewController.h"
#import "ReaderContentView.h"
#import "ReaderThumbCache.h"
#import "ThumbsViewController.h"
#import "PDFViewController.h"
#import "ReaderMainPagebar.h"

#define TOOLBAR_HEIGHT 44.0f
#define PAGEBAR_HEIGHT 48.0f

@interface ReaderPageViewController () <ReaderContentViewDelegate, ThumbsViewControllerDelegate, ReaderMainPagebarDelegate, UIPageViewControllerDelegate, UIPageViewControllerDataSource, UIGestureRecognizerDelegate>

@end

@implementation ReaderPageViewController {
    ReaderDocument *document;
    ReaderMainPagebar *mainPagebar;
    
}

@synthesize pageVC, vcArray, delegate;

- (id)initWithReaderDocument:(ReaderDocument *)object {
	//id reader = nil; // ReaderViewController object
    
	if ((object != nil) && ([object isKindOfClass:[ReaderDocument class]]))
	{
		if ((self = [super init])) // Designated initializer
		{
            [self setWantsFullScreenLayout:YES];
			[object updateProperties]; document = object; // Retain the supplied ReaderDocument object for our use
            
			[ReaderThumbCache touchThumbCacheWithGUID:object.guid]; // Touch the document thumb cache directory
            
            return self;
		}
	}
    return nil;
	//return reader;
}

- (void)viewDidLoad {
    [super viewDidLoad];
	// Do any additional setup after loading the view.
    [self addChildViewController:[self pageVC]];
    [self.view addSubview:[self pageVC].view];
    
    self.vcArray = [[NSMutableArray alloc] initWithCapacity:[document.pageCount integerValue]];
    
    
    NSURL *fileURL = document.fileURL; NSString *phrase = document.password; // Document properties
    
    PDFViewController *pdfVC;
    
    
    for (int x = 1; x < [document.pageCount integerValue]+1; ++x) {
        pdfVC = [[PDFViewController alloc] initWithFilePath:fileURL Password:phrase PageNumber:x];
        //[self.pageVC addChildViewController:pdfVC];
        [self.vcArray addObject:pdfVC];
    }
    
    [self.pageVC setViewControllers:[NSArray arrayWithObject:[vcArray objectAtIndex:0] ]
                          direction:UIPageViewControllerNavigationDirectionForward
                           animated:NO
                         completion:^(BOOL finished) {
                         }];
    //[self.pageVC didMoveToParentViewController:self];
    
    /*
    CGRect viewRect = self.view.bounds;
    CGRect pagebarRect = viewRect;
	pagebarRect.size.height = PAGEBAR_HEIGHT;
	pagebarRect.origin.y = (viewRect.size.height - PAGEBAR_HEIGHT);
    
	mainPagebar = [[ReaderMainPagebar alloc] initWithFrame:pagebarRect document:document]; // At bottom
    
	mainPagebar.delegate = self;
    
	[self.view addSubview:mainPagebar];
    */
    
    //UITapGestureRecognizer *singleTapOne = [[UITapGestureRecognizer alloc] initWithTarget:self action:@selector(handleSingleTap:)];
	//singleTapOne.numberOfTouchesRequired = 1; singleTapOne.numberOfTapsRequired = 1; singleTapOne.delegate = self;
	//[self.view addGestureRecognizer:singleTapOne];
}

/*
- (void)viewDidAppear:(BOOL)animated {
    [super viewDidAppear:animated];
    [self performSelector:@selector(hideNavBar) withObject:nil afterDelay:0.5f];
}
*/

- (void)willRotateToInterfaceOrientation:(UIInterfaceOrientation)toInterfaceOrientation duration:(NSTimeInterval)duration {
    if (delegate && [delegate respondsToSelector:@selector(ReaderPageViewController:RefreshZoom:)]) {
        [delegate ReaderPageViewController:self RefreshZoom:YES];
    }
    
}

- (void)didReceiveMemoryWarning {
    [super didReceiveMemoryWarning];
    // Dispose of any resources that can be recreated.
}

- (BOOL)shouldAutorotateToInterfaceOrientation:(UIInterfaceOrientation)toInterfaceOrientation {
    return ((toInterfaceOrientation == UIInterfaceOrientationPortrait) || (toInterfaceOrientation == UIInterfaceOrientationLandscapeLeft) || (toInterfaceOrientation == UIInterfaceOrientationLandscapeRight));
}

- (NSUInteger)supportedInterfaceOrientations {
    return (UIInterfaceOrientationMaskPortrait | UIInterfaceOrientationMaskLandscapeLeft | UIInterfaceOrientationMaskLandscapeRight);
}

-(void)didRotateFromInterfaceOrientation:(UIInterfaceOrientation)fromInterfaceOrientation {
    
    if(!UIInterfaceOrientationIsPortrait(fromInterfaceOrientation)) {
        NSLog(@"Reloader les viewcontroller -- 2ime viewcontroller bugé");
        
    }
    
}

/*
- (void)hideNavBar {
    UIInterfaceOrientation orientation= [[UIApplication sharedApplication] statusBarOrientation];
    BOOL animated = YES;
    if(!UIInterfaceOrientationIsPortrait(orientation)) {
        animated = NO;
    }
    
    if ((self.navigationController.navigationBar.hidden == NO) || (mainPagebar.hidden == NO))
    {
        [self.navigationController setNavigationBarHidden:YES animated:animated];
        [mainPagebar hidePagebar];
    }
}

- (void)showNavBar {
    UIInterfaceOrientation orientation= [[UIApplication sharedApplication] statusBarOrientation];
    BOOL animated = YES;
    if(!UIInterfaceOrientationIsPortrait(orientation)) {
        animated = NO;
    }
    
    if ((self.navigationController.navigationBar.hidden == YES) || (mainPagebar.hidden == YES))
    {
        [self.navigationController setNavigationBarHidden:NO animated:animated];
        [mainPagebar showPagebar];
    }
}
*/
-(UIPageViewController *)pageVC {
    if (pageVC == nil) {
        
        pageVC = [[UIPageViewController alloc] initWithTransitionStyle:UIPageViewControllerTransitionStylePageCurl
                                                navigationOrientation:UIPageViewControllerNavigationOrientationHorizontal
                                                              options:nil];
        
        pageVC.view.frame = self.view.bounds;
        pageVC.delegate = self;
        pageVC.dataSource = self;
        
    }
    return pageVC;
}

- (int)getCurrentPageId {
    
    UIViewController *viewController = [self.pageVC.viewControllers objectAtIndex:0];
    for (int x = 0; x < [self.vcArray count]; ++x) {
        if ([self.vcArray objectAtIndex:x] == viewController) {
            return x;
        }
    }
    
    return -1;
}

#pragma mark - PageViewControllerDataSource

- (UIPageViewControllerSpineLocation)pageViewController:(UIPageViewController *)pageViewController
                   spineLocationForInterfaceOrientation:(UIInterfaceOrientation)orientation {
    
    if(UIInterfaceOrientationIsPortrait(orientation))
    {
        //Set the array with only 1 view controller
        UIViewController *currentViewController = [self.pageVC.viewControllers objectAtIndex:0];
        NSArray *viewControllers = [NSArray arrayWithObject:currentViewController];
        [self.pageVC setViewControllers:viewControllers direction:UIPageViewControllerNavigationDirectionForward animated:YES completion:NULL];
        
        //Important- Set the doubleSided property to NO.
        self.pageVC.doubleSided = NO;
        //Return the spine location
        return UIPageViewControllerSpineLocationMin;
        
    }
    
    else
    {
        self.pageVC.doubleSided = YES;
        NSArray *viewControllers = nil;
        
        UIViewController *currentViewController = [self.pageVC.viewControllers objectAtIndex:0];
        
        NSUInteger currentIndex = [self.vcArray indexOfObject:currentViewController];
        
        if(currentIndex == 0 || currentIndex %2 == 0)
        {
            UIViewController *nextViewController = [self pageViewController:self.pageVC viewControllerAfterViewController:currentViewController];
            viewControllers = [NSArray arrayWithObjects:currentViewController, nextViewController, nil];
        }
        else
        {
            UIViewController *previousViewController = [self pageViewController:self.pageVC viewControllerBeforeViewController:currentViewController];
            viewControllers = [NSArray arrayWithObjects:previousViewController, currentViewController, nil];
        }
        //Now, set the viewControllers property of UIPageViewController
        [self.pageVC setViewControllers:viewControllers direction:UIPageViewControllerNavigationDirectionForward animated:YES completion:NULL];
        
        return UIPageViewControllerSpineLocationMid;
    }
    
    /*
    if(UIInterfaceOrientationIsPortrait(orientation)) {
        
        
        
        //Set the array with only 1 view controller
        UIViewController *currentViewController = [self.pageVC.viewControllers objectAtIndex:0];
        NSArray *viewControllers = [NSArray arrayWithObject:currentViewController];
        [self.pageVC setViewControllers:viewControllers direction:UIPageViewControllerNavigationDirectionForward animated:YES completion:NULL];
        
        //Important- Set the doubleSided property to NO.
        self.pageVC.doubleSided = NO;
        //Return the spine location
        return UIPageViewControllerSpineLocationMin;
        
    }
    else
    {
        NSArray *viewControllers = nil;
        
        UIViewController *currentViewController = [self.pageVC.viewControllers objectAtIndex:0];
        
        NSUInteger currentIndex = [self.vcArray indexOfObject:currentViewController];
        
        if(currentIndex == 0 || currentIndex %2 == 0)
        {
            UIViewController *nextViewController = [self pageViewController:self.pageVC viewControllerAfterViewController:currentViewController];
            viewControllers = [NSArray arrayWithObjects:currentViewController, nextViewController, nil];
        }
        else
        {
            UIViewController *previousViewController = [self pageViewController:self.pageVC viewControllerBeforeViewController:currentViewController];
            viewControllers = [NSArray arrayWithObjects:previousViewController, currentViewController, nil];
        }
        //Now, set the viewControllers property of UIPageViewController
        [self.pageVC setViewControllers:viewControllers direction:UIPageViewControllerNavigationDirectionForward animated:YES completion:NULL];
        
        return UIPageViewControllerSpineLocationMid;
    }
    */
}


- (UIViewController *)pageViewController:(UIPageViewController *)pageViewController viewControllerBeforeViewController:(UIViewController *)viewController {
    int index = [self.vcArray indexOfObject:viewController];
    
    //[self checkViewControllers];
    if (index - 1 >= 0)
    {
        ind = index;
        UIViewController *tempVC = [self.vcArray objectAtIndex:index - 1];
        /*
        if (tempVC == nil) {
            ReaderContentView *contentView;
            tempVC = [[UIViewController alloc] init];
            contentView = [[ReaderContentView alloc] initWithFrame:self.view.bounds fileURL:document.fileURL page:index-1 password:document.password];
            contentView.backgroundColor = [UIColor whiteColor];
            contentView.message = self;
            [tempVC setView:contentView];
            [self.vcArray insertObject:tempVC atIndex:index-1];
            [self.pageVC setViewControllers:self.vcArray direction:UIPageViewControllerNavigationDirectionForward animated:NO completion:nil];
        }
        */
        return tempVC;
    }
    ind = index;
    return nil;
}

- (UIViewController *)pageViewController:(UIPageViewController *)pageViewController viewControllerAfterViewController:(UIViewController *)viewController {
    int index = [self.vcArray indexOfObject:viewController];
    //[self checkViewControllers];
    if (index + 1 < [self.vcArray count])
    {
        ind = index;
        return [self.vcArray objectAtIndex:index + 1];
        /*
        UIViewController *tempVC = [self.vcArray objectAtIndex:index + 1];
        if (tempVC == nil) {
            ReaderContentView *contentView;
            tempVC = [[UIViewController alloc] init];
            contentView = [[ReaderContentView alloc] initWithFrame:self.view.bounds fileURL:document.fileURL page:index+1 password:document.password];
            contentView.backgroundColor = [UIColor whiteColor];
            contentView.message = self;
            [tempVC setView:contentView];
            [self.vcArray insertObject:tempVC atIndex:index+1];
            [self.pageVC setViewControllers:self.vcArray direction:UIPageViewControllerNavigationDirectionForward animated:NO completion:nil];
        }
        return tempVC;
        */
    }
    ind = index;
    
    return nil;
}

#pragma mark - PageViewControllerDelegate

-(void)pageViewController:(UIPageViewController *)pageViewController didFinishAnimating:(BOOL)finished previousViewControllers:(NSArray *)previousViewControllers transitionCompleted:(BOOL)completed {
    
    if (delegate && [delegate respondsToSelector:@selector(ReaderPageViewController:RefreshZoom:)]) {
        [delegate ReaderPageViewController:self RefreshZoom:YES];
    }
    
    
    
    //[self hideNavBar];
}

- (void)pageViewController:(UIPageViewController *)pageViewController willTransitionToViewControllers:(NSArray *)pendingViewControllers {
    int pagenumber = 0;
    for (PDFViewController *tempVC in vcArray) {
        ++pagenumber;
        if (tempVC == [pendingViewControllers objectAtIndex:0]) {
            document.pageNumber = [NSNumber numberWithInt:pagenumber];
            break;
        }
    }
    
    if (delegate && [delegate respondsToSelector:@selector(ReaderPageViewController:CurrentPage:)]) {
        [delegate ReaderPageViewController:self CurrentPage:[document.pageNumber intValue]];
    }
}

#pragma mark ReaderContentViewDelegate methods

- (void)showThumb {
    ThumbsViewController *thumbsViewController = [[ThumbsViewController alloc] initWithReaderDocument:document];
    UINavigationController *tempNavigationController = [[UINavigationController alloc] initWithRootViewController:thumbsViewController];
	thumbsViewController.delegate = self; thumbsViewController.title = self.title;
    
	//thumbsViewController.modalTransitionStyle = UIModalTransitionStyleCrossDissolve;
	thumbsViewController.modalPresentationStyle = UIModalPresentationFullScreen;
    
	[self presentModalViewController:tempNavigationController animated:YES];
}

- (void)contentView:(ReaderContentView *)contentView touchesBegan:(NSSet *)touches {
    /*
	if (self.navigationController.navigationBar.hidden == NO || (mainPagebar.hidden == NO))
	{
		if (touches.count == 1) // Single touches only
		{
			UITouch *touch = [touches anyObject]; // Touch info
            
			CGPoint point = [touch locationInView:self.view]; // Touch location
            
			CGRect areaRect = CGRectInset(self.view.bounds, TAP_AREA_SIZE, TAP_AREA_SIZE);
            
			if (CGRectContainsPoint(areaRect, point) == false) return;
		}
        
		lastHideTime = [NSDate date];
	}
    */
}

#pragma mark - Single Touch Gesture
/*
- (void)handleSingleTap:(UITapGestureRecognizer *)recognizer {
	if (recognizer.state == UIGestureRecognizerStateRecognized)	{
        if ((self.navigationController.navigationBar.hidden == YES) || (mainPagebar.hidden == YES)) {
            [self showNavBar];
        }
        else {
            [self hideNavBar];
        }
    }
}
*/

#pragma mark ReaderMainPagebarDelegate methods

-(void)toPage:(int)pageId {
    
    [self pagebar:nil gotoPage:pageId];
    
}

- (void)pagebar:(ReaderMainPagebar *)pagebar gotoPage:(NSInteger)page {
    
    if (delegate && [delegate respondsToSelector:@selector(ReaderPageViewController:RefreshZoom:)]) {
        [delegate ReaderPageViewController:self RefreshZoom:YES];
    }
    
    document.pageNumber = [NSNumber numberWithInt:page];
    
    UIInterfaceOrientation orientation= [[UIApplication sharedApplication] statusBarOrientation];
    
    if(UIInterfaceOrientationIsPortrait(orientation))
    {
        //Set the array with only 1 view controller
        UIViewController *currentViewController = [self.vcArray objectAtIndex:page-1];
        NSArray *viewControllers = [NSArray arrayWithObject:currentViewController];
        [self.pageVC setViewControllers:viewControllers direction:UIPageViewControllerNavigationDirectionForward animated:NO completion:NULL];
        
        self.pageVC.doubleSided = NO;
        
    }
    
    else
    {
        NSArray *viewControllers = nil;
        
        UIViewController *currentViewController = [self.vcArray objectAtIndex:page-1];
        
        NSUInteger currentIndex = [self.vcArray indexOfObject:currentViewController];
        
        if(currentIndex == 0 || currentIndex %2 == 0)
        {
            UIViewController *nextViewController = [self pageViewController:self.pageVC viewControllerAfterViewController:currentViewController];
            viewControllers = [NSArray arrayWithObjects:currentViewController, nextViewController, nil];
        }
        else
        {
            UIViewController *previousViewController = [self pageViewController:self.pageVC viewControllerBeforeViewController:currentViewController];
            viewControllers = [NSArray arrayWithObjects:previousViewController, currentViewController, nil];
        }
        //Now, set the viewControllers property of UIPageViewController
        [self.pageVC setViewControllers:viewControllers direction:UIPageViewControllerNavigationDirectionForward animated:NO completion:NULL];
        
    }
    
    
    
    
}

@end
