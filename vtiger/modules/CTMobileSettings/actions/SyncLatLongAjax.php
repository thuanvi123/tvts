<?php
/*+*******************************************************************************
 * The content of this file is subject to the CRMTiger Pro license.
 * ("License"); You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is vTiger
 * The Modified Code of the Original Code owned by https://crmtiger.com/
 * Portions created by CRMTiger.com are Copyright(C) CRMTiger.com
 * All Rights Reserved.
  ***************************************************************************** */
class CTMobileSettings_SyncLatLongAjax_Action extends Vtiger_Save_Action {
    public function process(Vtiger_Request $request) {
        global $adb,$current_user;
        $moduleName = $request->get("search_module");
        $filterId = $request->get("filterId");
        
        /*$listViewModel = Vtiger_ListView_Model::getInstance($moduleName, $filterId);
        $query = $listViewModel->getQuery();*/
    
        $ModuleModel =Vtiger_Module_Model::getInstance($moduleName);
        $basetable = $ModuleModel->get('basetable');
        $basetableid = $ModuleModel->get('basetableid');

        $result2 = $adb->pquery("SELECT id FROM vtiger_ws_entity WHERE name=?", array($moduleName));
        $moduleId = $adb->query_result($result2, 0, 'id');

        $resultAddress = $adb->pquery("SELECT * FROM ctmobile_address_fields WHERE module = ?",array($moduleName));
        $count = $adb->num_rows($resultAddress);
        $field = array();
        $addressFields = array();
        for($j=0;$j<$count;$j++){
           $fields = $adb->query_result($resultAddress,$j,'fieldname');
           $test = explode(":",$fields);
           $addressFields[] = $test[1];
           $field[] = $test[1];
        }
        $NameFields = $ModuleModel->getNameFields();
        foreach ($NameFields as $key => $value) {
            $field[] = $value;
        }
        $field[]='id';

        $generator = new QueryGenerator($moduleName, $current_user);
        $generator->initForCustomViewById($filterId);
        $generator->setFields($field);
        $query = $generator->getQuery();
        
        $newQuery  = $query." AND $basetable.$basetableid NOT IN (SELECT recordid FROM ct_address_lat_long WHERE moduleid = $moduleId ) LIMIT 1,500";

        $result  = $adb->pquery($newQuery,array());
        $noOfRecords = $adb->num_rows($result);
        $AddressRecords = array();
        for($i=0;$i<$noOfRecords;$i++){
           $counter = 0;
           $address = '';
           foreach($addressFields as $key => $add){
                $newField = $adb->query_result($result, $i, $add);
                if($newField != '') {
                    if($counter+1 == count($addressFields)){
                        $address .= $newField;
                    }else{
                        $address .= $newField.', ';
                    }
                }
                $counter++;
           }
           $recordData = $this->getLatAndLong($address);
           $id = $adb->query_result($result, $i, $basetableid);
           $recordData['recordid'] = $id;
           $recordData['moduleid'] = $moduleId; 

           $this->insertLatLong($recordData);
        }         

        $response = new Vtiger_Response();
        $response->setEmitType(Vtiger_Response::$EMIT_JSON);
        $response->setResult(array('sync'=>true,'message'=>'sync successfully'));
        $response->emit();
    }

    public function insertLatLong($recordData) {
         global $adb;
         $recordid = $recordData['recordid'];
         $moduleid = $recordData['moduleid'];
         $latitude = $recordData['latitude'];
         $longitude = $recordData['longitude'];
         if($latitude != '' && $longitude != ''){
             $checkRecordExit = $adb->pquery("SELECT * FROM ct_address_lat_long WHERE recordid = ?", array($recordid));
             $countRecord = $adb->num_rows($checkRecordExit);
             
             if($countRecord > 0) {
                $adb->pquery("UPDATE ct_address_lat_long SET latitude = ?, longitude = ? WHERE recordid = ?", array($latitude, $longitude, $recordid));
             } else {
                $adb->pquery("INSERT INTO ct_address_lat_long(recordid, moduleid, latitude, longitude) VALUES(?,?,?,?)", array($recordid, $moduleid, $latitude, $longitude)); 
            } 
         }
         
    }

    public function getLatAndLong($address) {
        // Get lat and long by address
        $address=urlencode($address);
        $data = array();
        $opts = array('http'=>array('header'=>"User-Agent: Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/65.0.3325.146 Safari/537.36\r\n"));
        $context = stream_context_create($opts);
    
        $formattedAddr = str_replace(' ','+',$address);
        global $adb;
        $resultApi = $adb->pquery("SELECT * FROM ctmobile_api_settings",array());
        $apiKey = $adb->query_result($resultApi,0,'api_key');
        if($apiKey != ''){
            //Send request and receive json data by address
            $geocodeFromAddr = file_get_contents('https://maps.googleapis.com/maps/api/geocode/json?address='.$formattedAddr.'&key='.$apiKey, false, $context);
            $output = json_decode($geocodeFromAddr);
            
            //Get latitude and longitute from json data
            $data['latitude']  = $output->results[0]->geometry->location->lat; 
            $data['longitude'] = $output->results[0]->geometry->location->lng;
        }else{
            //Send request and receive json data by address
            $geocodeFromAddr = file_get_contents('https://nominatim.openstreetmap.org/search?q='.$formattedAddr.'&format=json&polygon=1&addressdetails=1',false, $context);
            $output = json_decode($geocodeFromAddr);
            //Get latitude and longitute from json data
            $data['latitude']  = $output[0]->lat;
            $data['longitude'] = $output[0]->lon;
        }
                    
        return $data;
     }
}
