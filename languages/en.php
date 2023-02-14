<?php

return [
	'settings:head_extend' => "Code and scripts in the header (before &#60;body&#62;)",
	'settings:head_extend:help' => 'E.g. this is required for Google Analytics, Matomo (Piwik), chat bots etc',
	'settings:footer_extend' => "Code and scripts in the footer (before &#60;/body&#62;)",
	'settings:footer_extend:help' => 'E.g. this is required for Google Adsense, metrics etc',
	
	//HTMLAWED
	'settings:htmlawed_deny_attribute' => 'Select HTML attributes to deny on inputs',
	'settings:htmlawed_deny_attribute:help' => 'By default. <a href="https://www.bioinformatics.org/phplabware/internal_utilities/htmLawed/index.php">Htmlawed library</a> restrict HTML attributes with block rules. In this option you can select such attributes to protect the inputs on your app. Use commas.',
];