var page = require('webpage').create();
page.open('http://www.cheaperthandirt.com/Search.aspx?site=All%20Products&num=15&q=029757010032&fgb=t', function(status) {
  console.log("Status: " + status);
  if(status === "success") {
    console.log(page.content);
  }
  phantom.exit();
});

