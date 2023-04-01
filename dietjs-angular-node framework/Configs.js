module.exports = function Configs() {
    Configs.prototype.mysql = {
        connectionLimit : 10,
        host     : 'localhost',
        user     : 'root',
        password : 'vbox@1234',
        database : 'vbox'
    };
    Configs.prototype.paypal= {
        "port" : 5000,
        "api" : {
          "host" : "api.sandbox.paypal.com",
          "port" : "",            
          "client_id" : "ASTi1LhaanplBWPgtsqpBCt8fq6ZL8uco-A7Y6bkMXgVr2An-dBR9L3b01b4QapUZkoEcbi8VgF6rlWt",
          "client_secret" : "ENXJ5v626w-ut4OyqJ-YpFxllMfUhnLrjOGePaUULjd9nyb5e8f2OxeWxhClkHn_TONxnt6-gzFf6vmO"
        }
    };
}
