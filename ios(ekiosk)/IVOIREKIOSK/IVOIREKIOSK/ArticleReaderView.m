//
//  ArticleReaderView.m
//  NGSER
//
//  Created by Maxime Julien-Paquet on 2013-10-25.
//
//

#import "ArticleReaderView.h"

@implementation ArticleReaderView {
    NSArray *articleArray;
    UIView *titleView;
    UIView *contentView;
    
    UILabel *resumeLabel;
    
    BOOL touched;
}

@synthesize delegate;

- (id)initWithFrame:(CGRect)frame AndArray:(NSArray*)article {
    self = [super initWithFrame:frame];
    if (self) {
        // Initialization code
        articleArray = article;
        
        UIImageView *separateur = [[UIImageView alloc] initWithFrame:CGRectMake(0, frame.size.height-1, frame.size.width, 1)];
        separateur.backgroundColor = [UIColor lightGrayColor];
        separateur.autoresizingMask = UIViewAutoresizingFlexibleTopMargin|UIViewAutoresizingFlexibleWidth;
        [self addSubview:separateur];
        
        [self setTitleView];
        [self setContentView];
        
        touched = NO;
    }
    return self;
}

-(void)setTitleView {
    titleView = [[UIView alloc] init];
    titleView.backgroundColor = [UIColor clearColor];
    
    
    UILabel *titleLabel, *subtitleLabel;
    titleLabel = [[UILabel alloc] init];
    titleLabel.font = [UIFont fontWithName:@"Helvetica" size:28];
    titleLabel.backgroundColor = [UIColor clearColor];
    titleLabel.textColor = [UIColor blackColor];
    titleLabel.numberOfLines = 0;
    titleLabel.lineBreakMode = NSLineBreakByWordWrapping;
    NSString *titleString = [articleArray valueForKey:@"titre"];
    
    
    CGSize maximumSize = CGSizeMake(self.frame.size.width - 40, 999999);
    CGSize titleSize = [titleString sizeWithFont:titleLabel.font
                        constrainedToSize:maximumSize
                            lineBreakMode:NSLineBreakByWordWrapping];
    titleLabel.frame = CGRectMake(20, 0, titleSize.width, titleSize.height);
    titleLabel.text = titleString;
    [titleView addSubview:titleLabel];
    
    
    subtitleLabel = [[UILabel alloc] init];
    subtitleLabel.font = [UIFont fontWithName:@"Helvetica-Bold" size:38];
    subtitleLabel.backgroundColor = [UIColor clearColor];
    subtitleLabel.textColor = [UIColor blackColor];
    subtitleLabel.numberOfLines = 0;
    subtitleLabel.lineBreakMode = NSLineBreakByWordWrapping;
    NSString *subtitleString = [articleArray valueForKey:@"soustitre"];
    CGSize subtitleSize = [subtitleString sizeWithFont:subtitleLabel.font
                              constrainedToSize:maximumSize
                                  lineBreakMode:NSLineBreakByWordWrapping];
    
    subtitleLabel.frame = CGRectMake(20, (titleLabel.frame.origin.y + titleLabel.frame.size.height + 10), subtitleSize.width, subtitleSize.height);
    subtitleLabel.text = subtitleString;
    [titleView addSubview:subtitleLabel];
    
    
    
    resumeLabel = [[UILabel alloc] init];
    resumeLabel.font = [UIFont fontWithName:@"Helvetica" size:24];
    resumeLabel.backgroundColor = [UIColor clearColor];
    resumeLabel.textColor = [UIColor blackColor];
    resumeLabel.numberOfLines = 4;
    resumeLabel.lineBreakMode = NSLineBreakByWordWrapping;
    
    NSString *resumeString;
    if (![[articleArray valueForKey:@"maincontenu"] isEqualToString:@""]) {
        resumeString = [articleArray valueForKey:@"maincontenu"];
    }
    else {
        resumeString = [articleArray valueForKey:@"contenu"];
    }
    
    if ([resumeString length] > 180) {
        resumeString = [resumeString substringWithRange:NSMakeRange(0, 180)];
        
    }
    resumeString = [resumeString stringByAppendingString:@" ... Lire plus"];
    
    CGSize resumeSize = [resumeString sizeWithFont:resumeLabel.font
                                     constrainedToSize:maximumSize
                                         lineBreakMode:NSLineBreakByWordWrapping];
    
    resumeLabel.frame = CGRectMake(20, (subtitleLabel.frame.origin.y + subtitleLabel.frame.size.height + 10), resumeSize.width, resumeSize.height);
    resumeLabel.text = resumeString;
    [titleView addSubview:resumeLabel];
    
    int height = (resumeLabel.frame.origin.y + resumeLabel.frame.size.height);
    titleView.frame = CGRectMake(0, 0, self.frame.size.width, height);
    [self addSubview:titleView];
    
    [self resizeMainViewCollapsed];
    
}

-(void)setContentView {
    contentView = [[UIView alloc] init];
    contentView.backgroundColor = [UIColor clearColor];
    UILabel *maincontentLabel, *contentLabel;
    
    maincontentLabel = [[UILabel alloc] init];
    maincontentLabel.font = [UIFont fontWithName:@"Helvetica-Bold" size:20];
    maincontentLabel.backgroundColor = [UIColor clearColor];
    maincontentLabel.textColor = [UIColor blackColor];
    maincontentLabel.numberOfLines = 0;
    maincontentLabel.lineBreakMode = NSLineBreakByWordWrapping;
    NSString *maincontentString = [articleArray valueForKey:@"maincontenu"];
    
    
    CGSize maximumSize = CGSizeMake(self.frame.size.width - 40, 999999);
    CGSize maincontentSize = [maincontentString sizeWithFont:maincontentLabel.font
                               constrainedToSize:maximumSize
                                   lineBreakMode:NSLineBreakByWordWrapping];
    maincontentLabel.frame = CGRectMake(20, 0, maincontentSize.width, maincontentSize.height);
    maincontentLabel.text = maincontentString;
    [contentView addSubview:maincontentLabel];
    
    
    contentLabel = [[UILabel alloc] init];
    contentLabel.font = [UIFont fontWithName:@"Helvetica" size:20];
    contentLabel.backgroundColor = [UIColor clearColor];
    contentLabel.textColor = [UIColor blackColor];
    contentLabel.numberOfLines = 0;
    contentLabel.lineBreakMode = NSLineBreakByWordWrapping;
    NSString *contentString = [articleArray valueForKey:@"contenu"];
    
    CGSize contentSize = [contentString sizeWithFont:contentLabel.font
                                           constrainedToSize:maximumSize
                                               lineBreakMode:NSLineBreakByWordWrapping];
    contentLabel.frame = CGRectMake(20, (maincontentLabel.frame.origin.y + maincontentLabel.frame.size.height + 10), contentSize.width, contentSize.height);
    contentLabel.text = contentString;
    [contentView addSubview:contentLabel];
    
    int height = (contentLabel.frame.origin.y + contentLabel.frame.size.height);
    contentView.frame = CGRectMake(0, (titleView.frame.origin.y + titleView.frame.size.height + 5)-resumeLabel.frame.size.height, self.frame.size.width, height);
    [self addSubview:contentView];
    [contentView setHidden:YES];
    
    //[self resizeMainViewExpanded];
}

-(void)resizeMainViewCollapsed {
    CGRect frame = self.frame;
    frame.size.height = [self getCollapsedHeight];
    self.frame = frame;
}
-(void)resizeMainViewExpanded {
    CGRect frame = self.frame;
    frame.size.height = [self getExpandedHeight];
    self.frame = frame;
}

-(int)getCollapsedHeight {
    return titleView.frame.origin.y + titleView.frame.size.height + 20;
}
-(int)getExpandedHeight {
    return contentView.frame.origin.y + contentView.frame.size.height + 20;
}

-(void)rotateView {
    BOOL isExpanded = NO;
    
    if (![contentView isHidden]) {
        isExpanded = YES;
    }
    
    [titleView removeFromSuperview]; titleView = nil;
    [contentView removeFromSuperview]; contentView = nil;
    
    [self setTitleView];
    [self setContentView];
    
    if (isExpanded) {
        [self resizeMainViewExpanded];
        [contentView setHidden:NO];
        [contentView setAlpha:1];
    }
    
}

#pragma mark - Animation function

-(void)expandAnimation {
    [contentView setAlpha:0];
    [contentView setHidden:NO];
    [self performSelector:@selector(expandedAnimation) withObject:nil afterDelay:0.1];
    
    [UIView beginAnimations:nil context:nil];
    [UIView setAnimationDuration:0.5];
    [resumeLabel setHidden:YES];
    [contentView setAlpha:1];
    [self resizeMainViewExpanded];
    
    [UIView commitAnimations];
}
-(void)collapseAnimation {
    
    [self performSelector:@selector(collapsedAnimation) withObject:nil afterDelay:0.1];
    [resumeLabel setAlpha:0];
    [resumeLabel setHidden:NO];
    [UIView beginAnimations:nil context:nil];
    [UIView setAnimationDuration:0.5];
    [resumeLabel setAlpha:1];
    [contentView setAlpha:0];
    [self resizeMainViewCollapsed];
    
    [UIView commitAnimations];
}
-(void)expandedAnimation {
    if (delegate && [delegate respondsToSelector:@selector(ArticleReaderView:didExpandToHeight:)]) {
        [delegate ArticleReaderView:self didExpandToHeight:[self getExpandedHeight]];
    }
}
-(void)collapsedAnimation {
    [contentView setAlpha:1];
    [contentView setHidden:YES];
    if (delegate && [delegate respondsToSelector:@selector(ArticleReaderView:didCollapseToHeight:)]) {
        [delegate ArticleReaderView:self didCollapseToHeight:[self getCollapsedHeight]];
    }
}


#pragma mark - Touch Function

-(void)touchesBegan:(NSSet *)touches withEvent:(UIEvent *)event {
    touched = YES;
}
-(void)touchesMoved:(NSSet *)touches withEvent:(UIEvent *)event {
    touched = NO;
}
-(void)touchesEnded:(NSSet *)touches withEvent:(UIEvent *)event {
    if (!touched) {
        return;
    }
    if ([contentView isHidden]) {
        
        if (delegate && [delegate respondsToSelector:@selector(ArticleReaderView:willExpandToHeight:)]) {
            [delegate ArticleReaderView:self willExpandToHeight:[self getExpandedHeight]];
        }
        
        [self expandAnimation];
    }
    else {
        
        
        if (delegate && [delegate respondsToSelector:@selector(ArticleReaderView:willCollapseToHeight:)]) {
            [delegate ArticleReaderView:self willCollapseToHeight:[self getCollapsedHeight]];
        }
        [self collapseAnimation];
    }
    touched = NO;
}
-(void)touchesCancelled:(NSSet *)touches withEvent:(UIEvent *)event {
    touched = NO;
}

@end
