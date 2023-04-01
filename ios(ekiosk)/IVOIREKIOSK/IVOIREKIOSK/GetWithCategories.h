//
//  GetWithCategories.h
//  IVOIREKIOSK
//
//  Created by Maxime Julien-Paquet on 2013-12-19.
//  Copyright (c) 2013 Maxime Julien-Paquet. All rights reserved.
//

#import <UIKit/UIKit.h>

@protocol GetWithCategoriesDelegate <NSObject>

-(void)importerDidFinishParsingData:(NSMutableArray *)data;
-(void)importerDidFailedOrNoInternet;

@optional
-(void)importer:(NSOperation *)importer didFailWithError:(NSError *)error;

@end

@interface GetWithCategories : NSOperation

@property (nonatomic, strong) NSString *categorieString;
@property (nonatomic, weak) __weak id <GetWithCategoriesDelegate> delegate;

-(id)initWithCategorie:(NSString*)categorie;

@end
