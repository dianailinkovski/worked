//
//  ArticleReaderView.h
//  NGSER
//
//  Created by Maxime Julien-Paquet on 2013-10-25.
//
//

#import <UIKit/UIKit.h>

@protocol ArticleReaderViewDelegate;

@interface ArticleReaderView : UIView {
    __weak id <ArticleReaderViewDelegate> delegate;
}

@property (nonatomic, weak) id delegate;

- (id)initWithFrame:(CGRect)frame AndArray:(NSArray*)articles;
-(void)setViews;
-(void)rotateView;

@end

@protocol ArticleReaderViewDelegate <NSObject>

-(void)ArticleReaderView:(ArticleReaderView*)articleReaderView willExpandToHeight:(int)height;
-(void)ArticleReaderView:(ArticleReaderView*)articleReaderView didExpandToHeight:(int)height;

-(void)ArticleReaderView:(ArticleReaderView*)articleReaderView willCollapseToHeight:(int)height;
-(void)ArticleReaderView:(ArticleReaderView*)articleReaderView didCollapseToHeight:(int)height;

@end