<?php

class It6_Cron_Job_Email_ReturnedBetEmail extends It6_Cron_Job_Email_UserEmail {

// data of returned bet (texts untranslated): array(
//  'id' => ID of bet,
//  'type' => type name,
//  'event' => event name,
//  'text' = bet text
// )
const PARAM_RETURNED_BET_INFO = 'betInfo';

// array (
//  'currencies' => array( currencyId => balance, ... ),
//  'points' => array( pointTypeId => ballance, ... )
// )
const PARAM_DEBTS = 'debts';

// array (
//  'currencies' => array( currencyId => loss, ... ),
//  'points' => array( pointTypeId => loss, ... )
// )
const PARAM_LOSSES = 'losses';

// array (
//  ticketId => ticketLoss
// )
const PARAM_TICKETS = 'tickets';


} // class ReturnedBetEmail
