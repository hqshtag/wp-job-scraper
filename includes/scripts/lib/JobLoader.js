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
		return [ `https://data.usajobs.gov/api/search?DatePosted=${numberOfDays}&ResultsPerPage=232` ];
	};
}

export default JobLoader;
