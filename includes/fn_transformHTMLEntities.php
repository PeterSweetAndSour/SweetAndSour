<?php
// React escapes HTML entities so have to convert them to hex codes. It seems crazy to me they set it this way.

function transformHTMLEntities($contentHTML) {
	$replacements = [
		"&ndash;" => "&#8211;",
		"&mdash;" => "&#8212;",
		"&apos;" => "&#39;",
		"&amp;" => "&#38;",
		"&quot;" => "&#34;",
		"&ldquo;" => "&#8220;",
		"&rdquo;" => "&#8221;",
		"&lsquo;" => "&#8216;",
		"&rsquo;" => "&#8217;",
		"&deg;" => "&#176;",
		"&frac14;" => "&#188;",
		"&frac12;" => "&#189;",
		"&frac34;" => "&#190;",
		"&aacute;" => "&#225;",
		"&atilde;" => "&#227;",
		"&eacute;" => "&#233;",
		"&egrave;" => "&#232;",
		"&sup2;" => "&#178;",
		"&sup3;" => "&#179;",
		"&lt;" => "&#60;",
		"&gt;" => "&#62;",
		"&copy;" => "&#169;",
		"&euro;" => "&#8364;",
		"&cent;" => "&#162;",
		"&hellip;" => "&#8230;"
	
	];

	return strtr($contentHTML, $replacements);
}
?>