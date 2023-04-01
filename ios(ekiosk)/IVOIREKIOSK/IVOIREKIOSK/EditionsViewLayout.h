//
//  IssuesViewLayout.h
//  NGSER
//
//  Created by Maxime Julien-Paquet on 2013-10-31.
//
//

#import <UIKit/UIKit.h>

@interface EditionsViewLayout : UICollectionViewLayout

@property (nonatomic) UIEdgeInsets itemInsets;
@property (nonatomic) CGSize itemSize;
@property (nonatomic) CGFloat interItemSpacingY;
@property (nonatomic) NSInteger numberOfColumns;
@property (nonatomic) NSInteger numberOfRow;

@end
