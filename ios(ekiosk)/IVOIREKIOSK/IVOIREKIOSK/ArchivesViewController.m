//
//  ArchivesViewController.m
//  IVOIREKIOSK
//
//  Created by Maxime Julien-Paquet on 2014-01-15.
//  Copyright (c) 2014 Maxime Julien-Paquet. All rights reserved.
//

#import "ArchivesViewController.h"
#import "ArchivesMonthViewController.h"


@interface ArchivesViewController ()

@end

@implementation ArchivesViewController

@synthesize archivesMonthViewController, nextArchivesMonthViewController, idJournal, navbar, nameJournal, currentCreditLabel;

-(id)initWithNibName:(NSString *)nibNameOrNil bundle:(NSBundle *)nibBundleOrNil {
    self = [super initWithNibName:nibNameOrNil bundle:nibBundleOrNil];
    if (self) {
        // Custom initialization
    }
    return self;
}

-(id)initWithIdJournal:(NSString*)idjournal AndName:(NSString*)name {
    self = [super init];
    if (self) {
        self.idJournal = idjournal;
        self.nameJournal = name;
    }
    return self;
}

-(void)viewDidLoad {
    [super viewDidLoad];
	// Do any additional setup after loading the view.
    
    self.title = self.nameJournal;
    
    UIImageView *bg;
    
    bg = [[UIImageView alloc] initWithFrame:self.view.bounds];
    bg.autoresizingMask = UIViewAutoresizingFlexibleHeight | UIViewAutoresizingFlexibleWidth;
    bg.backgroundColor = [UIColor whiteColor];
    bg.alpha = 0.3;
    [self.view addSubview:bg];
    [self.view sendSubviewToBack:bg];
    
    bg = [[UIImageView alloc] initWithFrame:self.view.bounds];
    bg.autoresizingMask = UIViewAutoresizingFlexibleWidth | UIViewAutoresizingFlexibleTopMargin | UIViewAutoresizingFlexibleBottomMargin;
    [bg setImage:[UIImage imageNamed:@"bg-street.jpg"]];
    [self.view addSubview:bg];
    [self.view sendSubviewToBack:bg];
    
    currentMonth = 0;
    
    UISwipeGestureRecognizer *swipeLeftGestureRecognizer = [[UISwipeGestureRecognizer alloc] initWithTarget:self action:@selector(switchToLeft)];
    swipeLeftGestureRecognizer.numberOfTouchesRequired = 1;
    swipeLeftGestureRecognizer.direction = UISwipeGestureRecognizerDirectionLeft;
    [self.view addGestureRecognizer:swipeLeftGestureRecognizer];
    
    UISwipeGestureRecognizer *swipeRightGestureRecognizer = [[UISwipeGestureRecognizer alloc] initWithTarget:self action:@selector(switchToRight)];
    swipeRightGestureRecognizer.numberOfTouchesRequired = 1;
    swipeRightGestureRecognizer.direction = UISwipeGestureRecognizerDirectionRight;
    [self.view addGestureRecognizer:swipeRightGestureRecognizer];
    
    
    archivesMonthViewController = [[ArchivesMonthViewController alloc] initWithIdJournal:self.idJournal AndDate:[NSString stringWithFormat:@"%d",currentMonth]];
    [self addChildViewController:archivesMonthViewController];
    [self.view addSubview:archivesMonthViewController.view];
    
    
    
    
    
    
    
    
    NSDateFormatter *dateFormatter = [[NSDateFormatter alloc] init];
    [dateFormatter setDateFormat:@"yyyy MMMM"];
    //NSLog(@"%@", [dateFormatter stringFromDate:[NSDate new]]);
    
    NSDateComponents *componentsToSubtract = [[NSDateComponents alloc] init];
    
    //NSDateComponents *componentsToSubtract = [[NSDateComponents alloc] init];
    //[componentsToSubtract setMonth:-1];
    
    //NSDate *yesterday = [[NSCalendar currentCalendar] dateByAddingComponents:componentsToSubtract toDate:[NSDate date] options:0];
    //NSLog(@"%@", [dateFormatter stringFromDate:yesterday]);
    
    NSMutableArray *array = [[NSMutableArray alloc] init];
    for (int x = 0; x < 12; ++x) {
        [componentsToSubtract setMonth:-x];
        [dateFormatter setDateFormat:@"yyyy"];
        NSString *tempString = [dateFormatter stringFromDate:[[NSCalendar currentCalendar] dateByAddingComponents:componentsToSubtract toDate:[NSDate date] options:0]];
        [dateFormatter setDateFormat:@"MMMM"];
        tempString = [tempString stringByAppendingFormat:@" %@",[self convertMonthStringToFR:[dateFormatter stringFromDate:[[NSCalendar currentCalendar] dateByAddingComponents:componentsToSubtract toDate:[NSDate date] options:0]]]];
        
        [array addObject:tempString];
    }
    NSLog(@"%@",array);
    
    navbar = [[ArchivesNavBarView alloc] initWithFrame:CGRectMake(0, 64, self.view.frame.size.width, 44)];
    [navbar setDelegate:self];
    [navbar setMonthArray:array];
    [navbar setcurrentmonth:currentMonth];
    [navbar setup];
    [self.view addSubview:navbar];
    
    UIBarButtonItem *temp = [[UIBarButtonItem alloc] initWithCustomView:[self currentCreditLabel]];
    [self.navigationItem setRightBarButtonItem:temp];
}

-(void)didReceiveMemoryWarning {
    [super didReceiveMemoryWarning];
    // Dispose of any resources that can be recreated.
}

-(MiniVCLabel *)currentCreditLabel {
    if (currentCreditLabel == nil) {
        if (isPad()) {
            currentCreditLabel = [[MiniVCLabel alloc] initWithFrame:CGRectMake(self.view.frame.size.width-220, 2, 200, 40)];
        }
        else {
            currentCreditLabel = [[MiniVCLabel alloc] initWithFrame:CGRectMake(self.view.frame.size.width-120, 2, 100, 40)];
        }
        
    }
    return currentCreditLabel;
}

-(void)viewWillAppear:(BOOL)animated {
    [super viewWillAppear:animated];
    NSUserDefaults *defaults = [NSUserDefaults standardUserDefaults];
    int current = [[defaults valueForKey:@"ekcredit"] intValue];
    [self.currentCreditLabel.prixLabel setText:[NSString stringWithFormat:@"%d",current]];
}

-(void)switchToLeft {
    if (currentMonth < 11) {
        ++currentMonth;
        
        [self animationLeft];
        [self.navbar animationLeft];
        
    }
}

-(void)switchToRight {
    if (currentMonth > 0) {
        --currentMonth;
        
        [self animationRight];
        [self.navbar animationRight];
        
    }
}

-(void)animationLeft {
    nextArchivesMonthViewController = [[ArchivesMonthViewController alloc] initWithIdJournal:self.idJournal AndDate:[NSString stringWithFormat:@"%d",currentMonth]];
    nextArchivesMonthViewController.view.frame = CGRectMake(self.view.frame.size.width, 0, self.view.frame.size.width, self.view.frame.size.height);
    [self addChildViewController:nextArchivesMonthViewController];
    [self.view addSubview:nextArchivesMonthViewController.view];
    
    
    [UIView animateWithDuration:0.4
                     animations:^{
                         archivesMonthViewController.view.frame = CGRectMake(-self.view.frame.size.width, 0, self.view.frame.size.width, self.view.frame.size.height);
                         nextArchivesMonthViewController.view.frame = CGRectMake(0, 0, self.view.frame.size.width, self.view.frame.size.height);
                     }
                     completion:^(BOOL finished){
                         [self.archivesMonthViewController.view removeFromSuperview];
                         [self.archivesMonthViewController removeFromParentViewController];
                         [self setArchivesMonthViewController:self.nextArchivesMonthViewController];
                         //[self.navbar setCurrentMonth:currentMonth];
                         [self.view bringSubviewToFront:navbar];
                     }];
}

-(void)animationRight {
    nextArchivesMonthViewController = [[ArchivesMonthViewController alloc] initWithIdJournal:self.idJournal AndDate:[NSString stringWithFormat:@"%d",currentMonth]];
    nextArchivesMonthViewController.view.frame = CGRectMake(-self.view.frame.size.width, 0, self.view.frame.size.width, self.view.frame.size.height);
    [self addChildViewController:nextArchivesMonthViewController];
    [self.view addSubview:nextArchivesMonthViewController.view];
    
    
    [UIView animateWithDuration:0.4
                     animations:^{
                         archivesMonthViewController.view.frame = CGRectMake(self.view.frame.size.width, 0, self.view.frame.size.width, self.view.frame.size.height);
                         nextArchivesMonthViewController.view.frame = CGRectMake(0, 0, self.view.frame.size.width, self.view.frame.size.height);
                     }
                     completion:^(BOOL finished){
                         [self.archivesMonthViewController.view removeFromSuperview];
                         [self.archivesMonthViewController removeFromParentViewController];
                         [self setArchivesMonthViewController:self.nextArchivesMonthViewController];
                         //[self.navbar setCurrentMonth:currentMonth];
                         [self.view bringSubviewToFront:navbar];
                     }];
    
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

#pragma mark - navbardelegate

-(void)leftGetTouched {
    [self switchToLeft];
}

-(void)rightGetTouched {
    [self switchToRight];
}

@end
