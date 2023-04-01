"use strict";
/**
 ==================================================================================
 Description:       Collection of network functions.
 Creation Date:     1/11/16
 Author:            Osipe
 ==================================================================================
 Revision History
 ==================================================================================
 Rev    Date        Author           Task                Description
 ==================================================================================
 1      1/11/16     Osipe              14                 Created
 ==================================================================================
 */

var React = require('react-native');
var {NetInfo} = React;

var is_device_online = function(callback) {
    NetInfo.isConnected.fetch().done((isConnected) => {
        return callback(isConnected);
    });
};

exports.is_device_online = is_device_online;