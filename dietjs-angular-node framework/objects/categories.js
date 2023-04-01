Categories = function(){ 
	return [
	  {
	    "label": "Cars & Vehicles",
	    "options": [
	      [
	        "1",
	        "Cars"
	      ],
	      [
	        "2",
	        "Accessories &amp; Parts"
	      ],
	      [
	        "3",
	        "Motorbikes &amp; Scooters"
	      ],
	      [
	        "4",
	        "Trucks &amp; Vans"
	      ],
	      [
	        "5",
	        "Bicycles"
	      ],
	      [
	        "6",
	        "Other Vehicles"
	      ]
	    ]
	  },
	  {
	    "label": "Property",
	    "options": [
	      [
	        "7",
	        "Apartments"
	      ],
	      [
	        "8",
	        "Houses"
	      ],
	      [
	        "9",
	        "Portions"
	      ],
	      [
	        "10",
	        "Rooms &amp; Sharing"
	      ],
	      [
	        "11",
	        "Plots"
	      ],
	      [
	        "12",
	        "Commercial Property"
	      ],
	      [
	        "13",
	        "Other Property"
	      ]
	    ]
	  },
	  {
	    "label": "Electronics",
	    "options": [
	      [
	        "14",
	        "Mobile Phones"
	      ],
	      [
	        "15",
	        "Mobile Phone Accessories"
	      ],
	      [
	        "16",
	        "TV, Audio, Video &amp; Cameras"
	      ],
	      [
	        "17",
	        "Computers, Games &amp; Accessories"
	      ],
	      [
	        "18",
	        "Other Electronics"
	      ]
	    ]
	  },
	  {
	    "label": "Home & Personal Items",
	    "options": [
	      [
	        "19",
	        "Furniture"
	      ],
	      [
	        "20",
	        "Home &amp; Garden"
	      ],
	      [
	        "21",
	        "White Goods &amp; Kitchenware"
	      ],
	      [
	        "22",
	        "Clothes, Footwear &amp; Accessories"
	      ],
	      [
	        "23",
	        "Children's Items"
	      ],
	      [
	        "24",
	        "Health &amp; Beauty"
	      ],
	      [
	        "25",
	        "Other Home &amp; Personal Items"
	      ]
	    ]
	  },
	  {
	    "label": "Food & Agriculture",
	    "options": [
	      [
	        "26",
	        "Fruit"
	      ],
	      [
	        "27",
	        "Vegetables"
	      ],
	      [
	        "28",
	        "Fish"
	      ],
	      [
	        "29",
	        "Meat"
	      ],
	      [
	        "30",
	        "Crop Seeds &amp; Plants"
	      ],
	      [
	        "31",
	        "Other Food &amp; Agriculture"
	      ]
	    ]
	  },
	  {
	    "label": "Animals",
	    "options": [
	      [
	        "32",
	        "Cats"
	      ],
	      [
	        "33",
	        "Dogs"
	      ],
	      [
	        "34",
	        "Rabbits"
	      ],
	      [
	        "35",
	        "Reptiles"
	      ],
	      [
	        "36",
	        "Birds"
	      ],
	      [
	        "37",
	        "Fish"
	      ],
	      [
	        "38",
	        "Farm Animals"
	      ],
	      [
	        "39",
	        "Accessories"
	      ],
	      [
	        "40",
	        "Other Animals"
	      ]
	    ]
	  },
	  {
	    "label": "Leisure, Sport & Hobby",
	    "options": [
	      [
	        "41",
	        "Sports Equipment"
	      ],
	      [
	        "42",
	        "Movies, Books &amp; Magazines"
	      ],
	      [
	        "43",
	        "Art &amp; Collectibles"
	      ],
	      [
	        "44",
	        "Music &amp; Instruments"
	      ],
	      [
	        "45",
	        "Tickets"
	      ],
	      [
	        "46",
	        "Other Leisure, Sport &amp; Hobby"
	      ]
	    ]
	  },
	  {
	    "label": "Jobs",
	    "options": [
	      [
	        "47",
	        "Jobs - Offered"
	      ],
	      [
	        "48",
	        "Services"
	      ]
	    ]
	  },
	  {
	    "label": "Education",
	    "options": [
	      [
	        "49",
	        "Books"
	      ],
	      [
	        "50",
	        "Tuition"
	      ],
	      [
	        "51",
	        "Other Education"
	      ]
	    ]
	  },
	  {
	    "label": "Other",
	    "options": [
	      [
	        "52",
	        "Other"
	      ]
	    ]
	  }
	]
};

if(!Array.isArray) {
  Array.isArray = function(arg) {
    return Object.prototype.toString.call(arg) === '[object Array]';
  };
}

getCategory = function(CATEGORY_ID){
	var output = false;
	var categories = new Categories();
	for(var i = 0; i < categories.length; i++){
		var category = categories[i];
		for(var b = 0; b < category.options.length; b++){
			if(category.options[b][0] == CATEGORY_ID) {
				output = category.options[b][1];
				break;
			}
		}
	}
	return output;
} 
