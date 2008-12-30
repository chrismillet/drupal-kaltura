<?php
class KalturaHelpers
{
	static function getContributionWizardFlashVars($ks, $kshowId, $partner_data, $type, $comment)
	{
		$sessionUser = KalturaHelpers::getSessionUser();
		$config = KalturaHelpers::getServiceConfiguration();
		
		$flashVars = array();

		$flashVars["userId"] = $sessionUser->userId;
		$flashVars["sessionId"] = $ks;
	
		if ($sessionUserId == KalturaSettings::ANONYMOUS_USER_ID) {
			 $flashVars["isAnonymous"] = true;
		}
			
		$flashVars["partnerId"] 	= $config->partnerId;
		$flashVars["subPartnerId"] 	= $config->subPartnerId;
		if ($kshowId)
			// TODO: change the following line for roughcut
			$flashVars["kshow_id"] 	= ($type == 'entry')? $type.'-'.$kshowId: $kshowId;
		else
			$flashVars["kshow_id"] 	= -2;
		
		$flashVars["afterAddentry"] 	= "onContributionWizardAfterAddEntry";
		$flashVars["close"] 		= "onContributionWizardClose";
		$flashVars["partnerData"] 	= $partner_data;
		
		if (!$comment)
			$flashVars["uiConfId"] 		= KalturaSettings::CW_UICONF_ID;
		else
			$flashVars["uiConfId"] 		= KalturaSettings::CW_COMMENTS_UICONF_ID;
			
		$flashVars["terms_of_use"] 	= "http://corp.kaltura.com/tandc" ;
		
		return $flashVars;
	}
	
	static function getSimpleEditorFlashVars($ks, $kshowId, $type, $partner_data)
	{
		$sessionUser = KalturaHelpers::getSessionUser();
		$config = KalturaHelpers::getServiceConfiguration();
		
		$flashVars = array();
		
		if($type == 'entry')
		{
			$flashVars["entry_id"] 		= $kshowId;
			$flashVars["kshow_id"] 		= 'entry-'.$kshowId;
		} else {
			$flashVars["entry_id"] 		= -1;
			$flashVars["kshow_id"] 		= $kshowId;
		}

		$flashVars["partner_id"] 	= $config->partnerId;;
		$flashVars["partnerData"] 	= $partner_data;
		$flashVars["subp_id"] 		= $config->subPartnerId;
		$flashVars["uid"] 			= $sessionUser->userId;
		$flashVars["ks"] 			= $ks;
		$flashVars["backF"] 		= "onSimpleEditorBackClick";
		$flashVars["saveF"] 		= "onSimpleEditorSaveClick";
		$flashVars["uiConfId"] 		= KalturaSettings::SE_UICONF_ID;
		
		return $flashVars;
	}
	
	static function getKalturaPlayerFlashVars($ks, $kshowId = -1, $entryId = -1)
	{
		$sessionUser = KalturaHelpers::getSessionUser();
		$config = KalturaHelpers::getServiceConfiguration();
		
		$flashVars = array();
		
		$flashVars["kshowId"] 		= $kshowId;
		$flashVars["entryId"] 		= $entryId;
		$flashVars["partner_id"] 	= $config->partnerId;
		$flashVars["subp_id"] 		= $config->subPartnerId;
		$flashVars["uid"] 			= $sessionUser->userId;
		$flashVars["ks"] 			= $ks;
		
		return $flashVars;
	}
	
	static function flashVarsToString($flashVars)
	{
		$flashVarsStr = "";
		foreach($flashVars as $key => $value)
		{
			$flashVarsStr .= ($key . "=" . urlencode($value) . "&"); 
		}
		return substr($flashVarsStr, 0, strlen($flashVarsStr) - 1);
	}
	
	static function getSwfUrlForBaseWidget() 
	{
		return KalturaHelpers::getSwfUrlForWidget(KalturaSettings::BASE_WIDGET_ID);
	}
	
	static function getSwfUrlForWidget($widgetId)
	{
		return KalturaHelpers::getKalturaServerUrl() . "/kwidget/wid/" . $widgetId;
	}
	
	static function getContributionWizardUrl($uiConfId = null)
	{
		if ($uiConfId)
			return KalturaHelpers::getKalturaServerUrl() . "/kcw/ui_conf_id/" . $uiConfId;
		else
			return KalturaHelpers::getKalturaServerUrl() . "/kcw/ui_conf_id/" . KalturaSettings::CW_UICONF_ID;
	}
	
	static function getSimpleEditorUrl($uiConfId = null)
	{
		if ($uiConfId)
			return KalturaHelpers::getKalturaServerUrl() . "/kse/ui_conf_id/" . $uiConfId;
		else
			return KalturaHelpers::getKalturaServerUrl() . "/kse/ui_conf_id/" . KalturaSettings::SE_UICONF_ID;
	}
	
	static function getThumbnailUrl($widgetId = null, $entryId = null, $width = 240, $height= 180)
	{
		$config = KalturaHelpers::getServiceConfiguration();
		$url = KalturaHelpers::getKalturaServerUrl();
		$url .= "/p/" . $config->partnerId;
		$url .= "/sp/" . $config->subPartnerId;
		$url .= "/thumbnail";
		if ($widgetId)
			$url .= "/widget_id/" . $widgetId;
		else if ($entryId)
			$url .= "/entry_id/" . $entryId;
		$url .= "/width/" . $width;
		$url .= "/height/" . $height;
		$url .= "/type/2";
		$url .= "/bgcolor/000000"; 
		return $url;
	}
	
	static function getServiceConfiguration() {
		$partnerId = variable_get('kaltura_partner_id', 0);
		if($partnerId == '') $partnerId = 0;
		
		$subPartnerId = variable_get('kaltura_subp_id', 0);		
		if($subPartnerId == '') $subPartnerId = 0;
		
		$config = new KalturaConfiguration($partnerId, $subPartnerId);
		$config->serviceUrl = KalturaHelpers::getKalturaServerUrl();
		$config->setLogger(new KalturaLogger());
		return $config;
	}
	
	function getKalturaServerUrl() {
		$url = variable_get('kaltura_server_url', KalturaSettings::SERVER_URL);
		if($url == '') $url = KalturaSettings::SERVER_URL;
		
		// remove the last slash from the url
		if (substr($url, strlen($url) - 1, 1) == '/')
			$url = substr($url, 0, strlen($url) - 1);
		return $url;
	}
	
	function getSessionUser() {
		global $user;
	
		$kalturaUser = new KalturaSessionUser();

		if ($user->uid) {
			$kalturaUser->userId= $user->uid;
			$kalturaUser->screenName = $user->name;			
		}
		else
		{
			$kalturaUser->userId = KalturaSettings::ANONYMOUS_USER_ID; 
		}

		return $kalturaUser;
	}
	
	function getKalturaClient($isAdmin = false, $privileges = null)
	{
		// get the configuration to use the kaltura client
		$kalturaConfig = KalturaHelpers::getServiceConfiguration();
		
		if(!$privileges) $privileges = 'edit:*';
		// inititialize the kaltura client using the above configurations
		$kalturaClient = new KalturaClient($kalturaConfig);
	
		// get the current logged in user
		$sessionUser = KalturaHelpers::getSessionUser();
		
		if ($isAdmin)
		{
			$adminSecret = variable_get("kaltura_admin_secret", "");
			$result = $kalturaClient->startSession($sessionUser, $adminSecret, true, $privileges);
		}
		else
		{
			$secret = variable_get("kaltura_secret", "");
			$result = $kalturaClient->startSession($sessionUser, $secret, false, $privileges);
		}
			
		if (count(@$result["error"]))
		{
			watchdog("kaltura", $result["error"][0]["code"] . " - " . $result["error"][0]["desc"]);
			return null;
		}
		else
		{
			// now lets get the session key
			$session = $result["result"]["ks"];
			
			// set the session so we can use other service methods
			$kalturaClient->setKs($session);
		}
		
		return $kalturaClient;
	}
}
?>