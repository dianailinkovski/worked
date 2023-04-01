//
//  EditionsStoreView.h
//  IVOIREKIOSK
//
//  Created by Maxime Julien-Paquet on 2013-12-19.
//  Copyright (c) 2013 Maxime Julien-Paquet. All rights reserved.
//

#import <UIKit/UIKit.h>
#import "Editions.h"

@class EditionImageView;

@interface EditionsStoreView : UICollectionViewCell

@property (nonatomic, strong) Editions *edition;
@property (nonatomic, strong) EditionImageView *coverImageView;

@property (nonatomic, strong) UIImageView *bordertop;
@property (nonatomic, strong) UIImageView *borderright;
@property (nonatomic, strong) UIImageView *bannerImageView;
@property (nonatomic, strong) UILabel *titleLabel;
//@property (nonatomic, strong) UILabel *categorieLabel;
//@property (nonatomic, strong) UILabel *dateLabel;
//@property (nonatomic, strong) UILabel *prixLabel;

//@property (nonatomic, strong) UIButton *actionButton;
//@property (nonatomic, strong) UIButton *prixButton;

@property (nonatomic, strong) NSDictionary *dataDictionary;

@property (nonatomic, strong) NSManagedObjectContext *managedObjectContext;

-(void)setData:(NSDictionary*)data;
-(void)setArchivesData:(NSDictionary*)data;
-(void)setMemeEditeurData:(NSDictionary*)data;
-(void)setEditionsData:(Editions*)data;

@end
