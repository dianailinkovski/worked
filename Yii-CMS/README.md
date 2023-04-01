# Yii CMS

A CMS focused on giving developers tools to easily create modular, multilingual (optional) and user-friendly website and administration. It is meant to serve as a convenient base on which to start and provides a lot of the functionality commonly needed.

Requires Yii 1.1.16+ and PHP 5.4+ (Might work with 5.3)

# Important

Recently the admin panel's theme was upgraded to Gentellela but not all modules view files have been upgraded yet so if you want to use them right now you'll have to upgrade them yourself until it's done.

# Features

* Heavy focus on facilitating the creation of multilingual websites
* Heavy focus on modular development
* SEO friendly routing system to create and parse urls with translatable and editable aliases
* Module based URL rewriting
* "Section" system which allows modules to be added or removed from the admin panel, as well as allowing multiple instanciations of the same module
* Many tools to ease the creation of forms in the administration or otherwise such as a Form manager class for the controller, a javascript based CRUD (TabularInputWidget), Uploading Behavior class, and a lot more
* "Blocs" system, which allows for user-friendly editing of content

# Installation

Copy the files to your hosting folder and edit index.php to configure your environment if needed. By default it's set as a 2 environment system where you get "dev" config for local (from 127.0.0.1) and "prod" config for production and "main" config for both. 

As for the location of the Yii library, it's set to read the path to the framework folder from a (gitignored) .yii_location file if local and a hardcoded path in index.php for production (for a situation where you have multiple developers each with their own local servers). Alternativeely you can just drop the Yii's framework folder and rename it "yii" to the root of the website.

A .htaccess file is provided for url rewrite. If you don't use Apache, delete it and set up the equivalent for whatever server you're using, don't forget to deny access to the "protected" directory where another .htaccess file is located.

Read through the "main" config file and customize it to your needs.

Currently you can install the database by going to the url /install or by simply executing the MySQL queries found in the "data" folder. Modules also have a "data" folder that need to be executed if you're going to use them. There will be a proper GUI installer in the future.

You must also set write permissions on the following folders:

* /files/_user (all user-generated files go there)
* /assets (Yii assets folder)
* /protected/cache (cache folder)
* /protected/runtime (Yii runtime folder)

Lastly, change the super-admin name and password in the "user" table to something more secure, default is Username: Administrator, Password: temporary

# Front-end

You can use whatever front-end CSS framework or layout you'd like, but the modules and blocs were developed with Bootstrap 3 so if you want to use them it would be easier to use Bootstrap or you'll have to re-write all the view files. A base layout and css is given you can start off that or from scratch. Keep in mind that the modules publish their own assets so if you want to change them you'll need to do so in the module. Administration uses the Gentelella bootstrap theme (http://demo.kimlabs.com/gentelella/production/form.html).

# Modules

Here are the currently available modules. Note that some of them are older and based off a previous version of the CMS and so might need refreshing.

* [Content] Manages editable pages with blocs, generates multilang aliases for pages automatically.
* [Contest] Manages contests, with customizable fields and presentation.
* [Event] Manages events, module is instantiable, has RSS feed.
* [Job] Manages job postings with categories and possibility to post CV, has RSS feed.
* [Member] Manages members, with widgets for creating new account, login.
* [Message] Publish messages to website users, display using the widget.
* [News] Manages news, module is instantiable, has RSS feed.
* [Newsletter]  Manages newsletters, the module delivers newsletters in the form of an API and you must have a cron script calling it and deliver the newsletters. 
* [Product] Manages products.

# Blocs

Blocs are snippets of content that administrators can use. It's a way to allow non-technical users to easily add and edit dynamic content however they like.

* [Achievement] Shows a description of achievements with a gallery of photos relating to them.
* [Citation] List of citations from people.
* [Cloud document] List contents of a folder from a cloud hosting source.
* [Contact] "Contact us" type content with information, address, google maps, etc as well as the possibility of including a contact form (email)
* [Document] Upload and allow downloading of documents.
* [Editor] Editable content with CKeditor.
* [Feature] List a description of features of something with possibly images.
* [Flickr] Show a gallery of photos pulled from Flickr.
* [Google map] Display a Google map.
* [Image] Display an image.
* [People] List a number of people with their description.
* [Youtube] Display a Youtube video.

# Third party addons

Here are the third party addons used. Some of them had to be modified. Not all of them are up to date.

* SimplePie (vendors)
  * Autoloader "prepend" instead of "append" (spl_autoload_register(array(new SimplePie_Autoloader(), 'autoload'), true, true))
* PrettyPhoto (extension) :
  * Modification of the registerScript to allow multiples widgets per page
  * Modification of the javascript file to replace the 'rel' attribute (non-valid) to 'data-lightbox'
* Multilang Behavior (model behavior)
  * Line 304, if ($rule[1] !== 'required' || ($this->forceOverwrite && $l != $this->defaultLanguage)), to not have required rule for language same as default language
  * Added package tag since it's included in the class reference
* QTreeGridView (extension)
  * Modified to support NestedSetBehavior
  * Commented JQuery register
  * Modified treeTable javascript public function names because of conflicts
* EMailer (extension)
  * Commented the error echos (we don't want them echoed we want them in $mailer->ErrorInfo)
* EFeed (extension)
  * Commented RenderItems() to allow feeds without items
* Bootstrap (CSS)
* Gentelella bootstrap theme (CSS)
* JQuery and JQuery UI (javascript)
* EActiveRecordRelationBehavior (extension)
* JColorPicker (extension)
* Kohana Image (extension)
* NestedSetBehavior (extension)
* facebook-opengraph (extension)
* CKEditor (extension)

# Documentation

Other than this readme file, you can look at the "examples" folder (some modules might also have examples folders) for examples on how to do certain things. It might answer a lot of your questions.

There is also the code which is heavily commented, and referenced in the class reference. To access it simply point your browser to /docs.