//
//  AbonnementRowView.h
//  NGSER
//
//  Created by Maxime Julien-Paquet on 2013-11-06.
//
//

#import <UIKit/UIKit.h>

@interface AbonnementRowView : UIView

@property (nonatomic, strong) UILabel *abonnementLabel;
@property (nonatomic, strong) UILabel *titleLabel;
@property (nonatomic, strong) UILabel *prixLabel;
@property (nonatomic, strong) UIButton *touchButton;

-(void)addBottomSeparator;
-(void)addLeftSeparator;

@end
