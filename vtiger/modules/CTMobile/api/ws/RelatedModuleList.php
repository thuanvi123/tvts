<?php
 /*+*******************************************************************************
 * The content of this file is subject to the CRMTiger Pro license.
 * ("License"); You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is vTiger
 * The Modified Code of the Original Code owned by https://crmtiger.com/
 * Portions created by CRMTiger.com are Copyright(C) CRMTiger.com
 * All Rights Reserved.
  ***************************************************************************** */

//include_once 'include/data/CRMEntity.php';

class CTMobile_WS_RelatedModuleList extends CTMobile_WS_Controller {
	public $totalQuery = "";
	public $totalParams = array();
	
	function process(CTMobile_API_Request $request) {
		$default_charset = VTWS_PreserveGlobal::getGlobal('default_charset');
		
		global $adb,$site_URL,$current_user;
		$current_user = $this->getActiveUser();
		$refrenceUitypes = array(10,51,57,58,59,66,73,75,76,77,78,80,81,101,117);
		$sourcemoduleName = trim($request->get('module'));
		$recordid = trim($request->get('record'));
		$tablabel = trim($request->get('tablabel'));
		$display_params = $request->get('display_params');
		$params = Zend_Json::decode($display_params);
		$index = $request->get('index');
		$size = $request->get('size');
		$limit = ($index*$size) - $size;
		$record = explode('x', $recordid); 
		$relatedModuleName = trim($request->get('relatedmodule'));
		if(!getTabid($sourcemoduleName)){
			$message = $sourcemoduleName.' '.$this->CTTranslate('Module does not exists');
			throw new WebServiceException(404,$message);
		}
		if(!getTabid($relatedModuleName)){
			$message = $relatedModuleName.' '.$this->CTTranslate('Module does not exists');
			throw new WebServiceException(404,$message);
		}
		//get source module tabid
		$sql1 = "select tabid,name,tablabel from vtiger_tab where name='".$sourcemoduleName."'";
		$result1 = $adb->pquery($sql1,array()); 
		$sourcemoduletabid =$adb->query_result($result1,0,'tabid');

		//get Related Module tabid
		$sql3 = "select tabid,name from vtiger_tab where name='".$relatedModuleName."'";
		$result3 = $adb->pquery($sql3,array());
		$relatedmoduletabid =$adb->query_result($result3,0,'tabid');

		//get entity table id
		$sql4 = "select id,name from vtiger_ws_entity where name='".$relatedModuleName."'";
		$result4 = $adb->pquery($sql4,array());
		$relatedmoduleentitytabid =$adb->query_result($result4,0,'id');


		$sqltabname = "select tablename,fieldname from vtiger_entityname where tabid = '".$relatedmoduletabid."'";
		$resulttabname = $adb->pquery($sqltabname,array());
		$entityField = $adb->query_result($resulttabname,0,'fieldname');
		$tablename = $adb->query_result($resulttabname,0,'tablename');
		$tablexplode = explode("_",$tablename);	 
		
		$entityField_array = explode(',',$entityField);
		$entityField = $entityField_array[0];
		
		
		$entityQuery11 = $adb->pquery("SELECT * FROM vtiger_field WHERE columnname = ? and tabid= ?",array($entityField,$relatedmoduletabid));
		$fieldlabel = $adb->query_result($entityQuery11,0,'fieldlabel');
		$fieldlabel = vtranslate($fieldlabel,$relatedModuleName);
		
		//get fieldname
		$sql4 = "select fieldname,fieldlabel, columnname,tablename, uitype from vtiger_field where tabid='".$relatedmoduletabid."' AND presence IN (0,2)";
		$result4 = $adb->pquery($sql4,array());
		$numofrows1 = $adb->num_rows($result4);
		
		
		
		$fieldtabmerge2 = '';
		$relatedfield = '';
		for ($j=0; $j < $numofrows1; $j++){
			$relatedfieldname =$adb->query_result($result4,$j,'columnname');
			$fieldname =$adb->query_result($result4,$j,'fieldname');
			if($relatedfieldname == 'crmid'){
				$relatedfieldname = $fieldname;
			}	
			$relatedfieldlabel = $adb->query_result($result4, $j,'fieldlabel');
			$relatedfieldtabname =$adb->query_result($result4,$j,'tablename');
			$relatedfielduitype =$adb->query_result($result4,$j,'uitype');
			$relatedfieldarray1[$relatedfieldname]['label'] =  strip_tags($relatedfieldlabel);
			$relatedfieldarray1[$relatedfieldname]['uitype'] =  $relatedfielduitype;
			$relatedfieldarray12 = $relatedfieldarray1;
			$relatedfieldnamelist[$j] = $relatedfieldname;
			array_push($relatedfieldarray12['crmid'], "crmid");
			array_push($relatedfieldnamelist, "crmid");
			
			$relatedfieldarray =  $relatedfieldname;
			$fieldtabmerge = $relatedfieldtabname.'.'.$relatedfieldarray; 
			if ($fieldtabmerge2 == '') {
				$fieldtabmerge2 .= $fieldtabmerge.',vtiger_crmentity.crmid';	
			}else{
				$fieldtabmerge2 .= ','.$fieldtabmerge;	
			}
			$fieldtabmerge1 = $fieldtabmerge2;	
		}

		$innerjoin .= $tablename.' INNER JOIN vtiger_crmentity on vtiger_crmentity.crmid='.$tablename.".".$tablexplode[1].id. " where ".$tablename.'.'.$tablexplode[1].id ;
		 
		//Campare to sourcemodule and relatedmodule
		$comparetabidsql = "SELECT relation_id,name,label FROM vtiger_relatedlists where tabid = '".$sourcemoduletabid."' AND related_tabid = '".$relatedmoduletabid."' AND name != 'get_history'";
		$getfunctionres = $adb->pquery($comparetabidsql,array());
		
		$relatedfunctionname = array();
		foreach($getfunctionres as $gval){
			$relatedfunctionname = $gval['name'];
			$relation_id = $gval['relation_id'];
			$relation_label = $gval['label'];
		}
		global $currentModule;
		$currentModule = $sourcemoduleName;
		if($sourcemoduleName == $relatedModuleName){
			$relation_label = $tablabel;
		}



		$parentRecordModel = Vtiger_Record_Model::getInstanceById($record[1], $sourcemoduleName);
		$relationListView = Vtiger_RelationListView_Model::getInstance($parentRecordModel, $relatedModuleName, $relation_label);
		$relationModel = $relationListView->getRelationModel();
		$deleteAction = $relationModel->isDeletable();
		$query = $relationListView->getRelationQuery();

		//echo $relation_label;exit;
		if(!empty($index) && !empty($size)){
			$this->totalQuery = $query;
			$this->totalParams = array();
			$query .= sprintf(" LIMIT %s, %s", $limit, $size);
		}
		
		
		$getfunctionres = $adb->pquery($query,array());
		$numofrows2 = $adb->num_rows($getfunctionres);
		
		for ($i=0; $i < $numofrows2; $i++) { 
			foreach($relatedfieldarray12 as $fieldnamekey => $fieldValue) {
				$relatedfetchrecord =$adb->query_result($getfunctionres,$i,$fieldnamekey);
				$fetchrecord[$i][$fieldnamekey]['fieldlabel'] = vtranslate($relatedfieldarray12[$fieldnamekey]['label'], $relatedModuleName, $current_user->language);
				$uitype = $relatedfieldarray12[$fieldnamekey]['uitype'];
				
				if(in_array($uitype,$refrenceUitypes)){
					if($relatedfetchrecord == 0){
						$fetchrecord[$i][$fieldnamekey]['value'] = "";
					}else{
						if($uitype == 77){
							$userRecordModel = Vtiger_Record_Model::getInstanceById($relatedfetchrecord,'Users');
							if($userRecordModel->get('user_name') == ''){
								$query = "SELECT groupname FROM vtiger_groups WHERE groupid = ?";
								$groupResults = $adb->pquery($query,array($relatedfetchrecord));
								$fetchrecord[$i][$fieldnamekey]['value'] = html_entity_decode($adb->query_result($groupResults,0,'groupname'),ENT_QUOTES,$default_charset);
							}else{
								$fetchrecord[$i][$fieldnamekey]['value'] = html_entity_decode($userRecordModel->get('first_name').' '.$userRecordModel->get('last_name'),ENT_QUOTES,$default_charset);
							}
						}else{
							$labelresult = $adb->pquery("SELECT label FROM vtiger_crmentity WHERE crmid = ?",array($relatedfetchrecord));
							$new = $adb->query_result($labelresult,0,'label');
							$fetchrecord[$i][$fieldnamekey]['value'] = decode_html(decode_html($new));
						}
					}
					
					if($fieldnamekey == 'contact_relation' && $relatedModuleName == 'CaseRelation') {
						$AttachmentQuery =$adb->pquery("select vtiger_attachments.attachmentsid, vtiger_attachments.name, vtiger_attachments.subject, vtiger_attachments.path FROM vtiger_seattachmentsrel
											INNER JOIN vtiger_attachments ON vtiger_seattachmentsrel.attachmentsid = vtiger_attachments.attachmentsid  
											WHERE vtiger_seattachmentsrel.crmid = ?", array($relatedfetchrecord));
											
						$AttachmentQueryCount = $adb->num_rows($AttachmentQuery);
						$document_path = array();
						
						if($AttachmentQueryCount > 0) {
							$name = $adb->query_result($AttachmentQuery, 0, 'name');
							$Path = $adb->query_result($AttachmentQuery, 0, 'path');
							$attachmentsId = $adb->query_result($AttachmentQuery, 0, 'attachmentsid');
							$imagepath = $site_URL.$Path.$attachmentsId."_".$name;
							$fetchrecord[$i][$fieldnamekey]['url'] = $imagepath;
						} else {
							$fetchrecord[$i][$fieldnamekey]['url'] = '';
						}
						
					}
				} else if($uitype == 53) {
					$getAssignedUserNameQuery = $adb->pquery("SELECT first_name, last_name from vtiger_users where id = ?", array($relatedfetchrecord));
					$first_name = $adb->query_result($getAssignedUserNameQuery, 0, 'first_name');
					$last_name = $adb->query_result($getAssignedUserNameQuery, 0, 'last_name');
					$assigned_user_name = $first_name." ".$last_name;
					$assigned_user_name = html_entity_decode($assigned_user_name, ENT_QUOTES, $default_charset);
					$fetchrecord[$i][$fieldnamekey]['value'] = $assigned_user_name; 
				}else if($uitype == 56){
					if($relatedfetchrecord == 1){
						$fetchrecord[$i][$fieldnamekey]['value'] = vtranslate('LBL_YES');
					}else{
						$fetchrecord[$i][$fieldnamekey]['value'] = vtranslate('LBL_NO');
					}
				}else if($uitype == 5){
					$fetchrecord[$i][$fieldnamekey]['value'] = Vtiger_Date_UIType::getDisplayValue($relatedfetchrecord);
				}else {
					$relatedfetchrecord = html_entity_decode($relatedfetchrecord, ENT_QUOTES, $default_charset);
					$fetchrecord[$i][$fieldnamekey]['value'] = $relatedfetchrecord;
					if($fieldnamekey == 'crmid' && $relatedModuleName == 'Documents') {
						$AttachmentQuery =$adb->pquery("select vtiger_attachments.attachmentsid, vtiger_attachments.name, vtiger_attachments.subject, vtiger_attachments.path FROM vtiger_seattachmentsrel
											INNER JOIN vtiger_attachments ON vtiger_seattachmentsrel.attachmentsid = vtiger_attachments.attachmentsid 
											LEFT JOIN vtiger_notes ON vtiger_notes.notesid = vtiger_seattachmentsrel.crmid 
											WHERE vtiger_seattachmentsrel.crmid = ?", array($relatedfetchrecord));
											
						$AttachmentQueryCount = $adb->num_rows($AttachmentQuery);
						$document_path = array();
						
						if($AttachmentQueryCount > 0) {
							for($j=0;$j<$AttachmentQueryCount;$j++) {
								$name = $adb->query_result($AttachmentQuery, $j, 'name');
								$Path = $adb->query_result($AttachmentQuery, $j, 'path');
								$attachmentsId = $adb->query_result($AttachmentQuery, $j, 'attachmentsid');
								
								$document_path[] = array('doc_url'.$j=>$site_URL.$Path.$attachmentsId."_".$name, 'file_name'.$j=>$name);
							} 
						} 
						$fetchrecord[$i]['filename']['url'] = $document_path;
					}
				}
				
				
			}
		} 

		if ($numofrows2 == '') {
			$sql3 = "SELECT relcrmid FROM vtiger_crmentityrel INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_crmentityrel.relcrmid WHERE crmid='".$record[1]."' AND relmodule='".$relatedModuleName."' AND vtiger_crmentity.deleted = 0";
			$result3 = $adb->pquery($sql3,array());
			$numofrows3 = $adb->num_rows($result3);
		 
			for ($k=0; $k < $numofrows3 ; $k++) { 
				$relatedmoduleid =$adb->query_result($result3,$k,'relcrmid');
				$sqlfetchrecord = "select ". $fieldtabmerge1." from ".$innerjoin.'='.$relatedmoduleid."";
				$result5 = $adb->pquery($sqlfetchrecord,array());
				$numofrows5 = $adb->num_rows($result5);
				if ($numofrows5 > 0) {
					
					foreach($relatedfieldarray12 as $fieldnamekey => $fieldValue) {
						$relatedfetchrecord =$adb->query_result($getfunctionres,$i,$fieldnamekey);
						
						$fetchrecord[$i][$fieldnamekey]['fieldlabel'] = vtranslate($relatedfieldarray12[$fieldnamekey]['label'], $relatedModuleName, $current_user->language);
						$uitype = $relatedfieldarray12[$fieldnamekey]['uitype'];
						
						if($uitype == 10) {
							$getRelatedFieldValueQuery = $adb->pquery("SELECT label from vtiger_crmentity where crmid = ? and deleted = 0", array($relatedfetchrecord));
							$relatedFieldValue = $adb->query_result($getRelatedFieldValueQuery, 0, 'label'); 
							$relatedFieldValue = html_entity_decode($relatedFieldValue, ENT_QUOTES, $default_charset);
							$fetchrecord[$i][$fieldnamekey]['value'] = $relatedFieldValue;
						} else if($uitype == 53) {
							$getAssignedUserNameQuery = $adb->pquery("SELECT first_name, last_name from vtiger_users where id = ?", array($relatedfetchrecord));
							$first_name = $adb->query_result($getAssignedUserNameQuery, 0, 'first_name');
							$last_name = $adb->query_result($getAssignedUserNameQuery, 0, 'last_name');
							$assigned_user_name = $first_name." ".$last_name;
							$assigned_user_name = html_entity_decode($assigned_user_name, ENT_QUOTES, $default_charset);
							$fetchrecord[$i][$fieldnamekey]['value'] = $assigned_user_name; 
						}else {
							$relatedfetchrecord = html_entity_decode($relatedfetchrecord, ENT_QUOTES, $default_charset);
							$fetchrecord[$i][$fieldnamekey]['value'] = $relatedfetchrecord;
						}
						
					}
				}
			}
		}

		if($relatedModuleName == 'Events'){
			$ModuleName = 'Calendar';
		}else{
			$ModuleName = $relatedModuleName;
		}
		$moduleModel = Vtiger_Module_Model::getInstance($ModuleName);
		$fieldModels = $moduleModel->getFields();
		//$allowedParams =  array('recordModule','record','entityFieldlabel','entityFieldValue');
		$permittedRecords =  array();
	    foreach ($fetchrecord as $key => $part) {
			if($relatedModuleName == 'Calendar' || $relatedModuleName == 'Events'){
				//for get Webservice Id of Calender and Events Module
				$EventTaskQuery = $adb->pquery("SELECT * FROM  `vtiger_activity` WHERE activitytype = ? AND activityid = ?",array('Task',$part['crmid']['value'])); 
				if($adb->num_rows($EventTaskQuery) > 0){
					$wsid = CTMobile_WS_Utils::getEntityModuleWSId('Calendar');
					$recordId = $wsid.'x'.$part['crmid']['value'];
					$recordModule = 'Calendar';
				}else{
					$wsid = CTMobile_WS_Utils::getEntityModuleWSId('Events');
					$recordId = $wsid.'x'.$part['crmid']['value'];
					$recordModule = 'Events';
				}
			}else{
				$wsid = CTMobile_WS_Utils::getEntityModuleWSId($relatedModuleName);
				$recordId = $wsid.'x'.$part['crmid']['value'];
				$recordModule = $relatedModuleName;
			}

			if($relatedModuleName == 'Events'){
				$prevModule = 'Calendar';
			}else{
				$prevModule = $relatedModuleName;
			}

			if(Users_Privileges_Model::isPermitted($prevModule, 'DetailView', $part['crmid']['value'])){
				$modifiedRecord = array('recordModule' => $recordModule, 'record'=>$recordId,'label'=>$part[$entityField]['value']);
				foreach ($params as $keys => $p) { 
					$newKey = $keys+2;
					$nkeys = 'label'.$newKey;
					$column = $fieldModels[$p]->get('column');
					if($column == 'crmid'){
						$column = $p;
						$recordModel = Vtiger_Record_Model::getInstanceById($part['crmid']['value'],$relatedModuleName);
						$part[$column]['value'] = $recordModel->get($p);
					}
					if(trim($part[$column]['value']) == ''){
						$recordModel = Vtiger_Record_Model::getInstanceById($part['crmid']['value'],$relatedModuleName);
						$part[$column]['value'] = $recordModel->get($p);
						if($part[$column]['value'] != ''){
							if($fieldModels[$p]->get('uitype') == 53){
								$userRecordModel = Vtiger_Record_Model::getInstanceById($part[$column]['value'],'Users');
								if($userRecordModel->get('user_name') == ''){
									$query = "SELECT groupname FROM vtiger_groups WHERE groupid = ?";
									$groupResults = $adb->pquery($query,array($part[$column]['value']));
									$part[$column]['value'] = html_entity_decode($adb->query_result($groupResults,0,'groupname'),ENT_QUOTES,$default_charset);
								}else{
									$part[$column]['value'] = html_entity_decode($userRecordModel->get('first_name').' '.$userRecordModel->get('last_name'),ENT_QUOTES,$default_charset);
								}
							}else if(!in_array($fieldModels[$p]->get('uitype'),$refrenceUitypes)){
								$part[$column]['value'] = $fieldModels[$p]->getDisplayValue($part[$column]['value']);
							} 
						}
					}
					if(in_array($fieldModels[$p]->get('uitype'), $refrenceUitypes) && $part[$column]['value'] != ''){
						if($part[$column]['value'] == 0){
							$part[$column]['value'] = "";
						}else{
							if(is_numeric($part[$column]['value'])){
								if($fieldModels[$p]->get('uitype') == 77){
									$userRecordModel = Vtiger_Record_Model::getInstanceById($part[$column]['value'],'Users');
									if(empty($userRecordModel->get('user_name'))){
										$query = "SELECT groupname FROM vtiger_groups WHERE groupid = ?";
										$groupResults = $adb->pquery($query,array($part[$column]['value']));
										$part[$column]['value'] = html_entity_decode($adb->query_result($groupResults,0,'groupname'),ENT_QUOTES,$default_charset);
									}else{
										$part[$column]['value'] = html_entity_decode($userRecordModel->get('first_name').' '.$userRecordModel->get('last_name'),ENT_QUOTES,$default_charset);
									}
								}else if($fieldModels[$p]->get('uitype') == 117){
									$query = "SELECT id,currency_name,currency_symbol FROM  `vtiger_currency_info` WHERE currency_status = 'Active' AND id = ?";
									$result = $adb->pquery($query,array($part[$column]['value']));
									$new = $adb->query_result($result,0,'currency_name');
									$part[$column]['value'] = decode_html(decode_html($new));
								}else{
									$labelresult = $adb->pquery("SELECT label FROM vtiger_crmentity WHERE crmid = ?",array($part[$column]['value']));
									$new = $adb->query_result($labelresult,0,'label');
									$part[$column]['value'] = decode_html(decode_html($new));
								}
							}
						}
					}else if($fieldModels[$p]->get('uitype') == 9){
						$part[$column]['value'] = Vtiger_Double_UIType::getDisplayValue($part[$column]['value']);
					}else if($p == 'currency_id'){
						$query = "SELECT currency_name FROM  `vtiger_currency_info` WHERE id = '".$part[$column]['value']."'";
						$result = $adb->pquery($query,array());
						$part[$column]['value'] = $adb->query_result($result,0,'currency_name');
					}else if($fieldModels[$p]->get('uitype') == 71 || $fieldModels[$p]->get('uitype') == 72){
						$part[$column]['value'] = $fieldModels[$p]->getDisplayValue($part[$column]['value']); 
					}
					$modifiedRecord[$nkeys] = decode_html(decode_html($part[$column]['value']));
				}

				$allowedDisplayImageModules = array('Contacts','Products');
				if(in_array($relatedModuleName, $allowedDisplayImageModules)){
					$is_display_image = true;
					$record_id = $part['crmid']['value'];
					$AttachmentQuery =$adb->pquery("select vtiger_attachments.attachmentsid, vtiger_attachments.name, vtiger_attachments.subject, vtiger_attachments.path FROM vtiger_seattachmentsrel
												INNER JOIN vtiger_attachments ON vtiger_seattachmentsrel.attachmentsid = vtiger_attachments.attachmentsid 
												LEFT JOIN vtiger_notes ON vtiger_notes.notesid = vtiger_seattachmentsrel.crmid 
												WHERE vtiger_seattachmentsrel.crmid = ?", array($record_id));
												
					$AttachmentQueryCount = $adb->num_rows($AttachmentQuery);
					$document_path = array();
					
					if($AttachmentQueryCount > 0) {
						$name = $adb->query_result($AttachmentQuery, 0, 'name');
						$Path = $adb->query_result($AttachmentQuery, 0, 'path');
						$attachmentsId = $adb->query_result($AttachmentQuery, 0, 'attachmentsid');
						$ImageUrl = $site_URL.$Path.$attachmentsId."_".$name;
					} else {
						$ImageUrl = '';
					}
					$modifiedRecord['ImageUrl'] = $ImageUrl;
				}else{
					$is_display_image = false;
				}
				$modifiedRecord['is_display_image'] = $is_display_image;
				

				if($relatedModuleName == 'CTTimeTracker'){
					$modifiedRecord['tracking_date'] = Vtiger_Date_UIType::getDisplayDateValue($part['createdtime']['value']);
				}
				if($relatedModuleName == 'Events'){
					$prevModule = 'Calendar';
				}else{
					$prevModule = $relatedModuleName;
				}
				if($prevModule == 'CTTimeTracker'){
					$editAction = false;
				}else{

		    		$editAction = Users_Privileges_Model::isPermitted($prevModule, 'EditView', $part['crmid']['value']);
				}
				$modifiedRecord['editAction'] = $editAction;
				$permittedRecords[] = $modifiedRecord;
			}
			if($relatedModuleName != 'Calendar'){
				$sort[$part['modifiedtime']['value']] = strtotime($part['modifiedtime']['value']);
			}
		 }
		 array_multisort($sort, SORT_DESC, $permittedRecords);

		

		$isLast = true;
		if($this->totalQuery != ""){
			$totalResults = $adb->pquery($this->totalQuery,$this->totalParams);
			$totalRecords = $adb->num_rows($totalResults);
			if($totalRecords > $index*$size){
				$isLast = false;	
			}else{
				$isLast = true;
			}
		}
		 
		if ($permittedRecords == '') {
			$message = $this->CTTranslate('No records found');
			$allrelatedid =  array('relatedtabid' => $relatedmoduleentitytabid,'relatedModuleName'=>$relatedModuleName,'relatedModuleLabel'=>vtranslate($relatedModuleName,$relatedModuleName),'fetchrecord'=>array(),'message'=>$message,'isLast'=>$isLast,'deleteAction'=>$deleteAction);
		}else{

			$allrelatedid =  array('relatedtabid' => $relatedmoduleentitytabid,'relatedModuleName'=>$relatedModuleName,'relatedModuleLabel'=>vtranslate($relatedModuleName,$relatedModuleName),'fetchrecord'=>$permittedRecords,'message'=>'','isLast'=>$isLast,'deleteAction'=>$deleteAction);
		}
		$response = new CTMobile_API_Response();
		$response->setResult($allrelatedid);
		return $response;
	}		
}
