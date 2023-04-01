cordova.define("com.phonegap.LowLatencyAudio.LowLatencyAudio", function(require, exports, module) { var exec = require('cordova/exec');

var LowLatencyAudio = {

	preloadFX: function ( id, assetPath, success, fail) {
		return exec(success, fail, "LowLatencyAudio", "preloadFX", [id, assetPath]);
	},    

	preloadAudio: function ( id, assetPath, voices, success, fail) {
		return exec(success, fail, "LowLatencyAudio", "preloadAudio", [id, assetPath, voices]);
	},

	play: function (id, duck, success, fail) {
		return exec(success, fail, "LowLatencyAudio", "play", [id, duck]);
	},

	stop: function (id, success, fail) {
		return exec(success, fail, "LowLatencyAudio", "stop", [id]);
	},

	loop: function (id, success, fail) {
		return exec(success, fail, "LowLatencyAudio", "loop", [id]);
	},

	unload: function (id, success, fail) {
		return exec(success, fail, "LowLatencyAudio", "unload", [id]);
	},

	turnOffAudioDuck: function (id, success, fail) {
    	return exec(success, fail, "LowLatencyAudio", "turnOffAudioDuck", [id]);
	}
};

module.exports = LowLatencyAudio;



});
