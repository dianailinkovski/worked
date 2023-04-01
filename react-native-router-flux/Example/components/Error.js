'use strict';

var React = require('react-native');
var {View, Text, StyleSheet, Animated, Dimensions} = React;
var Button = require('react-native-button');
var Actions = require('react-native-router-flux').Actions;

var {
  height: deviceHeight
} = Dimensions.get('window');


class Error extends React.Component {
    constructor(props){
        super (props)

        this.state = {
            offset: new Animated.Value(-deviceHeight)
        }
    }

    componentDidMount() {
        Animated.timing(this.state.offset, {
            duration: 150,
            toValue: 0
        }).start();
    }

    closeModal() {
        Animated.timing(this.state.offset, {
            duration: 150,
            toValue: -deviceHeight
        }).start(Actions.pop);
    }

    render(){
        return (
            <Animated.View style={[styles.container, {backgroundColor:'rgba(52,52,52,0.5)'}, 
                                  {transform: [{translateY: this.state.offset}]}]}>
                <View style={{  width:250,
                                height:250,
                                justifyContent: 'center',
                                alignItems: 'center',
                                backgroundColor:'white' }}>
                    <Text>{this.props.data}</Text>
                    <Button onPress={this.closeModal.bind(this)}>Close</Button>
                </View>
            </Animated.View>
        );
    }
}


module.exports = Error;

var styles = StyleSheet.create({
    container: {
        position: 'absolute',
        top:0,
        bottom:0,
        left:0,
        right:0,
        backgroundColor:'transparent',
        justifyContent: 'center',
        alignItems: 'center',
    },
});

