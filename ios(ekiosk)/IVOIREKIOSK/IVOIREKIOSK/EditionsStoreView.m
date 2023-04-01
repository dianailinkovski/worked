//
//  EditionsStoreView.m
//  IVOIREKIOSK
//
//  Created by Maxime Julien-Paquet on 2013-12-19.
//  Copyright (c) 2013 Maxime Julien-Paquet. All rights reserved.
//

#import "EditionsStoreView.h"
#import "EditionImageView.h"
#import "AppDelegate.h"
#import "Editions.h"

@implementation EditionsStoreView

//@synthesize coverImageView, titleLabel, categorieLabel, dateLabel, actionButton, prixLabel, prixButton, dataDictionary, managedObjectContext;
@synthesize coverImageView, dataDictionary, managedObjectContext, bordertop, borderright, titleLabel, edition,bannerImageView;

-(id)initWithFrame:(CGRect)frame {
    self = [super initWithFrame:frame];
    if (self) {
        // Initialization code
        [self setup];
    }
    return self;
}

-(void)setup {
    self.clipsToBounds = NO;
    self.backgroundColor = [UIColor clearColor];
    [self addSubview:[self coverImageView]];
    [self addSubview:[self borderright]];
    [self addSubview:[self bordertop]];
    
    [self addSubview:[self titleLabel]];
    [self addSubview:[self bannerImageView]];
    //[self addSubview:[self titleLabel]];
    //[self addSubview:[self categorieLabel]];
    //[self addSubview:[self dateLabel]];
    //[self addSubview:[self prixLabel]];
    //[self addSubview:[self actionButton]];
    //[self addSubview:[self prixButton]];
}


-(void)prepareForReuse {
    [super prepareForReuse];
    
    [titleLabel removeFromSuperview];
    [coverImageView removeFromSuperview];
    [borderright removeFromSuperview];
    [bordertop removeFromSuperview];
    [bannerImageView removeFromSuperview];

    titleLabel = nil;
    coverImageView = nil;
    borderright = nil;
    bordertop = nil;
    bannerImageView = nil;
    [self setup];
    
}

-(UIImageView *)borderright {
    if (borderright == nil) {
        borderright =  [[UIImageView alloc] initWithFrame:CGRectMake(self.frame.size.width + 30, -10, 1, self.frame.size.height + 20)];
        borderright.backgroundColor = [UIColor colorWithWhite:0 alpha:0.1];
        borderright.hidden = YES;
    }
    return borderright;
}
-(UIImageView *)bordertop {
    if (bordertop == nil) {
        bordertop = [[UIImageView alloc] initWithFrame:CGRectMake(-10, -20, self.frame.size.width + 20, 1)];
        bordertop.backgroundColor = [UIColor colorWithWhite:0 alpha:0.1];
        bordertop.hidden = YES;
    }
    return bordertop;
}

-(EditionImageView *)coverImageView {
    if (coverImageView == nil) {
        if (isPad()) {
            coverImageView = [[EditionImageView alloc] initWithFrame:CGRectMake(0,
                                                                                0,
                                                                                STATIC_EDITIONSIMAGEVIEW_WIDTH*1.2,
                                                                                STATIC_EDITIONSIMAGEVIEW_HEIGHT*1.2)];
        }
        else {
            coverImageView = [[EditionImageView alloc] initWithFrame:CGRectMake(0,
                                                                                0,
                                                                                STATIC_EDITIONSIMAGEVIEW_WIDTH*0.7,
                                                                                STATIC_EDITIONSIMAGEVIEW_HEIGHT*0.7)];
        }
        
        [coverImageView addBorderAndDropShadow];
    }
    return coverImageView;
}

-(UILabel *)titleLabel {
    if (titleLabel == nil) {
        if (isPad()) {
            titleLabel = [[UILabel alloc] initWithFrame:CGRectMake(-10,
                                                                   STATIC_EDITIONSIMAGEVIEW_HEIGHT*1.2 + 5,
                                                                   STATIC_EDITIONSIMAGEVIEW_WIDTH*1.2 + 20,
                                                                   25)];
            titleLabel.font = [UIFont fontWithName:@"Helvetica-Bold" size:16];
        }
        else {
            titleLabel = [[UILabel alloc] initWithFrame:CGRectMake(-5,
                                                                   STATIC_EDITIONSIMAGEVIEW_HEIGHT*0.7 + 2,
                                                                   STATIC_EDITIONSIMAGEVIEW_WIDTH*0.7 + 10,
                                                                   23)];
            titleLabel.font = [UIFont fontWithName:@"Helvetica-Bold" size:12];
        }
        
        titleLabel.textColor = [UIColor colorWithWhite:0.1 alpha:1];
        titleLabel.numberOfLines = 2;
        titleLabel.adjustsLetterSpacingToFitWidth = YES;
        titleLabel.adjustsFontSizeToFitWidth = YES;
        titleLabel.minimumScaleFactor = 0.5;
        titleLabel.textAlignment = NSTextAlignmentCenter;
        
        titleLabel.backgroundColor = [UIColor clearColor];
        
    }
    return titleLabel;
}

-(UIImageView *)bannerImageView
{
    if (bannerImageView == nil) {
        if (isPad()) {
            bannerImageView = [[UIImageView alloc] initWithFrame:CGRectMake(-2, -2, 80, 80)];
        }
        else {
            if([UIScreen mainScreen].bounds.size.height == 568.0) {
                bannerImageView = [[UIImageView alloc] initWithFrame:CGRectMake(-2, -2, 55, 55)];
            }
            else {
                bannerImageView = [[UIImageView alloc] initWithFrame:CGRectMake(-2, -2, 55, 55)];
            }
        }
        
        [bannerImageView setImage:[UIImage imageNamed:@"Header_subscription"]];
        [bannerImageView setBackgroundColor:[UIColor clearColor]];
        [bannerImageView setHidden:YES];
    }
    return bannerImageView;
}

/*
-(UILabel *)dateLabel {
    if (dateLabel == nil) {
        dateLabel = [[UILabel alloc] initWithFrame:CGRectMake(30+STATIC_EDITIONSIMAGEVIEW_WIDTH+10,
                                                              60,
                                                              self.bounds.size.width - 40 - STATIC_EDITIONSIMAGEVIEW_WIDTH,
                                                              20)];
        dateLabel.textColor = [UIColor colorWithWhite:0.2 alpha:1];
        dateLabel.font = [UIFont fontWithName:@"Helvetica" size:16];
    }
    return dateLabel;
}

-(UILabel *)categorieLabel {
    if (categorieLabel == nil) {
        categorieLabel = [[UILabel alloc] initWithFrame:CGRectMake(30+STATIC_EDITIONSIMAGEVIEW_WIDTH+10,
                                                                   90,
                                                                   self.bounds.size.width - 40 - STATIC_EDITIONSIMAGEVIEW_WIDTH,
                                                                   20)];
        categorieLabel.textColor = [UIColor colorWithWhite:0.2 alpha:1];
        categorieLabel.font = [UIFont fontWithName:@"Helvetica" size:16];
    }
    return categorieLabel;
}

-(UIButton *)actionButton {
    if (actionButton == nil) {
        actionButton = [UIButton buttonWithType:UIButtonTypeCustom];
        actionButton.frame = self.bounds;
    }
    return actionButton;
}

-(UILabel *)prixLabel {
    if (prixLabel == nil) {
        
        prixLabel = [[UILabel alloc] initWithFrame:CGRectMake(self.frame.size.width - 100, self.frame.size.height - 60, 80, 30)];
        prixLabel.font = [UIFont fontWithName:@"Arial" size:16];
        prixLabel.textColor = [UIColor colorWithWhite:0 alpha:0.9];
        prixLabel.textAlignment = NSTextAlignmentCenter;
        
        prixLabel.backgroundColor = [UIColor colorWithRed:0.1960f green:0.2f blue:0.2156f alpha:0.1];
        [prixLabel.layer setCornerRadius:10];
        [prixLabel.layer setBorderWidth:2];
        [prixLabel.layer setBorderColor:[UIColor colorWithRed:0.2196 green:0.8196 blue:0.3373 alpha:0.5].CGColor];
        
    }
    return prixLabel;
}

-(UIButton *)prixButton {
    if (prixButton == nil) {
        
        prixButton = [UIButton buttonWithType:UIButtonTypeCustom];
        prixButton.frame = prixLabel.frame;

        prixButton.backgroundColor = [UIColor clearColor];
        
        [prixButton addTarget:self action:@selector(touchDownButton) forControlEvents:UIControlEventTouchDown];
        [prixButton addTarget:self action:@selector(touchUpButton) forControlEvents:UIControlEventTouchUpInside];
        [prixButton addTarget:self action:@selector(touchUpButton) forControlEvents:UIControlEventTouchUpOutside];
        
    }
    return prixButton;
}

-(void)touchDownButton {
    prixLabel.backgroundColor = [UIColor colorWithRed:0.1960f green:0.2f blue:0.2156f alpha:0.6];
}

-(void)touchUpButton {
    prixLabel.backgroundColor = [UIColor colorWithRed:0.1960f green:0.2f blue:0.2156f alpha:0.1];
}
*/

-(void)setData:(NSDictionary*)data {
    
    [self setDataDictionary:data];
    if ([[data valueForKey:@"isSubscription"] intValue] == 1)
    {
        self.bannerImageView.hidden = NO;
    }
    else
    {
        self.bannerImageView.hidden = YES;

    }
    
    [self.coverImageView setUrl:[NSURL URLWithString:[data valueForKey:@"coverPath"]]];
    [self.coverImageView startDownload];
    
    [self.titleLabel setText:[data valueForKey:@"nom"]];
    
    /*
    [self.titleLabel setText:[data valueForKey:@"nom"]];
    [self.categorieLabel setText:[data valueForKey:@"categorie"]];
    [self.dateLabel setText:[data valueForKey:@"datePublication"]];
    [self.prixLabel setText:[NSString stringWithFormat:@"%@$",[data valueForKey:@"prix"]]];
    
    managedObjectContext = [[NSManagedObjectContext alloc] init];
    [managedObjectContext setUndoManager:nil];
    [managedObjectContext setPersistentStoreCoordinator:[(AppDelegate*)[[UIApplication sharedApplication] delegate] persistentStoreCoordinator]];
    
    NSFetchRequest *request = [[NSFetchRequest alloc] init];
    [request setEntity:[NSEntityDescription entityForName:@"Editions" inManagedObjectContext:managedObjectContext]];
    
    NSError *error = nil;
    
    
    NSPredicate *predicate = [NSPredicate predicateWithFormat:@"id == %d", [[data valueForKey:@"id"] intValue]];
    [request setPredicate:predicate];
    
    NSArray *results = [managedObjectContext executeFetchRequest:request error:&error];
    if ([results count] != 0) {
        [self.prixLabel setText:[NSString stringWithFormat:@"Ouvrir"]];
    }
    */
    //Editions *tempEdition = [results objectAtIndex:0];
}

-(void)setMemeEditeurData:(NSDictionary*)data {
    [self setDataDictionary:data];
    
    [self.coverImageView setUrl:[NSURL URLWithString:[data valueForKey:@"coverPath"]]];
    [self.coverImageView startDownload];
    
    [self.titleLabel setText:[data valueForKey:@"datePublication"]];
}

-(void)setEditionsData:(Editions*)data {
    [self setEdition:data];
    
    [self.coverImageView setUrl:[NSURL URLWithString:[data coverpath]]];
    [self.coverImageView startDownload];
    
    
    NSDateFormatter *dateFormatter = [[NSDateFormatter alloc] init];
    [dateFormatter setDateFormat:@"yyyy-MM-dd"];
    NSDate *tempDate = [self.edition publicationdate];
    [dateFormatter setDateFormat:@"d"];
    
    NSString *dateString = [dateFormatter stringFromDate:tempDate];
    [dateFormatter setDateFormat:@"MMMM"];
    dateString = [dateString stringByAppendingFormat:@" %@ ", [self convertMonthStringToFR:[dateFormatter stringFromDate:tempDate]]];
    [dateFormatter setDateFormat:@"yyyy"];
    dateString = [dateString stringByAppendingFormat:@"%@",[dateFormatter stringFromDate:tempDate]];
    
    [self.titleLabel setText:dateString];
    
    if ([[data favoris] boolValue]) {
        [self.coverImageView showFav];
    }
}

-(void)setArchivesData:(NSDictionary*)data {
    [self setDataDictionary:data];
    
    [self.coverImageView setUrl:[NSURL URLWithString:[data valueForKey:@"coverPath"]]];
    [self.coverImageView startDownload];
    
    NSDateFormatter *dateFormatter = [[NSDateFormatter alloc] init];
    [dateFormatter setDateFormat:@"yyyy-MM-dd"];
    NSDate *tempDate = [dateFormatter dateFromString:[data valueForKey:@"datePublication"]];
    
    [dateFormatter setDateFormat:@"EEEE"];
    
    NSString *dateString = [self convertDayStringToFR:[dateFormatter stringFromDate:tempDate]];
    
    [dateFormatter setDateFormat:@"dd"];
    
    dateString = [dateString stringByAppendingFormat:@" %@",[dateFormatter stringFromDate:tempDate]];
    
    [self.titleLabel setText:dateString];
}

-(NSString*)convertDayStringToFR:(NSString*)enDayString {
    NSString *frString;
    
    if ([enDayString isEqualToString:@"Monday"]) {
        frString = @"Lundi";
    }
    else if ([enDayString isEqualToString:@"Tuesday"]) {
        frString = @"Mardi";
    }
    else if ([enDayString isEqualToString:@"Wednesday"]) {
        frString = @"Mercredi";
    }
    else if ([enDayString isEqualToString:@"Thursday"]) {
        frString = @"Jeudi";
    }
    else if ([enDayString isEqualToString:@"Friday"]) {
        frString = @"Vendredi";
    }
    else if ([enDayString isEqualToString:@"Saturday"]) {
        frString = @"Samedi";
    }
    else if ([enDayString isEqualToString:@"Sunday"]) {
        frString = @"Dimanche";
    }
    else {
        frString = enDayString;
    }
    
    return frString;
}

-(NSString*)convertMonthStringToFR:(NSString*)enMonthString {
    NSString *frString;
    
    if ([enMonthString isEqualToString:@"January"]) {
        frString = @"Janvier";
    }
    else if ([enMonthString isEqualToString:@"February"]) {
        frString = @"Février";
    }
    else if ([enMonthString isEqualToString:@"March"]) {
        frString = @"Mars";
    }
    else if ([enMonthString isEqualToString:@"April"]) {
        frString = @"Avril";
    }
    else if ([enMonthString isEqualToString:@"May"]) {
        frString = @"Mai";
    }
    else if ([enMonthString isEqualToString:@"June"]) {
        frString = @"Juin";
    }
    else if ([enMonthString isEqualToString:@"July"]) {
        frString = @"Juillet";
    }
    else if ([enMonthString isEqualToString:@"August"]) {
        frString = @"Août";
    }
    else if ([enMonthString isEqualToString:@"September"]) {
        frString = @"Septembre";
    }
    else if ([enMonthString isEqualToString:@"October"]) {
        frString = @"Octobre";
    }
    else if ([enMonthString isEqualToString:@"November"]) {
        frString = @"Novembre";
    }
    else if ([enMonthString isEqualToString:@"December"]) {
        frString = @"Décembre";
    }
    else {
        frString = enMonthString;
    }
    
    return frString;
}

@end
