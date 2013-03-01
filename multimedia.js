/*
---

script: String.Slugify.js

description: Extends the String native object to have a slugify method, useful for url slugs.

license: MIT-style license

authors:
- Stian Didriksen
- Grzegorz Leoniec

...
*/

(function()
{
	String.implement(
	{
		slugify: function( replace )
		{
			if( !replace ) replace = '-';
			var str = this.toString().tidy().standardize().replace(/[\s\.]+/g,replace).toLowerCase().replace(new RegExp('[^a-z0-9'+replace+']','g'),replace).replace(new RegExp(replace+'+','g'),replace);
			if( str.charAt(str.length-1) == replace ) str = str.substring(0,str.length-1);
			return str;
		}
	});
})();

/*
map point class
*/
var MapPoint = new Class({
	Implements: [Options],
	initialize: function(id, lat, lng, fields) {
		this.id = id;
		this.lat = lat;
		this.lng = lng;
		this.fields = fields;

		this.marker = new google.maps.Marker({
			position: new google.maps.LatLng(this.lat, this.lng)
		});

		this.infowindow = new google.maps.InfoWindow({
			content: this.infoWindowContent()
		});
	},
	infoWindowContent: function() {

		var buffer = '<h1><a href="' + this.fields.url + '">' + this.fields.name + '</a></h1>';
		buffer += '<p>' + this.fields.thumb + '</p>';

		return buffer;

	},
	addToMap: function(map) {

		this.marker.setMap(map.gmap);

		map.addPoint(this); 

	}
})
/*
 maps class
*/
var Map = new Class({

	Implements: [Options],
	options: {
		center_lat: 45,
		center_lng: 7.9,
		zoom: 0,
		cluster_title: '',
	},
	initialize: function(id, options) {
		this.canvas = $(id);
		this.setOptions(options);
		this.points = [];
		this.opened_infowindow = false;
	
		var options = {
			center: new google.maps.LatLng(this.options.center_lat, this.options.center_lng),
			zoom: this.options.zoom,
			mapTypeId: google.maps.MapTypeId.ROADMAP,
		}

		this.gmap = new google.maps.Map(this.canvas, options);
		this.cluster = new MarkerClusterer(this.gmap);
		this.cluster.setTitle(this.options.cluster_title);

		this.bounds = new google.maps.LatLngBounds();

	},
	addPoint: function(map_point) {

		this.points[map_point.id] = map_point;

		var marker = map_point.marker;
		var infowindow = map_point.infowindow;

		this.cluster.addMarker(marker);

		google.maps.event.addListener(marker, 'click', function(e) {
			if(this.opened_infowindow) this.opened_infowindow.close();
			infowindow.open(this.gmap, marker);
			this.opened_infowindow = infowindow;
		}.bind(this));

		this.bounds.extend(marker.getPosition());

		this.gmap.fitBounds(this.bounds);		
	}

})

function loadSlider(cont, anim_speed, auto_play, direction_nav, direction_nav_hide, effect, orientation, interval, pause_on_hover, slices) {

	new NivooSlider($(cont), {
			animSpeed: anim_speed,
			effect: effect,
			autoPlay: auto_play,
			directionNav: direction_nav,
			directionNavHide: direction_nav_hide,
			interval: interval,
			pauseOnHover: pause_on_hover,
			orientation: orientation,
			slices: slices
		}).addEvents({
			'onFinish': function(){
				// fired after each transition
			},
			'onStart': function(){
				// fired right before each transition
			}
	}); 
}
