Application = require('diet');
formidable = require('formidable');
fs = require('fs');
mime  = require( "mime" );
http = require('http');
path = require('path');


//
sys         = require("util");

app = new Application.setup({
	domain: '192.241.239.235',
	path: __dirname,
	public: __dirname+'/resources',
	mysql: {
		host: 'localhost',
		user: 'root',
		password: 'vbox@1234',
		database: 'vbox'
	}
});

app.ready(function(){
	app.use('objects');
	app.use('routes');
	app.use('email');
});

