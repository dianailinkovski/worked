angular.module('starter.services', [])

.factory('People', function() {

  // Here I should fill the "people" with the database's datas - It's not working
  // Here I have a post that maybe can help http://forum.ionicframework.com/t/sqlite-integration/14136/5
  var people = [{
    id: 0,
    name: 'Ben',
    lastText: 'Sparrow',
	color: 'blue'
  }, {
    id: 1,
    name: 'Jack',
    lastText: 'Harrison',
	color: 'red'
  }];

  return {
    all: function() {
      return people;
    },
    remove: function(people) {
      peoples.splice(peoples.indexOf(chat), 1);
    },
    get: function(peopleId) {
      for (var i = 0; i < peoples.length; i++) {
        if (people[i].id === parseInt(peopleId)) {
          return peoples[i];
        }
      }
      return null;
    }
  }
})