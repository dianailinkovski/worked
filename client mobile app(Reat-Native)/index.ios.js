"use strict";
/**
 ==================================================================================
 Description:       desc
 Creation Date:     ${DATE}
 Author:            ${USER}
 ==================================================================================
 Revision History Osipe
 ==================================================================================
 Rev    Date        Author           Task                Description
 ==================================================================================
 1      ${DATE}     ${USER}          TaskNumber          Created
 ==================================================================================
 */

var React = require('react-native');
var synchronization = require('./synchronization.js');
var server_auth = require('./server_auth');
var LoginComponent = require('./app.component.login');
var SideBarSection = require('./app.component.side-bar');
import Swiper from 'react-native-swiper'
var {
  AppRegistry,
  StyleSheet,
  Text,
  View,
  Image,
  TouchableOpacity,
  DrawerLayoutAndroid,
  Dimensions,
  ToolbarAndroid,ListView,
  TextInput,
} = React;
var DRAWER_WIDTH_LEFT = 56;
var XenformaMobileSync = React.createClass({
    getInitialState: function() {
        var context = this;		 
        server_auth.is_user_authenticated().then(() => {
			
            context.start_synchronization();
			
        }).catch((err) => {
            if (err == "NOT_LOGGED_IN") {
                context.setState({login_required:true})
            }
            else {
                context.setState({data:"Error has occurred: " + err + " Please reload the application."});
            }
        });
	
        return {data:"Loading...",};
    },
    start_synchronization: function() {		
        var context = this;
		console.log("auth success after start sysnchrozation");
        synchronization.synchronize()
            .then(function() {
                context.setState({data:"Synchronization is now complete"});				
				context.setState({load:true});
            })
            .catch(function(err) {
                if (err == "SYNC_IN_PROGRESS") {
                    context.setState({data:"ERROR: Synchronization is already in progress"});
                }
                else if (err == "DEVICE_IS_OFFLINE") {
                    context.setState({data:"ERROR: Device is offline"});
                }
                else {
                    context.setState({data:"ERROR: " + JSON.stringify(err)});
                }
            });
		
        this.setState({login_required:false, data:"Synchronization is in progress..."});
    },
	_renderNavigation: function() {
   
    return (
      <View style={styles.container}>
        <ToolbarAndroid
          logo={require('./images/right_logo.jpg')}
          navIcon={require('./images/right_logo.jpg')}
          onIconClicked={() => this.drawer.openDrawer()}
          style={styles.toolbar}
          title="my sample"
        />
       
      </View>
    );
  },
  onActionSelected: function(position) {
	  this.drawer.openDrawer();  
  },
  onActionclose:function(rowID,rowData){  
    this.setState({itemcont:rowData})  
	this.drawer.closeDrawer(); 
  },
   navigationView:function(){
		return(	<SideBarSection on_close_Event={this.onActionclose}/>);
   },  
    render: function() {
        if (this.state.login_required) {			
            return (<LoginComponent on_auth_successful={this.start_synchronization.bind(null,this)} />);
        }
		if(this.state.load){		
			
			return (		
			   <DrawerLayoutAndroid
				  drawerWidth={300}
				  			 
				  drawerPosition={DrawerLayoutAndroid.positions.Left}
				  ref={(drawer) => { this.drawer = drawer; }}				
				   renderNavigationView={this.navigationView}>
				 <View style={styles.container}>
				   <ToolbarAndroid
				  logo={{uri: "http://prologic.xenforma.com:1337/images/logo.png"}}
				  title=""				 
                  navIcon={ require('./images/back.png')}
				   style={styles.toolbar}
				   onIconClicked={() => this.drawer.openDrawer()}
				  actions={[{title: 'Settings', icon: require('./images/title.png'), show: 'always'}]}
				   onActionSelected={this.onActionSelected}
				   /> 	
					<Text>{this.state.itemcont}</Text>				   
				 </View >
				 
				</DrawerLayoutAndroid>
			
			);
		}
		else{
			return (
				<View style={styles.container_welcome}>  
					<Text style={styles.welcome}>{this.state.data}</Text>
				</View>
			);
		}
    }
});

var styles = StyleSheet.create({
    container: {
        flex: 1,       	
        backgroundColor: '#E6E6E6',		
    }, 
	 toolbar: {
    backgroundColor: '#2DC3E8',
    height: 50,
  },
	container_welcome: {
        flex: 1,
		justifyContent: 'center',
		alignItems: 'center',
        backgroundColor: '#2DC3E8',
		
		
    },
	searchRow: {
    backgroundColor: 'red',
    paddingTop: 5,
    paddingLeft: 5,
    paddingRight: 5,
    paddingBottom: 5,
	
  },
  searchTextInput: {
    backgroundColor: 'white',
    borderColor: '#cccccc',
    borderRadius: 3,
    borderWidth: 1,
    paddingLeft: 8,
	height:30,
  },
	container_main: {
        flex: 1,
		top:37,
        justifyContent: 'center',
		alignItems: 'center',   
		backgroundColor: 'green',		
    },
	nav_bar_content:{
		flexDirection:'row',
		flex:1,height:35,
		justifyContent: 'space-between',
         alignItems: 'stretch',
		 flexWrap :'nowrap', 
		 backgroundColor: '#2DC3E8',
	},
	bottom_container: {
        flex: 1,       
        alignItems: 'center',
        backgroundColor: '#00FCFF',
		flexWrap :'nowrap',		
		marginBottom :10,
	   justifyContent: 'flex-end',
		
    },
    welcome: {
        fontSize: 20,
        textAlign: 'center',
         margin: 10,
		color:'#FFFFFF',
		
    },
    instructions: {
        textAlign: 'center',
        color: '#333333',
        marginBottom: 5
    },wrapper: {
		 
  },
  slide1: {
    flex: 1,
    justifyContent: 'center',
    alignItems: 'center',
    backgroundColor: '#EEEEEE',
  },
  slide2: {
    flex: 1,
    justifyContent: 'center',
    alignItems: 'center',
    backgroundColor: '#EEEEEE',
  },
  text: {
    color: '#2DC3E8',
    fontSize: 30,
    fontWeight: 'bold',
  }
});

AppRegistry.registerComponent('XenformaMobileSync', () => XenformaMobileSync);
