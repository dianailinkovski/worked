//
//  TextReaderViewController.h
//  NGSER
//
//  Created by Maxime Julien-Paquet on 2013-10-25.
//
//

#import <UIKit/UIKit.h>

#import "ArticleReaderView.h"

@interface TextReaderViewController : UIViewController <ArticleReaderViewDelegate>

-(id)initWithArticles:(NSArray*)articles AndTitleNavBar:(NSString*)title;

@end
