//
//  TextReaderViewController.m
//  NGSER
//
//  Created by Maxime Julien-Paquet on 2013-10-25.
//
//

#import "TextReaderViewController.h"
#import <QuartzCore/QuartzCore.h>

@interface TextReaderViewController () <UIScrollViewDelegate> {
    NSArray *articlesArray;
    UIScrollView *scrollView;
    UIView *subView;
    
    NSString *titleNavBar;
    BOOL dragingScrollView;
}

@end

@implementation TextReaderViewController

-(id)initWithArticles:(NSArray*)articles AndTitleNavBar:(NSString*)title {
    self = [super init];
    if (self) {
        // Custom initialization
        articlesArray = articles;
        titleNavBar = title;
    }
    return self;
}

-(void)viewDidLoad {
    [super viewDidLoad];
	// Do any additional setup after loading the view.
    [self.view setAutoresizingMask:UIViewAutoresizingFlexibleHeight|UIViewAutoresizingFlexibleWidth];
    [self.view setBackgroundColor:[UIColor colorWithWhite:0 alpha:0.7]];
    
    
    
    subView = [[UIView alloc] initWithFrame:CGRectMake(75, 125, 618, 824)];
    [subView setAutoresizingMask:UIViewAutoresizingFlexibleHeight|UIViewAutoresizingFlexibleWidth|UIViewAutoresizingFlexibleLeftMargin|UIViewAutoresizingFlexibleRightMargin];
    [subView setBackgroundColor:[UIColor whiteColor]];
    [self.view addSubview:subView];
    
    UINavigationBar *navBar = [[UINavigationBar alloc] initWithFrame:CGRectMake(0, 0, subView.bounds.size.width, 44)];
    [navBar setAutoresizingMask:UIViewAutoresizingFlexibleWidth|UIViewAutoresizingFlexibleBottomMargin];
    UIBarButtonItem *backButton = [[UIBarButtonItem alloc] initWithTitle:@"Fermer" style:UIBarButtonItemStyleDone target:self action:@selector(closeView)];
    UINavigationItem *navigationItem = [[UINavigationItem alloc] initWithTitle:titleNavBar];
    navigationItem.leftBarButtonItem = backButton;
    [navBar pushNavigationItem:navigationItem animated:NO];
    [subView addSubview:navBar];
    
    scrollView = [[UIScrollView alloc] initWithFrame:CGRectMake(0, 44, subView.frame.size.width, subView.frame.size.height-44)];
    [scrollView setAutoresizingMask:UIViewAutoresizingFlexibleHeight|UIViewAutoresizingFlexibleWidth|UIViewAutoresizingFlexibleLeftMargin|UIViewAutoresizingFlexibleRightMargin];
    [scrollView setDelegate:self];
    [subView addSubview:scrollView];
    
}

-(void)viewWillAppear:(BOOL)animated {
    [super viewWillAppear:animated];
    
    int y = 20;
    for (NSArray *article in articlesArray) {
        CGRect frame = CGRectMake(0, y, scrollView.bounds.size.width, 0);
        ArticleReaderView *temp = [[ArticleReaderView alloc] initWithFrame:frame AndArray:article];
        [temp setAutoresizingMask:UIViewAutoresizingFlexibleWidth|UIViewAutoresizingFlexibleLeftMargin|UIViewAutoresizingFlexibleRightMargin];
        [temp setDelegate:self];
        [scrollView addSubview:temp];
        y += temp.frame.size.height + 20;
    }
    
    CABasicAnimation *animationSide = [CABasicAnimation animationWithKeyPath:@"transform.scale"];
    animationSide.duration = 0.2;
    animationSide.fromValue = [NSNumber numberWithFloat:0.5];
    animationSide.toValue = [NSNumber numberWithFloat:1];
    animationSide.autoreverses = NO;
    
    CABasicAnimation *animationOpacity = [CABasicAnimation animationWithKeyPath:@"opacity"];
    animationOpacity.duration = 0.2;
    animationOpacity.fromValue = [NSNumber numberWithFloat:0.7];
    animationOpacity.toValue = [NSNumber numberWithFloat:1];
    animationOpacity.autoreverses = NO;
    
    [[subView layer] addAnimation:animationSide forKey:animationSide.keyPath];
    [[subView layer] addAnimation:animationOpacity forKey:animationOpacity.keyPath];
}
-(void)viewWillDisappear:(BOOL)animated {
    [super viewWillDisappear:animated];
    NSArray *subViewArray = [scrollView subviews];
    for (UIView *tempView in subViewArray) {
        if ([tempView isKindOfClass:[ArticleReaderView class]]) {
            [tempView removeFromSuperview];
        }
    }
    
}
-(void)didRotateFromInterfaceOrientation:(UIInterfaceOrientation)fromInterfaceOrientation {
    NSLog(@"willrotate");
    int y = 20;
    int currentMaxContent = 0;
    NSArray *subViewArray = [scrollView subviews];
    
    for (UIView *tempView in subViewArray) {
        if ([tempView isKindOfClass:[ArticleReaderView class]]) {
            ArticleReaderView *tempArticleView = (ArticleReaderView*)tempView;
            [tempArticleView rotateView];
            CGRect frame = tempArticleView.frame;
            frame.origin.y = y;
            tempArticleView.frame = frame;
            y += tempArticleView.frame.size.height + 20;
            
            
            
            
            int tempMaxContent = tempView.frame.origin.y + tempView.frame.size.height;
            if (tempMaxContent > currentMaxContent) {
                currentMaxContent = tempMaxContent;
            }
        }
    }
    [scrollView setContentSize:CGSizeMake(scrollView.frame.size.width, currentMaxContent + 0)];
}

-(void)didReceiveMemoryWarning {
    [super didReceiveMemoryWarning];
    // Dispose of any resources that can be recreated.
}

-(void)closingAnimation {
    CABasicAnimation *animationSide = [CABasicAnimation animationWithKeyPath:@"transform.scale"];
    animationSide.duration = 0.25;
    animationSide.fromValue = [NSNumber numberWithFloat:1];
    animationSide.toValue = [NSNumber numberWithFloat:0.5];
    animationSide.autoreverses = NO;
    
    CABasicAnimation *animationOpacity = [CABasicAnimation animationWithKeyPath:@"opacity"];
    animationOpacity.duration = 0.25;
    animationOpacity.fromValue = [NSNumber numberWithFloat:1];
    animationOpacity.toValue = [NSNumber numberWithFloat:0.7];
    animationOpacity.autoreverses = NO;
    
    [[subView layer] addAnimation:animationSide forKey:animationSide.keyPath];
    [[subView layer] addAnimation:animationOpacity forKey:animationOpacity.keyPath];
}
-(void)closeAnimation {
    [self.view removeFromSuperview];
    [self removeFromParentViewController];
}
-(void)closeView {
    [self closingAnimation];
    [self performSelector:@selector(closeAnimation) withObject:nil afterDelay:0.2];
    
}

#pragma mark - ArticlesReaderViewDelegate

-(void)ArticleReaderView:(ArticleReaderView *)articleReaderView willCollapseToHeight:(int)height {
    NSArray *subViewArray = [scrollView subviews];
    int y;
    BOOL belowTheExpandingView = NO;
    
    [UIView beginAnimations:nil context:nil];
    [UIView setAnimationDuration:0.5];
    //[UIView setAnimationDelay:0];
    
    for (UIView *tempView in subViewArray) {
        if ([tempView isKindOfClass:[ArticleReaderView class]]) {
            ArticleReaderView *tempArticleView = (ArticleReaderView*)tempView;
            
            if (belowTheExpandingView == NO) {
                if (tempArticleView == articleReaderView) {
                    belowTheExpandingView = YES;
                    y = tempArticleView.frame.origin.y + height + 20;
                }
            }
            else {
                CGRect frame = tempArticleView.frame;
                frame.origin.y = y;
                tempArticleView.frame = frame;
                y = tempArticleView.frame.size.height + 20;
            }
            
        }
    }
    
    [UIView commitAnimations];
}

-(void)ArticleReaderView:(ArticleReaderView *)articleReaderView willExpandToHeight:(int)height {
    
    NSArray *subViewArray = [scrollView subviews];
    int y;
    BOOL belowTheExpandingView = NO;
    
    [UIView beginAnimations:nil context:nil];
    [UIView setAnimationDuration:0.5];
    //[UIView setAnimationDelay:0];
    
    for (UIView *tempView in subViewArray) {
        if ([tempView isKindOfClass:[ArticleReaderView class]]) {
            ArticleReaderView *tempArticleView = (ArticleReaderView*)tempView;
            
            if (belowTheExpandingView == NO) {
                if (tempArticleView == articleReaderView) {
                    belowTheExpandingView = YES;
                    y = tempArticleView.frame.origin.y + height + 20;
                }
            }
            else {
                CGRect frame = tempArticleView.frame;
                frame.origin.y = y;
                tempArticleView.frame = frame;
                y = tempArticleView.frame.size.height + 20;
            }
            
        }
    }
    
    [UIView commitAnimations];
}

-(void)ArticleReaderView:(ArticleReaderView *)articleReaderView didCollapseToHeight:(int)height {
    int currentMaxContent = 0;
    NSArray *subViewArray = [scrollView subviews];
    for (UIView *tempView in subViewArray) {
        if ([tempView isKindOfClass:[ArticleReaderView class]]) {
            int tempMaxContent = tempView.frame.origin.y + tempView.frame.size.height;
            if (tempMaxContent > currentMaxContent) {
                currentMaxContent = tempMaxContent;
            }
        }
    }
    
    [UIView beginAnimations:nil context:nil];
    [UIView setAnimationDuration:0.2];
    [scrollView setContentSize:CGSizeMake(scrollView.frame.size.width, currentMaxContent + 0)];
    [UIView commitAnimations];
}

-(void)ArticleReaderView:(ArticleReaderView *)articleReaderView didExpandToHeight:(int)height {
    int currentMaxContent = 0;
    NSArray *subViewArray = [scrollView subviews];
    for (UIView *tempView in subViewArray) {
        if ([tempView isKindOfClass:[ArticleReaderView class]]) {
            int tempMaxContent = tempView.frame.origin.y + tempView.frame.size.height;
            if (tempMaxContent > currentMaxContent) {
                currentMaxContent = tempMaxContent;
            }
        }
    }
    
    [UIView beginAnimations:nil context:nil];
    [UIView setAnimationDuration:0.2];
    [scrollView setContentSize:CGSizeMake(scrollView.frame.size.width, currentMaxContent + 0)];
    [UIView commitAnimations];
}

#pragma mark - UIScrollViewDelegate
-(void)scrollViewWillBeginDragging:(UIScrollView *)scrollView {
    dragingScrollView = YES;
}
-(void)scrollViewDidScroll:(UIScrollView *)scrollView {
    if (dragingScrollView) {
        [self disableTouch];
    }
}
-(void)scrollViewDidEndDecelerating:(UIScrollView *)scrollView {
    [self enableTouch];
    dragingScrollView = NO;
}
-(void)scrollViewDidEndDragging:(UIScrollView *)scrollView willDecelerate:(BOOL)decelerate {
    if (!decelerate) {
        [self enableTouch];
        dragingScrollView = NO;
    }
}

-(void)enableTouch {
    NSArray *subViewArray = [scrollView subviews];
    for (UIView *tempView in subViewArray) {
        if ([tempView isKindOfClass:[ArticleReaderView class]]) {
            [tempView setUserInteractionEnabled:YES];
        }
    }
}
-(void)disableTouch {
    NSArray *subViewArray = [scrollView subviews];
    for (UIView *tempView in subViewArray) {
        if ([tempView isKindOfClass:[ArticleReaderView class]]) {
            [tempView setUserInteractionEnabled:NO];
        }
    }
}

@end
