/**
 * Content Security Policy Report URI
 *
 * Send a report if the policy is violated. For more information on this event
 * please visit https://developer.mozilla.org/en-US/docs/Web/API/SecurityPolicyViolationEvent
 *
 * @copyright 2019 NB Communication Ltd
 * @license Mozilla Public License v2.0 http://mozilla.org/MPL/2.0/
 * 
 */

document.addEventListener("securitypolicyviolation", function(event) {

	var report = {},
		params = [
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
		],
		request = new XMLHttpRequest;

	for(var i = 0; i < params.length; i++) report[params[i]] = event[params[i]];

	request.open("POST", "?csp-violations=1", true),
	request.setRequestHeader("content-type", "application/csp-report"),
	request.send(JSON.stringify(report));
});
