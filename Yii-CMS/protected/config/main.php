<?php
// uncomment the following to define a path alias
// Yii::setPathOfAlias('local','path/to/local-folder');

// This is the main Web application configuration. Any writable CWebApplication properties can be configured here.
return array(
	'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
	'name'=>'Name of the site', // careful to not have special html characters, this is not encoded in the view files

	// preloading 'log' component
	'preload'=>array('log', 'cms', 'languageManager'),

	// autoloading model and component classes
	'import'=>array(
		'application.models.*',
		'application.components.*',
		'application.helpers.Helper',
		'application.filters.*',
		'application.modules.rights.*', 
		'application.modules.rights.components.*',
	),
	
	'defaultController'=>'site',

	'sourceLanguage'=>'fr',

	'modules'=>array(
		'rights'=>array( 
			'appLayout'=>'application.views.adminLayouts.main',
			'superuserName'=>'Dev',
		),
		// uncomment the following to enable the Gii tool
		/*
		'gii'=>array(
			'class'=>'system.gii.GiiModule',
			'password'=>'', // set a password here
		 	// If removed, Gii defaults to localhost only. Edit carefully to taste.
			'ipFilters'=>array('127.0.0.1','::1'),
		),
		*/
	
		// Set up your modules here
		'content'=>array(
		),
		'news'=>array(
		),
		'event'=>array(
			'archivesVarName'=>array('fr'=>'archives','en'=>'archives')
		),
	),
	
	// application components
	'components'=>array(
		'userBack'=>array(
			// cookie-based authentication for admin
			'allowAutoLogin'=>true,
			'class'=>'RWebUser',
			'stateKeyPrefix'=>'back',
			'loginUrl'=>array('/admin/login'),
		),
		// uncomment to enable cookie-based authentication for front-end with "member" module
		/*
		'userFront'=>array(
			'allowAutoLogin'=>true,
			'class'=>'RWebUser',
			'stateKeyPrefix'=>'front',
			'loginUrl'=>array('/member/default/login'),
			
		),
		*/
        'cache'=>array(
            'class'=>'system.caching.CMemCache',
            'useMemcached'=>true,
        ),
		'authManager'=>array(
		     'class'=>'RDbAuthManager',
		     'connectionID'=>'db',
		),
		'installer'=>array(
			'class'=>'CmsInstaller'
		),
		'cms'=>array(
			'class'=>'Cms',
		),
		/*
		'languageManager'=>array(
			'class'=>'CmsLanguageManager',
			'languages' => array('en'=>'English'),
			'defaultLanguage' => 'en',
		),
		*/
		'languageManager'=>array(
			'class'=>'CmsLanguageManager',
			'languages' => array('fr'=>'Français', 'en'=>'English'),
			'defaultLanguage' => 'fr',
		),
		'urlManager'=>array(
			'class'=>'CmsUrlManager',
			'urlFormat'=>'path',
			'showScriptName'=>false,

			'rules'=>array(
				''=>'site/index',
				'site/index'=>'404',
			),
		),
		'messages'=>array(
			'forceTranslation' => true,
		),
		// set your db info here
		'db'=>array(
			'emulatePrepare' => true,
			'charset' => 'utf8',
			'enableParamLogging' => true,
			'connectionString' => 'mysql:host=;dbname=',
			'username' => '',
			'password' => '',
		),

		'errorHandler'=>array(
			// use 'site/error' action to display errors
            'errorAction'=>'site/error',
        ),
		'log'=>array(
			'class'=>'CLogRouter',
			'routes'=>array(
				// uncomment to turn on file logging
        		/*
				array(
					'class'=>'CFileLogRoute',
					'levels'=>'error, warning',
				),
				*/
				// Logging only in debug mode
				array(
					'class'=>'CWebLogRoute',
					'enabled' =>YII_DEBUG,
				),
			),
		),
		'image'=>array(
          'class'=>'application.extensions.image.CImageComponent',
		  //'driver'=>'ImageMagick',
        ),
        // some modules use the facebook extension, set your appId here
		'facebook'=>array(
			'class' => 'ext.yii-facebook-opengraph.SFacebook',
			'appId'=>'398045073566147',
			'locale'=>'en_US',
			'xfbml'=>true,
			'html5'=>true,
		),
	),
	'params'=>array(
		'adminLogo' => '/images/admin/admin_logo.jpg',
		'ckeditorUploadDir' => 'files/_user/ckeditor',
		'googleFont' => 'https://fonts.googleapis.com/css?family=Open+Sans:400,300,600', // Font for main layout and admin editor
		'emailFontFamily' => "Arial, sans-serif", // Font for emails


		// For contactUs widget (included in contact bloc), set email and subject here
		'contactUsWidgetEmail' => "",
		'contactUsWidgetSubject' => 'You have received a new message from the website.',

		'htmlPurifierOptions' => array(
			'HTML.SafeIframe' => true,
			'URI.SafeIframeRegexp' => '%^https?://(www.youtube(?:-nocookie)?.com/embed/|player.vimeo.com/video/|w.soundcloud.com/player/)%',
			'Attr.AllowedFrameTargets' => array('_blank'=>true),
			'Attr.EnableID' => true,
		),
		// some blocs and modules use mailer, set your smtp info here
		'mail' => array(
			'Host' => '',
			'Username' => '',
			'Password' => '',
			'Port' => 587,
			'From' => '',
			'FromName' => '',
		),
		// some blocs use pretty photo, change their global settings here
		'prettyPhotoOptions' => array(
			'animation_speed'=>'normal',
			'opacity'=>0.70,
			'modal'=>false,
			'slideshow'=>4000,
			'show_title'=>false,
			'allow_resize'=>true,
			'counter_separator_label'=>'/',
			'overlay_gallery'=>false,
		),
		// global settings for uploading behavior
		'uploadingBehavior' => array(
			// if you use uploadcare, put your keys here
			/*
			'uploadcare' => array(
			    'publicKey' => '',
			    'privateKey' => '',
			),
			*/
		),
 		'dropboxToken' => '', // for clouddocument bloc
		'kloudlessApiId' => '', // for clouddocument bloc

		'blocs' => array(
			'editor',
			'image',
			'youtube',
			'googlemap',
			'document',
			'people',
			'flickr',
			'citation',
			'achievement',
			'contact',
			'feature',
			//'clouddocument', // Set dropboxToken and kloudlessApiId before turning it on
		),
	),
);
?>
