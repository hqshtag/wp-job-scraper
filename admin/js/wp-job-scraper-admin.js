(function($) {
	'use strict';

	$(document).ready(function() {
		$('ul.tabs li').click(function() {
			var tab_id = $(this).attr('data-tab');

			$('ul.tabs li').removeClass('current');
			$('.tab-content').removeClass('current');

			$(this).addClass('current');
			$('#' + tab_id).addClass('current');
		});
		$('#wjs-update-btn').on('click', function() {
			$('.wjs-update-button').addClass('running');
			setTimeout(() => {
				$('.wjs-update-button').removeClass('running');
				$('#wjs-update-btn').html('No updates yet');
				$('#wjs-update-btn').css('background-color', 'orange');
			}, 2500);
		});
	});
})(jQuery);

/*window.addEventListener('load', () => {
	//store the tabs variable
	var tabs = document.querySelectorAll('ul.nav-tabs > li');

	for (let i = 0; i < tabs.length; i++) {
		tabs[i].addEventListener('click', switchTab);
	}

	function switchTab(event) {
		event.preventDefault();

		document.querySelector('ul.nav-tabs li.active').classList.remove('active');
		document.querySelector('.tab-pane.active').classList.remove('active');

		var clickedTab = event.currentTarget;
		var anchor = event.target;
		var activePaneId = anchor.getAttribute('href');

		clickedTab.classList.add('active');
		document.querySelector(activePaneId).classList.add('active');
	}
});

*/
