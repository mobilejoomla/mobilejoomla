<?php
/*
 * Tera_WURFL - PHP MySQL driven WURFL
 * 
 * Tera-WURFL was written by Steve Kamerman, Tera Technologies and is based on the
 * WURFL PHP Tools from http://wurfl.sourceforge.net/.  This version uses a MySQL database
 * to store the entire WURFL file to provide extreme performance increases.
 * 
 * @package TeraWurfl
 * @author Steve Kamerman, Tera Technologies (kamermans AT teratechnologies DOT net)
 * @version Stable 2.0.0 $Date: 2009/11/13 23:59:59
 * @license http://www.mozilla.org/MPL/ MPL Vesion 1.1
 * $Id: webservice.php,v 1.1.2.4 2007/07/19 23:02:31 kamermans Exp $
 * $RCSfile: webservice.php,v $
 * 
 * Based On: WURFL PHP Tools by Andrea Trasatti ( atrasatti AT users DOT sourceforge DOT net )
 *
 */

/*
 * webservice.php provides a backend XML method of querying Tera-WURFL for device info.
 * This file will only work correctly with Tera-WURFL >= 1.5.2
 * 
 * Usage: 
 * webservice.php?ua=MOT-RAZRV3x/&search=brand_name|model_name|uaprof|fakecapa&groups=image_format|fakegroup
 * 
 * Returns:
 * XML data with the results, in the same basic format as WURFL:
<?xml version="1.0" encoding="iso-8859-1"?>
<TeraWURFLQuery>
	<device useragent="MOT-RAZRV3x" actual_device_root="true" fallback="mot_v3_ver1" id="mot_v3x_ver1">
		<group id="image_format">
			<capability name="wbmp" value="true"/>
			<capability name="bmp" value="true"/>
			<capability name="epoc_bmp" value="false"/>
			<capability name="gif" value="true"/>
			<capability name="gif_animated" value="true"/>
			<capability name="jpg" value="true"/>
			<capability name="png" value="true"/>
			<capability name="tiff" value="false"/>
			<capability name="flash_lite" value="false"/>
			<capability name="flash_lite_1_1" value="false"/>
			<capability name="flash_lite_1_2" value="false"/>
			<capability name="flash_lite_2_1" value="false"/>
			<capability name="flash_lite_download_limit" value="0"/>
			<capability name="svgt_1_1" value="false"/>
			<capability name="svgt_1_1_plus" value="false"/>
			<capability name="greyscale" value="false"/>
			<capability name="colors" value="262144"/>
		</group>
		<group id="fakegroup">
		</group>
		<search>
			<capability name="brand_name" value="Motorola"/>
			<capability name="model_name" value="RAZR V3x"/>
			<capability name="uaprof" value="http://motorola.handango.com/phoneconfig/razrv3x/Profile/razrv3x_NO_BEARER.rdf"/>
			<capability name="fakecapa" value=""/>
		</search>
	</device>
	<errors>
		<error name="badcap" description="The capability 'fakecapa' is not valid or undefined."/>
		<error name="badgroup" description="The group 'fakegroup' is not valid or undefined."/>
	</errors>
</TeraWURFLQuery>
 * 
 * You can specify the following options via GET or POST:
 * ua:		User Agent (required)
 * groups:	Return these groups of capabilities (i.e. image_format) instead of everything.
 * 			This works with the "capabilities" option.  Multiple groups can be separated by '|'.
 * search:	Returns these individual capabilities (i.e. mp3) instead of everything.
 * 			Results from this will be returned in:
 * 				<TeraWURFLQuery>
 * 					<device>
 * 						<search>
 * 			Multiple capabilities can be separated by '|'.
 * 
 * This script is designed to always return valid XML data so your client doesn't
 * break.  If your query generated errors, they will be in:
 * 		<TeraWURFLQuery>
 * 			<errors>
 * See the example above.  If you searched for an invalid capability or group, it
 * will still be included in the XML data structure, but it will NULL or empty(),
 * respectively.
 * 
 * If the UA you're searching for does NOT result in a match, you will get the error name="nomatch".
 * Example: webservice.php?ua=not_a_ua
 * Result:
<TeraWURFLQuery>
	<device useragent="" actual_device_root="" fallback="" id="">
		<group id="product_info">
			<capability name="is_wireless_device" value="false"/>
			<capability name="brand_name" value=""/>
			<capability name="model_name" value=""/>
		</group>
		<search/>
	</device>
	<errors>
		<error name="nomatch" description="No devices matched the User Agent provided"/>
	</errors>
</TeraWURFLQuery>
 * 
 * I am not a Microsoft friendly programmer so this is a very simple XML implementation
 * and it does not contain a WSDL at this point in time.  If you have a use for this
 * feature and would like to see something included, by all means, let me know!
 * 
 * Please note: I will not include anything that requires other classes/prerequisites
 * at this point because I want to make this package compatible with PHP 4.1-5.x.
 * 
 * This is a BETA feature.
 * 
 * Special thanks to Lars S. Nielsen for convincing me to include this functionality.
 */
header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past // header("Content-Type: text/plain");
header("Content-Type: text/xml");

error_reporting(E_ALL ^ E_NOTICE);
require_once('./TeraWurfl.php');
$wurflObj = new TeraWurfl();

// this array will store the data to be returned
global $out_cap;
$out_cap = array();
$search_results = array();
$out_errors = array();

// check user agent
if ( isset($_REQUEST['ua']) && strlen($_REQUEST['ua'])>0 ) {
	$matched = $wurflObj->GetDeviceCapabilitiesFromAgent($_REQUEST['ua']);
	if(!$matched){
		teraWurflAddError("nomatch","No devices matched the User Agent provided");
	}
} else {
	// a UA needs to be specified
	teraWurflAddError("nomatch","You must specify a User Agent.");
}
// look for 'search' capabilities
if ( isset($_REQUEST['search']) && strlen($_REQUEST['search'])>0 ) {
	$capabilities = explode('|',$_REQUEST['search']);
	foreach($capabilities as $cap){
		$tmpcap = $wurflObj->getDeviceCapability($cap);
		if(is_null($tmpcap)){
			teraWurflAddError("badcap","The capability '$cap' is not valid or undefined.");
			$search_results[$cap] = '';
		}else{
			$search_results[$cap] = $tmpcap;
		}
	}
}
// look for 'groups' of capabilities
if ( isset($_REQUEST['groups']) && strlen($_REQUEST['groups'])>0 ) {
	// returning a group of capabilities
	$groups = explode('|',$_REQUEST['groups']);
	foreach($groups as $group){
		if(is_null($wurflObj->capabilities[$group])){
			teraWurflAddError("badgroup","The group '$group' is not valid or undefined.");
			$out_cap[$group] = array();
		}else{
			$out_cap[$group] = $wurflObj->capabilities[$group];
		}
	}
}
// return all capabilities
if(!isset($_REQUEST['search']) && !isset($_REQUEST['groups'])){
	// neither capabilities nor groups were specified to be returned,
	// returning all the capabilities
	$out_cap = $wurflObj->capabilities;
}

unset($group,$cap);
echo '<?xml version="1.0" encoding="iso-8859-1"?>'."\n";
echo "<TeraWURFLQuery>\n";
printf("\t".'<device useragent="%s" actual_device_root="%s" fallback="%s" id="%s">',
	$wurflObj->capabilities['user_agent'],
	$wurflObj->capabilities['actual_device_root'],
	$wurflObj->capabilities['fall_back'],
	$wurflObj->capabilities['id']
);
foreach( $out_cap as $group => $cap){
	if(!is_array($cap)) continue; // this is a property, not a group - skip it.
	echo "\t\t<group id=\"$group\">\n";
	foreach( $cap as $cap_name => $value){
		$value = teraWurflNicePrint($value);
		echo "\t\t\t<capability name=\"$cap_name\" value=\"$value\"/>\n";
	}
	echo "\t\t</group>\n";
}
if(count($search_results)==0){
	echo "\t\t<search/>\n";
}else{
	echo "\t\t<search>\n";
	foreach( $search_results as $cap_name => $value){
		$value = teraWurflNicePrint($value);
		echo "\t\t\t<capability name=\"$cap_name\" value=\"$value\"/>\n";
	}
	echo "\t\t</search>\n";
}
echo "\t</device>";
if(count($out_errors)==0){
	echo "\t<errors/>";
}else{
	echo "\t<errors>\n";
	foreach($out_errors as $name => $desc){
		echo "\t\t<error name=\"$name\" description=\"$desc\"/>\n";
	}
	echo "\t</errors>\n";
}
echo "</TeraWURFLQuery>";
function teraWurflNicePrint($in){
	if(is_bool($in))return var_export($in,true);
	if(is_null($in) || !isset($in))return '';
	return $in;
}
function teraWurflAddError($name,$desc){
	global $out_errors;
	$out_errors[$name]=$desc;
}
?>