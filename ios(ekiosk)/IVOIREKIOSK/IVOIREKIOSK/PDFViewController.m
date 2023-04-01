//
//  PDFViewController.m
//  NGSER
//
//  Created by Maxime Julien-Paquet on 2013-10-12.
//
//

#import "PDFViewController.h"
//#import "ReaderContentView.h"
#import "PDFView.h"

@interface PDFViewController () <UIGestureRecognizerDelegate> {
    BOOL touched;
}

@end

@implementation PDFViewController {
    //ReaderContentView *readerContentView;
}

-(id)initWithFilePath:(NSURL*)url Password:(NSString*)pass PageNumber:(int)page {
    self = [super init];
    if (self) {
        filePath = url;
        password = pass;
        pageNumber = page;
        isVisible = NO;
        touched = NO;
        [self setWantsFullScreenLayout:YES];
        
    }
    return self;
}

-(void)viewDidLoad {
    [super viewDidLoad];
	// Do any additional setup after loading the view.
    self.view.contentMode = UIViewContentModeRedraw;
    self.view.autoresizingMask = UIViewAutoresizingFlexibleWidth | UIViewAutoresizingFlexibleHeight;
    [self.view setBackgroundColor:[UIColor whiteColor]];
    pdfView = [[PDFView alloc] initWithURL:filePath page:pageNumber password:password];
    
    [self.view addSubview:pdfView];
    
    UITapGestureRecognizer *singleTapOne = [[UITapGestureRecognizer alloc] initWithTarget:self action:@selector(test:)];
	singleTapOne.numberOfTouchesRequired = 1; singleTapOne.numberOfTapsRequired = 1; singleTapOne.delegate = self;
	[self.view addGestureRecognizer:singleTapOne];
}

-(void)viewWillAppear:(BOOL)animated {
    [super viewWillAppear:animated];
    isVisible = YES;
    
    UIInterfaceOrientation orientation= [[UIApplication sharedApplication] statusBarOrientation];
    
    if(UIInterfaceOrientationIsPortrait(orientation)) {
        [pdfView setParentFrame:CGRectMake(0, 0, 768, 1024)];
    }
    else {
        [pdfView setParentFrame:CGRectMake(0, 0, 512, 768)];
    }
    [pdfView setNeedsDisplay];
    /*
    if (readerContentView == nil) {
        readerContentView = [[ReaderContentView alloc] initWithFrame:self.view.bounds fileURL:filePath page:pageNumber password:password];
        readerContentView.backgroundColor = [UIColor whiteColor];
        //readerContentView.message = self;
        [self.view addSubview:readerContentView];
    }
    */
}
-(void)viewWillDisappear:(BOOL)animated {
    [super viewWillDisappear:animated];
    isVisible = NO;
    
}

-(void)viewDidDisappear:(BOOL)animated {
    [super viewDidDisappear:animated];
    /*
    @synchronized(self) {
        [readerContentView removeFromSuperview];
        readerContentView = nil;
    }
    */
}

-(void)willRotateToInterfaceOrientation:(UIInterfaceOrientation)toInterfaceOrientation duration:(NSTimeInterval)duration {
    if(UIInterfaceOrientationIsPortrait(toInterfaceOrientation)) {
        [pdfView setParentFrame:CGRectMake(0, 0, 768, 1024)];
    }
    else {
        [pdfView setParentFrame:CGRectMake(0, 0, 512, 768)];
    }
}

- (BOOL)shouldAutorotateToInterfaceOrientation:(UIInterfaceOrientation)toInterfaceOrientation {
    return ((toInterfaceOrientation == UIInterfaceOrientationPortrait) || (toInterfaceOrientation == UIInterfaceOrientationLandscapeLeft) || (toInterfaceOrientation == UIInterfaceOrientationLandscapeRight));
}

- (NSUInteger)supportedInterfaceOrientations {
    return (UIInterfaceOrientationMaskPortrait | UIInterfaceOrientationMaskLandscapeLeft | UIInterfaceOrientationMaskLandscapeRight);
}

-(void)didReceiveMemoryWarning {
    [super didReceiveMemoryWarning];
    // Dispose of any resources that can be recreated.
    //if (!isVisible) {
    //    [readerContentView removeFromSuperview];
    //    readerContentView = nil;
    //}
}

-(void)test:(UITapGestureRecognizer *)recognizer {
    if (recognizer.state == UIGestureRecognizerStateRecognized)	{
        [[NSNotificationCenter defaultCenter] postNotificationName:@"OpenTexVersion" object:[NSNumber numberWithInt:pageNumber]];
    }
}


@end
