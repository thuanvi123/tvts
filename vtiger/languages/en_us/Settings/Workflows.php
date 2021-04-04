<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/
$languageStrings = array(
	//Basic Field Names
	'LBL_NEW' => 'New',
	'LBL_WORKFLOW' => 'Workflow',
	'LBL_CREATING_WORKFLOW' => 'Creating WorkFlow',
	'LBL_EDITING_WORKFLOW' => 'Editing Workflow',
	'LBL_ADD_RECORD' => 'New Workflow',

	//Edit view
	'LBL_STEP_1' => 'Step 1',
	'LBL_ENTER_BASIC_DETAILS_OF_THE_WORKFLOW' => 'Enter basic details of the Workflow',
	'LBL_SPECIFY_WHEN_TO_EXECUTE' => 'Specify when to execute this Workflow',
	'ON_FIRST_SAVE' => 'Only on the first save',
	'ONCE' => 'Until the first time the condition is true',
	'ON_EVERY_SAVE' => 'Every time the record is saved',
	'ON_MODIFY' => 'Every time a record is modified',
	'ON_SCHEDULE' => 'Schedule',
	'MANUAL' => 'System',
	'SCHEDULE_WORKFLOW' => 'Schedule Workflow',
	'ADD_CONDITIONS' => 'Add Conditions',
	'ADD_TASKS' => 'Add Actions',

	//Step2 edit view
	'LBL_EXPRESSION' => 'Expression',
	'LBL_FIELD_NAME' => 'Field',
	'LBL_SET_VALUE' => 'Set Value',
	'LBL_USE_FIELD' => 'Use Field',
	'LBL_USE_FUNCTION' => 'Use Function',
	'LBL_RAW_TEXT' => 'Raw text',
	'LBL_ENABLE_TO_CREATE_FILTERS' => 'Enable to create Filters',
	'LBL_CREATED_IN_OLD_LOOK_CANNOT_BE_EDITED' => 'This workflow was created in older look. Conditions created in older look cannot be edited. You can choose to recreate the conditions, or use the existing conditions without changing them.',
	'LBL_USE_EXISTING_CONDITIONS' => 'Use existing conditions',
	'LBL_RECREATE_CONDITIONS' => 'Recreate Conditions',
	'LBL_SAVE_AND_CONTINUE' => 'Save & Continue',

	//Step3 edit view
	'LBL_ACTIVE' => 'Active',
	'LBL_TASK_TYPE' => 'Action Type',
	'LBL_TASK_TITLE' => 'Action Title',
	'LBL_ADD_TASKS_FOR_WORKFLOW' => 'Add Action for Workflow',
	'LBL_EXECUTE_TASK' => 'Execute Action',
	'LBL_SELECT_OPTIONS' => 'Select Options',
	'LBL_ADD_FIELD' => 'Add field',
	'LBL_ADD_TIME' => 'Add time',
	'LBL_TITLE' => 'Title',
	'LBL_PRIORITY' => 'Priority',
	'LBL_ASSIGNED_TO' => 'Assigned to',
	'LBL_TIME' => 'Time',
	'LBL_DUE_DATE' => 'Due Date',
	'LBL_THE_SAME_VALUE_IS_USED_FOR_START_DATE' => 'The same value is used for the start date',
	'LBL_EVENT_NAME' => 'Event Name',
	'LBL_TYPE' => 'Type',
	'LBL_METHOD_NAME' => 'Method Name',
	'LBL_RECEPIENTS' => 'Recepients',
	'LBL_ADD_FIELDS' => 'Add Fields',
	'LBL_SMS_TEXT' => 'Sms Text',
	'LBL_SET_FIELD_VALUES' => 'Set Field Values',
	'LBL_ADD_FIELD' => 'Add Field',
	'LBL_IN_ACTIVE' => 'In Active',
	'LBL_SEND_NOTIFICATION' => 'Send Notification',
	'LBL_START_TIME' => 'Start Time',
	'LBL_START_DATE' => 'Start Date',
	'LBL_END_TIME' => 'End Time',
	'LBL_END_DATE' => 'End Date',
	'LBL_ENABLE_REPEAT' => 'Enable Repeat',
	'LBL_NO_METHOD_IS_AVAILABLE_FOR_THIS_MODULE' => 'No method is available for this module',
	
	'LBL_NO_TASKS_ADDED' => 'No Actions',
	'LBL_CANNOT_DELETE_DEFAULT_WORKFLOW' => 'You Cannot delete default Workflow',
	'LBL_MODULES_TO_CREATE_RECORD' => 'Create a record in',
	'LBL_EXAMPLE_EXPRESSION' => 'Expression',
	'LBL_EXAMPLE_RAWTEXT' => 'Rawtext',
	'LBL_VTIGER' => 'Vtiger',
	'LBL_EXAMPLE_FIELD_NAME' => 'Field',
	'LBL_NOTIFY_OWNER' => 'notify_owner',
	'LBL_ANNUAL_REVENUE' => 'annual_revenue',
	'LBL_EXPRESSION_EXAMPLE2' => "if mailingcountry == 'India' then concat(firstname,' ',lastname) else concat(lastname,' ',firstname) end",
	'LBL_FROM' => 'From',
	'LBL_RUN_WORKFLOW' => 'Run Workflow',
	'LBL_AT_TIME' => 'At Time',
	'LBL_HOURLY' => 'Hourly',
	'Optional' => 'Optional',
	'ENTER_FROM_EMAIL_ADDRESS'=> 'Enter a From email address',
	'LBL_ADD_TASK' => 'Add Action',
    'Portal Pdf Url' =>'Portal Pdf Url',

	'LBL_DAILY' => 'Daily',
	'LBL_WEEKLY' => 'Weekly',
	'LBL_ON_THESE_DAYS' => 'On these days',
	'LBL_MONTHLY_BY_DATE' => 'Monthly by Date',
	'LBL_MONTHLY_BY_WEEKDAY' => 'Monthly by Weekday',
	'LBL_YEARLY' => 'Yearly',
	'LBL_SPECIFIC_DATE' => 'On Specific Date',
	'LBL_CHOOSE_DATE' => 'Choose Date',
	'LBL_SELECT_MONTH_AND_DAY' => 'Select Month and Date',
	'LBL_SELECTED_DATES' => 'Selected Dates',
	'LBL_EXCEEDING_MAXIMUM_LIMIT' => 'Maximum limit exceeded',
	'LBL_NEXT_TRIGGER_TIME' => 'Next trigger time on',
    'LBL_ADD_TEMPLATE' => 'Add Template',
    'LBL_LINEITEM_BLOCK_GROUP' => 'LineItems Block For Group Tax',
    'LBL_LINEITEM_BLOCK_INDIVIDUAL' => 'LineItems Block For Individual Tax',
	'LBL_MESSAGE' => 'Message',
    'LBL_ADD_PDF' => 'Add PDF',
	
	//Translation for module
	'Calendar' => 'Task',
	'Send Mail' => 'Send Mail',
	'Invoke Custom Function' => 'Invoke Custom Function',
	'Create Todo' => 'Create Task',
	'Create Event' => 'Create Event',
	'Update Fields' => 'Update Fields',
	'Create Entity' => 'Create Record',
	'SMS Task' => 'SMS Task',
	'Mobile Push Notification' => 'Mobile Push Notification',
    
    // v7 translations
    'LBL_WORKFLOW_NAME' => 'Workflow Name',
    'LBL_TARGET_MODULE' => 'Target Module',
    'LBL_WORKFLOW_TRIGGER' => 'Workflow Trigger',
    'LBL_TRIGGER_WORKFLOW_ON' => 'Trigger Workflow On',
    'LBL_RECORD_CREATION' => 'Record Creation',
    'LBL_RECORD_UPDATE' => 'Record Update',
    'LBL_TIME_INTERVAL' => 'Time Interval',
    'LBL_RECURRENCE' => 'Recurrence',
    'LBL_FIRST_TIME_CONDITION_MET' => 'Only first time conditons are met',
    'LBL_EVERY_TIME_CONDITION_MET' => 'Every time conditons are met',
    'LBL_WORKFLOW_CONDITION' => 'Workflow Condition',
    'LBL_WORKFLOW_ACTIONS' => 'Workflow Actions',
    'LBL_DELAY_ACTION' => 'Delay Action',
    'LBL_FREQUENCY' => 'Frequency',
    'LBL_SELECT_FIELDS' => 'Select Fields',
    'LBL_INCLUDES_CREATION' => 'Includes Creation',
    'LBL_ACTION_FOR_WORKFLOW' => 'Action for Workflow',
    'LBL_WORKFLOW_SEARCH' => 'Search by Name',
	'LBL_ACTION_TYPE' => 'Action Type (Active Count)',
	'LBL_VTEmailTask' => 'Email',
    'LBL_VTEntityMethodTask' => 'Custom Function',
    'LBL_VTCreateTodoTask' => 'Task',
    'LBL_VTCreateEventTask' => 'Event',
    'LBL_VTUpdateFieldsTask' => 'Field Update',
    'LBL_VTSMSTask' => 'SMS', 
    'LBL_VTPushNotificationTask' => 'Mobile Notification',
    'LBL_VTCreateEntityTask' => 'Create Record',
	'LBL_MAX_SCHEDULED_WORKFLOWS_EXCEEDED' => 'Maximum number(%s) of scheduled workflows has been exceeded',
    
    'is' => 'is %s',
    'contains' => 'contains %s',
    'does not contain' => 'does not contain %s',
    'starts with' => 'starts with %s',
    'ends with' => 'ends with %s',
    'has changed' => 'has changed',
    'is empty' => 'is empty',
    'is not empty' => 'is not empty',
    'equal to' => 'equal to %s',
    'less than' => 'less than %s',
    'greater than' => 'greater than %s',
    'does not equal' => 'does not equal %s',
    'less than or equal to' => 'less than or equal to %s',
    'greater than or equal to' => 'greater than or equal to %s',
    'is not' => 'is not %s',
    'has changed to' => 'has changed to %s',
    'has changed from' => 'has changed from %s',
    'before' => 'before %s',
    'after' => 'after %s',
    'is today' => 'is today',
    'is tomorrow' => 'is tomorrow',
    'is yesterday' => 'is yesterday',
	'previous month' => 'previous month',
	'current month' => 'current month',
	'next month' => 'next month',
    'less than days ago' => 'less than %s days ago',
    'less than days later' => 'less than %s days later',
    'more than days ago' => 'more than %s days ago',
    'more than days later' => 'more than %s days later',
    'days ago' => '%s days ago',
    'days later' => '%s days later',
    'between' => 'between %s',
    'in less than' => 'in less than %s',
    'in more than' => 'in more than %s',
    'is added' => 'is added',
	'week days later' => '%s week days later',
    'more than week days later' => 'more than %s week days later',
    'less than week days later' => 'less than %s week days later',
    'week days ago' => '%s week days ago',
    'more than week days ago' => 'more than %s week days ago',
    'less than week days ago' => 'less than %s week days ago',
);

$jsLanguageStrings = array(
	'JS_STATUS_CHANGED_SUCCESSFULLY' => 'Status changed Successfully',
	'JS_TASK_DELETED_SUCCESSFULLY' => 'Action deleted Successfully',
	'JS_SAME_FIELDS_SELECTED_MORE_THAN_ONCE' => 'Same fields selected more than once',
	'JS_WORKFLOW_SAVED_SUCCESSFULLY' => 'Workflow saved successfully',
    'JS_CHECK_START_AND_END_DATE'=>'End Date & Time should be greater than or equal to Start Date & Time',
    'JS_TASK_STATUS_CHANGED' => 'Task status changed successfully.',
    'JS_WORKFLOWS_STATUS_CHANGED' => 'Workflow status changed successfully.',
    'VTEmailTask' => 'Send Mail',
    'VTEntityMethodTask' => 'Invoke Custom Function',
    'VTCreateTodoTask' => 'Create Task',
    'VTCreateEventTask' => 'Create Event',
    'VTUpdateFieldsTask' => 'Update Fields',
    'VTSMSTask' => 'SMS Task', 
    'VTPushNotificationTask' => 'Mobile Push Notification',
    'VTCreateEntityTask' => 'Create Record',
    'LBL_EXPRESSION_INVALID' => 'Expression Invalid'
);

