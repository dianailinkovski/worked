cordova.define("com.gylippus.googlefit.GoogleFit", function(require, exports, module) { module.exports = {
    insertSession: function (name, successCallback, errorCallback) {
        cordova.exec(successCallback, errorCallback, "GoogleFit", "ACTION_INSERT", [name]);
    }
};
});
