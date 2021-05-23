var o = {
	init: function(){
		this.diagram();
	},
	random: function(l, u){
		return Math.floor((Math.random()*(u-l+1))+l);
	},
	diagram: function(){
		var r = Raphael('diagram', 600, 600),
			rad = 73,
			defaultText = '78',
			speed = 250;
		
		r.circle(300, 300, 85).attr({ stroke: 'none', fill: 'none' });
		
		var title = r.text(300, 273, defaultText).attr({
			font: '55px Arial',
			fill: '#4bdd32'
		}).toFront();
		
		var title2 = r.text(340, 285, '%').attr({
			font: '20px Arial',
			fill: '#4bdd32'
		}).toFront();
		
		var title = r.text(140, 120, '54').attr({
			font: '55px Arial',
			fill: '#358fc1'
		}).toFront();
		
		var title2 = r.text(180, 132, '%').attr({
			font: '20px Arial',
			fill: '#358fc1'
		}).toFront();
		
		var title = r.text(460, 120, '24').attr({
			font: '55px Arial',
			fill: '#358fc1'
		}).toFront();
		
		var title2 = r.text(500, 132, '%').attr({
			font: '20px Arial',
			fill: '#358fc1'
		}).toFront();
		
		var title2 = r.text(304, 313, '4,902,090pcs').attr({
			font: '30px Arial',
			fill: '#000000'
		}).toFront();
		
		/*
			
		$('.gettxt').find('.txts').each(function(i){
			var t = $(this), 
				locateX = t.find('.locateX').val(),
				locateY = t.find('.locateY').val();
			var text = t.find('.text').text();
				fontT = t.find('.fontT').text();
				fcolor = t.find('.fcolor').text();
			
			
			var tx = r.text(locateX, locateY, text).attr({
				font: fontT,
				fill: fcolor
			}).toFront();
			
		});
		
		*/
		
		
		r.customAttributes.arc = function(value, color, rad, beg){
			var v = 3.6*value,
				alpha = v == 360 ? 359.99 : v,
				random = beg ,//o.random(91, 240),
				a = (random-alpha) * Math.PI/180,
				b = random * Math.PI/180,
				sx = 300 + rad * Math.cos(b),
				sy = 300 - rad * Math.sin(b),
				x = 300 + rad * Math.cos(a),
				y = 300 - rad * Math.sin(a),
				path = [['M', sx, sy], ['A', rad, rad, 0, +(alpha > 180), 1, x, y]];
			return { path: path, stroke: color }
		}
		 r.path().attr({ arc: [75, '#ff0000', 120,'-135'], 'stroke-width': '20' });

		
	}
}
$(function(){ o.init(); });
//Ò»Á÷ËØ²ÄÍøwww.16sucai.com