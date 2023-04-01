//
//  AbonnementViewCell.h
//  IVOIREKIOSK
//
//  Created by Maxime Julien-Paquet on 2014-01-10.
//  Copyright (c) 2014 Maxime Julien-Paquet. All rights reserved.
//

#import <UIKit/UIKit.h>

@interface AbonnementViewCell : UICollectionViewCell

@property (nonatomic, strong) NSMutableArray *dataArray;

@property (nonatomic, strong) UILabel *nomLabel;
@property (nonatomic, strong) UILabel *prixLabel;

//@property (nonatomic, strong) UILabel *quotidienLabel;
//@property (nonatomic, strong) UILabel *hebdoLabel;
//@property (nonatomic, strong) UILabel *mensuelLabel;
@property (nonatomic, strong) UILabel *totalLabel;

@property (nonatomic, strong) UIView *itemsView;

@property (nonatomic, strong) UILabel *selectLabel;

-(void)setItemsInView;

@end
