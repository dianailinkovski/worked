var selfEasyrtcid = "";
var haveSelfVideo = false;

function disable(domId) {
    document.getElementById(domId).disabled = "disabled";
    if ($("#"+domId).hasClass("visible")) {
        $("#"+domId).removeClass("visible");
        $("#"+domId).addClass("hidden");
    }
}


function enable(domId) {
    document.getElementById(domId).disabled = "";
    if ($("#"+domId).hasClass("hidden")) {
        $("#"+domId).removeClass("hidden");
        $("#"+domId).addClass("visible");
    }
}


//messaging

function addToConversation(who, msgType, content) {
    // Escape html special characters, then add linefeeds.
    content = content.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
    content = content.replace(/\n/g, '<br />');
   // document.getElementById('conversation').innerHTML +=
    //"<b>" + who + ":</b>&nbsp;" + content + "<br />";
    html = '<div class="person1chat">'+
		'<div class="image">'+
                    '<img src="http://www.gravatar.com/avatar/64793a3f8b4f79775c16663a3e31e5a1?s=140&amp;r=x&amp;d=mm">'+
		'</div>'+
		'<p id="person1SMS">'+content+'</p>'+
	    '</div>';
    $("#receiveMessageArea").append(html);
}

function sendStuffWS(otherEasyrtcid) {
    var text = document.getElementById('sendMessageText').value;
    if(text.replace(/\s/g, "").length === 0) { // Don't send just whitespace
        return;
    }

    easyrtc.sendDataWS(otherEasyrtcid, "message",  text);
    addToConversation("Me", "message", text);
    document.getElementById('sendMessageText').value = "";
}

//messaging



function connect() {
    username = prompt("Enter your username: ", "");
  easyrtc.enableAudio(true);
  easyrtc.enableVideo(true);
  easyrtc.enableDataChannels(true);
  easyrtc.setPeerListener(addToConversation);
  //easyrtc.setRoomOccupantListener( convertListToButtons);
  
    var qs = getQueryStrings();
  var room = qs['r'];
  easyrtc.setUsername(username);
  easyrtc.joinRoom(room, null, null, null);
  easyrtc.connect(room, loginSuccess, loginFailure);
  easyrtc.setRoomOccupantListener( makeTheCallButton );

}

function makeTheCallButton (roomName, occupants, isPrimary) {
   called = false;
    for(var easyrtcid in occupants) {
        called = true;
        enable("makeCallButton");
	$(".start_call i").addClass("text-success");
	$(".start_call").each(function(){
	    $(this).on("click", function(){
		performCall(easyrtcid);
		$(".start_call").addClass("hidden");
		$(".start_call").removeClass("visible");
	    });
	});
        makeChatSendButton(easyrtcid);
    }
    if (!called) {
        $("#noConnection").fadeIn();
    }
    else{ $("#noConnection").fadeOut(); }
}

function makeTheCall (roomName, occupants, isPrimary) {
   called = false;
    for(var easyrtcid in occupants) {
        called = true;
        performCall(easyrtcid);
        makeChatSendButton(easyrtcid);
    }
    if (!called) {
        $("#noConnection").fadeIn();
    }
    else{ $("#noConnection").fadeOut(); }
}

function makeChatSendButton(easyrtcid){
    clearConnectList2();
    var otherClientDiv = document.getElementById('otherClients2');
        var button = document.createElement('button');
        button.onclick = function(easyrtcid) {
            return function() {
                sendStuffWS(easyrtcid);
            };
        }(easyrtcid);

        var label = document.createTextNode("Send");
        button.appendChild(label);
        otherClientDiv.appendChild(button);
}


function hangup() {
    easyrtc.hangupAll();
    disable('hangupButton');
    enable('callButton');
    enable('makeCallButton');
}


function clearConnectList() {
    var otherClientDiv = document.getElementById('otherClients');
    while (otherClientDiv.hasChildNodes()) {
        otherClientDiv.removeChild(otherClientDiv.lastChild);
    }
}
function clearConnectList2() {
    var otherClientDiv = document.getElementById('otherClients2');
    while (otherClientDiv.hasChildNodes()) {
        otherClientDiv.removeChild(otherClientDiv.lastChild);
    }
}

function convertListToButtons (roomName, occupants, isPrimary) {
    clearConnectList();
    for(var easyrtcid in occupants) {
        html = '<a id="link_'+easyrtcid+'"><i class="glyphicon glyphicon-user"></i> '+easyrtc.idToName(easyrtcid)+' <i class="glyphicon glyphicon-earphone"></i></a>';
        
        $("#otherClients").append(html);
        $("#link_"+easyrtcid).on("click", function(easyrtcid) {
            return function() {
                performCall(easyrtcid);
            };
        }(easyrtcid));
    }
    
    clearConnectList2();
    var otherClientDiv = document.getElementById('otherClients2');
    for(var easyrtcid in occupants) {
        var button = document.createElement('button');
        button.onclick = function(easyrtcid) {
            return function() {
                sendStuffWS(easyrtcid);
            };
        }(easyrtcid);

        var label = document.createTextNode("Msg to " + easyrtc.idToName(easyrtcid));
        button.appendChild(label);
        otherClientDiv.appendChild(button);
    }
}


function setUpMirror() {
    if( !haveSelfVideo) {
        var selfVideo = document.getElementById("selfVideo");
        easyrtc.setVideoObjectSrc(selfVideo, easyrtc.getLocalStream());
        selfVideo.muted = true;
        haveSelfVideo = true;
    }
}

function performCall(otherEasyrtcid) {
    enable("call_waiting");
    $("#call_waiting").html("Calling " + easyrtc.idToName(otherEasyrtcid));
    easyrtc.hangupAll();
    var acceptedCB = function(accepted, easyrtcid) {
	
        if( !accepted ) {
            easyrtc.showError("CALL-REJECTED", "Sorry, your call to " + easyrtc.idToName(easyrtcid) + " was rejected");
            enable('otherClients');
        }
    };

    var successCB = function() {
        if( easyrtc.getLocalStream()) {
            setUpMirror();
        }
        enable('hangupButton');
        disable('callButton');
	disable('makeCallButton');
	disable("call_waiting");
    };
    var failureCB = function() {
        enable('otherClients');
    };
    easyrtc.call(otherEasyrtcid, successCB, failureCB, acceptedCB);
    enable('hangupButton');
    disable('callButton');
    disable('makeCallButton');
    disable("call_waiting");
}


function loginSuccess(easyrtcid) {
    //disable("connectButton");
    //enable("disconnectButton");
    enable('otherClients');
    selfEasyrtcid = easyrtcid;
    document.getElementById("iam").innerHTML = "Messaging as: " + easyrtc.idToName(easyrtcid);
    easyrtc.showError("noerror", "logged in");
}


function loginFailure(errorCode, message) {
    easyrtc.showError(errorCode, message);
}

function disconnect() {
  easyrtc.disconnect();			  
  document.getElementById("iam").innerHTML = "logged out";
  //enable("connectButton");
  //disable("disconnectButton"); 
  easyrtc.clearMediaStream( document.getElementById('selfVideo'));
  easyrtc.setVideoObjectSrc(document.getElementById("selfVideo"),"");
  easyrtc.closeLocalMediaStream();
  easyrtc.setRoomOccupantListener( function(){});  
  clearConnectList();
} 


easyrtc.setStreamAcceptor( function(easyrtcid, stream) {
    setUpMirror();
    var video = document.getElementById('callerVideo');
    var video2 = document.getElementById('selfVideo');
    easyrtc.setVideoObjectSrc(video,stream);
    easyrtc.setVideoObjectSrc(video2,stream);
    enable("hangupButton");
    disable('callButton');
    disable('makeCallButton');
});



easyrtc.setOnStreamClosed( function (easyrtcid) {
    easyrtc.setVideoObjectSrc(document.getElementById('callerVideo'), "");
    easyrtc.setVideoObjectSrc(document.getElementById('selfVideo'), "");
    disable("hangupButton");
    enable("callButton");
    enable('makeCallButton');
});


var callerPending = null;

easyrtc.setCallCancelled( function(easyrtcid){
    if( easyrtcid === callerPending) {
        document.getElementById('acceptCallBox').style.display = "none";
        callerPending = false;
    }
});


easyrtc.setAcceptChecker(function(easyrtcid, callback) {
    document.getElementById('acceptCallBox').style.display = "block";
    callerPending = easyrtcid;
    if( easyrtc.getConnectionCount() > 0 ) {
        document.getElementById('acceptCallLabel').innerHTML = "Drop current call and accept new from " + easyrtc.idToName(easyrtcid) + " ?";
    }
    else {
        document.getElementById('acceptCallLabel').innerHTML = "Accept incoming call from " + easyrtc.idToName(easyrtcid) + " ?";
    }
    var acceptTheCall = function(wasAccepted) {
        document.getElementById('acceptCallBox').style.display = "none";
        if( wasAccepted && easyrtc.getConnectionCount() > 0 ) {
            easyrtc.hangupAll();
        }
        callback(wasAccepted);
        callerPending = null;
    };
    document.getElementById("callAcceptButton").onclick = function() {
        acceptTheCall(true);
    };
    document.getElementById("callRejectButton").onclick =function() {
        acceptTheCall(false);
    };
} );
