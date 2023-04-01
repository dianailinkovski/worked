var debug=require('debug')("blog:"+__filename);
var express = require('express');

// Our app object
var app;

test = function(req, res) {
	app.render("index");
    console.log("In blog/test");
    res.send("Hey, this is blog/test");
};

module.exports = function() {
    //app = express();
    app.set('view engine', 'html');
    app.use(express.static(__dirname + '/public'));
    app.get('/',test);
    return app;
};
