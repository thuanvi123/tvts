<?php
/*+*******************************************************************************
 * The content of this file is subject to the CRMTiger Pro license.
 * ("License"); You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is vTiger
 * The Modified Code of the Original Code owned by https://crmtiger.com/
 * Portions created by CRMTiger.com are Copyright(C) CRMTiger.com
 * All Rights Reserved.
  ***************************************************************************** */
class CTMobileSettings_FilterAjax_Action extends Vtiger_Save_Action {
    public function process(Vtiger_Request $request) {
        global $adb;
        $moduleName = $request->get("search_module");
        $filterId = $request->get("filterId");
        
        $listViewModel = Vtiger_ListView_Model::getInstance($moduleName, $filterId);
        $noOfEntries = $listViewModel->getListViewCount();

        $AddressRecords = CTMobileSettings_Module_Model::getAddressRecords($moduleName,$filterId);
        $nonAddressRecords = $noOfEntries - $AddressRecords;

        $nonAddressRecordList =  CTMobileSettings_Module_Model::getNonAddressRecordsList($moduleName,$filterId);
        
        $nonAddressModal = '
            <div class="row">
            <div class="col-md-12">
                <div class="popupEntriesTableContainer">
                    <table class="listview-table table-bordered listViewEntriesTable">
                        <thead>
                            <tr class="listViewHeaders">
                                <th>Entity Field</th>
                            </tr>
                        </thead>';
        foreach($nonAddressRecordList as $LISTVIEW_ENTRY){
            $nonAddressModal.='<tr class="listViewEntries">
                         <td class="listViewEntryValue">
                         <a href="index.php?module='.$moduleName.'&view=Detail&record='.$LISTVIEW_ENTRY['id'].'" target="_blank">'.$LISTVIEW_ENTRY['label'].'</a>
                         </td>
                     </tr>';

        }
                    
        $nonAddressModal.='</table>
                </div>
            </div>
            </div>';

        $data = array('totalRecords'=>$noOfEntries,'AddressRecords'=>$AddressRecords,'nonAddressRecords'=>$nonAddressRecords,'nonAddressModal'=>$nonAddressModal);

        $response = new Vtiger_Response();
        $response->setEmitType(Vtiger_Response::$EMIT_JSON);
        $response->setResult($data);
        $response->emit();
    }
}
