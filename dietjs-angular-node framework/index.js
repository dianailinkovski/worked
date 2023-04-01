/** osip */
Application = require('diet');
formidable = require('formidable');
fs = require('fs');
mime  = require( "mime" );
path = require('path');
var http    = require("http");              // http server core module
var express = require("express");           // web framework external module
var io      = require("socket.io");         // web socket external module
var easyrtc = require("easyrtc");           // EasyRTC external module
var dl  = require('delivery');
var fs  = require('fs');

var connectedUsers = [];
sockets = [];
people = {};
userSockets = [];

// Setup and configure Express http server. Expect a subfolder called "static" to be the web root.
var httpApp = express();
httpApp.use(express.static(__dirname + "/views/"));

// Start Express http server on port 8080
var webServer = http.createServer(httpApp).listen(3000);

// Start Socket.io so it attaches itself to Express server
var socketServer = io.listen(webServer, {"log level":1});

// Start EasyRTC server
var rtc = easyrtc.listen(httpApp, socketServer);

var mysql = require('mysql');
    var pool  = mysql.createPool({
        connectionLimit : 10,
        host     : 'localhost',
        user     : 'root',
        password : 'vbox@1234',
        database : 'vbox'
    });

socketServer.on('connection', function (socket) {
    
    var delivery = dl.listen(socket);
    delivery.on('receive.success',function(file){
 
        fs.writeFile(shared_dir+file.name,file.buffer, function(err){
          if(err){
            console.log('File could not be saved.');
          }else{
            
            console.log('File saved.');
          }
          
        });
    });
    socket.on('newUserConnect', function(data){
        people[socket.id] = data.id_user;
        sockets.push(socket);
        userSockets[data.id_user] = socket.id;
        socket.join(data.id_room);
        //console.log(sockets);
        //sockets[socket.id].join(id_room);
       users = getUsersInRoom(data.id_room);
        //socketServer.sockets.manager.roomClients[socket.id];
       // console.log(socketServer.of('').clients(data.id_room));
        //console.log(socketServer.sockets.manager.roomClients);
        //console.log(users);
        console.log(socketServer.sockets.manager.roomClients);
        console.log(socket.id + " connected");
        socketServer.sockets.in(data.id_room).emit('newUserConnectCallback', {id_user: data.id_user, users: users});
        socket.on('disconnect', function(){
            socket.leave(data.id_room);
            console.log(socket.id + " disconnected");
            
            id_user_left = people[socket.id];
            delete people[socket.id];
            sockets.splice(sockets.indexOf(socket), 1);
            socketServer.sockets.in(data.id_room).emit('userLeftCall', {id_user_left: id_user_left});
        });
    });
  //  socket.on('disconnect', function(){
        
  //  });
    

    socket.on('getUserInfo',function(data){
        id_user = data.id_user;
        userObj = new User(id_user);
        userObj.getUser();
        
    });
    socket.on('getUsersInfo',function(data){
        
        ids = data.ids;
        console.log("getting users info");
        console.log(ids);
        if (!data.callback) {
            callback = "getUsersInfoCallback";
        }
        else{
            callback = data.callback;
        }
        where = "WHERE ";
       
        ids.forEach(function(id){
            where += "`id` = '"+id+"' OR ";
        });
        where = where.replace(/( OR )$/g, '');
       // console.log("fetching user info");
        userObj = new User();
        pool.getConnection(function(err, connection) {
            connection.query("SELECT * FROM `"+userObj.table+"` " + where, function(err, rows, fields){
                connection.release();
                if (err) {
                  console.log(err);
                  socket.emit(callback, {error: true, err: err});
                  return;
                }else{
                  //  console.log(callback);
                   // console.log(rows);
                  // console.log(ids);
                    socket.emit(callback, {error: false, rows: rows, ids: ids});
                    return;
                }
            });
        });
    });
    
    socket.on("getUserTimeSlotDates", function(data){
        console.log("getUserTimeSlotDates");
        userObj = new User();
        userObj.getUserTimeSlotDates(data.id_account, data.month);
    });
    
    socket.on("saveUserChat", function(data){
        userObj = new User();
        userObj.saveUserChat(data.from,data.to, data.text);
    });
    
    socket.on("getChatHistory", function(data){
        userObj = new User();
        console.log("getChatHistory ======================================");
       // console.log(data.userinfo);
        userObj.getChatHistory(data);
        return;
    });
    
    socket.on("getMessageBlock", function(data){
        userObj = new User(data.id_user);
        userObj.getMessageBlock(data.id_user,data.limit);
    });
    socket.on("getAppointmentsBlock", function(data){
        userObj = new User();
        userObj.getAppointmentsBlock(data.id_user, data.limit);
    });

    socket.on("saveAppointment", function(data){
        userObj = new User();
        userObj.saveAppointment(data);
    });
    socket.on("completeAppointment", function(data){
        userObj = new User();
        userObj.removeAppointment(data.id);
    });
    socket.on("isAppointAvailable", function(data){
        userObj = new User();
        userObj.isAppointAvailable(data);
    });


function getUsersInRoom(id_room){
    var room_info = socketServer.of('').clients(id_room);
    users = [];
    for (var clientId in room_info) {
     // console.log(room_info[clientId]);
      var socket_id = room_info[clientId].id;
      users.push(people[socket_id]);
    }
    //console.log(users);
    return users;
}





function User(id) {
    this.id = id;
    this.table = 'accounts';
    this.result = false;
    this.err = false;

  }

User.prototype = {
    constructor: User,
    getFullName : function(id){
        console.log("getting name");
        pool.getConnection(function(err, connection) {
            connection.query("SELECT `full_name` FROM `"+this.table+"` WHERE `id` = '"+id+"'", function(err, rows, fields){
            connection.release();
            if (err) {
              console.log(err);
              socket.emit("getFullNameCallback", {error: true, err: err});
            }else{
                socket.emit("getFullNameCallback", {error: false, rows: rows});
            }
          });
        });
        
    },
    getUserTimeSlotDates : function(id_user, month){
        console.log("getting timeslot dates");
	sdate = month+"-01";
	edate = month+"-31";
	data = [];
	pool.getConnection(function(err, connection) {
            connection.query("SELECT distinct(`date`) FROM `time_slots` WHERE `id_user`='"+id_user+"' AND `date` between '"+sdate+"' and '"+edate+"' AND `reserved_by`='0' LIMIT 30", function(err, rows, fields){
            connection.release();
            if (err) {
              console.log(err);
              socket.emit("getUserTimeSlotDatesCallback", {error: true, err: err});
            }else{
                //console.log(rows);
                if (rows && typeof rows !== undefined && rows != null && rows.length > 0) {
                    rows.forEach(function(row){
                        date = new Date(row.date);
                        date = convertDate(date);
                      //console.log(date);
                        data.push({date:date, title:"Available Date"});
                    });
                    //console.log(data);
                    socket.emit("getUserTimeSlotDatesCallback", {error: false, rows: data});
                }
                else{
                    var errMessage = "User has not set any available dates";
                    console.log(errMessage);
                    socket.emit("getUserTimeSlotDatesCallback", {error: false, rows: {}, errMessage: errMessage});
                }
            }
          });
        });
    },
    saveUserChat: function(from, to, text){
        pool.getConnection(function(err, connection) {
            connection.query("INSERT INTO `chat`(`from`, `to`, `message`) VALUES("+connection.escape(from)+", "+connection.escape(to)+", "+connection.escape(text)+")", function(err, result){
            connection.release();
            if (err) {
              console.log(err);
              socket.emit("saveUserChatCallback", {error: true, err: err});
            }else{
                console.log(result.insertId);
                socket.emit("saveUserChatCallback", {error: false, insertID: result.insertId});
            }
          });
        });
    },
    saveAppointment: function(data){
        pool.getConnection(function(err, connection) {
            connection.query("INSERT INTO `appointments`(`id_user`, `with_user`, `on_time`) VALUES("+connection.escape(data.id_user)+", "+connection.escape(data.id_visitor)+", "+connection.escape(data.appTime)+")", function(err, result){
            connection.release();
            if (err) {
              console.log(err);
              socket.emit("saveAppointmentCallback", {error: true, err: err});
            }else{
                console.log("Appointment Saved ");
                socket.emit("saveAppointmentCallback", {error: false, insertID: result.insertId});
            }
          });
        });
    },
    getChatHistory: function(data){
        
        pool.getConnection(function(err, connection) {
            //console.log("SELECT * FROM `chat` WHERE `from` = "+connection.escape(from)+" OR `from` = "+connection.escape(to) + " ORDER BY `id` DESC LIMIT 50");
            pool.query("SELECT * FROM `chat` WHERE (`from` = "+connection.escape(data.from)+" AND `to` = "+connection.escape(data.to) + ") OR (`to` = "+connection.escape(data.from)+" AND `from` = "+connection.escape(data.to) + ") ORDER BY `id` DESC LIMIT 150", function(err, rows){
                connection.release();
                if (err) {
                  console.log(err);
                  socket.emit("getChatHistoryCallback", {error: true, err: err});
                }else{
                    //console.log(rows);
                    console.log("sending callback for message called for " + data.from);
                    socket.emit("getChatHistoryCallback", {error: false, rows: rows, data: data});
                }
            });
        });
    },
    getMessageBlock: function(id, limit){
        limit = limit * 2;
        pool.getConnection(function(err, connection) {
            connection.query("SELECT DISTINCT(`from`), b.first_name, b.last_name, b.id as id_account, b.avatar FROM `chat` as `a` left join `accounts` as `b` ON a.`from` = b.id WHERE `to` = "+ connection.escape(id) +" ORDER BY a.`id` DESC LIMIT "+limit, function(err, rows){
                connection.release();
                if (err) {
                  console.log(err);
                  //socketServer.sockets.socket(userSockets[id]).emit("getMessageBlockCallback", {error: true, err: err});
                  //socket.emit("getMessageBlockCallback", {error: true, err: err});
                }else{
                    //console.log("===============================================");
                   // console.log(rows);
                    //socketServer.sockets.socket(userSockets[id]).emit("getMessageBlockCallback", {error: false, rows: rows});
                    socket.emit("getMessageBlockCallback", {error: false, rows: rows});
                }
            });
       });
    },
    getAppointmentsBlock: function(id, limit){
        pool.getConnection(function(err, connection) {
            if (err) {
                console.log(err);
            }
            //console.log("SELECT a.`on_time`, a.id, a.id_user, a.with_user, b.first_name, b.last_name, b.id as id_account, b.avatar FROM `appointments` as `a` left join `accounts` as `b` ON (a.`with_user` = b.id OR a.`id_user` = b.id) WHERE (`id_user` = "+connection.escape(this.id)+" OR `with_user` = "+connection.escape(this.id)+") ORDER BY a.`on_time` ASC LIMIT "+limit);
            connection.query("SELECT a.`on_time`, a.id, a.id_user, a.with_user, b.first_name, b.last_name, b.id as id_account, b.avatar FROM `appointments` as `a` left join `accounts` as `b` ON (a.`with_user` = b.id OR a.`id_user` = b.id) WHERE (`id_user` = "+connection.escape(id)+" AND `with_user` = "+connection.escape(id)+") OR (`with_user` = "+connection.escape(id)+" OR `id_user` = "+connection.escape(id)+") ORDER BY a.`on_time` ASC LIMIT "+limit, function(err, rows){
                connection.release();
                if (err) {
                  console.log(err);
                  socket.emit("getAppointmentsBlockCallback", {error: true, err: err});
                }else{
                   //console.log(rows);
                    socket.emit("getAppointmentsBlockCallback", {error: false, rows: rows});
                }
            });
        });
    },
    getAvatar: function(){
        
    },
    getEmail: function(){
        
    },
    getUser: function(id){
        pool.getConnection(function(err, connection) {
            connection.query("SELECT * FROM `"+this.table+"` WHERE `id` = '"+id+"'", function(err, rows, fields){
                connection.release();
                if (err) {
                  console.log(err);
                  socket.emit("getUserInfoCallback", {error: true, err: err});
                }else{
                    //console.log(rows);
                    console.log("sending callback");
                    socket.emit("getUserInfoCallback", {error: false, rows: rows});
                }
            });
        });
    },
    isAppointAvailable: function(data){
        pool.getConnection(function(err, connection) {
            connection.query("SELECT COUNT(*) as count FROM `appointments` WHERE `on_time` = "+connection.escape(data.appointmentTime)+" AND ( `id_user` = '"+data.id_visitor+"' OR  `with_user` = '"+data.id_visitor+"')", function(err, rows, fields){
                connection.release();
                if (err) {
                  console.log(err);
                  socket.emit("isAppointAvailableCallback", {error: true, err: err});
                }else{
                    //console.log(rows);
                    socket.emit("isAppointAvailableCallback", {error: false, rows: rows});
                }
            });
        });
    },
    removeAppointment: function(id){
        pool.getConnection(function(err, connection) {
            connection.query("DELETE FROM `appointments` WHERE `id` = "+connection.escape(id), function(err, result){
                connection.release();
                if (err) {
                  console.log(err);
                  socket.emit("removeAppointmentCallback", {error: true, err: err});
                }else{
                    socket.emit("removeAppointmentCallback", {error: false, rows: result});
                }
            });
        });
    },
    getAppointments: function(){
        
    },
    setAppointment: function(id_owner, id_user, time){
        
    },
    getAvailableDays: function(){
        
    },
    getAvailableTimes: function(){
        
    }
    
}

});

function convertDate(date){
    month = date.getMonth() + 1;
    return date.getFullYear() + "-" + month + "-" +date.getDate();
}
//socketServer.on("/getUserInfo", function(params, fn){
//	fn(params.name);
//});
//var connection = mysql.createConnection({
//  host     : 'localhost',
//  user     : 'me',
//  password : 'secret',
//  database : 'my_db'
//});



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
