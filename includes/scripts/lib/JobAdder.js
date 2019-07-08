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

export default JobAdder;
