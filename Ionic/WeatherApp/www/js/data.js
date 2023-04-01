var LocalData = (function () {
  "use strict";
  return {
    GetWorkoutTypes: (function () {
      var workoutData = {
        fullBody : { id: 0, activityWeight: 7, activityMFP: "134026252709869", activityNames: "FULL", icon: "fullBody.png", exercises: false, description: "FULL_DESC"},
        upperBody : { id: 1, activityWeight: 6, activityMFP: "134026252709869", activityNames: "UPPER", icon: "upperBody.png", exercises: ["Push-ups" , "Overhead Press" , "Diamond Push-ups" , "Overhead Arm Clap", "Wide Arm Push-ups" , "Tricep Dips" , "Alternating Push-up Plank", "Wall Push-ups", "One Arm Side Push-up", "Jumping Jacks" , "Dive Bomber Push-ups", "Chest Expander", "Shoulder Tap Push-ups", "T Raise", "Spiderman Push-up", "Lying Triceps Lifts", "Push-up and Rotation", "Power Circles", "Reverse Plank"], description: "UPPER_DESC"},
        coreExercise : { id: 2, activityWeight: 6, activityMFP: "134026252709869", activityNames: "CORE", icon: "coreExercise.png", exercises: ["Sit-up" , "V Sit-up" , "Elevated Crunches" , "Leg Spreaders" , "Leg Lifts" , "Supine Bicycle" , "Plank" , "Burpees" , "Twisting Crunches", "Inch Worms", "Supermans", "Windmill", "Bent Leg Twist", "Side Bridge", "Quadraplex", "Swimmer", "Mason Twist", "Steam Engine", "In and Out Abs", "Six Inch and Hold", "Scissor Kicks"], description: "CORE_DESC"},
        lowerBody : { id: 3, activityWeight: 6, activityMFP: "134026252709869", activityNames: "LOWER", icon: "lowerBody.png", exercises: ["Squats" , "Jump Squats", "Forward Lunges" , "Rear Lunges" , "Mountain Climbers" , "Front Kicks" , "Running in Place" , "Side Leg Lifts" , "Single Leg Squats", "Reverse V Lunges", "Hip Raise", "High Jumper", "Side to Side Knee Lifts", "Frog Jumps", "Calf Raises", "Genie Sit", "High Knees", "Butt Kickers", "Wall Sit"], description: "LOWER_DESC"},
        stretchExercise : { id: 4, activityWeight: 4, activityMFP: "133478623407981", activityNames: "STRETCH", icon: "stretchExercise.png", exercises: ["Quadricep Stretch" , "Hamstring Stretch Standing" , "Kneeling Hip Flexor", "Overhead Arm Pull" , "Chest Stretch" , "Abdominal Stretch" , "Side Stretch" , "Butterfly Stretch" , "Seated Hamstring Stretch" , "Calf Stretch" , "Neck Stretch" , "Lower Back Stretch", "Bending Windmill Stretch", "Bend and Reach", "Arm and Shoulder Stretch", "Shoulder Shrugs", "Hurdlers Stretch", "Ankle on the Knee", "Arm Circles", "Knee to Chest Stretch", "Single Leg Hamstring"], description: "FULL_STRETCH_DESC"},
        backStrength : { id: 5, activityWeight: 5, activityMFP: "133476496895469", activityNames: "BACK_STRENGTH", icon: "backStrength.png", exercises: ["Hip Raise", "Quadraplex", "Side Plank", "Forward Lunges", "Plank", "Lower Back Stretch", "Laying Spinal Twist", "Kneeling Hip Flexor", "Side Stretch", "Genie Sit"], description: "BACK_DESC"},
        anythingGoes : { id: 6, activityWeight: 5, activityMFP: "134026252709869", activityNames: "ANYTHING", icon: "anythingGoes.png", exercises: false, description: "ANYTHING_DESC"},
        sunSalutation : { id: 7, activityWeight: 5, activityMFP: "133751232154941", activityNames: "SUN_SALUTATION", icon: "sunSalutation.png", exercises: ["Prayer Pose","Raised Arms Pose","Forward Fold","Low Lunge (Left Forward)","Downward Dog","Plank Pose","Four Limbs Pose","Cobra Pose","Downward Dog","Low Lunge (Right Forward)","Forward Fold","Raised Arms Pose","Prayer Pose","Raised Arms Pose","Forward Fold","Low Lunge (Right Forward)","Downward Dog","Plank Pose","Four Limbs Pose","Cobra Pose","Downward Dog","Low Lunge (Left Forward)","Forward Fold","Raised Arms Pose"], description: "SUN_DESC"},
        fullSequence : { id: 8, activityWeight: 4, activityMFP: "133751232154941", activityNames: "FULL_SEQ", icon: "fullSequence.png", exercises: ["Mountain Pose","Raised Arms Pose","Side Bend Left","Side Bend Right","Forward Fold", "Forward Fold Hands Behind", "Chair Pose", "Chair Pose Twist Left", "Chair Pose Twist Right", "Forward Fold", "Mountain Pose", "Wide Leg Stance", "Wide Leg Stance Arms Up", "Wide Leg Forward Fold", "Wide Leg Stance", "Triangle Left", "Wide Leg Stance", "Triangle Right", "Wide Leg Stance", "Warrior II (Left Forward)", "Side Angle Left", "Wide Leg Stance", "Warrior II (Right Forward)", "Side Angle Right", "Wide Leg Stance", "Mountain Pose", "Forward Fold", "Low Lunge (Left Forward)", "Plank Pose", "Four Limbs Pose", "Cobra Pose", "Downward Dog", "Low Lunge (Right Forward)", "Forward Fold", "Mountain Pose", "Tree Pose Left", "Tree Pose Right", "Head to Knee Left", "Head to Knee Right", "Twist Left", "Twist Right", "Lay on Back", "Prep for Shoulder Stand", "Plow", "Shoulder Stand", "Lay on Back", "Fish Pose", "Lay on Back", "Lay on Back", "Lay on Back", "Lay on Back"], description: "SEQ_DESC"},
        bootCamp : { id: 9, activityWeight: 7, activityMFP: "134026252709869", activityNames: "BOOT_CAMP", icon: "bootCamp.png", exercises: ["Push-ups" , "Overhead Press" , "Overhead Arm Clap" , "Jumping Jacks", "Sit-up", "Leg Spreaders" , "Supine Bicycle" , "Windmill" , "Squats" , "Mountain Climbers" , "High Jumper" , "Plank"  , "Front Kicks", "Star Jumps", "Steam Engine", "Diamond Push-ups", "Dive Bomber Push-ups", "Six Inch and Hold", "Swimmer", "Star Jumps", "Squat Jacks"], description: "BOOT_DESC"},
        rumpRoaster : { id: 10, activityWeight: 6, activityMFP: "134026252709869", activityNames: "RUMP", icon: "rumpRoaster.png", exercises: ["Leg Spreaders" , "Leg Lifts" , "Squats" , "Mountain Climbers" , "Hip Raise" , "Quadraplex"  , "Bent Leg Twist", "Side Bridge" , "Forward Lunges" , "Rear Lunges", "Kneeling Hip Flexor", "Side Leg Lifts", "Side to Side Knee Lifts", "High Knees", "Squat Jacks"], description:"RUMP_DESC"},
        cardio : { id: 11, activityWeight: 8, activityMFP: "134026252709869", activityNames: "CARDIO_FULL", icon: "cardio.png", exercises: ["Fast Feet", "Step Touch", "Power Skip", "High Knees", "Butt Kickers", "Jump Rope Hops", "Side Hops", "Pivoting Upper Cuts", "Squat Jabs", "Skaters", "Single Leg Hops", "Switch Kick", "Jumping Planks", "Star Jumps", "Running in Place", "Jumping Jacks", "Front Kicks", "Windmill", "Sprinter", "Power Jump",  "Single Lateral Hops", "Shoulder Tap Push-ups",  "Squat Jacks", "Lunge Jumps", "Up Downs", "Burpees", "Mountain Climbers"], description:"CARDIO_FULL_DESC"},
        bringThePain : { id: 12, activityWeight: 8, activityMFP: "134026252709869", activityNames: "BRING_PAIN", icon: "bringThePain.png", exercises: ["Push-ups", "Alternating Push-up Plank", "Tricep Dips", "Dive Bomber Push-ups", "Supine Bicycle", "Burpees", "Spiderman Push-up", "Steam Engine","Six Inch and Hold", "Jump Squats", "Mountain Climbers", "Pivoting Upper Cuts", "Squat Jabs","Sprinter","Power Jump","Up Downs","Shoulder Tap Push-ups", "Lunge Jumps", "Squat Jacks", "Leg Spreaders", "Fast Feet", "Switch Kick"], description: "BRING_DESC"},
        customWorkout : { id: 13, activityWeight: 6, activityMFP: "134026252709869", activityNames: "CUSTOM_SM", icon: "fullBody.png", exercises: false, description:"CUSTOM_DESC"},
        headToToe : { id: 14, activityWeight: 4, activityMFP: "133478623407981", activityNames: "HEAD_TOE", icon: "headToToe.png", exercises: ["Neck Stretch", "Arm and Shoulder Stretch", "Overhead Arm Pull", "Abdominal Stretch", "Chest Stretch", "Quadricep Stretch" , "Hamstring Stretch Standing", "Calf Stretch", "Butterfly Stretch", "Seated Hamstring Stretch" , "Kneeling Hip Flexor"  , "Lower Back Stretch", "Ankle on the Knee"], description:"HEAD_DESC"},
        cardioLight : { id: 15, activityWeight: 5, activityMFP: "133476505251693", activityNames: "CARDIO_LIGHT", icon: "cardioLight.png", exercises: ["Step Touch", "High Knees", "Butt Kickers", "Jump Rope Hops", "Single Leg Hops", "Running in Place", "Jumping Jacks", "Front Kicks", "Windmill", "Bend and Reach", "Calf Raises", "Arm Circles", "Side Hops"], description:"CARDIO_LIGHT_DESC"},
        sevenMinute : { id: 16, activityWeight: 7, activityMFP: "134026252709869", activityNames: "SEVEN_MINUTE", icon: "sevenMinute.png", exercises: ["Jumping Jacks", "Wall Sit", "Push-ups", "Abdominal Crunch", "Step Ups", "Squats", "Tricep Dips", "Plank", "High Knees", "Lunge", "Push-up and Rotation", "Side Plank"], description:"SEVEN_MIN_DESC"},
        standingStretches : { id: 17, activityWeight: 4, activityMFP: "133478623407981", activityNames: "STANDING", icon: "standingStretches.png", exercises: ["Quadricep Stretch" , "Hamstring Stretch Standing" , "Overhead Arm Pull" , "Chest Stretch" , "Abdominal Stretch" , "Side Stretch" , "Calf Stretch" , "Neck Stretch" , "Arm and Shoulder Stretch", "Shoulder Shrugs", "Arm Circles"], description:"STANDING_DESC"},
        pilatesWorkout : { id: 18, activityWeight: 6, activityMFP: "133201476341181", activityNames: "PILATES", icon: "pilatesWorkout.png", exercises: ["Swan", "Double Leg Stretch", "Spine Stretch Forward", "Seated Spine Twist", "Leg Pull Front", "Leg Pull Back", "The Hundred", "Rollover", "Back Arm Rowing", "Swimming", "Double Leg Kick", "Laying Side Kick", "Teaser", "Wag Your Tail", "Corkscrew", "Roll Up", "One Leg Circles"], description:"PILATES_DESC"},
        quickFive : {id: 19, activityWeight: 7, activityMFP: "134026252709869", activityNames: "QUICK", icon: "fullBody.png", exercises: ["Alternating Push-up Plank", "Wall Push-ups", "Jumping Jacks", "Supine Bicycle", "Plank", "Squats", "Forward Lunges", "Mountain Climbers", "Running in Place", "Windmill", "Bent Leg Twist", "Squat Jacks", "Up Downs", "One Arm Side Push-up", "Calf Raises", "Mason Twist", "Steam Engine", "Seated Spine Twist", "Swimming"], description:"QUICK"},
        plyometrics : {id: 20, activityWeight: 8, activityMFP: "133476505251693", activityNames: "PLYOMETRICS", icon: "plyometrics.png", exercises: ["Jumping Jacks" , "Burpees" , "Jump Squats" , "Mountain Climbers" , "High Jumper" , "Frog Jumps" , "Power Skip" , "Jump Rope Hops" , "Side Hops" , "Skaters" , "Single Leg Hops" , "Switch Kick" , "Jumping Planks" , "Star Jumps" , "Sprinter" , "Power Jump" , "Squat Jacks" , "Lunge Jumps" , "Up Downs"], description:"PLYOMETRICS_DESC"},
        runnerYoga : { id: 21, activityWeight: 5, activityMFP: "133751232154941", activityNames: "RUNNER_YOGA", icon: "runnerYoga.png", exercises: ["Mountain Pose","Raised Arms Pose","Forward Fold","Low Lunge (Left Forward)","Warrior II (Left Forward)","Triangle Left","Low Lunge (Left Forward)","Downward Dog","Child Pose","Head to Knee Left","Butterfly Stretch","Head to Knee Right","Child Pose","Downward Dog","Forward Fold","Mountain Pose","Raised Arms Pose","Forward Fold","Low Lunge (Right Forward)","Warrior II (Right Forward)","Triangle Right","Low Lunge (Right Forward)","Downward Dog","Child Pose","Head to Knee Right","Butterfly Stretch","Head to Knee Left","Child Pose","Downward Dog","Forward Fold","Raised Arms Pose", "Mountain Pose"], description: "RUNNER_DESC"},
      };

      return workoutData;
    }()),
    GetWorkoutCategories: (function () {
      var workoutCategories = [
        {workoutTypes: ["fullBody", "upperBody", "coreExercise", "lowerBody"], fullName: "STRENGTH"},
        {workoutTypes: ["cardioLight", "cardio", "plyometrics", "bootCamp"], fullName: "CARDIO"},
        {workoutTypes: ["sunSalutation", "fullSequence", "runnerYoga", "pilatesWorkout"], fullName: "YOGA"},
        {workoutTypes: ["headToToe", "stretchExercise", "standingStretches", "backStrength"], fullName: "STRETCHING"}
      ];
      return workoutCategories;
    }())
  }
}());

var TimingData = (function () {
  "use strict";
  return {
    GetTimingSettings: (function () {
      var timingData = {
        customSet: false,
        breakFreq: 5,
        exerciseTime: 30,
        breakTime: 30,
        transitionTime: 5,
        transition:true,
        randomizationOption: true,
        workoutLength: 15,
        audioOption: true,
        warningAudio: true,
        countdownBeep: true,
        autoPlay: true,
        countdownStyle: true,
        welcomeAudio: true,
        autoStart: true
      };
      return timingData;
    }()),
    GetSevenMinuteSettings: (function () {
      var timingData = {
        customSetSeven: true,
        breakFreqSeven: 0,
        exerciseTimeSeven: 30,
        breakTimeSeven: 0,
        transitionTimeSeven: 10,
        randomizationOptionSeven: false,
        workoutLengthSeven: 7
      };
      return timingData;
    }())
  }
}());

var PersonalData = (function () {
  "use strict";
  return {
    GetUserSettings: (function () {
      var userData = {
        weight: 150,
        weightType: 0,
        kiipRewards: true,
        mPoints: true,
        mfpStatus: false,
        myFitnessReady: false,
        mfpWeight: false,
        mfpAccessToken: false,
        mfpRefreshToken: false,
        videosDownloaded: false,
        downloadDecision: true,
        healthKit: false,
        lastLength: 5,
        timerTaps: 0,
        showAudioTip: true
      };
      return userData;
    }()),
    GetUserGoals: (function () {
      var userGoals = {
        dailyGoal: 15,
        weeklyGoal: 75
      };
      return userGoals;
    }()),
    GetUserProgress: (function () {
      var userProgress = {
        monthlyTotal: 0,
        weeklyTotal: 0,
        dailyTotal: 0,
        totalCalories: 0,
        totalProgress: 0,
        day: 0,
        week: 0
      };
      return userProgress;
    }()),
    GetCustomWorkouts: (function () {
      var userCustomWorkouts = {
        savedWorkouts: []
      };
      return userCustomWorkouts;
    }()),
    GetWorkoutArray: (function () {
      var userCustomArray = {
        workoutArray: []
      };
      return userCustomArray;
    }()),
    GetLanguageSettings: (function () {
      var userLanguages = {
        EN: true,
        DE: false,
        FR: false,
        ES: false,
        ESLA: false,
        IT: false,
        PT: false,
        HI: false,
        JA: false,
        ZH: false,
        KO: false,
        RU: false,
        TR: false
      };
      return userLanguages;
    }()),
    GetAudioSettings: (function () {
      var backgroundAudio = {
        ignoreDuck: false,
        duckOnce: true,
        duckEverything: false
      };
      return backgroundAudio;
    }()),
    GetGoogleFit: (function () {
      var googleFitData = {
        enabled: false,
        attempted: false
      };
      return googleFitData;
    }())
  }
}());

var LocalHistory = (function () {
  "use strict";
  return {
    getCustomHistory: (function () {
      var lastHomeURL = {
        url: ''
      };
      return lastHomeURL;
    }())
  }
}());

LocalData.SetReminder = {daily: {status:false,time:7,minutes:0}, inactivity: {frequency: 2, status:false,time:7,minutes:0}};

var exerciseObject = {
  "Push-ups":{"name":"PUSH_UPS","image":"Pushup.jpg","audio":"Pushup.mp3","youtube":"esV_0R3vCgM","switchOption":false,"video":"Pushup.mp4","category":"upper","videoTiming":[false,false]},
  "Overhead Press":{"name":"OVERHEADPRESS","image":"OverheadPress.jpg","audio":"OverheadPress.mp3","youtube":"qLNO65idcA4","switchOption":false,"video":"OverheadPress.mp4","category":"upper","videoTiming":[false,false]},
  "Overhead Arm Clap":{"name":"OVERHEADARMCLAP","image":"OverheadArmClap.jpg","audio":"OverheadArmClap.mp3","youtube":"JAY8z66cWBQ","switchOption":false,"video":"OverheadArmClap.mp4","category":"upper","videoTiming":[false,false]},
  "Diamond Push-ups":{"name":"DIAMONDPUSH_UPS","image":"DiamondPushup.jpg","audio":"DiamondPushup.mp3","youtube":"WaZ21WJLMIE","switchOption":false,"video":"DiamondPushup.mp4","category":"upper","videoTiming":[false,false]},
  "Wide Arm Push-ups":{"name":"WIDEARMPUSH_UPS","image":"WideArmPushup.jpg","audio":"WideArmPushup.mp3","youtube":"dVswcADbKvc","switchOption":false,"video":"WideArmPushup.mp4","category":"upper","videoTiming":[false,false]},
  "Alternating Push-up Plank":{"name":"ALTERNATINGPUSH_UPPLANK","image":"PushupToPlank.jpg","audio":"PushupToPlank.mp3","youtube":"XrynicUr9m0","switchOption":false,"video":"PushupToPlank.mp4","category":"upper","videoTiming":[false,false]},
  "Tricep Dips":{"name":"TRICEPDIPS","image":"TricepDips.jpg","audio":"TricepDips.mp3","youtube":"EQGJFWcc7ek","switchOption":false,"video":"TricepDips.mp4","category":"upper","videoTiming":[false,false]},
  "Wall Push-ups":{"name":"WALLPUSH_UPS","image":"WallPushups.jpg","audio":"WallPushups.mp3","youtube":"mbGj2KULJY4","switchOption":false,"video":"WallPushups.mp4","category":"upper","videoTiming":[false,false]},
  "Jumping Jacks":{"name":"JUMPINGJACKS","image":"JumpingJacks.jpg","audio":"JumpingJacks.mp3","youtube":"JRbClSwzGCo","switchOption":false,"video":"JumpingJacks.mp4","category":"upper","videoTiming":[false,false]},
  "Chest Expander":{"name":"CHESTEXPANDER","image":"ChestExpander.jpg","audio":"ChestExpander.mp3","youtube":"seWu6TM8Bjw","switchOption":false,"video":"ChestExpander.mp4","category":"upper","videoTiming":[false,false]},
  "Sit-up":{"name":"SIT_UP","image":"Situps.jpg","audio":"Situps.mp3","youtube":"mOu5pS9LyOo","switchOption":false,"video":"Situps.mp4","category":"core","videoTiming":[false,false]},
  "V Sit-up":{"name":"VSIT_UP","image":"VSitups.jpg","audio":"VSitups.mp3","youtube":"vbD4wCvseSM","switchOption":false,"video":"VSitups.mp4","category":"core","videoTiming":[false,false]},
  "Elevated Crunches":{"name":"ELEVATEDCRUNCHES","image":"Crunches.jpg","audio":"Crunches.mp3","youtube":"a-yY30-DCxk","switchOption":false,"video":"Crunches.mp4","category":"core","videoTiming":[false,false]},
  "Leg Spreaders":{"name":"LEGSPREADERS","image":"LegSpreaders.jpg","audio":"LegSpreaders.mp3","youtube":"GLOOymmFsaA","switchOption":false,"video":"LegSpreaders.mp4","category":"core","videoTiming":[false,false]},
  "Leg Lifts":{"name":"LEGLIFTS","image":"LegLifts.jpg","audio":"LegLifts.mp3","youtube":"tOKQ7fdCITc","switchOption":false,"video":"LegLifts.mp4","category":"core","videoTiming":[false,false]},
  "Supine Bicycle":{"name":"SUPINEBICYCLE","image":"SupineBicycle.jpg","audio":"SupineBicycle.mp3","youtube":"jDrXvpnuJmA","switchOption":false,"video":"SupineBicycle.mp4","category":"core","videoTiming":[false,false]},
  "Plank":{"name":"PLANK","image":"Plank1.jpg","audio":"Plank1.mp3","youtube":"aHPf4sBapq0","switchOption":false,"video":"Plank1.mp4","category":"core","videoTiming":[1200,false]},
  "Burpees":{"name":"BURPEES","image":"Burpees.jpg","audio":"Burpees.mp3","youtube":"DDxlDuvVzN0","switchOption":false,"video":"Burpees.mp4","category":"core","videoTiming":[false,false]},
  "Squats":{"name":"SQUATS","image":"Squats.jpg","audio":"Squats.mp3","youtube":"THNv75tfG6E","switchOption":false,"video":"Squats.mp4","category":"lower","videoTiming":[false,false]},
  "Twisting Crunches":{"name":"TWISTINGCRUNCHES","image":"TwistingCrunches.jpg","audio":"TwistingCrunches.mp3","youtube":"dKppHZA4vVg","switchOption":false,"video":"TwistingCrunches.mp4","category":"core","videoTiming":[false,false]},
  "Wall Sit":{"name":"WALLSIT","image":"WallSit.jpg","audio":"WallSit.mp3","youtube":"OSkn6l01PWM","switchOption":false,"video":"WallSit.mp4","category":"lower","videoTiming":[false,false]},
  "Single Leg Squats":{"name":"SINGLELEGSQUATS","image":"SingleLegSquats.jpg","audio":"SingleLegSquats.mp3","youtube":"y_WNGxB7CUw","switchOption":true,"video":"SingleLegSquats.mp4","category":"lower","videoTiming":[false,false]},
  "Inch Worms":{"name":"INCHWORMS","image":"InchWorms.jpg","audio":"InchWorms.mp3","youtube":"WW6yhvpFTYY","switchOption":false,"video":"InchWorms.mp4","category":"core","videoTiming":[false,false]},
  "Hip Raise":{"name":"HIPRAISE","image":"HipRaises.jpg","audio":"HipRaises.mp3","youtube":"w794tAm_vzM","switchOption":false,"video":"HipRaises.mp4","category":"back","videoTiming":[false,false]},
  "Supermans":{"name":"SUPERMANS","image":"Supermans.jpg","audio":"Supermans.mp3","youtube":"8e2hBvVtjVE","switchOption":false,"video":"Supermans.mp4","category":"core","videoTiming":[false,false]},
  "Spiderman Push-up":{"name":"SPIDERMANPUSH_UP","image":"Spidermans.jpg","audio":"Spidermans.mp3","youtube":"p27oRTihzzk","switchOption":false,"video":"Spidermans.mp4","category":"core","videoTiming":[false,false]},
  "Jump Squats":{"name":"JUMPSQUATS","image":"JumpSquats.jpg","audio":"JumpSquats.mp3","youtube":"Sr_0y4XS8ws","switchOption":false,"video":"JumpSquats.mp4","category":"lower","videoTiming":[false,false]},
  "Forward Lunges":{"name":"FORWARDLUNGES","image":"ForwardLunges.jpg","audio":"ForwardLunges.mp3","youtube":"jSnSKT2g9PY","switchOption":false,"video":"ForwardLunges.mp4","category":"lower","videoTiming":[false,false]},
  "Rear Lunges":{"name":"REARLUNGES","image":"RearLunges.jpg","audio":"RearLunges.mp3","youtube":"XN_BYC1OGz4","switchOption":false,"video":"RearLunges.mp4","category":"lower","videoTiming":[false,false]},
  "Mountain Climbers":{"name":"MOUNTAINCLIMBERS","image":"MountainClimbers.jpg","audio":"MountainClimbers.mp3","youtube":"l8fgZDP1Nks","switchOption":false,"video":"MountainClimbers.mp4","category":"lower","videoTiming":[false,false]},
  "Front Kicks":{"name":"FRONTKICKS","image":"FrontKicks.jpg","audio":"FrontKicks.mp3","youtube":"2XIndSFscFs","switchOption":false,"video":"FrontKicks.mp4","category":"lower","videoTiming":[false,false]},
  "Running in Place":{"name":"RUNNINGINPLACE","image":"RunningInPlace.jpg","audio":"RunningInPlace.mp3","youtube":"REVE_Pt96-4","switchOption":false,"video":"RunningInPlace.mp4","category":"lower","videoTiming":[false,false]},
  "Side Leg Lifts":{"name":"SIDELEGLIFTS","image":"SideLegLifts.jpg","audio":"SideLegLifts.mp3","youtube":"4N_K0PvWUQE","switchOption":true,"video":"SideLegLifts.mp4","category":"lower","videoTiming":[false,false]},
  "Reverse V Lunges":{"name":"REVERSEVLUNGES","image":"ReverseVLunges.jpg","audio":"ReverseVLunges.mp3","youtube":"RPfqyE_BMPc","switchOption":false,"video":"ReverseVLunges.mp4","category":"lower","videoTiming":[false,false]},
  "Quadricep Stretch":{"name":"QUADRICEPSTRETCH","image":"Quadricep.jpg","audio":"Quadricep.mp3","youtube":"Y-GvduytBmg","switchOption":true,"video":"Quadricep.mp4","category":"stretch","videoTiming":[1500, 4750]},
  "Hamstring Stretch Standing":{"name":"HAMSTRINGSTRETCHSTANDING","image":"HamstringStanding.jpg","audio":"HamstringStanding.mp3","youtube":"QdYDpDoYau4","switchOption":false,"video":"HamstringStanding.mp4","category":"stretch","videoTiming":[2400, false]},
  "Hip Flexor Stretch":{"name":"HIPFLEXORSTRETCH","image":"HipFlexor.jpg","audio":"HipFlexor.mp3","youtube":"3MKV1Ht-3VU","switchOption":true,"video":"HipFlexor.mp4","category":"stretch","videoTiming":[1200, 3500]},
  "Overhead Arm Pull":{"name":"OVERHEADARMPULL","image":"OverheadArmPull.jpg","audio":"OverheadArmPull.mp3","youtube":"2AvkRSY9Iw8","switchOption":true,"video":"OverheadArmPull.mp4","category":"stretch","videoTiming":[1800,4400]},
  "Chest Stretch":{"name":"CHESTSTRETCH","image":"Chest.jpg","audio":"Chest.mp3","youtube":"Tsr1b7szjek","switchOption":false,"video":"Chest.mp4","category":"stretch","videoTiming":[3000,false]},
  "Abdominal Stretch":{"name":"ABDOMINALSTRETCH","image":"Abdominal.jpg","audio":"Abdominal.mp3","youtube":"GFbMWt8uuHE","switchOption":false,"video":"Abdominal.mp4","category":"stretch","videoTiming":[3000,false]},
  "Side Stretch":{"name":"SIDESTRETCH","image":"SideStretch.jpg","audio":"SideStretch.mp3","youtube":"1Fp5vykrOZU","switchOption":true,"video":"SideStretch.mp4","category":"stretch","videoTiming":[1800,3800]},
  "Butterfly Stretch":{"name":"BUTTERFLYSTRETCH","image":"Butterfly.jpg","audio":"Butterfly.mp3","youtube":"UZ1zS_oQdvE","switchOption":false,"video":"Butterfly.mp4","category":"stretch","videoTiming":[false,false]},
  "Seated Hamstring Stretch":{"name":"SEATEDHAMSTRINGSTRETCH","image":"HamstringSeated.jpg","audio":"HamstringSeated.mp3","youtube":"KjRxrCNDDXY","switchOption":false,"video":"HamstringSeated.mp4","category":"stretch","videoTiming":[1200,false]},
  "Calf Stretch":{"name":"CALFSTRETCH","image":"Calf.jpg","audio":"Calf.mp3","youtube":"cxmp-YC_lwA","switchOption":true,"video":"Calf.mp4","category":"stretch","videoTiming":[2600,5600]},
  "Neck Stretch":{"name":"NECKSTRETCH","image":"Neck.jpg","audio":"Neck.mp3","youtube":"Ci-D9SwX02I","switchOption":true,"video":"Neck.mp4","category":"stretch","videoTiming":[4200,6200]},
  "Lower Back Stretch":{"name":"LOWERBACKSTRETCH","image":"LowerBack.jpg","audio":"LowerBack.mp3","youtube":"v6OVxFpFIYY","switchOption":true,"video":"LowerBack.mp4","category":"stretch","videoTiming":[2400, 6450]},
  "Bending Windmill Stretch":{"name":"BENDINGWINDMILLSTRETCH","image":"BendingWindmill.jpg","audio":"BendingWindmill.mp3","youtube":"1drPTxOQqmA","switchOption":true,"video":"BendingWindmill.mp4","category":"stretch","videoTiming":[1800,3600]},
  "Standing Forward Bend":{"name":"STANDINGFORWARDBEND","image":"ForwardFold.jpg","audio":"ForwardFold.mp3","youtube":"bVZFts7QPIA","switchOption":false,"video":"ForwardFold.mp4","category":"yoga","videoTiming":[false,false]},
  "Lunge Pose":{"name":"LUNGEPOSE","image":"LowLunge.jpg","audio":"LowLunge.mp3","youtube":"3vPuSVKIAWk","switchOption":true,"video":"LowLunge.mp4","category":"yoga","videoTiming":[false,false]},
  "Plank Pose":{"name":"PLANKPOSE","image":"Plank.jpg","audio":"Plank.mp3","youtube":"FEFYp1ESL3U","switchOption":false,"video":"Plank.mp4","category":"yoga","videoTiming":[false,false]},
  "Half Spinal Twist":{"name":"HALFSPINALTWIST","image":"LowerBack.jpg","audio":"LowerBack.mp3","youtube":"v6OVxFpFIYY","switchOption":true,"video":"LowerBack.mp4","category":"yoga","videoTiming":[2400, 6350]},
  "Side Plank":{"name":"SIDEPLANK","image":"SideBridge.jpg","audio":"SidePlank.mp3","youtube":"ZVG30XkbaAc","switchOption":true,"video":"SidePlank.mp4","category":"back","videoTiming":[2200,6600]},
  "Lunge":{"name":"LUNGE","image":"ForwardLunges.jpg","audio":"Lunge.mp3","youtube":"jSnSKT2g9PY","switchOption":false,"video":"ForwardLunges.mp4","category":"lower","videoTiming":[false,false]},
  "Laying Spinal Twist":{"name":"LAYINGSPINALTWIST","image":"SingleLegOver.jpg","audio":"LayingSpinal.mp3","youtube":"WP3GSdsj8Ds","switchOption":true,"video":"LayingSpinal.mp4","category":"back","videoTiming":[2800,6100]},
  "Kneeling Hip Flexor":{"name":"KNEELINGHIPFLEXOR","image":"HipFlexor.jpg","audio":"KneelingHipFlexor.mp3","youtube":"3MKV1Ht-3VU","switchOption":true,"video":"KneelingHipFlexor.mp4","category":"back","videoTiming":[1200, 3500]},
  "T Raise":{"name":"TRAISE","image":"TRaises.jpg","audio":"TRaises.mp3","youtube":"n2Y173usrvQ","switchOption":false,"video":"TRaises.mp4","category":"upper","videoTiming":[false,false]},
  "Lying Triceps Lifts":{"name":"LYINGTRICEPSLIFTS","image":"LyingTriceps.jpg","audio":"LyingTriceps.mp3","youtube":"Sma4t8m1sl4","switchOption":false,"video":"LyingTriceps.mp4","category":"upper","videoTiming":[false,false]},
  "Reverse Plank":{"name":"REVERSEPLANK","image":"ReversePlank.jpg","audio":"ReversePlank.mp3","youtube":"cBy9Q__NmuY","switchOption":false,"video":"ReversePlank.mp4","category":"upper","videoTiming":[2200,false]},
  "Windmill":{"name":"WINDMILL","image":"Windmill.jpg","audio":"Windmill.mp3","youtube":"6g6bTRqEcXw","switchOption":false,"video":"Windmill.mp4","category":"core","videoTiming":[false,false]},
  "Bent Leg Twist":{"name":"BENTLEGTWIST","image":"BentLegTwist.jpg","audio":"BentLegTwist.mp3","youtube":"84JdoB7VbfI","switchOption":false,"video":"BentLegTwist.mp4","category":"core","videoTiming":[false,false]},
  "Side Bridge":{"name":"SIDEBRIDGE","image":"SideBridge.jpg","audio":"SideBridge.mp3","youtube":"ZVG30XkbaAc","switchOption":true,"video":"SidePlank.mp4","category":"core","videoTiming":[2200,6600]},
  "Quadraplex":{"name":"QUADRAPLEX","image":"Quadraplex.jpg","audio":"Quadraplex.mp3","youtube":"MslgraT68n0","switchOption":false,"video":"Quadraplex.mp4","category":"back","videoTiming":[false,false]},
  "High Jumper":{"name":"HIGHJUMPER","image":"HighJumper.jpg","audio":"HighJumper.mp3","youtube":"ijEnR3J0LHM","switchOption":false,"video":"HighJumper.mp4","category":"cardio","videoTiming":[false,false]},
  "Side to Side Knee Lifts":{"name":"SIDETOSIDEKNEELIFTS","image":"SideToSide.jpg","audio":"SideToSide.mp3","youtube":"BQg_YJk11oI","switchOption":false,"video":"SideToSide.mp4","category":"lower","videoTiming":[false,false]},
  "Frog Jumps":{"name":"FROGJUMPS","image":"FrogJumps.jpg","audio":"FrogJumps.mp3","youtube":"1F2Yu1l4M1g","switchOption":false,"video":"FrogJumps.mp4","category":"lower","videoTiming":[false,false]},
  "Bend and Reach":{"name":"BENDANDREACH","image":"BendAndReach.jpg","audio":"BendAndReach.mp3","youtube":"HXL3MAnjkFo","switchOption":false,"video":"BendAndReach.mp4","category":"stretch","videoTiming":[false,false]},
  "Arm and Shoulder Stretch":{"name":"ARMANDSHOULDERSTRETCH","image":"ArmAndShoulder.jpg","audio":"ArmAndShoulder.mp3","youtube":"Pox8nzxHuzk","switchOption":true,"video":"ArmAndShoulder.mp4","category":"stretch","videoTiming":[3500,6300]},
  "Shoulder Shrugs":{"name":"SHOULDERSHRUGS","image":"ShoulderShrug.jpg","audio":"ShoulderShrug.mp3","youtube":"5PqXQluk6qs","switchOption":false,"video":"ShoulderShrug.mp4","category":"stretch","videoTiming":[false,false]},
  "Fast Feet":{"name":"FASTFEET","image":"fastFeet.jpg","audio":"fastFeet.mp3","youtube":"RWkUDUugAOM","switchOption":false,"video":"fastFeet.mp4","category":"cardio","videoTiming":[false,false]},
  "Step Touch":{"name":"STEPTOUCH","image":"stepTouch.jpg","audio":"stepTouch.mp3","youtube":"gDfKb6L6IOk","switchOption":false,"video":"stepTouch.mp4","category":"cardio","videoTiming":[false,false]},
  "Power Skip":{"name":"POWERSKIP","image":"powerSkip.jpg","audio":"powerSkip.mp3","youtube":"zANZ3-z1Mtk","switchOption":false,"video":"powerSkip.mp4","category":"cardio","videoTiming":[false,false]},
  "High Knees":{"name":"HIGHKNEES","image":"highKnees.jpg","audio":"highKnees.mp3","youtube":"OzawXhbQ4AM","switchOption":false,"video":"highKnees.mp4","category":"cardio","videoTiming":[false,false]},
  "Butt Kickers":{"name":"BUTTKICKERS","image":"buttKickers.jpg","audio":"buttKickers.mp3","youtube":"yM8tBZiTJEQ","switchOption":false,"video":"buttKickers.mp4","category":"cardio","videoTiming":[false,false]},
  "Jump Rope Hops":{"name":"JUMPROPEHOPS","image":"jumpRope.jpg","audio":"jumpRope.mp3","youtube":"jwbNnI81cfQ","switchOption":false,"video":"jumpRope.mp4","category":"cardio","videoTiming":[false,false]},
  "Side Hops":{"name":"SIDEHOPS","image":"sideHops.jpg","audio":"sideHops.mp3","youtube":"eZZEDUDW9U0","switchOption":false,"video":"sideHops.mp4","category":"cardio","videoTiming":[false,false]},
  "Pivoting Upper Cuts":{"name":"PIVOTINGUPPERCUTS","image":"pivotingUpper.jpg","audio":"pivotingUpper.mp3","youtube":"eulZUGt8ZXQ","switchOption":false,"video":"pivotingUpper.mp4","category":"cardio","videoTiming":[false,false]},
  "Squat Jabs":{"name":"SQUATJABS","image":"squatJabs.jpg","audio":"squatJabs.mp3","youtube":"1CNwWtvMtWo","switchOption":false,"video":"squatJabs.mp4","category":"cardio","videoTiming":[false,false]},
  "Skaters":{"name":"SKATERS","image":"skaters.jpg","audio":"skaters.mp3","youtube":"ODRGFLzeWYU","switchOption":false,"video":"skaters.mp4","category":"cardio","videoTiming":[false,false]},
  "Single Leg Hops":{"name":"SINGLELEGHOPS","image":"singleLegHops.jpg","audio":"singleLegHops.mp3","youtube":"lTyY3bfhoPQ","switchOption":true,"video":"singleLegHops.mp4","category":"cardio","videoTiming":[false,false]},
  "Jumping Planks":{"name":"JUMPINGPLANKS","image":"jumpingPlanks.jpg","audio":"jumpingPlanks.mp3","youtube":"b0ph_y0Khg4","switchOption":false,"video":"jumpingPlanks.mp4","category":"cardio","videoTiming":[false,false]},
  "Star Jumps":{"name":"STARJUMPS","image":"starJumps.jpg","audio":"starJumps.mp3","youtube":"zqMOALAFi7g","switchOption":false,"video":"starJumps.mp4","category":"cardio","videoTiming":[false,false]},
  "Sprinter":{"name":"SPRINTER","image":"sprinter.jpg","audio":"sprinter.mp3","youtube":"SIkSMk92DRc","switchOption":false,"video":"sprinter.mp4","category":"cardio","videoTiming":[false,false]},
  "Power Jump":{"name":"POWERJUMP","image":"powerJump.jpg","audio":"powerJump.mp3","youtube":"I5v7zh9dd6E","switchOption":false,"video":"powerJump.mp4","category":"cardio","videoTiming":[false,false]},
  "Single Lateral Hops":{"name":"SINGLELATERALHOPS","image":"singleLateralHops.jpg","audio":"singleLateralHops.mp3","youtube":"jrFc-pDYLMQ","switchOption":true,"video":"singleLateralHops.mp4","category":"cardio","videoTiming":[false,false]},
  "Shoulder Tap Push-ups":{"name":"SHOULDERTAPPUSH_UPS","image":"shoulderTap.jpg","audio":"shoulderTap.mp3","youtube":"qHA91YL5VjU","switchOption":false,"video":"shoulderTap.mp4","category":"cardio","videoTiming":[false,false]},
  "Squat Jacks":{"name":"SQUATJACKS","image":"squatJacks.jpg","audio":"squatJacks.mp3","youtube":"MKfQ-l7VSCc","switchOption":false,"video":"squatJacks.mp4","category":"cardio","videoTiming":[false,false]},
  "Lunge Jumps":{"name":"LUNGEJUMPS","image":"lungeJumps.jpg","audio":"lungeJumps.mp3","youtube":"WUfjtNeb16w","switchOption":false,"video":"lungeJumps.mp4","category":"cardio","videoTiming":[false,false]},
  "Up Downs":{"name":"UPDOWNS","image":"upDowns.jpg","audio":"upDowns.mp3","youtube":"bVtUGCJLuNQ","switchOption":false,"video":"upDowns.mp4","category":"cardio","videoTiming":[false,false]},
  "Swimmer":{"name":"SWIMMER","image":"swimmer.jpg","audio":"swimmer.mp3","youtube":"JAUK08qf16c","switchOption":false,"video":"swimmer.mp4","category":"core","videoTiming":[false,false]},
  "One Arm Side Push-up":{"name":"ONEARMSIDEPUSH_UP","image":"oneArmSide.jpg","audio":"oneArmSide.mp3","youtube":"KqPg3BAyrmM","switchOption":true,"video":"oneArmSide.mp4","category":"upper","videoTiming":[false,false]},
  "Power Circles":{"name":"POWERCIRCLES","image":"powerCircles.jpg","audio":"powerCircles.mp3","youtube":"hMAx_y8qQFc","switchOption":false,"video":"powerCircles.mp4","category":"upper","videoTiming":[false,false]},
  "Dive Bomber Push-ups":{"name":"DIVEBOMBERPUSH_UPS","image":"diveBomber.jpg","audio":"diveBomber.mp3","youtube":"FRyxMQmeaoA","switchOption":false,"video":"diveBomber.mp4","category":"upper","videoTiming":[false,false]},
  "Calf Raises":{"name":"CALFRAISES","image":"calfRaises.jpg","audio":"calfRaises.mp3","youtube":"jtbxfT9sPts","switchOption":false,"video":"calfRaises.mp4","category":"lower","videoTiming":[false,false]},
  "Genie Sit":{"name":"GENIESIT","image":"genie.jpg","audio":"genie.mp3","youtube":"3HDgdrS4Y38","switchOption":false,"video":"genie.mp4","category":"core","videoTiming":[false,false]},
  "Mason Twist":{"name":"MASONTWIST","image":"masonTwist.jpg","audio":"masonTwist.mp3","youtube":"zKJbTZv27u4","switchOption":false,"video":"masonTwist.mp4","category":"core","videoTiming":[false,false]},
  "Steam Engine":{"name":"STEAMENGINE","image":"steamEngine.jpg","audio":"steamEngine.mp3","youtube":"LjRRcQClF_k","switchOption":false,"video":"steamEngine.mp4","category":"core","videoTiming":[false,false]},
  "In and Out Abs":{"name":"INANDOUTABS","image":"inOutAbs.jpg","audio":"inOutAbs.mp3","youtube":"cfunMu14Hws","switchOption":false,"video":"inOutAbs.mp4","category":"core","videoTiming":[false,false]},
  "Six Inch and Hold":{"name":"SIXINCHANDHOLD","image":"sixInch.jpg","audio":"sixInch.mp3","youtube":"6ne45VyH7YQ","switchOption":false,"video":"sixInch.mp4","category":"core","videoTiming":[false,false]},
  "Hurdlers Stretch":{"name":"HURDLERSSTRETCH","image":"hurdlers.jpg","audio":"hurdlers.mp3","youtube":"8h_8C4ZaIqQ","switchOption":true,"video":"hurdlers.mp4","category":"stretch","videoTiming":[2300,5900]},
  "Ankle on the Knee":{"name":"ANKLEONTHEKNEE","image":"ankleOnKnee.jpg","audio":"ankleOnKnee.mp3","youtube":"-mlnFTcMwkI","switchOption":true,"video":"ankleOnKnee.mp4","category":"stretch","videoTiming":[1900,4400]},
  "Arm Circles":{"name":"ARMCIRCLES","image":"armCircles.jpg","audio":"armCircles.mp3","youtube":"xNAbzeNev40","switchOption":false,"video":"armCircles.mp4","category":"stretch","videoTiming":[false,false]},
  "Abdominal Crunch":{"name":"ABDOMINALCRUNCH","image":"AbdominalCrunch.jpg","audio":"AbdominalCrunch.mp3","youtube":"k9f-eaKojwU","switchOption":false,"video":"AbdominalCrunch.mp4","category":"core","videoTiming":[false,false]},
  "Step Ups":{"name":"STEPUPS","image":"StepUps.jpg","audio":"StepUps.mp3","youtube":"ETiSDt8TBNo","switchOption":false,"video":"StepUps.mp4","category":"lower","videoTiming":[false,false]},
  "Push-up and Rotation":{"name":"PUSH_UPANDROTATION","image":"PushupAndRotation.jpg","audio":"PushupAndRotation.mp3","youtube":"LxdMKjUgLLM","switchOption":false,"video":"PushupAndRotation.mp4","category":"upper","videoTiming":[false,false]},
  "Good Mornings":{"name":"GOODMORNINGS","image":"GoodMornings.jpg","audio":"GoodMornings.mp3","youtube":"bw2Z5QSncjo","switchOption":false,"video":"GoodMornings.mp4","category":"core","videoTiming":[false,false]},
  "Knee to Chest Stretch":{"name":"KNEETOCHESTSTRETCH","image":"KneeToChest.jpg","audio":"KneeToChest.mp3","youtube":"mdbgOnRSf6Q","switchOption":true,"video":"KneeToChest.mp4","category":"stretch","videoTiming":[2300,4800]},
  "Scissor Kicks":{"name":"SCISSORKICKS","image":"ScissorKicks.jpg","audio":"ScissorKicks.mp3","youtube":"9o2LVIrI3YA","switchOption":false,"video":"ScissorKicks.mp4","category":"core","videoTiming":[false,false]},
  "Single Leg Hamstring":{"name":"SINGLELEGHAMSTRING","image":"SingleLegHamstring.jpg","audio":"SingleLegHamstring.mp3","youtube":"UyAJFaqYhi0","switchOption":true,"video":"SingleLegHamstring.mp4","category":"stretch","videoTiming":[1900,4000]},
  "Swipers":{"name":"SWIPERS","image":"Swipers.jpg","audio":"Swipers.mp3","youtube":"07U7MKMYe7A","switchOption":false,"video":"Swipers.mp4","category":"cardio","videoTiming":[false,false]},
  "Switch Kick":{"name":"SWITCHKICK","image":"switchKick.jpg","audio":"switchKick.mp3","youtube":"2eSK2W-5FAY","switchOption":false,"video":"switchKick.mp4","category":"cardio","videoTiming":[false,false]},
  "Prayer Pose":{"name":"PRAYERPOSE","image":"PrayerPose.jpg","audio":"PrayerPose.mp3","youtube":"dEGJDBWq2Ww","switchOption":false,"video":"PrayerPose.mp4","category":"yoga","videoTiming":[false,false]},
  "Raised Arms Pose":{"name":"RAISEDARMSPOSE","image":"RaisedArmsPose.jpg","audio":"RaisedArmsPose.mp3","youtube":"jUal4g3YXUI","switchOption":false,"video":"RaisedArmsPose.mp4","category":"yoga","videoTiming":[false,false]},
  "Forward Fold":{"name":"FORWARDFOLD","image":"ForwardFold.jpg","audio":"ForwardFold.mp3","youtube":"bVZFts7QPIA","switchOption":false,"video":"ForwardFold.mp4","category":"yoga","videoTiming":[false,false]},
  "Low Lunge (Left Forward)":{"name":"LOWLUNGE_LEFTFORWARD","image":"LowLungeLeft.jpg","audio":"LowLungeLeft.mp3","youtube":"5RXMEid45Hw","switchOption":false,"video":"LowLungeLeft.mp4","category":"yoga","videoTiming":[false,false]},
  "Low Lunge (Right Forward)":{"name":"LOWLUNGE_RIGHTFORWARD","image":"LowLunge.jpg","audio":"LowLunge.mp3","youtube":"XDkciAmsFLM","switchOption":false,"video":"LowLunge.mp4","category":"yoga","videoTiming":[false,false]},
  "Four Limbs Pose":{"name":"FOURLIMBSPOSE","image":"FourLimbsPose.jpg","audio":"FourLimbsPose.mp3","youtube":"o0lESx0PLmE","switchOption":false,"video":"FourLimbsPose.mp4","category":"yoga","videoTiming":[false,false]},
  "Cobra Pose":{"name":"COBRAPOSE","image":"CobraPose.jpg","audio":"CobraPose.mp3","youtube":"yK0QbZiZMjw","switchOption":false,"video":"CobraPose.mp4","category":"yoga","videoTiming":[false,false]},
  "Downward Dog":{"name":"DOWNWARDDOG","image":"DownwardDog.jpg","audio":"DownwardDog.mp3","youtube":"0L4I5rCOK0g","switchOption":false,"video":"DownwardDog.mp4","category":"yoga","videoTiming":[false,false]},
  "Mountain Pose":{"name":"MOUNTAINPOSE","image":"MountainPose.jpg","audio":"MountainPose.mp3","youtube":"usFj4B1tmu8","switchOption":false,"video":"MountainPose.mp4","category":"yoga","videoTiming":[false,false]},
  "Side Bend Left":{"name":"SIDEBENDLEFT","image":"SideBend.jpg","audio":"SideBend.mp3","youtube":"Vr-xuDT_Deg","switchOption":false,"video":"SideBend.mp4","category":"yoga","videoTiming":[false,false]},
  "Side Bend Right":{"name":"SIDEBENDRIGHT","image":"SideBendRight.jpg","audio":"SideBendRight.mp3","youtube":"blcE0LPlM34","switchOption":false,"video":"SideBendRight.mp4","category":"yoga","videoTiming":[false,false]},
  "Forward Fold Hands Behind":{"name":"FORWARDFOLDHANDSBEHIND","image":"ForwardFoldWithHands.jpg","audio":"ForwardFoldWithHands.mp3","youtube":"KZQASJJvA4I","switchOption":false,"video":"ForwardFoldWithHands.mp4","category":"yoga","videoTiming":[false,false]},
  "Chair Pose":{"name":"CHAIRPOSE","image":"ChairPose.jpg","audio":"ChairPose.mp3","youtube":"hm7k57pgXUA","switchOption":false,"video":"ChairPose.mp4","category":"yoga","videoTiming":[false,false]},
  "Chair Pose Twist Left":{"name":"CHAIRPOSETWISTLEFT","image":"ChairPoseTwist.jpg","audio":"ChairPoseTwist.mp3","youtube":"6zRu5kPmsv4","switchOption":false,"video":"ChairPoseTwist.mp4","category":"yoga","videoTiming":[false,false]},
  "Chair Pose Twist Right":{"name":"CHAIRPOSETWISTRIGHT","image":"ChairPoseTwistRight.jpg","audio":"ChairPoseTwistRight.mp3","youtube":"qLKRQzvBM3o","switchOption":false,"video":"ChairPoseTwistRight.mp4","category":"yoga","videoTiming":[false,false]},
  "Wide Leg Stance":{"name":"WIDELEGSTANCE","image":"WideLegStance.jpg","audio":"WideLegStance.mp3","youtube":"bVEEhBQdvwc","switchOption":false,"video":"WideLegStance.mp4","category":"yoga","videoTiming":[false,false]},
  "Wide Leg Stance Arms Up":{"name":"WIDELEGSTANCEARMSUP","image":"WideLegStanceArms.jpg","audio":"WideLegStanceArms.mp3","youtube":"2i2KwqBFav4","switchOption":false,"video":"WideLegStanceArms.mp4","category":"yoga","videoTiming":[false,false]},
  "Wide Leg Forward Fold":{"name":"WIDELEGFORWARDFOLD","image":"WideLegForward.jpg","audio":"WideLegForward.mp3","youtube":"L3keKYTX8bs","switchOption":false,"video":"WideLegForward.mp4","category":"yoga","videoTiming":[false,false]},
  "Triangle Left":{"name":"TRIANGLELEFT","image":"Triangle.jpg","audio":"Triangle.mp3","youtube":"3q_3hR4C1lk","switchOption":false,"video":"Triangle.mp4","category":"yoga","videoTiming":[false,false]},
  "Triangle Right":{"name":"TRIANGLERIGHT","image":"TriangleRight.jpg","audio":"TriangleRight.mp3","youtube":"ht_c4dg6KdU","switchOption":false,"video":"TriangleRight.mp4","category":"yoga","videoTiming":[false,false]},
  "Warrior II (Left Forward)":{"name":"WARRIORII_LEFTFORWARD","image":"Warrior2Right.jpg","audio":"Warrior2.mp3","youtube":"BrkAcxHv3HI","switchOption":false,"video":"Warrior2Right.mp4","category":"yoga","videoTiming":[false,false]},
  "Warrior II (Right Forward)":{"name":"WARRIORII_RIGHTFORWARD","image":"Warrior2.jpg","audio":"Warrior2Right.mp3","youtube":"s0uxrM5XlKI","switchOption":false,"video":"Warrior2.mp4","category":"yoga","videoTiming":[false,false]},
  "Side Angle Left":{"name":"SIDEANGLELEFT","image":"SideAngle.jpg","audio":"SideAngle.mp3","youtube":"rrwxNOaT8HI","switchOption":false,"video":"SideAngle.mp4","category":"yoga","videoTiming":[false,false]},
  "Side Angle Right":{"name":"SIDEANGLERIGHT","image":"SideAngleRight.jpg","audio":"SideAngleRight.mp3","youtube":"8gmQRxIv5FI","switchOption":false,"video":"SideAngleRight.mp4","category":"yoga","videoTiming":[false,false]},
  "Tree Pose Left":{"name":"TREEPOSELEFT","image":"TreePose.jpg","audio":"TreePose.mp3","youtube":"QclIM7f5iUI","switchOption":false,"video":"TreePose.mp4","category":"yoga","videoTiming":[false,false]},
  "Tree Pose Right":{"name":"TREEPOSERIGHT","image":"TreePoseRight.jpg","audio":"TreePoseRight.mp3","youtube":"bpjuLM1rXJo","switchOption":false,"video":"TreePoseRight.mp4","category":"yoga","videoTiming":[false,false]},
  "Head to Knee Left":{"name":"HEADTOKNEELEFT","image":"HeadToKnee.jpg","audio":"HeadToKnee.mp3","youtube":"Ns9Z60NkwYM","switchOption":false,"video":"HeadToKnee.mp4","category":"yoga","videoTiming":[false,false]},
  "Head to Knee Right":{"name":"HEADTOKNEERIGHT","image":"HeadToKneeRight.jpg","audio":"HeadToKneeRight.mp3","youtube":"m16U3b7zah0","switchOption":false,"video":"HeadToKneeRight.mp4","category":"yoga","videoTiming":[false,false]},
  "Twist Left":{"name":"TWISTLEFT","image":"Twist.jpg","audio":"Twist.mp3","youtube":"svThICtK-SQ","switchOption":false,"video":"Twist.mp4","category":"yoga","videoTiming":[false,false]},
  "Twist Right":{"name":"TWISTRIGHT","image":"TwistRight.jpg","audio":"TwistRight.mp3","youtube":"Dwa2J8dqhHo","switchOption":false,"video":"TwistRight.mp4","category":"yoga","videoTiming":[false,false]},
  "Lay on Back":{"name":"LAYONBACK","image":"LayOnBack.jpg","audio":"LayOnBack.mp3","youtube":"hyZ1a6Z4VcE","switchOption":false,"video":"LayOnBack.mp4","category":"yoga","videoTiming":[false,false]},
  "Prep for Shoulder Stand":{"name":"PREPFORSHOULDERSTAND","image":"PrepForShoulder.jpg","audio":"PrepForShoulder.mp3","youtube":"bkbMNMpXnAk","switchOption":false,"video":"PrepForShoulder.mp4","category":"yoga","videoTiming":[false,false]},
  "Plow":{"name":"PLOW","image":"Plow.jpg","audio":"Plow.mp3","youtube":"36Aq02xEabk","switchOption":false,"video":"Plow.mp4","category":"yoga","videoTiming":[false,false]},
  "Shoulder Stand":{"name":"SHOULDERSTAND","image":"ShoulderStand.jpg","audio":"ShoulderStand.mp3","youtube":"GhN9k2J5CWs","switchOption":false,"video":"ShoulderStand.mp4","category":"yoga","videoTiming":[false,false]},
  "Fish Pose":{"name":"FISHPOSE","image":"FishPose.jpg","audio":"FishPose.mp3","youtube":"tj3AyrKMRLk","switchOption":false,"video":"FishPose.mp4","category":"yoga","videoTiming":[false,false]},
  "Swan":{"name":"SWAN","image":"Swan.jpg","audio":"Swan.mp3","youtube":"AXFx8Dg8F7k","switchOption":false,"video":"Swan.mp4","category":"pilates","videoTiming":[false,false]},
  "Double Leg Stretch":{"name":"DOUBLELEGSTRETCH","image":"DoubleLegStretch.jpg","audio":"DoubleLegStretch.mp3","youtube":"_g0sHVYFniw","switchOption":false,"video":"DoubleLegStretch.mp4","category":"pilates","videoTiming":[false,false]},
  "Spine Stretch Forward":{"name":"SPINESTRETCHFORWARD","image":"SpineStretchForward.jpg","audio":"SpineStretchForward.mp3","youtube":"Dx_HK7NiiL8","switchOption":false,"video":"SpineStretchForward.mp4","category":"pilates","videoTiming":[false,false]},
  "Seated Spine Twist":{"name":"SEATEDSPINETWIST","image":"SeatedSpineTwist.jpg","audio":"SeatedSpineTwist.mp3","youtube":"6VuCmafK9w8","switchOption":false,"video":"SeatedSpineTwist.mp4","category":"pilates","videoTiming":[false,false]},
  "Leg Pull Front":{"name":"LEGPULLFRONT","image":"LegPullFront.jpg","audio":"LegPullFront.mp3","youtube":"wuEf9fR9gXg","switchOption":false,"video":"LegPullFront.mp4","category":"pilates","videoTiming":[false,false]},
  "Leg Pull Back":{"name":"LEGPULLBACK","image":"LegPullBack.jpg","audio":"LegPullBack.mp3","youtube":"0lRkPJQsPY0","switchOption":false,"video":"LegPullBack.mp4","category":"pilates","videoTiming":[false,false]},
  "The Hundred":{"name":"THEHUNDRED","image":"TheHundred.jpg","audio":"TheHundred.mp3","youtube":"7Wn9C649Eyo","switchOption":false,"video":"TheHundred.mp4","category":"pilates","videoTiming":[false,false]},
  "Rollover":{"name":"ROLLOVER","image":"Rollover.jpg","audio":"Rollover.mp3","youtube":"Z0BUd_tGRI4","switchOption":false,"video":"Rollover.mp4","category":"pilates","videoTiming":[false,false]},
  "Shoulder Bridge":{"name":"SHOULDERBRIDGE","image":"ShoulderBridge.jpg","audio":"ShoulderBridge.mp3","youtube":"x9xuvJUJcLs","switchOption":false,"video":"ShoulderBridge.mp4","category":"pilates","videoTiming":[false,false]},
  "Back Arm Rowing":{"name":"BACKARMROWING","image":"BackArmRow.jpg","audio":"BackArmRow.mp3","youtube":"gmBi-Adb8VU","switchOption":false,"video":"BackArmRow.mp4","category":"pilates","videoTiming":[false,false]},
  "Swimming":{"name":"SWIMMING","image":"Swimming.jpg","audio":"Swimming.mp3","youtube":"9M7MBczuRJ4","switchOption":false,"video":"Swimming.mp4","category":"pilates","videoTiming":[false,false]},
  "Double Leg Kick":{"name":"DOUBLELEGKICK","image":"DoubleLegKick.jpg","audio":"DoubleLegKick.mp3","youtube":"W4ykrff_nrU","switchOption":false,"video":"DoubleLegKick.mp4","category":"pilates","videoTiming":[false,false]},
  "Laying Side Kick":{"name":"LAYINGSIDEKICK","image":"LayingSideKick.jpg","audio":"LayingSideKick.mp3","youtube":"w_vGiuqSscE","switchOption":true,"video":"LayingSideKick.mp4","category":"pilates","videoTiming":[false,false]},
  "Teaser":{"name":"TEASER","image":"Teaser.jpg","audio":"Teaser.mp3","youtube":"rGh48bkM6V4","switchOption":false,"video":"Teaser.mp4","category":"pilates","videoTiming":[false,false]},
  "Wag Your Tail":{"name":"WAGYOURTAIL","image":"WagYourTail.jpg","audio":"WagYourTail.mp3","youtube":"TT3DmBivxaE","switchOption":true,"video":"WagYourTail.mp4","category":"pilates","videoTiming":[false,false]},
  "Corkscrew":{"name":"CORKSCREW","image":"Corkscrew.jpg","audio":"Corkscrew.mp3","youtube":"5g3judn1ZRA","switchOption":false,"video":"Corkscrew.mp4","category":"pilates","videoTiming":[false,false]},
  "Roll Up":{"name":"ROLLUP","image":"Rollup.jpg","audio":"Rollup.mp3","youtube":"beGeEqv8yCY","switchOption":false,"video":"Rollup.mp4","category":"pilates","videoTiming":[false,false]},
  "One Leg Circles":{"name":"ONELEGCIRCLES","image":"OneLegCircles.jpg","audio":"OneLegCircles.mp3","youtube":"yjFmcYT2Jw0","switchOption":false,"video":"OneLegCircles.mp4","category":"pilates","videoTiming":[false,false]},
  "Cat Pose":{"name":"CATPOSE","image":"CatPose.jpg","audio":"CatPose.mp3","youtube":"FqOCsK8dlrg","switchOption":false,"video":"CatPose.mp4","category":"yoga","videoTiming":[false,false]},
  "Child Pose":{"name":"CHILDPOSE","image":"ChildPose.jpg","audio":"ChildPose.mp3","youtube":"wg8nFRNF5hQ","switchOption":false,"video":"ChildPose.mp4","category":"yoga","videoTiming":[false,false]},
  "Cow Pose":{"name":"COWPOSE","image":"CowPose.jpg","audio":"CowPose.mp3","youtube":"WVHSC_xwH6A","switchOption":false,"video":"CowPose.mp4","category":"yoga","videoTiming":[false,false]},
  "Bridge Pose":{"name":"BRIDGEPOSE","image":"BridgePose.jpg","audio":"BridgePose.mp3","youtube":"t-QU5pCg334","switchOption":false,"video":"BridgePose.mp4","category":"yoga","videoTiming":[false,false]},
  "Crow Pose":{"name":"CROWPOSE","image":"CrowPose.jpg","audio":"CrowPose.mp3","youtube":"JYVJjK87XVI","switchOption":false,"video":"CrowPose.mp4","category":"yoga","videoTiming":[false,false]},
  "Staff Pose":{"name":"STAFFPOSE","image":"StaffPose.jpg","audio":"StaffPose.mp3","youtube":"ZOiTBnvgZZI","switchOption":false,"video":"StaffPose.mp4","category":"yoga","videoTiming":[false,false]},
  "Pigeon Pose Left":{"name":"PIGEONPOSELEFT","image":"PigeonPoseLeft.jpg","audio":"PigeonPoseLeft.mp3","youtube":"b2VJk7gZNRM","switchOption":false,"video":"PigeonPoseLeft.mp4","category":"yoga","videoTiming":[false,false]},
  "Pigeon Pose Right":{"name":"PIGEONPOSERIGHT","image":"PigeonPoseRight.jpg","audio":"PigeonPoseRight.mp3","youtube":"eAtiFXULnwo","switchOption":false,"video":"PigeonPoseRight.mp4","category":"yoga","videoTiming":[false,false]}
};

