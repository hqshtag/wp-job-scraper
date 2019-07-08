import { flatten } from './lib/utils';

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

export default Parser;
