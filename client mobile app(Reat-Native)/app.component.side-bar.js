"use strict";
/**
 ==================================================================================
 Description:       desc
 Creation Date:     1/21/16
 Author:            kostya
 ==================================================================================
 Revision History
 ==================================================================================
 Rev    Date        Author           Task                Description
 ==================================================================================
 1       1/24/16     Osip          TaskNumber          Created
 ==================================================================================
 */
 var React = require('react-native');
 var server_auth = require('./server_auth');
 var ForerunnerDB = require("forerunnerdb");
 var mobile_utils = require('./mobile_utils');
 var fdb = new ForerunnerDB(); 
 var db = fdb.db('xenforma');
 var {
    AppRegistry,
    StyleSheet,
    Text,
    View,
    TextInput,
	TouchableOpacity,
	ToolbarAndroid,
	ListView,Image,
	TouchableHighlight,
	 LayoutAnimation,
    } = React;
	
	var SideBarSection = React.createClass({
	getInitialState: function() {
		
		
    var ds = new ListView.DataSource({rowHasChanged: (r1, r2) => r1 !== r2});
   
		return {		
		 dataSource: ds.cloneWithRows(this._genRows({})),thumbIndex: this._getThumbIdx(),
		 
		};
	},
	 _pressData: ({}: {[key: number]: boolean}),
   _genRows: function(pressData: {[key: number]: boolean}): Array<string> {		
		  var context = this;
		  var menu_items=[];
			
			var xenforma_menu_info = db.collection('xenforma_menu_info');	
			xenforma_menu_info.load(function (err) {
				if (!err) {
					var result=xenforma_menu_info.find({}); 				
					
					for (var i=0;i<result.length;i++) {						
						  var pressedText = pressData[i] ? ' (pressed)' : '';
						  menu_items.push(result[i].menuitem+pressedText);
					}					
					 context.setState({dataSource: context.state.dataSource.cloneWithRows( menu_items)});					 				
				}
			});
				  
	 return menu_items;
   },
	getsubdata_source:function(rowData,pressData: {[key: number]: boolean}): Array<string>{
		var xenforma_submenu_first = db.collection('xenforma_submenu_first');
		var menu_items=[];
		var context = this;
		xenforma_submenu_first.load(function (err) {
			if (!err) {
				var result=xenforma_submenu_first.find({idd:rowData});
						
                for(var i=0;i<result.length;i++){
                    var pressedText = pressData[i] ? ' (pressed)' : '';				
					 menu_items.push(result[i].submenu+pressedText);
				}	
				 context.setState({dataSource1: context.state.dataSource.cloneWithRows( menu_items)});					 				
			}
		});
		//return menu_items;
	},   
	 render:function() { 
	 
	    return(
		    <View style={{flex: 1, backgroundColor: '#fff'}}>
				   <ToolbarAndroid
				  logo={{uri: "http://prologic.xenforma.com:1337/images/logo.png"}}
				  title=""                
				   style={styles.toolbar}				 				   
				   /> 
				   <View style={styles.searchRow}>	
				   	   <Image
								style={{width:25,height:20,padding:1,right:2,top:3,}}
								source={require('./images/search.png')}
					   />					
					<TextInput
						  autoCapitalize="none"
						  autoCorrect={false}
						  clearButtonMode="always"						 
						  placeholder="Search..."
						  style={[styles.searchTextInput]}
						  testID="explorer_search"
					/>						
					</View>				
				     <ListView style={{top:20,}}
					  dataSource={this.state.dataSource}					  
					 renderRow={this._renderRow}
					 keyboardDismissMode="on-drag"
					 renderSeparator={(sectionID, rowID) => <View key={`${sectionID}-${rowID}`} style={styles.separator} />}
					/>
				</View>
		);
	},
    _renderRow: function(rowData: string, sectionID: number, rowID: number) {
    var rowHash = Math.abs(hashCode(rowData));
	 var imgSource = THUMB_URLS[rowID];	 
	 if( this.state.itemid==rowID){		 
		 var viewtxt= <ListView 
					  dataSource={this.state.dataSource1}					  
					 renderRow={this._renderRow_second}
					 keyboardDismissMode="on-drag"
					 renderSeparator={(sectionID, rowID) => <View key={`${sectionID}-${rowID}`} style={styles.separator} />}
	 		/>;
	 }
	 else{
		  var viewtxt=<Text>{""}</Text>;
	 }
    return (
      <TouchableHighlight onPress={() => this._pressRow(rowID,rowData)}>
        <View>
          <View style={styles.row}>
             <Image style={styles.thumb} source={imgSource} />
             <Text style={styles.text}>
              {rowData }
            </Text>
          </View>
		  {viewtxt}
        </View>
      </TouchableHighlight>
    );
  },
  _renderRow_second: function(rowData: string, sectionID: number, rowID: number) {
    var rowHash = Math.abs(hashCode(rowData));
	 var imgSource = THUMB_URLS[rowID];		
    return (
      <TouchableHighlight >
        <View>
          <View style={styles.row_second}>            
             <Text style={styles.text}>
              {rowData }
            </Text>
          </View>		
        </View>
      </TouchableHighlight>
    );
  },
    _pressRow(rowID: number,rowData: string): void {
		this.getsubdata_source(rowData);
		/**
		if(this.state.listflag){
			this.setState({listflag:false});			
		}
		else{
			this.setState({listflag:true});
			this.setState({itemid:rowID});
		
		}**/
		console.log(rowID);
   var config = layoutAnimationConfigs[this.state.thumbIndex % layoutAnimationConfigs.length];
    LayoutAnimation.configureNext(config);
     //this.props.on_close_Event && this.props.on_close_Event(rowID,rowData);
  },
  _getThumbIdx: function() {
    return Math.floor(Math.random() * 8);
  },
	});
	var styles = StyleSheet.create({
			container: {
				flexDirection:'row',
				flex:1,height:35,
				justifyContent: 'space-between',
				 alignItems: 'stretch',
				 flexWrap :'nowrap', 
				 backgroundColor: 'red',
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
			searchRow: {
				 top:5,borderRadius: 1,
				 borderStyle: 'solid',				
				 borderColor:'#D5D5D5',
				 paddingLeft:20,
				 paddingRight:20,
				 flexDirection:'row',
				 borderBottomWidth:3, 
			  },
			  searchTextInput: {
				  flex: 1,
				paddingLeft:10,
				backgroundColor :'#FFFFFF',
				height:24,	
				fontSize:13,
				paddingBottom:1,
				paddingTop:1,								
			  },
			   toolbar: {
				backgroundColor: '#2DC3E8',
				height: 50,
				},
				  text: {
					flex: 1,
				  },
			 row: {
				flexDirection: 'row',
				justifyContent: 'center',
				padding: 10,
				backgroundColor: '#FFFFFF',
			  },
			  row_second: {
				flexDirection: 'row',
				justifyContent: 'center',
				padding: 3,
				backgroundColor: '#Fe44F2',
			  },
			  separator: {
				height: 1,
				backgroundColor: '#CCCCCC',
			  },
			  thumb: {
				width: 28,
				height: 24,
			  },
		});

var THUMB_URLS = [
 require('./images/image/home.png'),
  require('./images/image/gear.png'),
  require('./images/image/pay.png'),
  require('./images/image/custom.png'),
  require('./images/image/setup.png'),
  require('./images/image/contact.png'),
  require('./images/image/inciden.png'),
  require('./images/image/track.png'),
  require('./images/image/work.png'),
   ];
   /* eslint no-bitwise: 0 */
var hashCode = function(str) {
  var hash = 15;
  for (var ii = str.length - 1; ii >= 0; ii--) {
    hash = ((hash << 5) - hash) + str.charCodeAt(ii);
  }
  return hash;
};
var animations = {
  layout: {
    spring: {
      duration: 750,
      create: {
        duration: 300,
        type: LayoutAnimation.Types.easeInEaseOut,
        property: LayoutAnimation.Properties.opacity,
      },
      update: {
        type: LayoutAnimation.Types.spring,
        springDamping: 0.4,
      },
    },
    easeInEaseOut: {
      duration: 300,
      create: {
        type: LayoutAnimation.Types.easeInEaseOut,
        property: LayoutAnimation.Properties.scaleXY,
      },
      update: {
        delay: 100,
        type: LayoutAnimation.Types.easeInEaseOut,
      },
    },
  },
};
var layoutAnimationConfigs = [
  animations.layout.spring,
  animations.layout.easeInEaseOut,
];
module.exports = SideBarSection;