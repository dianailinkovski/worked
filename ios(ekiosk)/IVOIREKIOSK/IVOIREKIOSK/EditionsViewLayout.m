//
//  IssuesViewLayout.m
//  NGSER
//
//  Created by Maxime Julien-Paquet on 2013-10-31.
//
//

#import "EditionsViewLayout.h"

static NSString * const IssuesViewLayoutissuesCellKind = @"issueCell";


@interface EditionsViewLayout ()

@property (nonatomic, strong) NSDictionary *layoutInfo;

@end


@implementation EditionsViewLayout

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
        self.itemInsets = UIEdgeInsetsMake(53.0f, 30.0f, 10.0f, 10.0f);
        self.itemSize = CGSizeMake(130.0f, 170.0f);
        self.interItemSpacingY = 55.0f;
        self.numberOfColumns = 5;
        self.numberOfRow = 4;
    }
    else {
        if([UIScreen mainScreen].bounds.size.height == 568.0) {
            self.itemInsets = UIEdgeInsetsMake(19.0f, 20.0f, 10.0f, 20.0f);
            self.itemSize = CGSizeMake(77.0f, 105.0f);
            self.interItemSpacingY = 33.0f;
            self.numberOfColumns = 3;
            self.numberOfRow = 3;
        }
        else {
            self.itemInsets = UIEdgeInsetsMake(16.0f, 11.0f, 11.0f, 11.0f);
            self.itemSize = CGSizeMake(66.0f, 90.0f);
            self.interItemSpacingY = 30.0f;
            self.numberOfColumns = 4;
            self.numberOfRow = 3;
        }
        
        
    }
}

#pragma mark - Layout

- (void)prepareLayout {
    NSLog(@"prepareLayout");
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
    
    NSInteger nbPage = [self.collectionView numberOfItemsInSection:0] / (self.numberOfRow * self.numberOfColumns);
    if ([self.collectionView numberOfItemsInSection:0] % (self.numberOfRow * self.numberOfColumns)) nbPage++;
    
    
    //NSInteger rowCount = [self.collectionView numberOfItemsInSection:0] / self.numberOfColumns;
    
    //NSLog(@"rowCount = %d", rowCount);
    // make sure we count another row if one is only partially filled
    //if ([self.collectionView numberOfItemsInSection:0] % self.numberOfColumns) rowCount++;
    
//    CGFloat height = self.itemInsets.top +
//    rowCount * self.itemSize.height + (rowCount - 1) * self.interItemSpacingY +
//    self.itemInsets.bottom;
    
    CGFloat height = self.itemInsets.top +
    self.numberOfRow * self.itemSize.height + (self.numberOfRow - 1) * self.interItemSpacingY +
    self.itemInsets.bottom - 64;
    
    return CGSizeMake(self.collectionView.bounds.size.width * nbPage, height);
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
    
    NSInteger nbPage = indexPath.row / (self.numberOfRow * self.numberOfColumns);
    //if ([self.collectionView numberOfItemsInSection:0] % (self.numberOfRow * self.numberOfColumns)) nbPage++;
    
    //NSLog(@"%@",NSStringFromCGRect(CGRectMake(originX + ((nbPage-1) * self.collectionView.frame.size.width), originY - ((nbPage-1) * 900), self.itemSize.width, self.itemSize.height)));
    NSInteger pageHeight;
    if (isPad()) {
        pageHeight = 225 * self.numberOfRow;
    }
    else {
        if([UIScreen mainScreen].bounds.size.height == 568.0) {
            pageHeight = 138 * self.numberOfRow;
        }
        else {
            pageHeight = 120 * self.numberOfRow;
        }
    }
    
    
    return CGRectMake(originX + (nbPage * self.collectionView.frame.size.width), originY - (nbPage * pageHeight), self.itemSize.width, self.itemSize.height);
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

@end
