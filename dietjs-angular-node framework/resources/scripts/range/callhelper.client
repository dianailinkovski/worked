 function getQueryStrings() {
      var assoc  = {};
      var decode = function (s) { return decodeURIComponent(s.replace(/\+/g, " ")); };
      var queryString = location.search.substring(1);
      var keyValues = queryString.split('&');

      for(var i in keyValues) {
        var key = keyValues[i].split('=');
        if (key.length > 1) {
          assoc[decode(key[0])] = decode(key[1]);
        }
      }
      return assoc;
    }
    var qs = getQueryStrings();
    if (qs['r'] == null) {
      window.location.href = window.location.pathname + '?r=' + Math.random().toString().slice(2, 12)
    }
    var userid = Math.random().toString().slice(2, 12);
    var room = qs['r'];
    var type = "random";
    var password = "";
    
$(document).ready(function(){
     connect();
})