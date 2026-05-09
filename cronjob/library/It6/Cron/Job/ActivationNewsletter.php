<?php

class It6_Cron_Job_ActivationNewsletter extends It6_Cron_Job_Abstract {

	private static function file_get_contents_curl($url) {
	    $ch = curl_init();
	 
	    curl_setopt($ch, CURLOPT_HEADER, 0);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //Set curl to return the data instead of printing it to the browser.
	    curl_setopt($ch, CURLOPT_URL, $url);
	 
	    $data = curl_exec($ch);
	    curl_close($ch);
	 
	    return $data;
	}

	public function execute(array $params = array(), &$errorMessage = null) {
		$ws = Zend_Registry::get('ws');

		$db = Zend_Registry::get('db');
		$dbAdmin = Zend_Registry::get('admindb');
		$round = $db->query("select round from newsletter_types where id = 1")->fetch();
		$round = $round["round"];

		$uzivatel = $db->select()
		->distinct()
		->from(array("u" => 'vic_main.uzivatel'), array(
				"u.*",
				"uo.vokativ",
				"uo.osloveni"
			))
		->joinLeft(array('uo' => 'vic_main.uzivatel_osloveni'),
		                 'uo.jmeno = u.jmeno')
		->where("anonymous = 0") //AND email = 'info@betservice.eu' OR email = 'josef.tamok@compbet.com'")
		->where("datum_aktivace IS NULL")
		->where("email != ''")
		->where("user_id NOT IN (select user_id from newsletter_history where round = $round AND newsletter_type_id = 1)")
		->limit(50)
		->query()
		->fetchAll();  

//			print_r($uzivatel);

		$html = new Zend_View();
		$html->setScriptPath(ROOT .'files/templates/html/');

		foreach ($uzivatel as $u) {
		    $sendEmail = empty($u['email']) ? false : true;
		    $_SESSION['osloveni'] = $u['osloveni'] . ' ' .$u['vokativ'];
		    $branch_name = $branch_street = $branch_town = $branch_zip = "";

		        // Doregistrace
		        if (empty($u["osloveni"])) {
		            if ( $u['pohlavi'] == "f" ) {
    					$_SESSION['osloveni'] = "Vážená paní " . $u["jmeno"]; 				
		            } else {
	    				$_SESSION['osloveni'] = "Vážený pane " . $u["jmeno"]; 				
		            }

		        }

		        $_SESSION["osloveni"] .= ",";

		        $_SESSION['branch_name'] = $_SESSION['branch_street'] = $_SESSION['branch_town'] = $_SESSION['branch_zip'] = "";
		        
		        if ($u['ulice'] != "" && $u['misto'] != "") {
		            $address = urlencode($u['ulice'].",".$u['misto'].",Czech Republic");
		            $region = "CZE";
		            $json = self::file_get_contents_curl("http://maps.google.com/maps/api/geocode/json?address=$address&sensor=false&region=$region");

		            $decoded_adr = json_decode($json);

		            if (count($decoded_adr->{'results'}) != 0) {
		                $latitude = $decoded_adr->{'results'}[0]->{'geometry'}->{'location'}->{'lat'};
		                $longitude = $decoded_adr->{'results'}[0]->{'geometry'}->{'location'}->{'lng'};

		                $sql = "SELECT branch.*, ( 3959 * acos( cos( radians($latitude) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians($longitude) ) + sin( radians($latitude) ) * sin( radians( latitude ) ) ) ) AS distance FROM branch WHERE is_active = 1 HAVING distance < 25 ORDER BY distance LIMIT 0 , 1";
		                $branch = $dbAdmin->query($sql)->fetchAll();

		                if ( count($branch) != 0 ) {
		                    $_SESSION['branch_name'] = $branch[0]['name'];
		                    $_SESSION['branch_street'] = $branch[0]['street']; 
		                    $_SESSION['branch_town'] = $branch[0]['town'];
		                    $_SESSION['branch_zip'] = $branch[0]['zip'];
		                }
		            }
		        } 

		        $bodyText = $html->render('newsletter_doregistrace.html');    

		    // Sending email
		    if ( $sendEmail ) {
		        $userEmail = $u['email'];
		        $mail = new Zend_Mail('UTF-8');
//		        $mail->setFrom(MAIL_FROM_ADDRESS, MAIL_FROM_NAME);       
//$mail->addTo("it6@gmail.com");
				$mail->addTo($userEmail);
		        $mail->setFrom(MAIL_FROM_ADDRESS, MAIL_FROM_NAME);
				$mail->setReplyTo(REPLY_TO_ADDRESS,REPLY_TO_NAME);
		        $mail->setSubject("Získejte bonus až 5000 Kč!");
		        $mail->setBodyHtml($bodyText);

		        // zkontroluji je li email validni
		        $emailValidator = new Zend_Validate_EmailAddress();
		        if (!$emailValidator->isValid($userEmail)) {

		        	$db->insert("newsletter_history", array(
		            				"user_id" => $u["user_id"],
		            				"sent"	=> It6_Date::dbNow(),
		            				"newsletter_type_id" => 1,
		            				"round" => $round,
		            				"success" => 0,
		            				"error"	=> "email $userEmail not valid"
		            			));
		        	continue;
		        }

		        try {
		            if ($mail->send()) {
		            	It6_Log::info('Newsletter activation email was sent to adress: '.$userEmail);
		            		$db->insert("newsletter_history", array(
		            				"user_id" => $u["user_id"],
		            				"sent"	=> It6_Date::dbNow(),
		            				"newsletter_type_id" => 1,
		            				"round" => $round,
		            				"success" => 1
		            			));
		            }

		           

		        }
		        catch (Exception $e) {
		        			        	$db->insert("newsletter_history", array(
		            				"user_id" => $u["user_id"],
		            				"sent"	=> It6_Date::dbNow(),
		            				"newsletter_type_id" => 1,
		            				"round" => $round,
		            				"success" => 0,
		            				"error"	=> $e->getMessage()
		            			));
		            It6_Log::err('Newsletter activation email not be sent. Exception:'.$e->getMessage());
		        }
		    }
		}

		 $users = $db->select()
							->distinct()
							->from('vic_main.uzivatel')
							->joinLeft(array('uo' => 'vic_main.uzivatel_osloveni'),
							                 'uo.jmeno = uzivatel.jmeno')
							->where("anonymous = 0")
							->where("datum_aktivace IS NULL")
							->where("email != ''")
							->where("user_id NOT IN (select user_id from newsletter_history where round = $round AND newsletter_type_id = 1)")
							->query()

							->fetchAll(); 

					if (count($users) > 0) {
						It6_Models_CronJob::scheduleJobNamed("ActivationNewsletter", array(), time());
					}
	}

}
