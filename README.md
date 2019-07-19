# Markup Content Security Policy
Configure and implement a Content Security Policy for all front-end HTML pages.

**This module should only be used in production once it has been fully tested in development. Deploying a Content Security Policy on a site without testing will almost certainly break something!**

## Overview

> **Content Security Policy** (CSP) is an added layer of security that helps to detect and mitigate certain types of attacks, including Cross Site Scripting (XSS) and data injection attacks. These attacks are used for everything from data theft to site defacement to distribution of malware.
>
> ... Configuring Content Security Policy involves adding the Content-Security-Policy HTTP header to a web page and giving it values to control resources the user agent is allowed to load for that page. For example, a page that uploads and displays images could allow images from anywhere, but restrict a form action to a specific endpoint. A properly designed Content Security Policy helps protect a page against a cross site scripting attack.
>
> &mdash; <cite>https://developer.mozilla.org/en-US/docs/Web/HTTP/CSP</cite>

Website Security Auditing Tools such as [Mozilla Observatory](https://observatory.mozilla.org/) will only return a high score if a Content Security Policy is implemented. It is therefore desirable to implement one.

A common way of adding the `Content-Security-Policy` header would be to add it to the .htaccess file in the site's root directory. However, this means the policy would also cover the ProcessWire admin, and this limits the level of security policy you can add.

The solution is to use the `<meta>` element to configure a policy, for example: `<meta http-equiv="Content-Security-Policy" content="default-src 'self'; img-src https://*; child-src 'none';">`. **MarkupContentSecurityPolicy** places this element with your configured policy at the beginning of the `<head>` element on each HTML page of your site.

There are some limitations to using the `<meta>` element:
- The `frame-ancestors`, `report-uri`, and `sandbox` directives cannot be used.
- The [`Content-Security-Policy-Report-Only`](https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Content-Security-Policy-Report-Only) header is not supported, so is not available for use by this module.

## Configuration
To configure this module, go to Modules > Configure > MarkupContentSecurityPolicy.

### Deploy Policy?
When enabled, the Content Security Policy `<meta>` tag will be added to all HTML pages for all users. When disabled, it will only be added for the superuser account, allowing you to test the policy.

### Directives
The most commonly used directives are listed, with a field for each. The placeholder values given are examples, not suggestions, but they may provide a useful starting point.

You will almost certainly need to use `'unsafe-inline'` in the `style-src` directive as this is required by some modules (e.g. [TextformatterVideoEmbed](http://modules.processwire.com/modules/textformatter-video-embed/)) or frameworks such as [UIkit](https://getuikit.com/).

Should you wish to add any other directives not listed, you can do so by adding them in *Any other directives*.

Please refer to these links for more information on how to configure your policy:
- https://developer.mozilla.org/en-US/docs/Web/HTTP/CSP
- https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Content-Security-Policy
- https://scotthelme.co.uk/content-security-policy-an-introduction/
- https://developers.google.com/web/fundamentals/security/csp/


### Violation Reporting
Because the `report-uri` directive is not available, when Violation Reporting is enabled a script is added to the `<head>`. This script listens for a [`SecurityPolicyViolationEvent`](https://developer.mozilla.org/en-US/docs/Web/API/SecurityPolicyViolationEvent) and posts the generated report. The module then processes and logs the violation report to **csp-violations**.

You can specify parameters to exclude from the report. The two suggested, `originalPolicy` and `disposition`, make sense to add; you should not need to log your own policy, and in the `<meta>` tag implementation, `disposition` will always return "enforce" as its value. Parameters with empty values are [automatically removed](https://github.com/processwire/processwire/blob/master/wire/core/Functions.php#L86) from the report.

Unfortunately, most of the violations that are reported are false positives, and not actual attempts to violate the policy. These are most likely from browser extensions and are not easy to determine and filter.

For this reason, there is no option for the report to be emailed when a policy is violated. Instead, you can specify an endpoint for the report to be sent to. This allows you to handle additional reporting in a way that meets your needs. For example, you may want to log all reports in a central location and send out an email once a day to an administrator notifying them of all sites with violations since the last email.

#### Filtering False Positives
The option to filter false positives should be used carefully, and only enabled when you have determined that your CSP violation report would benefit from it i.e. you are getting a lot of them. More filters will be added as they are . If you have any filter suggestions, please post them on this module's [support forum](https://processwire.com/talk/topic/21963-markupcontentsecuritypolicy).

#### Retrieving the Report
To retrieve the report at your endpoint, the following can be used:

```php
$report = file_get_contents("php://input");
if(!empty($report)) {
	$report = json_decode($report, 1);
	if(isset($report) && is_array($report) && isset($report["documentURI"])) {
		// Do something
	}
}
```

### Debug Mode
When this is enabled, a range of information is logged to **markup-content-security-policy**. This is probably most useful when debugging a reporting endpoint.

## Additional .htaccess Rules
To get an A+ score on Mozilla Observatory, besides using HTTPS and enabling the HSTS header, you can also place the following prior to ProcessWire's htaccess directives:

```
Header set Content-Security-Policy "frame-ancestors 'self'"
Header set Referrer-Policy "no-referrer-when-downgrade"
```

## Installation
1. Download the [zip file](https://github.com/chriswthomson/MarkupContentSecurityPolicy/archive/master.zip) at Github or clone the repo into your `site/modules` directory.
2. If you downloaded the zip file, extract it in your `sites/modules` directory.
3. In your admin, go to Modules > Refresh, then Modules > New, then click on the Install button for this module.

**ProcessWire >= 3.0.123 is required to use this module.**

## License
This project is licensed under the Mozilla Public License Version 2.0.
