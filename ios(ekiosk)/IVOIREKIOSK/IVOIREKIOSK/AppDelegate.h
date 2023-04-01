//
//  AppDelegate.h
//  IVOIREKIOSK
//
//  Created by Maxime Julien-Paquet on 2013-12-03.
//  Copyright (c) 2013 Maxime Julien-Paquet. All rights reserved.
//

#import <UIKit/UIKit.h>
//#import "MAJJournal.h"


@interface AppDelegate : UIResponder <UIApplicationDelegate, UIActionSheetDelegate> {
    NSManagedObjectContext *managedObjectContext;
    NSPersistentStoreCoordinator *persistentStoreCoordinator;
    NSString *persistentStorePath;
    
    //MAJJournal *majJournal;
    
}

@property (strong, nonatomic) UIWindow *window;



@property (nonatomic, strong) NSManagedObjectContext *managedObjectContext;
@property (nonatomic, strong) NSPersistentStoreCoordinator *persistentStoreCoordinator;
@property (nonatomic, strong, readonly) NSString *persistentStorePath;

@property (nonatomic, strong, readonly) NSOperationQueue *operationQueue;
//@property (nonatomic, strong) MAJJournal *majJournal;

@end
