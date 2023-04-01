//
//  JournalPickerHeaderViewCell.m
//  IVOIREKIOSK
//
//  Created by Maxime Julien-Paquet on 2014-01-11.
//  Copyright (c) 2014 Maxime Julien-Paquet. All rights reserved.
//

#import "JournalPickerHeaderViewCell.h"

@implementation JournalPickerHeaderViewCell

@synthesize titleLabel, compteurLabel, indexPathLocal,titleSwitchLabel,subscriptionSwitch;

- (id)initWithFrame:(CGRect)frame {
    self = [super initWithFrame:frame];
    if (self) {
        // Initialization code
        [[NSNotificationCenter defaultCenter] addObserver:self selector:@selector(updateLabel:) name:@"ReloadHeaderSection" object:nil];
        self.backgroundColor = [UIColor colorWithRed:0.9412 green:0.4510 blue:0.0 alpha:1.0];
        self.layer.borderWidth = 1;
        self.layer.borderColor = [UIColor grayColor].CGColor;
        
        [self setup];
        
    }
    return self;
}
-(void)dealloc {
    [[NSNotificationCenter defaultCenter] removeObserver:self name:@"ReloadHeaderSection" object:nil];
}
-(void)setup {
    
    [self addSubview:[self titleLabel]];
    [self addSubview:[self compteurLabel]];
    //[self addSubview:[self titleSwitchLabel]];
    //[self addSubview:[self subscriptionSwitch]];
}

-(void)prepareForReuse {
    [super prepareForReuse];
    
    [titleLabel removeFromSuperview];
    [compteurLabel removeFromSuperview];
    //[titleSwitchLabel removeFromSuperview];
    //[subscriptionSwitch removeFromSuperview];
   
    indexPathLocal = nil;
    titleLabel = nil;
    compteurLabel = nil;
    //titleSwitchLabel=nil;
    //subscriptionSwitch=nil;
    [self setup];
}

-(UILabel *)titleLabel {
    if (titleLabel == nil) {
        if (isPad()) {
            titleLabel = [[UILabel alloc] initWithFrame:CGRectMake(20, 10, self.frame.size.width - 40, 30)];
            titleLabel.font = [UIFont fontWithName:@"Helvetica-Bold" size:18];
        }
        else {
            titleLabel = [[UILabel alloc] initWithFrame:CGRectMake(20, 5, self.frame.size.width - 40, 20)];
            titleLabel.font = [UIFont fontWithName:@"Helvetica-Bold" size:16];
        }
        
        titleLabel.textColor = [UIColor whiteColor];
    }
    return titleLabel;
}

-(UILabel *)compteurLabel {
    if (compteurLabel == nil) {
        compteurLabel = [[UILabel alloc] initWithFrame:CGRectMake(20, 10, self.frame.size.width - 40, 30)];
        compteurLabel.autoresizingMask = UIViewAutoresizingFlexibleLeftMargin;
        compteurLabel.font = [UIFont fontWithName:@"Helvetica" size:22];
        compteurLabel.textAlignment = NSTextAlignmentRight;
        compteurLabel.textColor = [UIColor whiteColor];
        
    }
    return compteurLabel;
}


-(UILabel *)titleSwitchLabel {
    if (titleSwitchLabel == nil) {
        if (isPad()) {
            titleSwitchLabel = [[ UILabel alloc] initWithFrame:CGRectMake(self.frame.size.width - 180,self.titleLabel.frame.origin.y,100,self.titleLabel.frame.size.height)];
            titleSwitchLabel.font = [UIFont fontWithName:@"Helvetica" size:20];
        }
      
        
        titleSwitchLabel.autoresizingMask = UIViewAutoresizingFlexibleLeftMargin;
        titleSwitchLabel.textAlignment = NSTextAlignmentLeft;
        titleSwitchLabel.backgroundColor = [UIColor clearColor];
        titleSwitchLabel.textColor = [UIColor whiteColor];
        titleSwitchLabel.numberOfLines = 2;
        titleSwitchLabel.adjustsFontSizeToFitWidth = YES;
        titleSwitchLabel.text = @"Abonné";
        
    }
    return titleSwitchLabel;
}


-(UISwitch *)subscriptionSwitch {
    if (subscriptionSwitch == nil) {
        if (isPad()) {
            subscriptionSwitch = [[UISwitch  alloc] initWithFrame:CGRectMake(self.titleSwitchLabel.frame.size.width +self.titleSwitchLabel.frame.origin.x , self.titleSwitchLabel.frame.origin.y, 100, 100)];
        }

        subscriptionSwitch.autoresizingMask = UIViewAutoresizingFlexibleLeftMargin;
        
    }
    return subscriptionSwitch;
}


-(void)updateLabel:(NSNotification *)notification {
    
    NSIndexPath *indexPath = [notification.object objectAtIndex:0];
    if (indexPath.section != indexPathLocal.section) {
        return;
    }
    
    NSArray *packageArray = [notification.object objectAtIndex:1];
    
    int total = [[[packageArray objectAtIndex:indexPath.section] objectAtIndex:1] intValue];
    int choisi = [[[packageArray objectAtIndex:indexPath.section] objectAtIndex:0] intValue];
    
    if (total == 0) {
        //headerView.compteurLabel.textColor = [UIColor colorWithRed:0.0 green:0.6196 blue:0.3804 alpha:1.0];
        compteurLabel.text = [NSString stringWithFormat:@"Aucun disponible avec l'abonnement sélectionné"];
    }
    else if (total - choisi > 0) {
        //headerView.compteurLabel.textColor = [UIColor colorWithRed:0.0 green:0.6196 blue:0.3804 alpha:1.0];
        compteurLabel.text = [NSString stringWithFormat:@"Il vous reste %d choix", total - choisi];
    }
    else if (total - choisi < 0) {
        //headerView.compteurLabel.textColor = [UIColor redColor];
        compteurLabel.text = [NSString stringWithFormat:@"Vous avez %d choix de trop, %d maximum", abs(total - choisi), total];
    }
    else {
        //headerView.compteurLabel.textColor = [UIColor colorWithRed:0.0 green:0.6196 blue:0.3804 alpha:1.0];
        compteurLabel.text = [NSString stringWithFormat:@"Sélection complète"];
    }
    
}

@end
