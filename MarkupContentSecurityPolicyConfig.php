<?php namespace ProcessWire;

/**
 * Markup Content Security Policy Configuration
 *
 * @todo Is there a better way to get the module that this configures?
 *
 */

class MarkupContentSecurityPolicyConfig extends ModuleConfig {

	/**
	 * Returns default values for module variables
	 *
	 * @return array
	 *
	 */
	public function getDefaults() {
		return [];
	}

	/**
	 * Returns inputs for module configuration
	 *
	 * @return InputfieldWrapper
	 *
	 */
	public function getInputfields() {

		$modules = $this->wire("modules");
		$inputfields = parent::getInputfields();

		$csp = $modules->get(str_replace("Config", "", $this->className));
		$textCsp = $this->_("Content Security Policy");
		$urlInfo = "https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Content-Security-Policy";

		// Directives
		$fieldset = $modules->get("InputfieldFieldset");
		$fieldset->label = $this->_("Directives");
		$fieldset->description = sprintf($this->_('For more information on %1$s directives, please visit %2$s.'), $textCsp, "[$urlInfo]($urlInfo)");
		$fieldset->icon = "pencil";

		$directives = [[], []];
		$placeholders = [
			"default-src" => "'none'",
			"script-src" => "'self' cdnjs.cloudflare.com *.google.com www.google-analytics.com www.googletagmanager.com",
			"style-src" => "'self' 'unsafe-inline' cdnjs.cloudflare.com",
			"img-src" => "'self' data:",
			"connect-src" => "'self' www.google-analytics.com",
			"font-src" => "'self' fonts.gstatic.com",
			"media-src" => "'self' data:",
			"frame-src" => "www.google.com www.youtube.com www.youtube-nocookie.com player.vimeo.com",
		];

		foreach($csp::directives as $name) {
			$key = $csp->getDirectiveKey($name);
			$directives[(bool) $csp->get($key)][] = [
				"type" => "text",
				"name" => $key,
				"label" => $name,
				"placeholder" => (isset($placeholders[$name]) ? $placeholders[$name] : "'self'"),
				"collapsed" => 2,
			];
		}

		if(count($directives[1])) $fieldset->import($directives[1]);
		if(count($directives[0])) $fieldset->import($directives[0]);

		$fieldset->add([
			"type" => "textarea",
			"name" => "directivesOther",
			"label" => $this->_("Any other directives"),
			"description" => sprintf($this->_("If you wish to use any other available %s directives, you may add them here."), $textCsp),
			"notes" => $this->_("Please enter each directive on a new line."),
			"rows" => 3,
			"collapsed" => 2,
		]);

		$inputfields->add($fieldset);

		// Violation Reporting
		$fieldset = $modules->get("InputfieldFieldset");
		$fieldset->label = $this->_("Violation Reporting");
		$fieldset->icon = "file-text-o";
		$fieldset->collapsed = 2;

		$fieldset->add([
			"type" => "checkbox",
			"name" => "report",
			"label" => $this->_("Enable"),
			"notes" => sprintf($this->_('When enabled, %1$s will be inserted after the %2$s %3$s tag.'), "`report-uri.js`", $textCsp, "`<meta>`"),
			"icon" => "square-o",
		]);

		$fieldset->add([
			"type" => "URL",
			"name" => "reportEndpoint",
			"label" => $this->_("Endpoint"),
			"description" => $this->_("If a valid URL is entered, the report will be posted to it."),
			"placeholder" => "https://www.yourdomain.com/your-endpoint",
			"showIf" => "report=1",
			"collapsed" => 2,
		]);

		$inputfields->add($fieldset);

		// Debug Mode
		$inputfields->add([
			"type" => "checkbox",
			"name" => "debug",
			"label" => $this->_("Debug Mode"),
			"notes" => sprintf(
				$this->_("When enabled, error messages and other useful information will be logged to %s."),
				"**" . $this->wire("sanitizer")->kebabCase($csp->className) . "**"
			),
			"icon" => "search-plus",
			"collapsed" => 2,
		]);

		return $inputfields;
	}
}
