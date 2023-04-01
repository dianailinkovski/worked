//
//  StoreViewLayout.m
//  IVOIREKIOSK
//
//  Created by Maxime Julien-Paquet on 2014-01-06.
//  Copyright (c) 2014 Maxime Julien-Paquet. All rights reserved.
//

#import "StoreViewLayout.h"
#import "EditionImageView.h"

static NSString * const IssuesViewLayoutissuesCellKind = @"issueCell";


@interface StoreViewLayout ()

@property (nonatomic, strong) NSDictionary *layoutInfo;

@end

@implementation StoreViewLayout


- (id)init {
    self = [super init];
    if (self) {
        [self setup];
    }
    
    return self;
}

- (id)initWithCoder:(NSCoder *)aDecoder {
    self = [super init];
    if (self) {
        [self setup];
    }
    
    return self;
}

- (void)setup {
    
    if (isPad()) {
        self.itemInsets = UIEdgeInsetsMake(15.0f, 30.0f, 15.0f, 30.0f);
        self.itemSize = CGSizeMake(STATIC_EDITIONSIMAGEVIEW_WIDTH*1.2, STATIC_EDITIONSIMAGEVIEW_HEIGHT*1.2 + 30);
        self.interItemSpacingY = 20.0f;
        self.numberOfColumns = 4;
    }
    else {
        self.itemInsets = UIEdgeInsetsMake(10.0f, 20.0f, 10.0f, 20.0f);
        self.itemSize = CGSizeMake(STATIC_EDITIONSIMAGEVIEW_WIDTH*0.7, STATIC_EDITIONSIMAGEVIEW_HEIGHT*0.7 + 25);
        NSLog(@"%@", NSStringFromCGSize(self.itemSize));
        self.interItemSpacingY = 20.0f;
        self.numberOfColumns = 3;
    }
    
    [[NSNotificationCenter defaultCenter] addObserver:self
                                             selector:@selector(setColumnsForCollapsingMenu)
                                                 name:@"CollapsingMenu"
                                               object:nil];
    [[NSNotificationCenter defaultCenter] addObserver:self
                                             selector:@selector(setColumnsForExpandingMenu)
                                                 name:@"ExpandingMenu"
                                               object:nil];
}
-(void)dealloc {
    [[NSNotificationCenter defaultCenter] removeObserver:self name:@"CollapsingMenu" object:nil];
    [[NSNotificationCenter defaultCenter] removeObserver:self name:@"ExpandingMenu" object:nil];
}

#pragma mark - Layout

- (void)prepareLayout {
    NSMutableDictionary *newLayoutInfo = [NSMutableDictionary dictionary];
    NSMutableDictionary *cellLayoutInfo = [NSMutableDictionary dictionary];
    
    NSInteger sectionCount = [self.collectionView numberOfSections];
    NSIndexPath *indexPath = [NSIndexPath indexPathForItem:0 inSection:0];
    
    for (NSInteger section = 0; section < sectionCount; section++) {
        NSInteger itemCount = [self.collectionView numberOfItemsInSection:section];
        //NSInteger section = 0;
        //NSInteger itemCount = [self.collectionView numberOfItemsInSection:section];
        
        for (NSInteger item = 0; item < itemCount; ++item) {
            indexPath = [NSIndexPath indexPathForItem:item inSection:section];
            
            UICollectionViewLayoutAttributes *itemAttributes =
            [UICollectionViewLayoutAttributes layoutAttributesForCellWithIndexPath:indexPath];
            itemAttributes.frame = [self frameForAlbumPhotoAtIndexPath:indexPath];
            cellLayoutInfo[indexPath] = itemAttributes;
        }
    }
    
    newLayoutInfo[IssuesViewLayoutissuesCellKind] = cellLayoutInfo;
    
    self.layoutInfo = newLayoutInfo;
}

- (NSArray *)layoutAttributesForElementsInRect:(CGRect)rect {
    
    NSMutableArray *allAttributes = [NSMutableArray arrayWithCapacity:self.layoutInfo.count];
    
    [self.layoutInfo enumerateKeysAndObjectsUsingBlock:^(NSString *elementIdentifier,
                                                         NSDictionary *elementsInfo,
                                                         BOOL *stop) {
        [elementsInfo enumerateKeysAndObjectsUsingBlock:^(NSIndexPath *indexPath,
                                                          UICollectionViewLayoutAttributes *attributes,
                                                          BOOL *innerStop) {
            if (CGRectIntersectsRect(rect, attributes.frame)) {
                [allAttributes addObject:attributes];
            }
        }];
    }];
    
    return allAttributes;
}

- (UICollectionViewLayoutAttributes *)layoutAttributesForItemAtIndexPath:(NSIndexPath *)indexPath {
    return self.layoutInfo[IssuesViewLayoutissuesCellKind][indexPath];
}

- (CGSize)collectionViewContentSize {
    NSInteger rowCount = [self.collectionView numberOfItemsInSection:0] / self.numberOfColumns;
    
    // make sure we count another row if one is only partially filled
    if ([self.collectionView numberOfItemsInSection:0] % self.numberOfColumns) rowCount++;
    //NSLog(@"row count = %d",rowCount);
    CGFloat height = self.itemInsets.top +
    rowCount * self.itemSize.height + (rowCount - 1) * self.interItemSpacingY +
    self.itemInsets.bottom;
    
    return CGSizeMake(self.collectionView.bounds.size.width, height);
}

#pragma mark - Private

- (CGRect)frameForAlbumPhotoAtIndexPath:(NSIndexPath *)indexPath {
    NSInteger row = indexPath.row / self.numberOfColumns;
    NSInteger column = indexPath.row % self.numberOfColumns;
    CGFloat spacingX = self.collectionView.bounds.size.width -
    self.itemInsets.left -
    self.itemInsets.right -
    (self.numberOfColumns * self.itemSize.width);
    
    if (self.numberOfColumns > 1) spacingX = spacingX / (self.numberOfColumns - 1);
    
    CGFloat originX = floorf(self.itemInsets.left + (self.itemSize.width + spacingX) * column);
    
    CGFloat originY = floor(self.itemInsets.top +
                            (self.itemSize.height + self.interItemSpacingY) * row);
    
    return CGRectMake(originX, originY, self.itemSize.width, self.itemSize.height);
}

- (void)setItemInsets:(UIEdgeInsets)itemInsets {
    if (UIEdgeInsetsEqualToEdgeInsets(_itemInsets, itemInsets)) return;
    
    _itemInsets = itemInsets;
    
    [self invalidateLayout];
}

- (void)setItemSize:(CGSize)itemSize {
    if (CGSizeEqualToSize(_itemSize, itemSize)) return;
    
    _itemSize = itemSize;
    
    [self invalidateLayout];
}

- (void)setInterItemSpacingY:(CGFloat)interItemSpacingY {
    if (_interItemSpacingY == interItemSpacingY) return;
    
    _interItemSpacingY = interItemSpacingY;
    
    [self invalidateLayout];
}

- (void)setNumberOfColumns:(NSInteger)numberOfColumns {
    if (_numberOfColumns == numberOfColumns) return;
    
    _numberOfColumns = numberOfColumns;
    
    [self invalidateLayout];
}

-(void)setColumnsForCollapsingMenu {
    self.numberOfColumns = 3;
}
-(void)setColumnsForExpandingMenu {
    self.numberOfColumns = 2;
}


@end
