var mysql = require('mysql');
var Configs = require('./Configs.js');
var configs = new Configs();
var pool  = mysql.createPool(configs.mysql);

module.exports = function Wallet() {
    Wallet.prototype.addFunds = function(funds, id_user, callback){
        pool.getConnection(function(err, connection) {
            connection.query("UPDATE `accounts` SET `ad_funds` = `ad_funds` + "+connection.escape(funds)+" WHERE `id` = "+connection.escape(id_user), function(err, result){
            connection.release();
            if (err) {
                console.log(err);
                callback(false);
            }else{
                callback(true);
            }
          });
        });
    };
    Wallet.prototype.getFunds = function(id, callback){
        console.log("=========================================getting funds=============================");
        pool.getConnection(function(err, connection) {
            connection.query("SELECT `ad_funds` FROM `accounts` WHERE `id` = "+connection.escape(id), function(err, rows, fields){
            connection.release();
            if (err) {
                console.log(err);
                callback({success: false, error: err});
            }else{
                callback({success: true, funds: rows[0].ad_funds});
            }
          });
        });
        
    };
    
}