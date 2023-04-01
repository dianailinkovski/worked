//
//  AbonnementViewCell.m
//  IVOIREKIOSK
//
//  Created by Maxime Julien-Paquet on 2014-01-10.
//  Copyright (c) 2014 Maxime Julien-Paquet. All rights reserved.
//

#import "AbonnementViewCell.h"

@implementation AbonnementViewCell

@synthesize nomLabel, prixLabel, totalLabel, selectLabel, dataArray, itemsView;
// quotidienLabel, hebdoLabel, mensuelLabel

- (id)initWithFrame:(CGRect)frame {
    self = [super initWithFrame:frame];
    if (self) {
        // Initialization code
        
        self.opaque = NO;
        
        self.backgroundColor = [UIColor whiteColor];
        self.layer.cornerRadius = 15;
        self.layer.borderColor = [UIColor lightGrayColor].CGColor;
        self.layer.borderWidth = 1;
        
        self.layer.shadowColor = [UIColor blackColor].CGColor;
        self.layer.shadowOpacity = 0.5;
        self.layer.shadowRadius = 2;
        self.layer.shadowOffset = CGSizeMake(1.0f, 1.0f);
        
        
        UIImageView *tempImageView;
        
        //orange
        
        tempImageView = [[UIImageView alloc] initWithFrame:CGRectMake(0, 0, self.frame.size.width, 68)];
        tempImageView.backgroundColor = [UIColor colorWithRed:0.9412 green:0.4510 blue:0.0 alpha:1.0];
        tempImageView.layer.cornerRadius = 15;
        [self addSubview:tempImageView];
        
        tempImageView = [[UIImageView alloc] initWithFrame:CGRectMake(0, 48, self.frame.size.width, 20)];
        tempImageView.backgroundColor = [UIColor colorWithRed:0.9412 green:0.4510 blue:0.0 alpha:1.0];
        [self addSubview:tempImageView];
        
        // vert
        
        tempImageView = [[UIImageView alloc] initWithFrame:CGRectMake(346, 0, self.frame.size.width-346, 68)];
        tempImageView.backgroundColor = [UIColor colorWithRed:0.0 green:0.6196 blue:0.3804 alpha:1.0];
        tempImageView.layer.cornerRadius = 15;
        [self addSubview:tempImageView];
        
        tempImageView = [[UIImageView alloc] initWithFrame:CGRectMake(346, 0, 15, 68)];
        tempImageView.backgroundColor = [UIColor colorWithRed:0.0 green:0.6196 blue:0.3804 alpha:1.0];
        [self addSubview:tempImageView];
        
        tempImageView = [[UIImageView alloc] initWithFrame:CGRectMake(self.frame.size.width - 20, 68-20, 20, 20)];
        tempImageView.backgroundColor = [UIColor colorWithRed:0.0 green:0.6196 blue:0.3804 alpha:1.0];
        [self addSubview:tempImageView];
        
        
        //ligne grise
        
        //tempImageView = [[UIImageView alloc] initWithFrame:CGRectMake(346, 68, 1, 152)];
        tempImageView = [[UIImageView alloc] initWithFrame:CGRectMake(346, 0, 1, 210)];
        tempImageView.backgroundColor = [UIColor lightGrayColor];
        [self addSubview:tempImageView];
        
        tempImageView = [[UIImageView alloc] initWithFrame:CGRectMake(0, 68, 487, 1)];
        tempImageView.backgroundColor = [UIColor lightGrayColor];
        [self addSubview:tempImageView];
        
        tempImageView = [[UIImageView alloc] initWithFrame:CGRectMake(0, self.frame.size.height - 50, self.frame.size.width, 1)];
        tempImageView.backgroundColor = [UIColor lightGrayColor];
        [self addSubview:tempImageView];
        
        UILabel *tempLabel;
        
        /*tempLabel = [[UILabel alloc] initWithFrame:CGRectMake(28, 163, 75, 21)];
        tempLabel.textAlignment = NSTextAlignmentCenter;
        tempLabel.text = @"Quotidien";
        [self addSubview:tempLabel];
        
        tempLabel = [[UILabel alloc] initWithFrame:CGRectMake(123, 163, 114, 21)];
        tempLabel.textAlignment = NSTextAlignmentCenter;
        tempLabel.text = @"Hebodmadaire";
        [self addSubview:tempLabel];
        
        tempLabel = [[UILabel alloc] initWithFrame:CGRectMake(263, 163, 65, 21)];
        tempLabel.textAlignment = NSTextAlignmentCenter;
        tempLabel.text = @"Mensuel";
        [self addSubview:tempLabel];
        */
        tempLabel = [[UILabel alloc] initWithFrame:CGRectMake(359, 163, 108, 21)];
        tempLabel.textAlignment = NSTextAlignmentCenter;
        tempLabel.text = @"Total par mois";
        [self addSubview:tempLabel];
        
        
        [self setup];
        
    }
    return self;
}

-(void)setup {
    
    [self addSubview:[self nomLabel]];
    [self addSubview:[self prixLabel]];
    //[self addSubview:[self quotidienLabel]];
    //[self addSubview:[self hebdoLabel]];
    //[self addSubview:[self mensuelLabel]];
    [self addSubview:[self totalLabel]];
    [self addSubview:[self selectLabel]];
    [self addSubview:[self itemsView]];
    
}

-(void)prepareForReuse {
    [super prepareForReuse];
    
    [nomLabel removeFromSuperview];
    [prixLabel removeFromSuperview];
    [totalLabel removeFromSuperview];
    [selectLabel removeFromSuperview];
    [itemsView removeFromSuperview];
    
    nomLabel = nil;
    prixLabel = nil;
    totalLabel = nil;
    selectLabel = nil;
    itemsView = nil;
    
    [self setup];
    
}

-(UIView *)itemsView {
    if (itemsView == nil) {
        itemsView = [[UIView alloc] initWithFrame:CGRectMake(0, 69, 346, 141)];
        itemsView.backgroundColor = [UIColor clearColor];
    }
    return itemsView;
}

-(UILabel *)nomLabel {
    if (nomLabel == nil) {
        nomLabel = [[UILabel alloc] initWithFrame:CGRectMake(20, 14, 346, 40)];
        nomLabel.font = [UIFont fontWithName:@"Helvetica-Bold" size:24];
        nomLabel.textColor = [UIColor whiteColor];
        nomLabel.adjustsFontSizeToFitWidth = YES;
        nomLabel.adjustsLetterSpacingToFitWidth = YES;
    }
    return nomLabel;
}

-(UILabel *)prixLabel {
    if (prixLabel == nil) {
        prixLabel = [[UILabel alloc] initWithFrame:CGRectMake(325, 14, 140, 40)];
        prixLabel.textAlignment = NSTextAlignmentRight;
        prixLabel.font = [UIFont fontWithName:@"Helvetica-Bold" size:20];
        prixLabel.textColor = [UIColor whiteColor];
    }
    return prixLabel;
}

/*
-(UILabel *)quotidienLabel {
    if (quotidienLabel == nil) {
        quotidienLabel = [[UILabel alloc] initWithFrame:CGRectMake(35, 83, 60, 60)];
        quotidienLabel.font = [UIFont fontWithName:@"Helvetica-Bold" size:36];
        quotidienLabel.textAlignment = NSTextAlignmentCenter;
    }
    return quotidienLabel;
}

-(UILabel *)hebdoLabel {
    if (hebdoLabel == nil) {
        hebdoLabel = [[UILabel alloc] initWithFrame:CGRectMake(150, 83, 60, 60)];
        hebdoLabel.font = [UIFont fontWithName:@"Helvetica-Bold" size:36];
        hebdoLabel.textAlignment = NSTextAlignmentCenter;
    }
    return hebdoLabel;
}

-(UILabel *)mensuelLabel {
    if (mensuelLabel == nil) {
        mensuelLabel = [[UILabel alloc] initWithFrame:CGRectMake(265, 83, 60, 60)];
        mensuelLabel.font = [UIFont fontWithName:@"Helvetica-Bold" size:36];
        mensuelLabel.textAlignment = NSTextAlignmentCenter;
    }
    return mensuelLabel;
}
*/

-(UILabel *)totalLabel {
    if (totalLabel == nil) {
        totalLabel = [[UILabel alloc] initWithFrame:CGRectMake(386, 83, 60, 60)];
        totalLabel.font = [UIFont fontWithName:@"Helvetica-Bold" size:36];
        totalLabel.textAlignment = NSTextAlignmentCenter;
        
    }
    return totalLabel;
}

-(UILabel *)selectLabel {
    if (selectLabel == nil) {
        selectLabel = [[UILabel alloc] initWithFrame:CGRectMake(0, self.frame.size.height - 40, self.frame.size.width, 30)];
        selectLabel.font = [UIFont fontWithName:@"Helvetica" size:22];
        selectLabel.textColor = [UIColor colorWithRed:0.1098 green:0.5333 blue:1 alpha:1];
        selectLabel.textAlignment = NSTextAlignmentCenter;
        selectLabel.text = @"Selectionner";
    }
    return selectLabel;
}

-(UIView*)getViewWithCategorie:(NSString*)categorieString Count:(NSString*)countString {
    UIView *tempView = [[UIView alloc] initWithFrame:CGRectMake(0, 0, 115, 140)];
    tempView.backgroundColor = [UIColor clearColor];
    
    UILabel *tempLabel = [[UILabel alloc] initWithFrame:CGRectMake(0, 95, 115, 25)];
    tempLabel.textAlignment = NSTextAlignmentCenter;
    tempLabel.text = categorieString;
    [tempView addSubview:tempLabel];
    
    tempLabel = [[UILabel alloc] initWithFrame:CGRectMake(0, 15, 115, 60)];
    tempLabel.font = [UIFont fontWithName:@"Helvetica-Bold" size:36];
    tempLabel.textAlignment = NSTextAlignmentCenter;
    tempLabel.text = countString;
    [tempView addSubview:tempLabel];
    
    return tempView;
}

-(void)setItemsInView {
    if (self.dataArray == nil) {
        return;
    }
    
    int inset = 0;
    
    switch ([[self.dataArray valueForKey:@"items"] count]) {
        case 1:
            inset = 120;
            break;
        case 2:
            inset = 55;
            break;
        case 3:
            inset = 0;
            break;
            
        default:
            break;
    }
    
    int totalCount = 0;
    
    for (int x = 0; x < [[self.dataArray valueForKey:@"items"] count]; ++x) {
        
        UIView *tempView = [self getViewWithCategorie:[[[self.dataArray valueForKey:@"items"] objectAtIndex:x] valueForKey:@"type"] Count:[[[self.dataArray valueForKey:@"items"] objectAtIndex:x] valueForKey:@"amount"]];
        
        CGRect frame = tempView.frame;
        frame.origin.x = x * frame.size.width + inset;
        tempView.frame = frame;
        
        [self.itemsView performSelectorOnMainThread:@selector(addSubview:) withObject:tempView waitUntilDone:NO];
        
        totalCount += [[[[self.dataArray valueForKey:@"items"] objectAtIndex:x] valueForKey:@"amount"] intValue];
    }
    [self.totalLabel setText:[NSString stringWithFormat:@"%d",totalCount]];
}

@end
