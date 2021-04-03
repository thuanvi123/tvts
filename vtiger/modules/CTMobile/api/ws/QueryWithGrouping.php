<?php
 /*+*******************************************************************************
 * The content of this file is subject to the CRMTiger Pro license.
 * ("License"); You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is vTiger
 * The Modified Code of the Original Code owned by https://crmtiger.com/
 * Portions created by CRMTiger.com are Copyright(C) CRMTiger.com
 * All Rights Reserved.
  ***************************************************************************** */
include_once dirname(__FILE__) . '/Query.php';

include_once 'include/Webservices/Query.php';

class CTMobile_WS_QueryWithGrouping extends CTMobile_WS_Query {
	
	private $queryModule;
	
	function processQueryResultRecord($record, $user) {
		parent::processQueryResultRecord($record, $user);

		if ($this->cachedDescribeInfo() === false) {
			$describeInfo = vtws_describe($this->queryModule, $user);
			$this->cacheDescribeInfo($describeInfo);
		}
		$transformedRecord = $this->transformRecordWithGrouping($record, $this->queryModule);
		// Update entity fieldnames
		$transformedRecord['labelFields'] = $this->cachedEntityFieldnames($this->queryModule);
		return $transformedRecord;
	}
	
	function process(CTMobile_API_Request $request) {
		$this->queryModule = $request->get('module');
		return parent::process($request);
	}
}
