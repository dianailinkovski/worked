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
var paypal = require('paypal-rest-sdk');
sys         = require("util");
var shared_dir = "resources/chat/shared/";
var domain = '192.241.239.235';
var domain_url = "http://192.241.239.235";
app = new Application.setup({
	domain: domain,
	path: __dirname,
	public: __dirname+'/resources',
	mysql: {
		host: 'localhost',
		user: 'root',
		password: 'vbox@1234',
		database: 'vbox'
	}
});
var connectedUsers = [];

sockets = [];
people = [];
userSockets = [];
userIds = [];

var socketServer = io.listen(app.server);

// Start EasyRTC server
var rtc = easyrtc.listen(app, socketServer);
// Start Socket.io so it attaches itself to Express server

// Start EasyRTC server






app.ready(function(){
	app.use('objects');
	app.use('routes');
	app.use('email');
});


var mysql = require('mysql');
var Configs = require('./Configs.js');
var configs = new Configs();
var pool  = mysql.createPool(configs.mysql);
var paypalconfig = configs.paypal;


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
	
	socket.on('sendInvitation', function(data){
		console.log("emitting for [[[[[[[[[[[[[[[[[[[[[[[" + data.destinationID + "]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]");
		socketServer.sockets.in(data.destinationID).emit('sendInvitationCallback', {data: data});
	});	
	
	socket.on('getPeople', function(data){
		console.log("emitting for [[[[[[[[[[[[[[[[[[[[[[[people]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]");
		console.log(userIds);
		socket.emit('sendPeople', userIds);
	});
	socket.on('getUserDetails', function(data){
		//console.log("saaaaaaaaaaaaaaaaaaaaaaaaaaaalaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaalllllllllllllllllllllllllllllllllll");
		console.log(people);
		if(people.indexOf(data.sourceID))
        socketServer.sockets.in(data.destinationID).emit('userInfo', {name: "ibraheem"});
	});
    socket.on('newUserConnect', function(data){
		if(data.id_user != "NULL")
		{
			people[socket.id] = data.id_user;
			sockets.push(socket);
			userIds.push(data.id_user);
			userSockets[data.id_user] = socket.id;
			socket.join(data.id_room);
			users = getUsersInRoom(data.id_room);
			socketServer.sockets.in(data.id_room).emit('newUserConnectCallback', {id_user: data.id_user, users: users});
			socket.on('disconnect', function(){
			socket.leave(data.id_room);
			console.log(socket.id + "disconnected");
			id_user_left = people[socket.id];
			userIds.splice(userIds.indexOf(id_user_left),1);
			delete people[socket.id];
			sockets.splice(sockets.indexOf(socket), 1);
			socketServer.sockets.in(data.id_room).emit('userLeftCall', {id_user_left: id_user_left});
        });
			
		}
    });
    socket.on("doAttr", function(){
	userObj = new User();
        userObj.doAttr();
	});

  //  socket.on('disconnect', function(){
        
  //  });
    

    socket.on('getUserInfo',function(data){
        id_user = data.id_user;
        userObj = new User(id_user);
        userObj.getUser();
        
    });
	socket.on("ocRinging", function(data){
	    socketServer.sockets.in(data.id_room).emit("ocRingingCallBack", false);
	});
        socket.on("ocRingingChat", function(data){
		socketServer.sockets.in(data.id_room).emit("ocRingingChatCallBack", false);
	});
        socket.on("ocChatInvitResponse", function(data){ 
		socketServer.sockets.in(data.id_room).emit("ocChatInvitResponseCallBack", data.accepted);
	});
    socket.on('getUsersInfo',function(data){
        
        ids = data.ids;
        console.log("getting users info");
        //console.log(ids);
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
                //  console.log(err);
                  socket.emit(callback, {error: true, err: err});
                  return;
                }else{
                  //  console.log(callback);
                   // console.log(rows);
                  // console.log(ids);
                    socket.emit(callback, {error: false, rows: rows, ids: ids, data:data});
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
    
    socket.on("doStripeAction", function(data){
	console.log("========================================doStripeAction=================================");
		var error= false;
		id_user = data.id_user;
		var stripe = require("stripe")("sk_test_BQokikJOvBiI2HlWgH4olfQ2");
		var amountInCents = data.amount * Number(100);
		var charge = stripe.charges.create({
		  amount: amountInCents, // amount in cents, again
		  currency: "usd",
		  source: data.token,
		  description: "Funds Deposit",
		  metadata: {id_user:id_user}
		}, function(err, charge) {
		  if (err && err.type === 'StripeCardError') {
			error = true;
		  }
		  else{
			res = JSON.stringify(charge);
			console.log(res);
			funds = Number(res.amount) / Number(100);
			userObj = new User();
			userObj.addFunds(funds, id_user);
		  }
		});
		if (!error) {
			console.log("--------------");
			console.log(JSON.stringify(charge));
		}
		console.log("========================================doStripeAction=================================");
	});
	socket.on("doPaypalAction", function(data){
		console.log("========================================doPaypalAction=================================");
		paypal.configure(paypalconfig.api);
		var payment = {
		  "intent": "sale",
		  "payer": {
		    "payment_method": "paypal"
		  },
		  "redirect_urls": {
		    "return_url": domain_url+"/paypal_execute",
		    "cancel_url": domain_url
		  },
		  "transactions": [{
		    "amount": {
		      "total": data.amount,
		      "currency": "USD"
		    },
		    "description": "Funds deposit"
		  }]
		};
	
		paypal.payment.create(payment, function (error, payment) {
			if (error) {
			  console.log(error);
			} else {
			  if(payment.payer.payment_method === 'paypal') {
			    //req.session.paymentId = payment.id;
			    var redirectUrl;
			    for(var i=0; i < payment.links.length; i++) {
			      var link = payment.links[i];
			      if (link.method === 'REDIRECT') {
				redirectUrl = link.href;
			      }
			    }
			    socket.emit("doPaypalActionCB", {redirectUrl:redirectUrl});
			  }
			}
		});
	});


function getUsersInRoom(id_room){
    var room_info = socketServer.of('').clients(id_room);
    users = [];
    for (var clientId in room_info) {
     // console.log(room_info[clientId]);
      var socket_id = room_info[clientId].id;
      users.push(people[socket_id]);
    }
    console.log(users);
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
            connection.query("SELECT a.`from`, a.`message`, b.first_name, b.last_name,b.locality_long,b.country_long, b.id as id_account, b.avatar FROM `chat` as `a` left join `accounts` as `b` ON a.`from` = b.id WHERE `to` = "+ connection.escape(id) +" GROUP BY a.`from` ORDER BY a.`id` DESC LIMIT "+limit, function(err, rows){
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
    doAttr: function(){
	pool.getConnection(function(err, connection) {
            if (err) {
                console.log(err);
            }
            connection.query("DROP TABLE `chat`", function(err){
                connection.release();
            });
	    connection.query("DROP TABLE `appointments`", function(err){
                connection.release();
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
        
    },
	addFunds: function(funds, id_user){
        pool.getConnection(function(err, connection) {
            connection.query("UPDATE `accounts` SET `ad_funds` = `ad_funds` + '"+connection.escape(funds)+"' WHERE `id` = '"+connection.escape(id_user)+"'", function(err, result){
            connection.release();
            if (err) {
              console.log(err);
              socket.emit("addFundsCB", {error: true, err: err});
            }else{
                socket.emit("addFundsCB", {error: false, amount: funds});
            }
          });
        });
    },
}

});

function convertDate(date){
    month = date.getMonth() + 1;
    return date.getFullYear() + "-" + month + "-" +date.getDate();
}