$(document).ready(function(){
    
    $('#jcmenu').hide();
   // $('#msg').hide();
    
    $('#tb-menu').on('click',function(){
        $('#jcmenu').toggle();
    });
    $("#close-message-container").on("click", function(){
        $('#msg').hide();
    });
                       
    $('#tb-messages').on('click',function(){
        $('#msg').toggle();
    });
    
    
});


function toggleFullScreen() {
        if ((document.fullScreenElement && document.fullScreenElement !== null) ||    
         (!document.mozFullScreen && !document.webkitIsFullScreen)) {
               if (document.documentElement.requestFullScreen) {  
               document.documentElement.requestFullScreen();  
             } else if (document.documentElement.mozRequestFullScreen) {  
               document.documentElement.mozRequestFullScreen();  
             } else if (document.documentElement.webkitRequestFullScreen) {  
               document.documentElement.webkitRequestFullScreen(Element.ALLOW_KEYBOARD_INPUT);  
            }
            $("#oc_video").addClass("full-screen");
        } else {  
          if (document.cancelFullScreen) {
            document.cancelFullScreen();  
          } else if (document.mozCancelFullScreen) {  
            document.mozCancelFullScreen();  
          } else if (document.webkitCancelFullScreen) {  
            document.webkitCancelFullScreen();  
          }
          $("#oc_video").removeClass("full-screen");
        }  
}