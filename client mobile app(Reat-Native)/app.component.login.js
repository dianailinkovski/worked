"use strict";
/**
 ==================================================================================
 Description:       desc
 Creation Date:     1/14/16
 Author:            Glib and kostya
 ==================================================================================
 Revision History
 ==================================================================================
 Rev    Date        Author           Task                Description
 ==================================================================================
 1      1/14/16    Glib and Osip         TaskNumber          Created
 ==================================================================================
 */
var React = require('react-native');
var server_auth = require('./server_auth');
var {
    AppRegistry,
    StyleSheet,
    Text,
	Image,
    View,
    TextInput,
	TouchableOpacity,
    } = React;

var LoginComponent = React.createClass({
    getInitialState: function() {
        return {username:"TestUser123", password:"TestUser12", organization:"prologic",forgetview:"no",username1:"",disable_submit:true};
    },
    perform_login: function() {
        var context = this;
        server_auth.authenticate_username_password("http://prologic.xenforma.com:1337/", this.state.username, this.state.password).then(() => {
            if (this.props.on_auth_successful) {
			
                this.props.on_auth_successful();
            }
        }).catch((err) => {
            if (err == "INVALID_CREDENTIALS") {
                context.setState({error:"Invalid credentials"});
            }
            else {
                context.setState({error:err});
            }
			
        });
		this.setState({forgetview:"no"});
    },
	perform_submit: function() {// please your fill infora submit click
        var context = this;		
		 var request = {};
        request.secret_answer = this.state.secret_answer;       
        if (this.state.username1.indexOf("@") == -1) {
            request.login_name = this.state.username1;			
        }
        else {
            request.email_address = this.state.username1;
        }

		 fetch("http://prologic.xenforma.com:1337/api/auth/reset_password", {
                method: "POST",				
                headers: {   
                "Accept":"application/json",
                "Content-Type":"application/json"
                },
            body:JSON.stringify(request)
            })
		 .then((response) => {
			 response.json().then(function(json_response) {
				
				 if (json_response.success ) {  //if username exist ,success true.
				 }
				 else{
					 context.setState({error1: json_response.message});
				 }
				 
			 });
		 }).catch(function(err) {				    
        });
		
        
    },
	on_submit_blue: function(){ //text change event
	
		var lookup = {};
         if (this.state.username1.indexOf("@") == -1) {
            lookup.login_name = this.state.username1;			
        }
        else {
            lookup.email_address = this.state.username1;
        }		
		var context = this;
         fetch("http://prologic.xenforma.com:1337/api/auth/get_secret_question", {
                method: "POST",				
                headers: {           
                "Accept":"application/json",
                "Content-Type":"application/json"
                },
           body:JSON.stringify(lookup)
            })
		 .then((response) => {
			 response.json().then(function(json_response) {
					
				 if (json_response.success && json_response.secret_question) {  //if username exist ,success true.
                    
					context.setState({secret_question:json_response.secret_question, disable_submit:false});
					context.setState({secret_label:'Secret Question:  '});
					
					this.perform_submit();
                }
				else if(!json_response.success){					
					
					context.setState({error1:json_response.message});
				}
                else {
                    context.setState({secret_question:undefined});
                }
				 
			 }).catch(function(err) {
				  var response = err.responseJSON;
				  context.setState({error1:response.message});
				
			 });	 
			
			 
		 }).catch(function(err) {
              var response = err.responseJSON;
			  context.setState({error1:response.message});
				         
        });
	},
	forget_password:function(){ //kostya.b
		
		this.setState({forgetview:"yes"});
				
	}, //kostya.b
	error_clear:function(){
		this.setState({error:""});
		
	},
    render: function() {
       if(this.state.forgetview=="no"){
	    var error =<Text>{""}</Text>;
        if (this.state.error) {
            error = <View style={styles.viewerror}><Text style={styles.errortxt}>{this.state.error}</Text>
			 </View>;
        }
        return (
            <View style={{ flex: 1, justifyContent: 'center',  alignItems: 'center',   backgroundColor:'#B7C5FF',
               }} >
				<View style={styles.top_container}>
				    <Image
					source={{uri: "http://prologic.xenforma.com:1337/images/logo.png"}}					
					style={{ width:280, height: 80,padding:5,top:30, }}
				  />
				</View>
				
			    <View style={styles.container}>				
			    
				   <TouchableOpacity  onPress={this.error_clear}>
				   {error}
				   </TouchableOpacity>			   
				<View style={{ top:15,borderRadius: 2,borderStyle: 'solid',borderWidth:1,borderColor:'#D5D5D5',}}>				  
                <TextInput onChangeText={(text) => this.setState({username:text})} style={styles.usertxt} value={this.state.username} placeholder="Username" onSubmitEditing={this.perform_login}/>
                </View>
				<View style={{ top:15,borderRadius: 2,borderStyle: 'solid',borderWidth:1,borderColor:'#D5D5D5',}}>	
				<TextInput onChangeText={(text) => this.setState({password:text})} style={styles.passtxt} secureTextEntry={true} value={this.state.password} placeholder="Password" onSubmitEditing={this.perform_login}/>
			      </View>
				<TouchableOpacity style={styles.button} onPress={this.perform_login}>
			     <Text style={{fontSize:12,textAlign:'center',justifyContent :'center',borderWidth:2,color :'#FFFFFF',}}>Login</Text>
			    </TouchableOpacity>
				
			    <TouchableOpacity style={styles.forgetbutton} onPress={this.forget_password}>
			     <Text style={{fontSize:12,textAlign:'center',color :'#4AA6E5',}}>Forget my password?</Text>
			    </TouchableOpacity>
				
               </View>	
			   
				<View style={styles.bottom_container}>
				</View>	
				
            </View>
        );
	   }
	   else{
		 var secret_question = <Text>{""}</Text>;
		 var error1 =<Text>{""}</Text>;
		 if (this.state.error1) {
            error1 = <View style={styles.viewerror1}><Text style={styles.errortxt1}>{this.state.error1}</Text>
			 </View>;
        }
        if (this.state.secret_question) {			
            secret_question =<View style={{borderRadius: 2,top:10,borderStyle: 'solid',borderWidth:1,borderColor:'#D5D5D5',alignSelf:'center',alignItems:'center'}}>	
			         		 <TextInput onChangeText={(text) => this.setState({secret_answer:text})} style={styles.usertxt1} value={this.state.secret_answer} placeholder="Secret Answer" />
				            </View>;           		  
        }
		  
		    return(
			   <View style={{ flex: 1, justifyContent: 'center',  alignItems: 'center', backgroundColor:'#B7C5FF',}} >
				   <View style={styles.top_container}>
						<Image
						source={{uri: "http://prologic.xenforma.com:1337/images/logo.png"}}					
						style={{ width:280, height: 50,padding:5,alignSelf:'stretch',top:30, }}
					  />
					</View>
					 <View style={styles.container}>
						 <TouchableOpacity  onPress={this.error_clear}>
				          {error1}
				        </TouchableOpacity>		
						<Text style={{top:3,color:'#494949'}}>{"Please fill in your information"}</Text>
						
						<View style={{ top:5,borderRadius: 2,borderStyle: 'solid',borderWidth:1,borderColor:'#D5D5D5',}}>				  
						  <TextInput onChangeText={(text) => this.setState({username1:text})} style={styles.usertxt1} value={this.state.username1} placeholder="Username" onSubmitEditing={this.on_submit_blue}/>
						</View>		
						
						
						<View  style={{top:5}}>
						    <View style={styles.container_answer}>
							<Text style={{color:'#494949',fontWeight:'bold'}}>{this.state.secret_label}</Text>
							<Text style={{color:'#494949',fontSize:12,}}>{this.state.secret_question}</Text>
							
							</View>

						  {secret_question}
						 </View>	
						 
						<TouchableOpacity style={styles.button1} onPress={this.perform_submit}>
							<Text style={{fontSize:12,textAlign:'center',justifyContent :'center',borderWidth:2,color :'#FFFFFF',}}>Submit</Text>
						</TouchableOpacity>
						
					</View>
					<View style={styles.bottom_container}>
					</View>	
				
			</View>
			)
		   
	   }
    }
});

var styles = StyleSheet.create({
    top_container: {
        flex: 1,
        justifyContent: 'center',
        alignItems: 'center',       
		flexWrap :'nowrap',		
		
    },
	bottom_container: {
        flex: 1,
       
        alignItems: 'center',
        backgroundColor: '#00FCFF',
		flexWrap :'nowrap',		
		marginBottom :10,
	   justifyContent: 'flex-end',
		
    },
	banner:{
		    flex: 1,
            borderRadius: 5,
            borderStyle: 'solid',
			borderWidth:5,
			borderColor:'#FFFFFF',  
			backgroundColor:'#F4F4F4',
			height:20,
			alignSelf :'center',
            alignItems: 'center',
			
	},
	container: {
          
            flex: 1,
            borderRadius: 5,
            borderStyle: 'solid',
			borderWidth:5,
			borderColor:'#FFFFFF',
            paddingLeft: 15,           
            paddingRight: 15,           
            paddingBottom: 20,           
			backgroundColor:'#F4F4F4',
			height:230,
			alignSelf :'center',
            alignItems: 'center',
			justifyContent: 'space-around',	
			
    },
	container_answer: {          
            flex: 2,   			
			borderColor:'#FF442F', 
			alignSelf :'center',
            alignItems: 'center',			
			flexDirection:'row',		
    },
    welcome: {
        fontSize: 20,
        textAlign: 'center',
        margin: 10,
    },
    instructions: {
        textAlign: 'center',
        color: '#333333',
        marginBottom: 5,
    },
	usertxt:{			
		paddingLeft:10,
		backgroundColor :'#FAFFBD',
		height:24,	
		fontSize:13,
		paddingBottom:1,
		paddingTop:1,	
		borderRadius:3,	
		width:230		
	},
	usertxt1:{			
		paddingLeft:10,
		backgroundColor :'#FFFFFF',
		height:24,	
		fontSize:13,
		paddingBottom:1,
		paddingTop:1,	
		borderRadius:3,	
		width:230		
	},
	passtxt:{	
		backgroundColor :'#FAFFBD',
		height:24,	
		paddingLeft:15,
        fontSize:13,
		paddingBottom:1,
		paddingTop:1,
		borderRadius:3,
		width:230	
	
	},	
	button:{
		top:20,
		padding:1,
		borderWidth:0,
		borderRadius:2,
		width:230,
		height:23,
		backgroundColor:'#427FED',
	},
	button1:{
		top:15,
		padding:1,
		borderWidth:0,
		borderRadius:2,
		width:230,
		height:24,
		backgroundColor:'#427FED',
	},
	forgetbutton:{
		top:15,
		padding:3,
		borderWidth:0,
		borderRadius:3,
		width:250,
		height:25,
		
	},
	viewerror:{
		padding:1,
		top:10,
		  width:280,
		  backgroundColor:"#E46F61",
		  borderLeftWidth:7,
		  borderLeftColor:"#DF5138",
	},
	viewerror1:{		
		top:10,
		  width:280,
		  backgroundColor:"#E46F61",
		  borderLeftWidth:7,
		  borderLeftColor:"#DF5138",
		  padding:1,
	},
	errortxt:{	
		padding:1,	
		 textDecorationStyle: 'solid',	 	
		 fontSize: 12,
		 height:44,
		 color :'#FFFFFF',
		 paddingLeft:5,		
		 marginLeft:5,
		textAlign:'left',
		justifyContent :'center',
		
	},
	errortxt1:{		
			
		 textDecorationStyle: 'solid',	 	
		 fontSize: 14,
		 height:44,
		 color :'#FFFFFF',
		 paddingLeft:5,			 
		 marginLeft:5,
		textAlign:'center',
		justifyContent :'center',
		
	}
	
});

AppRegistry.registerComponent('LoginComponent', () => LoginComponent);
module.exports = LoginComponent;