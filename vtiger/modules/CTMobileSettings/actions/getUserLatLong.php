<?php
/*+*******************************************************************************
 * The content of this file is subject to the CRMTiger Pro license.
 * ("License"); You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is vTiger
 * The Modified Code of the Original Code owned by https://crmtiger.com/
 * Portions created by CRMTiger.com are Copyright(C) CRMTiger.com
 * All Rights Reserved.
  ***************************************************************************** */
class CTMobileSettings_getUserLatLong_Action extends Vtiger_Save_Action {
    
	public function process(Vtiger_Request $request) {
		global $adb;
		$user_id = $request->get('user_id');
		$daterange = $request->get('daterange');
		$checkmodule = $request->get('checkmodule');
		$searchvalue = 	$request->get('searchvalue');
		$mode = $request->get('mode');
		
		$date = explode(' - ',$daterange);
		$date1 = date("Y-m-d", strtotime($date[0]));
		$date2 = date("Y-m-d", strtotime($date[1]));

		if($mode == 'getLabelRecord'){

			$Query = "SELECT entity.crmid,usr_route.userid,usr_route.createdtime,usr_route.action,entity.setype,entity.label FROM ctmobile_userderoute as usr_route INNER JOIN vtiger_crmentity as entity ON  usr_route.record = entity.crmid GROUP BY entity.crmid HAVING DATE(usr_route.createdtime) BETWEEN '".$date1."' AND '".$date2."'";
			if(!empty($user_id))
				$Query.="AND usr_route.userid = '$user_id'";
			if(!empty($checkmodule))
				$Query .= " AND entity.setype = '".$checkmodule."'";	
			if(!empty($searchvalue))
				$Query .= " AND entity.label LIKE '%".$searchvalue."%'";
			
			$Query.= " ORDER BY usr_route.createdtime DESC LIMIT 0,10";
			$result = $adb->pquery($Query,array());
			$data = array();
			for($i=0;$i<$adb->num_rows($result);$i++){
				$id = $adb->query_result($result,$i,'crmid');
				$setype = $adb->query_result($result,$i,'setype');
				$label = decode_html(decode_html($adb->query_result($result,$i,'label')));
				$action = $adb->query_result($result,$i,'action');
				if(Users_Privileges_Model::isPermitted($setype, 'DetailView', $id)){
					$data[] = array('id' => $id ,'text' =>$label);
				}
			}
			
			$response = new Vtiger_Response();
			$response->setResult($data);
			$response->emit();

		}else if($mode == 'ExportData'){
			$searchvalue = 	$request->get('searchbox');
			$type = $request->get('type');
				
			$markquery = "SELECT usr_route.*,entity.setype,entity.label FROM ctmobile_userderoute as usr_route INNER JOIN vtiger_crmentity as entity ON  usr_route.record = entity.crmid WHERE usr_route.record != '' AND DATE(usr_route.createdtime) BETWEEN '".$date1."' AND '".$date2."'";
			if(!empty($user_id))
				$markquery.=" AND usr_route.userid = '$user_id' ";
			if(!empty($checkmodule))
				$markquery .= " AND entity.setype = '".$checkmodule."'";	
			if(!empty($searchvalue))
				$markquery .= " AND entity.label LIKE '%".$searchvalue."%'";
		 	

		 	$markquery.= " ORDER BY usr_route.createdtime DESC";	
	 		$result = $adb->pquery($markquery,array());
	 		$numofrows = $adb->num_rows($result);
	 		if($numofrows){
	 			$headers = array('Record Label','Date Time','Action','User Name','Latitude','Longitude','Map Link');
	 			$entries = array();
	 			for($i=0;$i<$numofrows;$i++){
	 				$row = $adb->query_result_rowdata($result,$i);
	 				if($row['createdtime'] != ''){
	 					$row['createdtime'] = Vtiger_Datetime_UIType::getDisplayValue($row['createdtime']);
	 				}
	 				$UserName  = '';
	 				if($row['userid'] != ''){
	 					$userModel = Users_Record_Model::getInstanceById($row['userid'],'Users');
	 					$UserName = decode_html(decode_html($userModel->get('first_name').' '.$userModel->get('last_name')));
	 				}
	 				if(Users_Privileges_Model::isPermitted($row['setype'], 'DetailView', $row['record'])){
	 					$entries[] = array(decode_html(decode_html($row['label'])),$row['createdtime'],$row['action'],$UserName,$row['latitude'],$row['longitude'],'https://maps.google.com/?q='.$row['latitude'].','.$row['longitude']);
	 				}
	 			}
	 			$this->output($headers,$entries,$type);
	 			
	 		}
		}else if($mode == 'timeline'){
			$html = '';
			$data = array();
			$draw = $_POST['draw'];
			$start = $_POST['start'];
			$length = $_POST['length'];

			$markquery = "SELECT usr_route.*,entity.setype,entity.label FROM ctmobile_userderoute as usr_route INNER JOIN vtiger_crmentity as entity ON  usr_route.record = entity.crmid WHERE usr_route.record != '' AND DATE(usr_route.createdtime) BETWEEN '".$date1."' AND '".$date2."'";
			if(!empty($user_id))
				$markquery.=" AND usr_route.userid = '$user_id' ";
			if(!empty($checkmodule))
				$markquery .= " AND entity.setype = '".$checkmodule."'";	
			if(!empty($searchvalue))
				$markquery .= " AND entity.label LIKE '%".$searchvalue."%'";
			
			$markquery.= " ORDER BY usr_route.createdtime DESC";
			$totalQuery = $markquery;
			if($start != '' && $length != ''){
				$markquery.= " LIMIT $start, $length";
			}
			
			$result1 = $adb->pquery($totalQuery,array());
			$recordsTotal = $adb->num_rows($result1);
			$result2 = $adb->pquery($markquery,array());
			$last_latitude = '';
			$last_longitude = '';
			for($i=0;$i<$adb->num_rows($result2);$i++){
				$latitude = $adb->query_result($result2,$i,'latitude');
				$longitude = $adb->query_result($result2,$i,'longitude');
				$userid  = $adb->query_result($result2,$i,'userid');
				$userName = "";
				if($userid != ''){
					$userModel = Users_Record_Model::getInstanceById($userid,'Users');
 					$userName = decode_html(decode_html($userModel->get('first_name').' '.$userModel->get('last_name')));
				}
				$recordid = $adb->query_result($result2,$i,'record');
				$action = $adb->query_result($result2,$i,'action');
				$createdtime = $adb->query_result($result2,$i,'createdtime');
				$module = $adb->query_result($result2,$i,'setype');
				$label = $adb->query_result($result2,$i,'label');

				$iconimagename = strtolower($module).'.png';
				if($action == 'edit'){
					$action = 'Updated';
					$DetaiViewurl = 'index.php?module='.$module.'&view=Detail&record='.$recordid;
					if($module == 'ModComments'){
						$iconimagename = 'chat.png';
					}
				}else{
					if($module == 'ModComments'){
						$CommentQuery = "SELECT * FROM vtiger_modcomments INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_modcomments.related_to WHERE modcommentsid = ?";
						$commentResult = $adb->pquery($CommentQuery,array($recordid));
						$setype = $adb->query_result($commentResult,0,'setype');
						$crmid = $adb->query_result($commentResult,0,'crmid');
						$relatedLabel = $adb->query_result($commentResult,0,'label');
						$DetaiViewurl = 'index.php?module='.$setype.'&relatedModule='.$module.'&view=Detail&record='.$crmid.'&mode=showRelatedList';
						
						$action = 'Commented On '.$relatedLabel;
						//$module = '';
						$iconimagename = 'chat.png';
					}else{
						$action = 'Created';
						$DetaiViewurl = 'index.php?module='.$module.'&view=Detail&record='.$recordid;
					}
					
				}
				$created_time = Vtiger_Util_Helper::formatDateDiffInStrings($createdtime);
				$iconpath = 'layouts/v7/skins/images/moduleImages/'.$iconimagename;
				if(!file_exists($iconpath)){
					$iconpath = 'layouts/v7/skins/images/moduleImages/image.png';
				}
				$entitylabel = '<div id="bodyContent"><p>'.$action.' '.vtranslate($module,$module).' : </p><p><a href="'.$DetaiViewurl.'" target="_blank"><b>'.$label.' </b></a><p>'.$created_time.'</p></div>';

				$data[] = array('lat'=>(double)$latitude,'lng'=>(double)$longitude,'label'=>$entitylabel,'activitytime'=>'<small title="'.$createdtime.'">'.$created_time.'</small>','moduleimg'=>"<img src='".$iconpath."' style='height: 30px;'' />",'action'=>"<a href='$DetaiViewurl' target='_blank' style='color:#15c;'>".$label."</a> <b>".$action."</b>",'modifiedby'=>$userName);

			}

			$result = array('draw'=>(int)$draw,'recordsTotal'=>$recordsTotal,'recordsFiltered'=>$recordsTotal,'data'=>$data);
			echo json_encode($result);
			exit;
		}else if($mode == 'ServerSideAjax'){
			$data = array();
			$draw = $_POST['draw'];
			$start = $_POST['start'];
			$length = $_POST['length'];
			$poliquery = "";
			$searchtext ="";
			$searchaction ="";
			
			$markquery = "SELECT usr_route.*,entity.setype,entity.label FROM ctmobile_userderoute as usr_route INNER JOIN vtiger_crmentity as entity ON  usr_route.record = entity.crmid WHERE usr_route.record != '' AND DATE(usr_route.createdtime) BETWEEN '".$date1."' AND '".$date2."'";
			if(!empty($user_id))
				$markquery.=" AND usr_route.userid = '$user_id' ";
			if(!empty($checkmodule))
				$markquery .= " AND entity.setype = '".$checkmodule."'";	
			if(!empty($searchvalue))
				$markquery .= " AND entity.label LIKE '%".$searchvalue."%'";
			
			$markquery.= " ORDER BY usr_route.createdtime DESC";
			$totalQuery = $markquery;
			if($start != '' && $length != ''){
				$markquery.= " LIMIT $start, $length";
			}

			$result1 = $adb->pquery($totalQuery,array());
			$recordsTotal = $adb->num_rows($result1);
			$result2 = $adb->pquery($markquery,array());
			$last_latitude = '';
			$last_longitude = '';
			for($i=0;$i<$adb->num_rows($result2);$i++){
				$latitude = $adb->query_result($result2,$i,'latitude');
				$longitude = $adb->query_result($result2,$i,'longitude');
				$userid  = $adb->query_result($result2,$i,'userid');
				$userName = "";
				if($userid != ''){
					$userModel = Users_Record_Model::getInstanceById($userid,'Users');
 					$userName = decode_html(decode_html($userModel->get('first_name').' '.$userModel->get('last_name')));
				}
				$recordid = $adb->query_result($result2,$i,'record');
				$action = $adb->query_result($result2,$i,'action');
				$createdtime = $adb->query_result($result2,$i,'createdtime');
				$module = $adb->query_result($result2,$i,'setype');
				$label = $adb->query_result($result2,$i,'label');		
				
				if($action == 'edit'){
					$action = 'Updated';
					$DetaiViewurl = 'index.php?module='.$module.'&view=Detail&record='.$recordid;
				}else{
					if($module == 'ModComments'){
						$CommentQuery = "SELECT * FROM vtiger_modcomments INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_modcomments.related_to WHERE modcommentsid = ?";
						$commentResult = $adb->pquery($CommentQuery,array($recordid));
						$setype = $adb->query_result($commentResult,0,'setype');
						$crmid = $adb->query_result($commentResult,0,'crmid');
						$relatedLabel = $adb->query_result($commentResult,0,'label');
						$DetaiViewurl = 'index.php?module='.$setype.'&relatedModule='.$module.'&view=Detail&record='.$crmid.'&mode=showRelatedList';
						
						$action = 'Commented On '.$relatedLabel;
						//$module = '';
					}else{
						$action = 'Created';
						$DetaiViewurl = 'index.php?module='.$module.'&view=Detail&record='.$recordid;
					}
					
				}
				if($module == 'Events')
					$module = 'Calendar';		
				$created_time = Vtiger_Util_Helper::formatDateDiffInStrings($createdtime);
				$entitylabel = '<div id="bodyContent"><p>'.$action.' '.vtranslate($module,$module).' : </p><p><a href="'.$DetaiViewurl.'" target="_blank"><b>'.$label.' </b></a><p>'.$created_time.'</p></div>';
				if(Users_Privileges_Model::isPermitted($module, 'DetailView', $recordid)){
					$detailLink = '<a title=""><i class="fa fa-map-marker"></i></a>&nbsp;&nbsp;&nbsp;<a href="index.php?module='.$module.'&view=Detail&record='.$recordid.'" target="_blank" title="'.vtranslate('LBL_VIEW_DETAILS').'"><i class="fa fa-eye"></i></a>';
					$data[] = array('lat'=>(double)$latitude,'lng'=>(double)$longitude,'label'=>$entitylabel,'record_label'=>$label,'datetime'=>Vtiger_Datetime_UIType::getDisplayValue($createdtime),'action'=>$action,'view_details'=>$detailLink,'username'=>$userName);
				}
				
			}
			$result = array('draw'=>(int)$draw,'recordsTotal'=>$recordsTotal,'recordsFiltered'=>$recordsTotal,'data'=>$data);
			echo json_encode($result);
			exit;
		}else{
			$poliquery = "";
			$searchtext ="";
			$searchaction ="";
			
			$markquery = "SELECT usr_route.*,entity.setype,entity.label FROM ctmobile_userderoute as usr_route INNER JOIN vtiger_crmentity as entity ON  usr_route.record = entity.crmid WHERE usr_route.record != '' AND DATE(usr_route.createdtime) BETWEEN '".$date1."' AND '".$date2."'";
			if(!empty($user_id))
				$markquery.=" AND usr_route.userid = '$user_id' ";
			if(!empty($checkmodule))
				$markquery .= " AND entity.setype = '".$checkmodule."'";	
			if(!empty($searchvalue))
				$markquery .= " AND entity.label LIKE '%".$searchvalue."%'";
			
			$markquery.= " ORDER BY usr_route.createdtime DESC";
			$result2 = $adb->pquery($markquery,array());
			$last_latitude = '';
			$last_longitude = '';
			for($i=0;$i<$adb->num_rows($result2);$i++){
				$latitude = $adb->query_result($result2,$i,'latitude');
				$longitude = $adb->query_result($result2,$i,'longitude');
				$userid  = $adb->query_result($result2,$i,'userid');
				$userName = "";
				if($userid != ''){
					$userModel = Users_Record_Model::getInstanceById($userid,'Users');
 					$userName = decode_html(decode_html($userModel->get('first_name').' '.$userModel->get('last_name')));
				}
				$recordid = $adb->query_result($result2,$i,'record');
				$action = $adb->query_result($result2,$i,'action');
				$createdtime = $adb->query_result($result2,$i,'createdtime');
				$module = $adb->query_result($result2,$i,'setype');
				$label = $adb->query_result($result2,$i,'label');		
				
				if($action == 'edit'){
					$action = 'Updated';
					$DetaiViewurl = 'index.php?module='.$module.'&view=Detail&record='.$recordid;
				}else{
					if($module == 'ModComments'){
						$CommentQuery = "SELECT * FROM vtiger_modcomments INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_modcomments.related_to WHERE modcommentsid = ?";
						$commentResult = $adb->pquery($CommentQuery,array($recordid));
						$setype = $adb->query_result($commentResult,0,'setype');
						$crmid = $adb->query_result($commentResult,0,'crmid');
						$relatedLabel = $adb->query_result($commentResult,0,'label');
						$DetaiViewurl = 'index.php?module='.$setype.'&relatedModule='.$module.'&view=Detail&record='.$crmid.'&mode=showRelatedList';
						
						$action = 'Commented On '.$relatedLabel;
						$module = '';
					}else{
						$action = 'Created';
						$DetaiViewurl = 'index.php?module='.$module.'&view=Detail&record='.$recordid;
					}
					
				}
				$created_time = Vtiger_Util_Helper::formatDateDiffInStrings($createdtime);
				$entitylabel = '<div id="bodyContent"><p>'.$action.' '.vtranslate($module,$module).' : </p><p><a href="'.$DetaiViewurl.'" target="_blank"><b>'.$label.' </b></a><p>'.$created_time.'</p></div>';
				if(Users_Privileges_Model::isPermitted($module, 'DetailView', $recordid)){
					$detailLink = '<a title=""><i class="fa fa-map-marker"></i></a>&nbsp;&nbsp;&nbsp;<a href="index.php?module='.$module.'&view=Detail&record='.$recordid.'" target="_blank" title="'.vtranslate('LBL_VIEW_DETAILS').'"><i class="fa fa-eye"></i></a>';
					$data['marker'][] = array('lat'=>(double)$latitude,'lng'=>(double)$longitude,'label'=>$entitylabel,'record_label'=>$label,'datetime'=>Vtiger_Datetime_UIType::getDisplayValue($createdtime),'action'=>$action,'view_details'=>$detailLink,'username'=>$userName);
				}
				
			}
			
			$response = new Vtiger_Response();
			$response->setEmitType(Vtiger_Response::$EMIT_JSON);
			$response->setResult($data);
			$response->emit();
		}
	}

	function output($headers, $entries,$type) {
		// for content disposition header comma should not be there in filename 
		if($type == 'csv'){
			$fileName = 'TeamTrackingData.csv';
			$exportType = 'text/csv';
		}else{
			$fileName = 'TeamTrackingData.xls';
			$exportType = 'application/x-msexcel';
		}
		header("Content-Disposition:attachment;filename=$fileName");
		header("Content-Type:$exportType;charset=UTF-8");
		header("Expires: Mon, 31 Dec 2000 00:00:00 GMT" );
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT" );
		header("Cache-Control: post-check=0, pre-check=0", false );

		if($type == 'csv'){
			$header = implode("\", \"", $headers);
			$header = "\"" .$header;
			$header .= "\"\r\n";
			echo $header;
			foreach($entries as $row) {
				foreach ($row as $key => $value) {
					/* To support double quotations in CSV format
					 * To review: http://creativyst.com/Doc/Articles/CSV/CSV01.htm#EmbedBRs
					 */
					$row[$key] = str_replace('"', '""', $value);
				}
				$line = implode("\",\"",$row);
				$line = "\"" .$line;
				$line .= "\"\r\n";
				echo $line;
			}
		}else{
			$header =  implode("\t", $headers) . "\n";
			echo $header;
			foreach($entries as $row) {
				echo implode("\t", array_values($row)) . "\n";
			}

		}
	}
}
?>
