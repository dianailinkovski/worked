angular.module('weatherapp.services', ['ionic', 'ngAnimate'])
.factory('FriendService', function($q, $timeout) {
  
  // Some fake testing data
  var friends = [
    {
      id: 'tt0080487',
      title: 'Caddyshack',
      birth: '1980',
      description: 'An exclusive golf course has to deal with a brash new member and a destructive .',
      name: 'Harold Ramis',
      rating: 4,
	  img: 'c01.png'
    },
    {
      id: 'tt0087332',
      title: 'Ghostbusters',
      birth: '1984',
      description: 'Three unemployed parapsychology professors set up shop as a unique ghost removal service.',
      name: 'Ivan Reitman',
      rating: 4,
	  img: 'c02.png'
    },
    {
      id: 'tt0097428',
      title: 'Ghostbusters II',
      birth: '1989',
      description: 'The discovery of a massive river of ectoplasm and a resurgence of spectral activity allows the staff of Ghostbusters to revive the business.',
      name: 'Ivan Reitman',
      rating: 5,
	  img: 'c03.png'
    },
    {
      id: 'tt0107048',
      title: 'Groundhog Day',
      birth: '1993',
      description: 'A weatherman finds himself living the same day over and over again.',
      name: 'Harold Ramis',
      rating: 3,
	  img: 'c04.png'
    },
    {
      id: 'tt0116778',
      title: 'Kingpin',
      birth: '1996',
      description: 'A star bowler whose career was prematurely "cut off" hopes to ride a new prodigy to success and riches.',
      name: 'Bobby Farrelly, Peter Farrelly',
      rating: 2,
	  img: 'c05.png'
    },
    {
      id: 'tt0335266',
      title: 'Lost in Translation',
      birth: '2003',
      description: 'A faded movie star and a neglected young wife form an unlikely bond after crossing paths in Tokyo.',
      name: 'Sofia Coppola',
      rating: 4,
	  img: 'c06.png'
    },
    {
      id: 'tt0079540',
      title: 'Meatballs',
      birth: '1979',
      description: 'Wacky hijinks of counselors and campers at a less-than-average summer camp.',
      name: 'Ivan Reitman',
      rating: 3,
	  img: 'c07.png'
    },
    {
      id: 'tt0128445',
      title: 'Rushmore',
      birth: '1998',
      description: 'The king of Rushmore prep school is put on academic probation.',
      name: 'Wes Anderson',
      rating: 5,
	  img: 'c08.png'
    },
    {
      id: 'tt0096061',
      title: 'Scrooged',
      birth: '1988',
      description: 'A cynically selfish TV executive gets haunted by three spirits bearing lessons on Christmas Eve.',
      name: 'Richard Donner',
      rating: 3,
	  img: 'c09.png'
    },
    {
      id: 'tt0083131',
      title: 'Stripes',
      birth: '1981',
      description: 'Two friends who are dissatisfied with their jobs decide to join the army for a bit of fun.',
      name: 'Ivan Reitman',
      rating: 4,
	  img: 'c10.png'
    },
    {
      id: 'tt0362270',
      title: 'The Life Aquatic with Steve Zissou',
      birth: '2004',
      description: 'With a plan to exact revenge on a mythical shark that killed his partner, oceanographer Steve Zissou rallies a crew that includes his estranged wife, a journalist, and a man who may or may not be his son.',
      name: 'Wes Anderson',
      rating: 3,
	  img: 'c11.png'
    },
    {
      id: 'tt0120483',
      title: 'The Man Who Knew Too Little',
      birth: '1997',
      description: 'Murray is mistaken for a spy and must stop a plot to assasinate international leaders at a banquet.',
      name: 'Jon Amiel',
      rating: 4,
	  img: 'c12.png'
    },
    {
      id: 'tt0265666',
      title: 'The Royal Tenenbaums',
      birth: '2001',
      description: 'An estranged family of former child prodigies reunites when one of their member announces he has a terminal illness.',
      name: 'Wes Anderson',
      rating: 3,
	  img: 'c13.png'
    },
    {
      id: 'tt0103241',
      title: 'What About Bob?',
      birth: '1991',
      description: 'A successful psychiatrist loses his mind after one of his most dependent patients, a highly manipulative obsessive-compulsive, tracks him down during his family vacation.',
      name: 'Frank Oz',
      rating: 3,
	  img: 'c14.png'
    },
    {
      id: 'tt1156398',
      title: 'Zombieland',
      birth: '2009',
      description: 'A shy student trying to reach his family in Ohio, and a gun-toting tough guy trying to find the Last Twinkie and a pair of sisters trying to get to an amusement park join forces to travel across a zombie-filled America.',
      name: 'Ruben Fleischer',
      rating: 4,
	  img: 'c15.png'
    }
  ];

  return {
    all: function() {
      var deferred = $q.defer();
      $timeout(function() {
        deferred.resolve(friends);
      }, 1000);
      return deferred.promise;
    },
    allSync : function() {
      return friends;
    },  
    get: function(friendId) {
      // Simple index lookup
      for(var i=0, l = friends.length; i < l; i++) {
        if(friends[i].id == friendId) {
          return friends[i];
        }
      }
    }
  }
});