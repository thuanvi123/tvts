<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

/**
 * Events Record Model Class
 */
class CTRoutePlanning_Record_Model extends Vtiger_Record_Model {
    
    public function getRelatedToctroute_realtedtoList() {
        $adb = PearDatabase::getInstance();
        $query = 'SELECT * from vtiger_ctrouteplanrel where ctrouteplanningid=?';
        $result = $adb->pquery($query, array($this->getId()));
        $num_rows = $adb->num_rows($result);

        $ctroute_realtedtoList = array();
        for($i=0; $i<$num_rows; $i++) {
            $row = $adb->fetchByAssoc($result, $i);
            $ctroute_realtedtoList[$i] = $row['ctroute_realtedto'];
        }
        return $ctroute_realtedtoList;
    }

    public function getRelatedCTRouteInfo() {
        $ctroute_realtedtoList = $this->getRelatedToctroute_realtedtoList();
        $relatedContactInfo = array();
        foreach($ctroute_realtedtoList as $ctroute_realtedto) {
            $relatedContactInfo[] = array('name' => decode_html(Vtiger_Util_Helper::toSafeHTML(Vtiger_Util_Helper::getRecordName($ctroute_realtedto))) ,'id' => $ctroute_realtedto);
        }
        return $relatedContactInfo;
     }
     
     public function getRelatedContactInfoFromIds($eventIds){
         $adb = PearDatabase::getInstance();
        $query = 'SELECT vtiger_ctrouteplanrel.ctrouteplanningid as id, vtiger_ctrouteplanrel.ctroute_realtedto, vtiger_contactdetails.email FROM vtiger_ctrouteplanrel INNER JOIN vtiger_contactdetails
                  ON vtiger_contactdetails.ctroute_realtedto = vtiger_ctrouteplanrel.ctroute_realtedto  WHERE ctrouteplanningid in ('. generateQuestionMarks($eventIds) .')';
        $result = $adb->pquery($query, array($eventIds));
        $num_rows = $adb->num_rows($result);

        $contactInfo = array();
        for($i=0; $i<$num_rows; $i++) {
            $row = $adb->fetchByAssoc($result, $i);
            $contactInfo[$row['id']][] = array('name' => Vtiger_Util_Helper::toSafeHTML(Vtiger_Util_Helper::getRecordName($row['ctroute_realtedto'])),
                                    'email' => $row['email'], 'id' => $row['ctroute_realtedto']);
        }
        return $contactInfo;
     }
     
     
}
