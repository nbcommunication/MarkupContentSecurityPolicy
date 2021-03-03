/**
 * Content Security Policy Report URI
 *
 * Send a report if the policy is violated. For more information on this event
 * please visit https://developer.mozilla.org/en-US/docs/Web/API/SecurityPolicyViolationEvent
 *
 * @copyright 2021 NB Communication Ltd
 * @license Mozilla Public License v2.0 http://mozilla.org/MPL/2.0/
 *
 */

var cspViolations = {};
document.addEventListener("securitypolicyviolation", function(event) {

	var report = {};
	var request = new XMLHttpRequest;
	var params = [
		"blockedURI",
		"columnNumber",
		"disposition",
		"documentURI",
		"effectiveDirective",
		"lineNumber",
		"originalPolicy",
		"referrer",
		"sample",
		"sourceFile",
		"statusCode",
		"violatedDirective",
	];

	var n = params.length;
	for(var i = 0; i < n; i++) {
		report[params[i]] = event[params[i]];
	}

	// Only log unique reports
	report = JSON.stringify(report);
	var id = btoa(report);
	if(!(id in cspViolations)) {
		cspViolations[id] = report;
		request.open("POST", "?csp-violations=1", true),
		request.setRequestHeader("Content-Type", "application/csp-report"),
		request.send(report);
	}
});
