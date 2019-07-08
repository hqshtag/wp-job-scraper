const flatten = (arr) => {
	return arr.reduce(function(flat, toFlatten) {
		return flat.concat(Array.isArray(toFlatten) ? flatten(toFlatten) : toFlatten);
	}, []);
};
const msToDay = (ms) => {
	return Math.floor(ms / 1000 / 3600 / 24);
};

export { flatten, msToDay };
