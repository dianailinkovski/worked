XPath Tester
------------

This program allows you to test xpaths using the PHP DOMXPath class (http://php.net/manual/en/class.domxpath.php).

Folder map:
├── images                                             ~ images for index display
├── includes                                          
│   └── main.php                                       ~ the guts of the program, do not change
├── xpaths                                             ~ make this directory writeable
│   └── totalhealthvitamins                            ~ auto generated directory 
│       └── totalhealthvitamins.tsv                    ~ program writes this data file
├── page_cache                                         ~ make this directory writeable
│   └── totalhealthvitaminscomohh-12111html.html       ~ cache of html page
├── _blank.php                                         ~ an example file, with no contents 
├── index.php                                          ~ index page
├── READ_ME.txt                                        ~ the file you are reading now
├── totalhealthvitamins.php                            ~ an example file, with contents
└── xpath_tester.tar.gz                                ~ tar/gzip file of this directory, includes all files


Installation:
PHP modules: curl, tidy, DOM
Make sure that root folder /tmp/ is writeable by PHP
Make sure that folder xpaths and page_cache is writeable by PHP

Instructions:
1. open up totalhealthvitamins.php and _blank.php in a text editor
2. open totalhealthvitamins.php and _blank.php in a web browser.
3. familiarize yourself with the files, directories, and how they work
4. for each new domain you want to create a crawler, copy the _blank file and save it, name it same as the target host name (eg, for http://www.amazon.com/ name the file amazon.php, or amazon.com.php).
5. install and use browser tools to view xpath and view DOM.  Use the cached html file (no javascript was rendered).
6. study about XPath 1.0 (http://www.edankert.com/xpathfunctions.html)
7. when the new xpaths you write work properly (ie, they return one and only one result for each xpath) then zip/tar the contents of this entire file structure and deliver it.
8. The only mandatory data is Price.  If you cannot get price from the web page then write a file explaining why, and how to get the price from that website.  Save that file into /xpaths/[hostname]/exceptions.txt.   The xpath is sufficient if it returns a mixture of the price plus text characters, as they will be filtered out later.

Delivery:
- read item 7
