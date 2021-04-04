<?php
/*+*******************************************************************************
 * The content of this file is subject to the CRMTiger Pro license.
 * ("License"); You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is vTiger
 * The Modified Code of the Original Code owned by https://crmtiger.com/
 * Portions created by CRMTiger.com are Copyright(C) CRMTiger.com
 * All Rights Reserved.
  ***************************************************************************** */

class CTMobileSettings_SaveRouteGeneralSettings_Action extends Vtiger_Save_Action {

    public function process(Vtiger_Request $request) {
        global $adb;
        $mode = $request->get('mode');
        if($mode == 'SaveStatus'){
             $routeStatus = CTMobileSettings_Module_Model::getRouteStatusFields();
             foreach ($routeStatus as $key => $status) {
                 $routestatusid = $status['routestatusid'];
                 $routestatuslabel = $request->get('status_'.$routestatusid);
                  $adb->pquery("UPDATE ctmobile_routestatus SET routestatuslabel = ? WHERE routestatusid = ?",array($routestatuslabel,$routestatusid));
             }
        }else{
            $distanceUnit = $request->get("distanceUnit");
            $route_users = implode(',',$request->get("route_users"));
            $adb->pquery("TRUNCATE ctmobile_routegeneralsettings");
            $adb->pquery("INSERT INTO ctmobile_routegeneralsettings (route_users,route_distance_unit) VALUES (?,?)",array($route_users,$distanceUnit));
        }

        $response = new Vtiger_Response();
        $response->setEmitType(Vtiger_Response::$EMIT_JSON);
        $response->setResult(true);
        $response->emit();
    } 
}

