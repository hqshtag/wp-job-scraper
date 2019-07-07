(function($) {
	'use strict';
	console.log('hello there');
	let userData = secretData.userAuth;
	$.ajax({
		url: 'https://data.usajobs.gov/api/search?DatePosted=1&ResultsPerPage=1',
		headers: {
			//Host: 'data.usajobs.gov',
			//'User-Agent': USAJOBSuserAcess.email,
			'Authorization-Key': userData.key
		},
		type: 'GET',
		success: function() {
			if (document.querySelector('.wjs-usajobs-title')) {
				document.querySelector('.wjs-usajobs-auth').lastChild.firstElementChild.style.borderBottom =
					'4px solid #4BB543';
				document.querySelector('.wjs-usajobs-auth').nextSibling.lastChild.firstElementChild.style.borderBottom =
					'4px solid #4BB543';
			}
			//console.log('200');
		},
		error: function() {
			if (document.querySelector('.wjs-usajobs-title')) {
				document.querySelector('.wjs-usajobs-auth').lastChild.firstElementChild.style.borderBottom =
					'4px solid #FFBABA';
				document.querySelector('.wjs-usajobs-auth').nextSibling.lastChild.firstElementChild.style.borderBottom =
					'4px solid #FFBABA';
			}
			//console.log('401');
		}
	});
	let timer = secretData.settings.timer;
	if (timer) {
		var x = setInterval(function() {
			// Get today's date and time
			var now = new Date().getTime();

			// Find the distance between now and the count down date
			var distance = timer * 1000 + 86400000 - now; //86400000 = 24 hours in ms

			// Time calculations for days, hours, minutes and seconds
			//var days = Math.floor(distance / (1000 * 60 * 60 * 24));
			var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
			var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
			var seconds = Math.floor((distance % (1000 * 60)) / 1000);

			// Display the result in the element with id="demo"
			let aff = hours + ':' + minutes + ':' + seconds + ' until next update';
			$('.wjs-timer').html('<span id="timer">' + aff + '</span>');
			//console.log(aff);

			// If the count down is finished, write some text
			if (distance <= 0) {
				clearInterval(x);
				console.log('done');
			}
		}, 1000);
	}
})(jQuery);
