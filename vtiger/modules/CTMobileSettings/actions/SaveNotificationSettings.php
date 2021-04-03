<?php
/*+*******************************************************************************
 * The content of this file is subject to the CRMTiger Pro license.
 * ("License"); You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is vTiger
 * The Modified Code of the Original Code owned by https://crmtiger.com/
 * Portions created by CRMTiger.com are Copyright(C) CRMTiger.com
 * All Rights Reserved.
  ***************************************************************************** */
require_once('include/utils/utils.php');
require_once 'modules/com_vtiger_workflow/VTEntityMethodManager.inc';

class CTMobileSettings_SaveNotificationSettings_Action extends Vtiger_Save_Action {

    public function process(Vtiger_Request $request) {
        global $adb;
        $event_invitation=$request->get("event_invitation");
        if($event_invitation == ''){
            $event_invitation = '0';
        }
        $selectResult =  $adb->pquery("SELECT workflow_id FROM ctmobile_notification_settings WHERE notification_type = ?",array('event_invitation'));
        $workflow_id = $adb->query_result($selectResult,0,'workflow_id');
        $event_invitation_title = $request->get("event_invitation_title");
        $event_invitation_message = $request->get("event_invitation_message");
        if($event_invitation == '1'){
            if(!$workflow_id){
                    $selectMethod =  $adb->pquery("SELECT workflowtasks_entitymethod_id FROM com_vtiger_workflowtasks_entitymethod WHERE module_name = ? AND method_name = ?",array('Events','SendEventNotificationInviteUser'));
                    if($adb->num_rows($selectMethod) == 0){
                        $module = 'Events';
                        $entityMethodManager = new VTEntityMethodManager($adb); 
                        $entityMethodManager->addEntityMethod($module, "SendEventNotificationInviteUser","modules/CTPushNotification/DefaultPushNotification.php","SendEventNotificationInviteUser");
                    }

                    $taskDecodedArray = array('record'=>'','workflowname'=>'Event Invitation','summary'=>'Event Invitation','module_name'=>$module,'status'=>'active','workflow_trigger'=>'3','workflow_recurrence'=>'3','schtypeid'=>'1','schdayofweek'=>'','schdate'=>'','schtime'=>'','conditions'=>'{}','filtersavedinnew'=>'6','date_filters'=>'','advanceFilterOpsByFieldType'=>'','advanceFilterOptions'=>'','columnname'=>'none','comparator'=>'none','column_condition'=>'or','condition'=>'and');
                    $taskDecodedArray['tasks'][0] = '{"module":"Workflows","parent":"Settings","action":"TaskAjax","mode":"Save","for_workflow":"","task_id":"","taskType":"VTEntityMethodTask","tmpTaskId":"121029191741238","summary":"Event Invitation to invite users","methodName":"SendEventNotificationInviteUser"}';
                    $vt_request = new Vtiger_Request($taskDecodedArray, $taskDecodedArray);
                    $workflow_id = $this->SaveWorkflow($vt_request);
                    $adb->pquery('UPDATE ctmobile_notification_settings SET workflow_id = ? WHERE notification_type = ?',array($workflow_id,'event_invitation'));
            }else{
                $status = 1;
                Settings_Workflows_Record_Model::updateWorkflowStatus($workflow_id, $status);
            }
        }else{
            if($workflow_id){
                $status = 0;
                Settings_Workflows_Record_Model::updateWorkflowStatus($workflow_id, $status);
            }
        }
        $adb->pquery('UPDATE ctmobile_notification_settings SET notification_enabled = ?,notification_title=?,notification_message=? WHERE notification_type = ?',array($event_invitation,$event_invitation_title,$event_invitation_message,'event_invitation'));

        $event_reminder=$request->get("event_reminder");
        if($event_reminder == ''){
            $event_reminder = '0';
        }
        
        $event_reminder_time = (int)$request->get('event_reminder_time');
        $event_reminder_title = $request->get("event_reminder_title");
        $event_reminder_message = $request->get("event_reminder_message");
        $selectResult =  $adb->pquery("SELECT workflow_id FROM ctmobile_notification_settings WHERE notification_type = ?",array('event_reminder'));
        $workflow_id2 = $adb->query_result($selectResult,0,'workflow_id');
        if($event_reminder == '1'){
            if(!$workflow_id2){
                    $selectMethod =  $adb->pquery("SELECT workflowtasks_entitymethod_id FROM com_vtiger_workflowtasks_entitymethod WHERE module_name = ? AND method_name = ?",array('Events','SendEventReminderNotification'));
                    if($adb->num_rows($selectMethod) == 0){
                        $module = 'Events';
                        $entityMethodManager = new VTEntityMethodManager($adb); 
                        $entityMethodManager->addEntityMethod($module, "SendEventReminderNotification","modules/CTPushNotification/DefaultPushNotification.php","SendEventReminderNotification");
                    }

                    $taskDecodedArray = array('record'=>'','workflowname'=>'Event Reminder','summary'=>'Event Reminder','module_name'=>$module,'status'=>'active','workflow_trigger'=>'3','workflow_recurrence'=>'3','schtypeid'=>'1','schdayofweek'=>'','schdate'=>'','schtime'=>'','conditions'=>'{}','filtersavedinnew'=>'6','date_filters'=>'','advanceFilterOpsByFieldType'=>'','advanceFilterOptions'=>'','columnname'=>'none','comparator'=>'none','column_condition'=>'or','condition'=>'and');
                    $taskDecodedArray['tasks'][0] = '{"module":"Workflows","parent":"Settings","action":"TaskAjax","mode":"Save","for_workflow":"","task_id":"","taskType":"VTEntityMethodTask","tmpTaskId":"121029191741238","summary":"Event Reminder Notification","methodName":"SendEventReminderNotification"}';
                    $vt_request = new Vtiger_Request($taskDecodedArray, $taskDecodedArray);
                    $workflow_id2 = $this->SaveWorkflow($vt_request);
                    $adb->pquery('UPDATE ctmobile_notification_settings SET workflow_id = ? WHERE notification_type = ?',array($workflow_id2,'event_reminder'));
            }else{
                $status = 1;
                Settings_Workflows_Record_Model::updateWorkflowStatus($workflow_id2, $status);
            }
        }else{
            if($workflow_id2){
                $status = 0;
                Settings_Workflows_Record_Model::updateWorkflowStatus($workflow_id2, $status);
            }
        }
        $adb->pquery('UPDATE ctmobile_notification_settings SET notification_enabled = ?,reminder_time = ?,notification_title=?,notification_message=? WHERE notification_type = ?',array($event_reminder,$event_reminder_time,$event_reminder_title,$event_reminder_message,'event_reminder'));

        $record_assigned=$request->get("record_assigned");
        $record_assigned_title = $request->get('record_assigned_title');
        $record_assigned_message = $request->get('record_assigned_message');
        if($record_assigned == ''){
            $record_assigned = '0';
        }
        $adb->pquery('UPDATE ctmobile_notification_settings SET notification_enabled = ?,notification_title=?,notification_message=? WHERE notification_type = ?',array($record_assigned,$record_assigned_title,$record_assigned_message,'record_assigned'));

        $comment_mentioned=$request->get("comment_mentioned");
        $comment_mentioned_title = $request->get('comment_mentioned_title');
        $comment_mentioned_message = $request->get('comment_mentioned_message');
        if($comment_mentioned == ''){
            $comment_mentioned = '0';
        }
        $adb->pquery('UPDATE ctmobile_notification_settings SET notification_enabled = ?,notification_title=?,notification_message=? WHERE notification_type = ?',array($comment_mentioned,$comment_mentioned_title,$comment_mentioned_message,'comment_mentioned'));

        $comment_assigned=$request->get("comment_assigned");
        $comment_assigned_title = $request->get('comment_assigned_title');
        $comment_assigned_message = $request->get('comment_assigned_message');
        if($comment_assigned == ''){
            $comment_assigned = '0';
        }
        $adb->pquery('UPDATE ctmobile_notification_settings SET notification_enabled = ?,notification_title=?,notification_message=? WHERE notification_type = ?',array($comment_assigned,$comment_assigned_title,$comment_assigned_message,'comment_assigned'));

        $task_reminder=$request->get("task_reminder");
        if($task_reminder == ''){
            $task_reminder = '0';
        }
        $task_reminder_time = (int)$request->get('task_reminder_time');
        $task_reminder_title = $request->get("task_reminder_title");
        $task_reminder_message = $request->get("task_reminder_message");
        $selectResult =  $adb->pquery("SELECT workflow_id FROM ctmobile_notification_settings WHERE notification_type = ?",array('task_reminder'));
        $workflow_id3 = $adb->query_result($selectResult,0,'workflow_id');
        if($task_reminder == '1'){
            if(!$workflow_id3){
                    $selectMethod =  $adb->pquery("SELECT workflowtasks_entitymethod_id FROM com_vtiger_workflowtasks_entitymethod WHERE module_name = ? AND method_name = ?",array('Calendar','SendTaskReminderNotification'));
                    if($adb->num_rows($selectMethod) == 0){
                        $module = 'Calendar';
                        $entityMethodManager = new VTEntityMethodManager($adb); 
                        $entityMethodManager->addEntityMethod($module, "SendTaskReminderNotification","modules/CTPushNotification/DefaultPushNotification.php","SendTaskReminderNotification");
                    }

                    $taskDecodedArray = array('record'=>'','workflowname'=>'Task Reminder','summary'=>'Task Reminder','module_name'=>$module,'status'=>'active','workflow_trigger'=>'3','workflow_recurrence'=>'3','schtypeid'=>'1','schdayofweek'=>'','schdate'=>'','schtime'=>'','conditions'=>'{}','filtersavedinnew'=>'6','date_filters'=>'','advanceFilterOpsByFieldType'=>'','advanceFilterOptions'=>'','columnname'=>'none','comparator'=>'none','column_condition'=>'or','condition'=>'and');
                    $taskDecodedArray['tasks'][0] = '{"module":"Workflows","parent":"Settings","action":"TaskAjax","mode":"Save","for_workflow":"","task_id":"","taskType":"VTEntityMethodTask","tmpTaskId":"121029191741238","summary":"Task Reminder Notification","methodName":"SendTaskReminderNotification"}';
                    $vt_request = new Vtiger_Request($taskDecodedArray, $taskDecodedArray);
                    $workflow_id3 = $this->SaveWorkflow($vt_request);
                    $adb->pquery('UPDATE ctmobile_notification_settings SET workflow_id = ? WHERE notification_type = ?',array($workflow_id3,'task_reminder'));
            }else{
                $status = 1;
                Settings_Workflows_Record_Model::updateWorkflowStatus($workflow_id3, $status);
            }
        }else{
            if($workflow_id3){
                $status = 0;
                Settings_Workflows_Record_Model::updateWorkflowStatus($workflow_id3, $status);
            }
        }
        $adb->pquery('UPDATE ctmobile_notification_settings SET notification_enabled = ?,reminder_time = ?,notification_title=?,notification_message=? WHERE notification_type = ?',array($task_reminder,$task_reminder_time,$task_reminder_title,$task_reminder_message,'task_reminder'));


        $follow_record=$request->get("follow_record");
        $follow_record_title = $request->get('follow_record_title');
        $follow_record_message = $request->get('follow_record_message');
        if($follow_record == ''){
            $follow_record = '0';
        }
        $adb->pquery('UPDATE ctmobile_notification_settings SET notification_enabled = ?,notification_title=?,notification_message=? WHERE notification_type = ?',array($follow_record,$follow_record_title,$follow_record_message,'follow_record'));

        $record_assigned_module =  $request->get('record_assigned_module');
        if(!empty($record_assigned_module)){
            $selectSQL = $adb->pquery("SELECT notification_id FROM ctmobile_notification_settings WHERE notification_type = ?",array('record_assigned'));
            $notification_id = $adb->query_result($selectSQL,0,'notification_id');
            if($notification_id){
                $existingRecordModules = array();
                $getModuleSql = $adb->pquery("SELECT id,modulename,workflow_id FROM ctmobile_notification_module_settings WHERE notification_id = ?",array($notification_id));
                for ($i=0; $i < $adb->num_rows($getModuleSql); $i++) { 
                    $id = $adb->query_result($getModuleSql,$i,'id');
                    $modulename = $adb->query_result($getModuleSql,$i,'modulename');
                    $workflow_id = $adb->query_result($getModuleSql,$i,'workflow_id');
                    if(!in_array($modulename, $record_assigned_module)){
                        if($workflow_id){
                            $recordModel = Settings_Workflows_Record_Model::getInstance($workflow_id);
                            $recordModel->delete();
                            $deleteSQl = $adb->pquery("DELETE FROM ctmobile_notification_module_settings WHERE id = ?",array($id));
                        }
                    }else{
                        $existingRecordModules[] = $modulename;
                        if($record_assigned == '0'){
                            $status = 0;
                            Settings_Workflows_Record_Model::updateWorkflowStatus($workflow_id, $status);
                        }else{
                            $status = 1;
                            Settings_Workflows_Record_Model::updateWorkflowStatus($workflow_id, $status);
                        }
                    }
                }
                foreach ($record_assigned_module as $key => $module) {
                    if(!in_array($module, $existingRecordModules)){
                        $taskDecodedArray = array('record'=>'','workflowname'=>'When Record assigned for '.vtranslate($module,$module),'summary'=>'When Record assigned for '.vtranslate($module,$module),'module_name'=>$module,'status'=>'active','workflow_trigger'=>'3','workflow_recurrence'=>'2','schtypeid'=>'1','schdayofweek'=>'','schdate'=>'','schtime'=>'','conditions'=>'{}','filtersavedinnew'=>'6','date_filters'=>'','advanceFilterOpsByFieldType'=>'','advanceFilterOptions'=>'','columnname'=>'none','comparator'=>'none','column_condition'=>'or','condition'=>'and');
                        $taskDecodedArray['tasks'][0] = '{"module":"Workflows","parent":"Settings","action":"TaskAjax","mode":"Save","for_workflow":"","task_id":"","taskType":"VTPushNotification","tmpTaskId":"12102819738498","summary":"'.$record_assigned_title.'","sms_recepient":",record_owner","content":"'.$record_assigned_message.'"}';
                        $vt_request = new Vtiger_Request($taskDecodedArray, $taskDecodedArray);
                        $workflow_id = $this->SaveWorkflow($vt_request);
                        $InsertModuleSql = $adb->pquery("INSERT INTO ctmobile_notification_module_settings(modulename,notification_id,workflow_id) VALUES(?,?,?)",array($module,$notification_id,$workflow_id));
                    }
                }
            }
        }else{
            $selectSQL = $adb->pquery("SELECT notification_id FROM ctmobile_notification_settings WHERE notification_type = ?",array('record_assigned'));
            $notification_id = $adb->query_result($selectSQL,0,'notification_id');
            if($notification_id){
                $existingRecordModules = array();
                $getModuleSql = $adb->pquery("SELECT id,modulename,workflow_id FROM ctmobile_notification_module_settings WHERE notification_id = ?",array($notification_id));
                for ($i=0; $i < $adb->num_rows($getModuleSql); $i++) { 
                    $id = $adb->query_result($getModuleSql,$i,'id');
                    $modulename = $adb->query_result($getModuleSql,$i,'modulename');
                    $workflow_id = $adb->query_result($getModuleSql,$i,'workflow_id');
                    if($workflow_id){
                        $recordModel = Settings_Workflows_Record_Model::getInstance($workflow_id);
                        $recordModel->delete();
                        $deleteSQl = $adb->pquery("DELETE FROM ctmobile_notification_module_settings WHERE id = ?",array($id));
                    }
                }
            }
        }

        $comment_assigned_module =  $request->get('comment_assigned_module');
        if(!empty($comment_assigned_module)){
            $selectSQL = $adb->pquery("SELECT notification_id FROM ctmobile_notification_settings WHERE notification_type = ?",array('comment_assigned'));
            $notification_id = $adb->query_result($selectSQL,0,'notification_id');
            if($notification_id){
                $existingCommentsModules = array();
                $getModuleSql = $adb->pquery("SELECT id,modulename,workflow_id FROM ctmobile_notification_module_settings WHERE notification_id = ?",array($notification_id));
                for ($i=0; $i < $adb->num_rows($getModuleSql); $i++) { 
                    $id = $adb->query_result($getModuleSql,$i,'id');
                    $modulename = $adb->query_result($getModuleSql,$i,'modulename');
                    $workflow_id = $adb->query_result($getModuleSql,$i,'workflow_id');
                    if(!in_array($modulename, $comment_assigned_module)){
                        if($workflow_id){
                            $recordModel = Settings_Workflows_Record_Model::getInstance($workflow_id);
                            $recordModel->delete();
                            $deleteSQl = $adb->pquery("DELETE FROM ctmobile_notification_module_settings WHERE id = ?",array($id));
                        }
                    }else{
                        $existingCommentsModules[] = $modulename;
                        if($comment_assigned == '0'){
                            $status = 0;
                            Settings_Workflows_Record_Model::updateWorkflowStatus($workflow_id, $status);
                        }else{
                            $status = 1;
                            Settings_Workflows_Record_Model::updateWorkflowStatus($workflow_id, $status);
                        }
                    }
                }
                foreach ($comment_assigned_module as $key => $module) {
                    if(!in_array($module, $existingCommentsModules)){
                        $taskDecodedArray = array('record'=>'','workflowname'=>'When Comment added to record assigned to you for '.vtranslate($module,$module),'summary'=>'When Comment added to record assigned to you for '.vtranslate($module,$module),'module_name'=>$module,'status'=>'active','workflow_trigger'=>'3','workflow_recurrence'=>'3','schtypeid'=>'1','schdayofweek'=>'','schdate'=>'','schtime'=>'','conditions'=>'{"1":{"columns":{"0":{"columnname":"_VT_add_comment","comparator":"is added","valuetype":"rawtext","column_condition":"","groupid":"0"}},"condition":"and"}}','filtersavedinnew'=>'6','date_filters'=>'','advanceFilterOpsByFieldType'=>'','advanceFilterOptions'=>'','columnname'=>'none','comparator'=>'none','column_condition'=>'or','condition'=>'and');
                        $taskDecodedArray['tasks'][0] = '{"module":"Workflows","parent":"Settings","action":"TaskAjax","mode":"Save","for_workflow":"","task_id":"","taskType":"VTPushNotification","tmpTaskId":"12102819738498","summary":"'.$comment_assigned_title.'","sms_recepient":",record_owner","content":"'.$comment_assigned_message.'"}';
                        $vt_request = new Vtiger_Request($taskDecodedArray, $taskDecodedArray);
                        $workflow_id = $this->SaveWorkflow($vt_request);
                        $InsertModuleSql = $adb->pquery("INSERT INTO ctmobile_notification_module_settings(modulename,notification_id,workflow_id) VALUES(?,?,?)",array($module,$notification_id,$workflow_id));
                    }
                }
            }
        }else{
            $selectSQL = $adb->pquery("SELECT notification_id FROM ctmobile_notification_settings WHERE notification_type = ?",array('comment_assigned'));
            $notification_id = $adb->query_result($selectSQL,0,'notification_id');
            if($notification_id){
                $existingCommentsModules = array();
                $getModuleSql = $adb->pquery("SELECT id,modulename,workflow_id FROM ctmobile_notification_module_settings WHERE notification_id = ?",array($notification_id));
                for ($i=0; $i < $adb->num_rows($getModuleSql); $i++) { 
                    $id = $adb->query_result($getModuleSql,$i,'id');
                    $modulename = $adb->query_result($getModuleSql,$i,'modulename');
                    $workflow_id = $adb->query_result($getModuleSql,$i,'workflow_id');
                    if($workflow_id){
                        $recordModel = Settings_Workflows_Record_Model::getInstance($workflow_id);
                        $recordModel->delete();
                        $deleteSQl = $adb->pquery("DELETE FROM ctmobile_notification_module_settings WHERE id = ?",array($id));
                    }
                }
            }
        }

        $follow_record_module =  $request->get('follow_record_module');
        if(!empty($follow_record_module)){
            $selectSQL = $adb->pquery("SELECT * FROM ctmobile_notification_settings WHERE notification_type = ?",array('follow_record'));
            $notification_id = $adb->query_result($selectSQL,0,'notification_id');
            if($notification_id){
                $existingFollowModules = array();
                $getModuleSql = $adb->pquery("SELECT id,modulename,workflow_id FROM ctmobile_notification_module_settings WHERE notification_id = ?",array($notification_id));
                for ($i=0; $i < $adb->num_rows($getModuleSql); $i++) { 
                    $id = $adb->query_result($getModuleSql,$i,'id');
                    $modulename = $adb->query_result($getModuleSql,$i,'modulename');
                    $workflow_id = $adb->query_result($getModuleSql,$i,'workflow_id');
                    if(!in_array($modulename, $follow_record_module)){
                        if($workflow_id){
                            $recordModel = Settings_Workflows_Record_Model::getInstance($workflow_id);
                            $recordModel->delete();
                            $deleteSQl = $adb->pquery("DELETE FROM ctmobile_notification_module_settings WHERE id = ?",array($id));
                        }
                    }else{
                        $existingFollowModules[] = $modulename;
                        if($follow_record == '0'){
                            $status = 0;
                            Settings_Workflows_Record_Model::updateWorkflowStatus($workflow_id, $status);
                        }else{
                            $status = 1;
                            Settings_Workflows_Record_Model::updateWorkflowStatus($workflow_id, $status);
                        }
                    }
                }
                foreach ($follow_record_module as $key => $module) {
                    if(!in_array($module, $existingFollowModules)){
                        $selectMethod =  $adb->pquery("SELECT workflowtasks_entitymethod_id FROM com_vtiger_workflowtasks_entitymethod WHERE module_name = ? AND method_name = ?",array($module,'SendNotificationFollowRecord'));
                        if($adb->num_rows($selectMethod) == 0){
                            $entityMethodManager = new VTEntityMethodManager($adb); 
                            $entityMethodManager->addEntityMethod($module, "SendNotificationFollowRecord","modules/CTPushNotification/DefaultPushNotification.php","SendNotificationFollowRecord");
                        }

                        $taskDecodedArray = array('record'=>'','workflowname'=>'Notify when any updates to the record you\'re following for '.vtranslate($module,$module),'summary'=>'Notify when any updates to the record you\'re following for '.vtranslate($module,$module),'module_name'=>$module,'status'=>'active','workflow_trigger'=>'3','workflow_recurrence'=>'3','schtypeid'=>'1','schdayofweek'=>'','schdate'=>'','schtime'=>'','conditions'=>'{}','filtersavedinnew'=>'6','date_filters'=>'','advanceFilterOpsByFieldType'=>'','advanceFilterOptions'=>'','columnname'=>'none','comparator'=>'none','column_condition'=>'or','condition'=>'and');
                        $taskDecodedArray['tasks'][0] = '{"module":"Workflows","parent":"Settings","action":"TaskAjax","mode":"Save","for_workflow":"","task_id":"","taskType":"VTEntityMethodTask","tmpTaskId":"121029191741238","summary":"Notify when any updates on Follow Record","methodName":"SendNotificationFollowRecord"}';
                        $vt_request = new Vtiger_Request($taskDecodedArray, $taskDecodedArray);
                        $workflow_id = $this->SaveWorkflow($vt_request);

                        $InsertModuleSql = $adb->pquery("INSERT INTO ctmobile_notification_module_settings(modulename,notification_id,workflow_id) VALUES(?,?,?)",array($module,$notification_id,$workflow_id));
                    }
                }
            }
        }else{
            $selectSQL = $adb->pquery("SELECT * FROM ctmobile_notification_settings WHERE notification_type = ?",array('follow_record'));
            $notification_id = $adb->query_result($selectSQL,0,'notification_id');
            if($notification_id){
                $existingFollowModules = array();
                $getModuleSql = $adb->pquery("SELECT id,modulename,workflow_id FROM ctmobile_notification_module_settings WHERE notification_id = ?",array($notification_id));
                for ($i=0; $i < $adb->num_rows($getModuleSql); $i++) { 
                    $id = $adb->query_result($getModuleSql,$i,'id');
                    $modulename = $adb->query_result($getModuleSql,$i,'modulename');
                    $workflow_id = $adb->query_result($getModuleSql,$i,'workflow_id');
                    if($workflow_id){
                        $recordModel = Settings_Workflows_Record_Model::getInstance($workflow_id);
                        $recordModel->delete();
                        $deleteSQl = $adb->pquery("DELETE FROM ctmobile_notification_module_settings WHERE id = ?",array($id));
                    }
                }
            }
        }

        $response = new Vtiger_Response();
        $response->setEmitType(Vtiger_Response::$EMIT_JSON);
        $response->setResult(array('_module'=>true));
        $response->emit();
    }


    public function SaveWorkflow(Vtiger_Request $request) {
        $recordId = $request->get('record');
        $summary = $request->get('summary');
        $moduleName = $request->get('module_name');
        $conditions = $request->get('conditions');
        $filterSavedInNew = $request->get('filtersavedinnew');
        $workflow_trigger = $request->get('workflow_trigger');
        $workflow_recurrence = $request->get('workflow_recurrence');
        $name = $request->get('workflowname');
        if ($workflow_trigger == 3) {
            $executionCondition = $workflow_recurrence;
        } else {
            $executionCondition = $workflow_trigger;
        }

        $moduleModel = Settings_Vtiger_Module_Model::getInstance($request->getModule(false));
        if ($recordId) {
            $workflowModel = Settings_Workflows_Record_Model::getInstance($recordId);
        } else {
            $workflowModel = Settings_Workflows_Record_Model::getCleanInstance($moduleName);
        }

        $status = $request->get('status');
        if ($status == "active") {
            $status = 1;
        } else {
            $status = 0;
        }
        require_once 'modules/com_vtiger_workflow/expression_engine/include.inc';

        foreach ($conditions as $info) {
            foreach ($info['columns'] as $conditionRow) {
                if ($conditionRow['valuetype'] == "expression") {
                    try {
                        $parser = new VTExpressionParser(new VTExpressionSpaceFilter(new VTExpressionTokenizer($conditionRow['value'])));
                        $expression = $parser->expression();
                    } catch (Exception $e) {
                        //It should generally not come in to this block of code , Since before save we will be checking expression validation as 
                        //Seperte ajax request
                        echo $e->getMessage();
                        die;
                    }
                }
            }
        }

        $workflowModel->set('summary', $summary);
        $workflowModel->set('module_name', $moduleName);
        $workflowModel->set('conditions', $conditions);
        $workflowModel->set('execution_condition', $executionCondition);
        $workflowModel->set('status', $status);
        $workflowModel->set('name', $name);
        if ($executionCondition == '6') {
            $schtime = $request->get("schtime");
            if (!preg_match('/^[0-2]\d(:[0-5]\d){1,2}$/', $schtime) or substr($schtime, 0, 2) > 23) {  // invalid time format
                $schtime = '00:00';
            }
            $schtime .=':00';

            $workflowModel->set('schtime', $schtime);

            $workflowScheduleType = $request->get('schtypeid');
            $workflowModel->set('schtypeid', $workflowScheduleType);

            $dayOfMonth = null;
            $dayOfWeek = null;
            $month = null;
            $annualDates = null;

            if ($workflowScheduleType == Workflow::$SCHEDULED_WEEKLY) {
                $dayOfWeek = Zend_Json::encode(explode(',', $request->get('schdayofweek')));
            } else if ($workflowScheduleType == Workflow::$SCHEDULED_MONTHLY_BY_DATE) {
                $dayOfMonth = Zend_Json::encode($request->get('schdayofmonth'));
            } else if ($workflowScheduleType == Workflow::$SCHEDULED_ON_SPECIFIC_DATE) {
                $date = $request->get('schdate');
                $dateDBFormat = DateTimeField::convertToDBFormat($date);
                $nextTriggerTime = $dateDBFormat . ' ' . $schtime;
                $currentTime = Vtiger_Util_Helper::getActiveAdminCurrentDateTime();
                if ($nextTriggerTime > $currentTime) {
                    $workflowModel->set('nexttrigger_time', $nextTriggerTime);
                } else {
                    $workflowModel->set('nexttrigger_time', date('Y-m-d H:i:s', strtotime('+10 year')));
                }
                $annualDates = Zend_Json::encode(array($dateDBFormat));
            } else if ($workflowScheduleType == Workflow::$SCHEDULED_ANNUALLY) {
                $annualDates = Zend_Json::encode($request->get('schannualdates'));
            }
            $workflowModel->set('schdayofmonth', $dayOfMonth);
            $workflowModel->set('schdayofweek', $dayOfWeek);
            $workflowModel->set('schannualdates', $annualDates);
        }

        // Added to save the condition only when its changed from vtiger6
        if ($filterSavedInNew == '6') {
            //Added to change advanced filter condition to workflow
            $workflowModel->transformAdvanceFilterToWorkFlowFilter();
        }
        $workflowModel->set('filtersavedinnew', $filterSavedInNew);

        if ($executionCondition == '6') {
            if ($workflowScheduleType == Workflow::$SCHEDULED_HOURLY) {
                $workflowModel->set('nexttrigger_time', $workflowModel->getWorkflowObject()->getNextTriggerTimeValue());
            }
            $workflowModel->save();
            //Update only for scheduled workflows other than specific date
            if (($workflowScheduleType != Workflow::$SCHEDULED_ON_SPECIFIC_DATE || $workflowScheduleType == Workflow::$SCHEDULED_HOURLY) && $executionCondition == '6') {
                $workflowModel->updateNextTriggerTime();
            }
        } else {
            $workflowModel->save();
        }

        $this->saveTasks($workflowModel, $request);

        /*$returnPage = $request->get("returnpage", null);
        $returnSourceModule = $request->get("returnsourcemodule", null);
        $returnSearchValue = $request->get("returnsearch_value", null);
        $redirectUrl = $moduleModel->getDefaultUrl() . "&sourceModule=$returnSourceModule&page=$returnPage&search_value=$returnSearchValue";

        header("Location: " . $redirectUrl);*/
        return $workflowModel->get('workflow_id');
    }

    public function saveTasks($workflowModel, $request) {
        $tasks = $request->getRaw('tasks');
        $id = $workflowModel->get('workflow_id');
        if (!empty($tasks)) {
            foreach ($tasks as $task) {
                $taskDecodedArray = json_decode($task, true);
                $request = new Vtiger_Request($taskDecodedArray, $taskDecodedArray);
                $request->set('for_workflow', $id);
                $this->saveTask($request);
            }
        }
    }

    public function saveTask(Vtiger_Request $request) {
        $workflowId = $request->get('for_workflow');
        if(!empty($workflowId)) {
            $record = $request->get('task_id');
            if($record) {
                $taskRecordModel = Settings_Workflows_TaskRecord_Model::getInstance($record);
            } else {
                $workflowModel = Settings_Workflows_Record_Model::getInstance($workflowId);
                $taskRecordModel = Settings_Workflows_TaskRecord_Model::getCleanInstance($workflowModel, $request->get('taskType'));
            }

            $taskObject = $taskRecordModel->getTaskObject();
            $taskObject->summary = $request->get("summary");

            $active = $request->get("active");
            if($active == "true") {
                $taskObject->active = true;
            } else if ($active == "false"){
                $taskObject->active = false;
            }
            $checkSelectDate = $request->get('check_select_date');

            if(!empty($checkSelectDate)){
                $trigger = array(
                    'days'=>($request->get('select_date_direction') == 'after' ? 1 : -1) * (int)$request->get('select_date_days'),
                    'field'=>$request->get('select_date_field')
                    );
                $taskObject->trigger = $trigger;
            } else {
                $taskObject->trigger = null;
            }

            $fieldNames = $taskObject->getFieldNames();
                        $getRawFields = array('field_value_mapping', 'content', 'fromEmail');
            foreach($fieldNames as $fieldName){
                if(in_array($fieldName, $getRawFields)) {
                    $taskObject->$fieldName = $request->getRaw($fieldName);
                } else {
                    $taskObject->$fieldName = $request->get($fieldName);
                }
                if ($fieldName == 'calendar_repeat_limit_date') {
                    $taskObject->$fieldName = DateTimeField::convertToDBFormat($request->get($fieldName));
                }
            }

            require_once 'modules/com_vtiger_workflow/expression_engine/include.inc';

            $fieldMapping = Zend_Json::decode($taskObject->field_value_mapping);
            if (is_array($fieldMapping)) {
                foreach ($fieldMapping as $key => $mappingInfo) {
                    if ($mappingInfo['valuetype'] == 'expression') {
                        try {
                            $parser = new VTExpressionParser(new VTExpressionSpaceFilter(new VTExpressionTokenizer($mappingInfo['value'])));
                            $expression = $parser->expression();
                        } catch (Exception $e) {
                            $result = new Vtiger_Response();
                            $result->setError($mappingInfo);
                            $result->emit();
                            return;
                        }
                    }
                }
            }

            $taskType = get_class($taskObject);
            if ($taskType === 'VTCreateEventTask' || $taskType === 'VTCreateTodoTask') {
                if($taskType === 'VTCreateEventTask') {
                    $module = 'Events';
                } else {
                    $module = 'Calendar';
                }
                $moduleModel = Vtiger_Module_Model::getInstance($module);
                $fieldsList = $moduleModel->getFields();
                foreach($fieldsList as $fieldName => $fieldModel) {
                    $fieldValue = $request->get($fieldName);
                    if($fieldModel->get('uitype') == 33) {
                        if(is_array($fieldValue)) {
                            $field_list = implode(' |##| ', $fieldValue);
                        } else {
                            $field_list = $fieldValue;
                        }
                        $taskObject->$fieldName = $field_list;
                    } else {
                        $taskObject->$fieldName = $fieldValue;
                    }
                }
            }

            if ($taskType === 'VTCreateEntityTask') {
                $relationModuleModel = Vtiger_Module_Model::getInstance($taskObject->entity_type);
                $ownerFieldModels = $relationModuleModel->getFieldsByType('owner');

                $fieldMapping = Zend_Json::decode($taskObject->field_value_mapping);
                foreach ($fieldMapping as $key => $mappingInfo) {
                    if (array_key_exists($mappingInfo['fieldname'], $ownerFieldModels)) {
                        $userRecordModel = Users_Record_Model::getInstanceById($mappingInfo['value'], 'Users');
                        $ownerName = $userRecordModel->get('user_name');

                        if (!$ownerName && !empty($mappingInfo['value'])) {
                            $groupRecordModel = Settings_Groups_Record_Model::getInstance($mappingInfo['value']);
                            $ownerName = $groupRecordModel->getName();
                        }
                        $fieldMapping[$key]['value'] = $ownerName;
                    }
                }
                $taskObject->field_value_mapping = Zend_Json::encode($fieldMapping);
            }
            $taskRecordModel->save();
        }
    }
}
