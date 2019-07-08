/* 	import JobLoader from './lib/JobLoader';
	import JobAdder from './lib/JobAdder';
	import Parser from './lib/Parser';

	import { msToDay } from './lib/utils'; */

class JobLoader {
	constructor(email, key, number, update = false) {
		this.update = update;
		this.urls = this.update ? this.getUrlWithin(number) : this.getUrls(number);
		this.settings = {
			method: 'GET',
			headers: {
				Host: 'data.usajobs.gov',
				'User-Agent': email,
				'Authorization-Key': key
			}
		};
		this.results = [];
		this.requests = [];
	}

	init = () => {
		this.load(this.urls, this.settings);
		return Promise.all(this.requests).then((results) => {
			results.forEach((result) => {
				this.results.push(result);
			});
		});
	};

	load = (urls, settings) => {
		let req;
		urls.map((url) => {
			req = fetch(url, settings).then((res) => res.json());
			this.requests.push(req);
		});
	};

	getResults = () => {
		return this.results;
	};

	getUrls = (numberOfJobs) => {
		let x, r;
		if (numberOfJobs < 500) {
			return [ `https://data.usajobs.gov/api/search?Page=1&ResultsPerPage=${numberOfJobs}` ];
		} else {
			x = numberOfJobs / 500;
			r = numberOfJobs % 500;
		}
		let res = [];
		let page = 1;
		do {
			res.push(`https://data.usajobs.gov/api/search?Page=${page}&ResultsPerPage=500`);
			page++;
		} while (page < 20 && page <= x);
		if (r !== 0) {
			res.push(`https://data.usajobs.gov/api/search?Page=${page}&ResultsPerPage=${r}`);
		}
		return res;
	};
	getUrlWithin = (numberOfDays) => {
		return [ `https://data.usajobs.gov/api/search?DatePosted=${numberOfDays}&ResultsPerPage=142` ];
	};
}

class Parser {
	constructor(map) {
		this.readyJobAds = [];
		this.jobAds = [];
		this.typeMap = map; //obj
	}

	init = (results) => {
		this.parseResults(results);
		this.parseJobs(flatten(this.jobAds));
	};

	parseResults = (results) => {
		//parsing the fetch result to an actual job
		results.map((result) => {
			this.jobAds.push(result.SearchResult.SearchResultItems);
		});
	};
	parseJobs = (jobs) => {
		jobs.map((job) => {
			this.readyJobAds.push(this.parseJob(job));
		});
	};

	parseJob = (job) => {
		job = job.MatchedObjectDescriptor;
		let typeCode = job.PositionSchedule[0].Code;
		return {
			status: 'publish',
			type: 'job_listing',
			title: job.PositionTitle,
			'job-types': this.typeMap[typeCode],
			//'job-categories': [ 8 ],
			content: this.getContent(job),
			meta: {
				_job_location: job.PositionLocation[0].LocationName,
				_company_name: job.OrganizationName,
				_job_expires: job.ApplicationCloseDate
			},
			_job_expires: job.ApplicationCloseDate,
			geolocation_lat: job.PositionLocation[0].Latitude,
			geolocation_long: job.PositionLocation[0].Longitude,
			geolocation_formatted_address: job.PositionLocation[0].LocationName,
			geolocation_city: job.PositionLocation[0].CityName,
			geolocated: 1
		};
	};
	getContent = (job) => {
		let summary, qualification, offeringType, salMin, salMax, per, endDate, link, remuneration;
		summary = job.UserArea.Details.JobSummary;
		qualification = job.QualificationSummary;
		offeringType = job.PositionOfferingType[0].Name;
		salMin = job.PositionRemuneration[0].MinimumRange;
		salMax = job.PositionRemuneration[0].MinimumRange ? job.PositionRemuneration[0].MinimumRange : null;
		per = job.PositionRemuneration[0].RateIntervalCode ? job.PositionRemuneration[0].RateIntervalCode : null;
		endDate = job.ApplicationCloseDate;

		link = job.ApplyURI[0];

		remuneration = salMin
			? salMax ? `Starting from $${salMin} up to $${salMax}` : `starting from $${salMin}`
			: 'unkown';
		if (per) {
			remuneration += ` ${per}`;
		}
		return `<div class="job_summary jcard">
				<p> <b>Summary: </b> ${summary}</p>
			</div>  
			</br>
			<div class="job_qualification jcard">
				 <p> <b> Qualification: </b> ${qualification} </p>
			</div> 
			</br>
			<div class="job_additional informations jcard">
				  <p><b>Appointment type: </b>${offeringType} </p> </br>
				   <p><b>Salary: </b> ${remuneration} </p> </br> 
				<p><b>Available: </b> until ${endDate} </p>
			</div>
			</br>
			<div class="job_application application">
				<a href="${link}" target="_blank" class="application_button button job-apply-button"><button id="ujs-apply-button">Apply for job</button></a>
			</div>
			`;
	};
}
class JobAdder {
	constructor(api, nonce, jobs) {
		this.api = api;
		this.jobs = jobs ? jobs : [];
		this.settings = {
			method: 'POST',
			headers: {
				'Content-Type': 'application/json',
				accept: 'application/json',
				'X-WP-Nonce': nonce
			}
		};
		this.requests = [];
		this.results = [];
		this.resultStream = [];
	}

	init = () => {
		this.add(this.jobs, this.settings);
		return Promise.all(this.requests).then((results) => {
			results.forEach((result) => {
				this.results.push(result);
			});
		});
	};
	add = (jobs, settings) => {
		let req;
		jobs.map((job) => {
			req = fetch(this.api, {
				...settings,
				body: JSON.stringify(job)
			}).then((res) => res.json());
			this.requests.push(req);
		});
	};
}

const flatten = (arr) => {
	return arr.reduce(function(flat, toFlatten) {
		return flat.concat(Array.isArray(toFlatten) ? flatten(toFlatten) : toFlatten);
	}, []);
};
const msToDay = (ms) => {
	return Math.floor(ms / 1000 / 3600 / 24);
};

(function($) {
	'use strict';

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
	var now = new Date().getTime();
	let settings = secretData.settings;
	let ajax = secretData.ajax;

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

			// If the count down is finished enable the button
			if (distance < 0) {
				$('.toplevel_page_wp-job-scraper > .wp-menu-name')
					.last()
					.append('      <span class="dashicons dashicons-warning"></span>');
				$('#timer').addClass('timer-finished');
				$('.wjs-timer').html('<span id="timer">Update is Ready</span>');
				clearInterval(x);
				let daysSinceLastUpdate = msToDay(now - timer * 1000);
				if (daysSinceLastUpdate === 0) daysSinceLastUpdate = 1;
				const loader = new JobLoader(userData.email, userData.key, daysSinceLastUpdate, true);
				const parser = new Parser(settings.typeMap);
				const adder = new JobAdder(settings.url, settings.nonce);
				$(document).ready(function() {
					$('#wjs-update-btn').off('click');
					$('#wjs-update-btn').on('click', function() {
						$('.wjs-update-button').removeClass('ld-over');
						$('#wjs-update-btn').html('Downloading..');
						$('#wjs-update-btn').css('background-color', '#4BB543');
						$('.wjs-update-button').addClass('ld-over-full-inverse running');
						loader
							.init()
							.then((res) => {
								//console.log(res);
								parser.init(loader.results);
							})
							.then(() => {
								adder.jobs = parser.readyJobAds;
							})
							.then(() => {
								adder.init();
							})
							.then(() => {
								/*
								 * RESET TIMER
							 	*/
								let data = {
									action: 'reset_timer',
									security: ajax.nonce
								};
								$.ajax({
									method: 'POST',
									url: ajax.url,
									data: data,
									success: (res) => {
										console.log(res);
										window.location.reload();
									},
									fail: (res) => {
										console.log(res);
									}
								});
							});
					});
				});
			}
			$('#timer').addClass('timer-finished');
		}, 1000);
	}
	/* $(document).ready(function() {
		$('#wjs-update-btn').on('click', function() {
			let data = {
				action: 'reset_timer',
				security: ajax.nonce
			};
			$.ajax({
				method: 'POST',
				url: ajax.url,
				data: data,
				success: (res) => {
					console.log(res);
				},
				fail: (res) => {
					console.log(res);
				}
			});
		});
	}); */
})(jQuery);
