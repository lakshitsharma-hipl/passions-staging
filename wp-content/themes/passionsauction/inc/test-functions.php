<?php

add_action( 'wpcf7_before_send_mail', 'change_cf7_email_subject' );

function change_cf7_email_subject( $contact_form ) {
    if ( $contact_form->id() == 1938 ) {

        $submission = WPCF7_Submission::get_instance();
        if ( $submission ) {
            $posted_data = $submission->get_posted_data();
            if ( isset( $posted_data['wpcf7_container_post'] ) ) {
                $current_page_id = absint( $posted_data['wpcf7_container_post'] ); 
                $title = get_the_title( $current_page_id ); 

                $mail = $contact_form->prop( 'mail' );
                $mail['subject'] = 'Téléchargement de la fiche technique des ' . $title;
                $contact_form->set_properties( array( 'mail' => $mail ) );
            }
        }
    }
}


function generateCountdownTimer(){
	$curl = curl_init();
	$user_timezone = 'Europe/Amsterdam';
	$end_time = new DateTime('now', new DateTimeZone($user_timezone));
	$end_time->add(new DateInterval('PT10M'));
	$end_time_formatted = $end_time->format('Y-m-d H:i:s');

	$post_fields = array(
	    "skin_id" => 14,
	    "name" => "Limited Time Offer!",
	    "time_end" => $end_time_formatted,
	    "time_zone" => $user_timezone,
	    "font_family" => "Roboto-Bold",
	    "color_primary" => "0e6bd1",
	    "color_text" => "1E3556",
	    "color_bg" => "FFFFFF",
	    "transparent" => 1,
	    "lang_local" => 1,
	    "font_size" => 72,
	    "label_font_size" => 22,
	    "day" => 0,
	    "hours" => 0,
	    "lang" => "en",
	    "expired_mes_on" => 1,
	    "expired_mes" => "This offer has expired",
	    "labels" => 1,
	    "days" => "days",
	    "hours" => "HOURS",
	    "minutes" => "MINUTES",
	    "seconds" => "SECONDS",
	);

	$post_data = json_encode($post_fields);

	curl_setopt_array($curl, array(
	    CURLOPT_URL => 'https://countdownmail.com/api/create',
	    CURLOPT_RETURNTRANSFER => true,
	    CURLOPT_ENCODING => '',
	    CURLOPT_MAXREDIRS => 10,
	    CURLOPT_TIMEOUT => 0,
	    CURLOPT_FOLLOWLOCATION => true,
	    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	    CURLOPT_CUSTOMREQUEST => 'POST',
	    CURLOPT_POSTFIELDS => $post_data,
	    CURLOPT_HTTPHEADER => array(
	        'Content-Type: application/json',
	        'Accept: application/json',
	        'Authorization: hCFPVJGklecbr519k0ETwSGPB1lEOr6S'
	    ),
	));

	$response = curl_exec($curl);

	if ($response === false) {
	    echo 'Error: ' . curl_error($curl);
	} else {
	    $response_array = json_decode($response, true);

	    if ($response_array === null) {
	        echo 'Failed to decode JSON response.';
	    } else {
	        $countdown_timer_src = $response_array['message']['src'];
	        $countdown_timer_code = $response_array['message']['code'];
	    }
	}
	return $response_array['message'];
	curl_close($curl);
}


function handle_send_email() {

	/*$countdown = generateCountdownTimer();

	$countdown_timer_src = $countdown['src'];
	$countdown_timer_code = $countdown['code'];*/

    $to = 'wordpressdeveloper.his@gmail.com';
    $subject = 'Test Email from WordPress AJAX';
    ob_start();

    ?>
    <!DOCTYPE HTML PUBLIC "-//W3C//DTD XHTML 1.0 Transitional //EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
	<html xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
	<head>
	<!--[if gte mso 9]>
	<xml>
	  <o:OfficeDocumentSettings>
	    <o:AllowPNG/>
	    <o:PixelsPerInch>96</o:PixelsPerInch>
	  </o:OfficeDocumentSettings>
	</xml>
	<![endif]-->
	  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	  <meta name="viewport" content="width=device-width, initial-scale=1.0">
	  <meta name="x-apple-disable-message-reformatting">
	  <!--[if !mso]><!--><meta http-equiv="X-UA-Compatible" content="IE=edge"><!--<![endif]-->
	  <title></title>
	  
	    <style type="text/css">
	      @media only screen and (min-width: 620px) {
	  .u-row {
	    width: 600px !important;
	  }
	  .u-row .u-col {
	    vertical-align: top;
	  }

	  .u-row .u-col-100 {
	    width: 600px !important;
	  }

	}

	@media (max-width: 620px) {
	  .u-row-container {
	    max-width: 100% !important;
	    padding-left: 0px !important;
	    padding-right: 0px !important;
	  }
	  .u-row .u-col {
	    min-width: 320px !important;
	    max-width: 100% !important;
	    display: block !important;
	  }
	  .u-row {
	    width: 100% !important;
	  }
	  .u-col {
	    width: 100% !important;
	  }
	  .u-col > div {
	    margin: 0 auto;
	  }
	}
	body {
	  margin: 0;
	  padding: 0;
	}

	table,
	tr,
	td {
	  vertical-align: top;
	  border-collapse: collapse;
	}

	p {
	  margin: 0;
	}

	.ie-container table,
	.mso-container table {
	  table-layout: fixed;
	}

	* {
	  line-height: inherit;
	}

	a[x-apple-data-detectors='true'] {
	  color: inherit !important;
	  text-decoration: none !important;
	}

	table, td { color: #000000; } #u_body a { color: #0000ee; text-decoration: underline; } @media (max-width: 480px) { #u_content_text_1 .v-container-padding-padding { padding: 51px 10px 180px !important; } #u_content_image_1 .v-src-width { width: auto !important; } #u_content_image_1 .v-src-max-width { max-width: 100% !important; } #u_content_text_3 .v-container-padding-padding { padding: 10px !important; } #u_content_button_1 .v-size-width { width: 72% !important; } #u_content_text_2 .v-container-padding-padding { padding: 10px 30px 40px !important; } #u_content_social_1 .v-container-padding-padding { padding: 40px 10px 10px !important; } #u_content_text_5 .v-container-padding-padding { padding: 10px 10px 40px !important; } #u_content_text_4 .v-container-padding-padding { padding: 30px 10px !important; } }
	    </style>
	  
	  

	<!--[if !mso]><!--><link href="https://fonts.googleapis.com/css?family=Raleway:400,700&display=swap" rel="stylesheet" type="text/css"><link href="https://fonts.googleapis.com/css?family=Open+Sans:400,700&display=swap" rel="stylesheet" type="text/css"><!--<![endif]-->

	</head>

	<body class="clean-body u_body" style="margin: 0;padding: 0;-webkit-text-size-adjust: 100%;background-color: #ecf0f1;color: #000000">
	  <!--[if IE]><div class="ie-container"><![endif]-->
	  <!--[if mso]><div class="mso-container"><![endif]-->
	  <table id="u_body" style="border-collapse: collapse;table-layout: fixed;border-spacing: 0;mso-table-lspace: 0pt;mso-table-rspace: 0pt;vertical-align: top;min-width: 320px;Margin: 0 auto;background-color: #ecf0f1;width:100%" cellpadding="0" cellspacing="0">
	  <tbody>
	  <tr style="vertical-align: top">
	    <td style="word-break: break-word;border-collapse: collapse !important;vertical-align: top">
	    <!--[if (mso)|(IE)]><table width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td align="center" style="background-color: #ecf0f1;"><![endif]-->
	    
	  
	  
	    <!--[if gte mso 9]>
	      <table cellpadding="0" cellspacing="0" border="0" style="margin: 0 auto;min-width: 320px;max-width: 600px;">
	        <tr>
	          <td background="https://cdn.templates.unlayer.com/assets/1714334540278-header.png" valign="top" width="100%">
	      <v:rect xmlns:v="urn:schemas-microsoft-com:vml" fill="true" stroke="false" style="width: 600px;">
	        <v:fill type="frame" src="https://cdn.templates.unlayer.com/assets/1714334540278-header.png" /><v:textbox style="mso-fit-shape-to-text:true" inset="0,0,0,0">
	      <![endif]-->
	  
	<div class="u-row-container" style="padding: 0px;background-image: url('https://passionsauction.hipl-staging1.com/wp-content/uploads/2024/05/image-8.png');background-repeat: no-repeat;background-position: center top;background-color: transparent">
	  <div class="u-row" style="margin: 0 auto;min-width: 320px;max-width: 600px;overflow-wrap: break-word;word-wrap: break-word;word-break: break-word;background-color: transparent;">
	    <div style="border-collapse: collapse;display: table;width: 100%;height: 100%;background-color: transparent;">
	      <!--[if (mso)|(IE)]><table width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td style="padding: 0px;background-image: url('https://passionsauction.hipl-staging1.com/wp-content/uploads/2024/05/image-8.png');background-repeat: no-repeat;background-position: center top;background-color: transparent;" align="center"><table cellpadding="0" cellspacing="0" border="0" style="width:600px;"><tr style="background-color: transparent;"><![endif]-->
	      
	<!--[if (mso)|(IE)]><td align="center" width="600" style="width: 600px;padding: 0px;border-top: 0px solid transparent;border-left: 0px solid transparent;border-right: 0px solid transparent;border-bottom: 0px solid transparent;" valign="top"><![endif]-->
	<div class="u-col u-col-100" style="max-width: 320px;min-width: 600px;display: table-cell;vertical-align: top;">
	  <div style="height: 100%;width: 100% !important;">
	  <!--[if (!mso)&(!IE)]><!--><div style="box-sizing: border-box; height: 100%; padding: 0px;border-top: 0px solid transparent;border-left: 0px solid transparent;border-right: 0px solid transparent;border-bottom: 0px solid transparent;"><!--<![endif]-->
	  
	<table id="u_content_text_1" style="font-family:'Raleway',sans-serif;" role="presentation" cellpadding="0" cellspacing="0" width="100%" border="0">
	  <tbody>
	    <tr>
	      <td class="v-container-padding-padding" style="overflow-wrap:break-word;word-break:break-word;padding:20px 10px 180px;font-family:'Raleway',sans-serif;" align="left">
	        
	  <div style="font-family: 'Open Sans',sans-serif; font-size: 18px; line-height: 130%; text-align: center; word-wrap: break-word;">
	    <p style="line-height: 130%;"><span style="text-decoration: underline; line-height: 23.4px;">7 Days Only</span><br />Explore Flight Discounts Today!</p>
	  </div>

	      </td>
	    </tr>
	  </tbody>
	</table>

	<table id="u_content_image_1" style="font-family:'Raleway',sans-serif;" role="presentation" cellpadding="0" cellspacing="0" width="100%" border="0">
	  <tbody>
	    <tr>
	      <td class="v-container-padding-padding" style="overflow-wrap:break-word;word-break:break-word;padding:10px 10px 260px;font-family:'Raleway',sans-serif;" align="left">
	        
	<table width="100%" cellpadding="0" cellspacing="0" border="0">
	  <tr>
	    <td style="padding-right: 0px;padding-left: 0px;" align="center">
	      
	      <img align="center" border="0" src="https://passionsauction.hipl-staging1.com/wp-content/uploads/2024/05/image-1.png" alt="image" title="image" style="outline: none;text-decoration: none;-ms-interpolation-mode: bicubic;clear: both;display: inline-block !important;border: none;height: auto;float: none;width: 68%;max-width: 394.4px;" width="394.4" class="v-src-width v-src-max-width"/>
	      
	    </td>
	  </tr>
	</table>

	      </td>
	    </tr>
	  </tbody>
	</table>

	<table id="u_content_text_3" style="font-family:'Raleway',sans-serif;" role="presentation" cellpadding="0" cellspacing="0" width="100%" border="0">
	  <tbody>
	    <tr>
	      <td class="v-container-padding-padding" style="overflow-wrap:break-word;word-break:break-word;padding:10px 60px;font-family:'Raleway',sans-serif;" align="left">
	        
	  <div style="font-size: 14px; line-height: 140%; text-align: center; word-wrap: break-word;">
	    <p style="line-height: 140%;">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Quis ipsum suspen disse ultrices gravida. Risus commodo viverra </p>
	  </div>

	      </td>
	    </tr>
	  </tbody>
	</table>

	<table id="u_content_button_1" style="font-family:'Raleway',sans-serif;" role="presentation" cellpadding="0" cellspacing="0" width="100%" border="0">
	  <tbody>
	    <tr>
	      <td class="v-container-padding-padding" style="overflow-wrap:break-word;word-break:break-word;padding:10px;font-family:'Raleway',sans-serif;" align="left">
	        
	  <!--[if mso]><style>.v-button {background: transparent !important;}</style><![endif]-->
	<div align="center">
	  <!--[if mso]><table border="0" cellspacing="0" cellpadding="0"><tr><td align="center" bgcolor="#ffce4f" style="padding:10px 20px;" valign="top"><![endif]-->
	    <a href="https://unlayer.com" target="_blank" class="v-button v-size-width" style="box-sizing: border-box;display: inline-block;text-decoration: none;-webkit-text-size-adjust: none;text-align: center;color: #000000; background-color: #ffce4f; border-radius: 4px;-webkit-border-radius: 4px; -moz-border-radius: 4px; width:39%; max-width:100%; overflow-wrap: break-word; word-break: break-word; word-wrap:break-word; mso-border-alt: none;font-size: 14px;">
	      <span style="display:block;padding:10px 20px;line-height:120%;"><strong>Grab Your Savings Now!</strong></span>
	    </a>
	    <!--[if mso]></td></tr></table><![endif]-->
	</div>

	      </td>
	    </tr>
	  </tbody>
	</table>

	<table id="u_content_text_2" style="font-family:'Raleway',sans-serif;" role="presentation" cellpadding="0" cellspacing="0" width="100%" border="0">
	  <tbody>
	    <tr>
	      <td class="v-container-padding-padding" style="overflow-wrap:break-word;word-break:break-word;padding:10px 60px 60px;font-family:'Raleway',sans-serif;" align="left">
	        
		  <div style="font-size: 12px; line-height: 140%; text-align: center; word-wrap: break-word;">
		    <p style="line-height:140%;font-size: 16px;font-weight: 600;margin-top: 25px;">Final Countdown! 10 Minutes Remaining to Claim Your Discount!</p>
		  </div>
		  <div style="font-size: 12px; line-height: 140%; text-align: center; word-wrap: break-word; margin: 20px 0;">
		   <table width="100%" cellspacing="0" cellpadding="0">
		   	<tbody>
		   		<tr>
		   			<td align="center">
		   			<img src="https://m3.promofeatures.com/annc.gif" border="0" alt="https://promofeatures.com" />
		   				
		   			</td>
		   		</tr>
		   	</tbody>
		   </table>
		  </div>

	      </td>
	    </tr>
	  </tbody>
	</table>

	  <!--[if (!mso)&(!IE)]><!--></div><!--<![endif]-->
	  </div>
	</div>
	<!--[if (mso)|(IE)]></td><![endif]-->
	      <!--[if (mso)|(IE)]></tr></table></td></tr></table><![endif]-->
	    </div>
	  </div>
	  </div>
	  
	    <!--[if gte mso 9]>
	      </v:textbox></v:rect>
	    </td>
	    </tr>
	    </table>
	    <![endif]-->
	    


	  
	  
	<div class="u-row-container" style="padding: 0px;background-color: transparent">
	  <div class="u-row" style="margin: 0 auto;min-width: 320px;max-width: 600px;overflow-wrap: break-word;word-wrap: break-word;word-break: break-word;background-color: transparent;">
	    <div style="border-collapse: collapse;display: table;width: 100%;height: 100%;background-color: transparent;">
	      <!--[if (mso)|(IE)]><table width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td style="padding: 0px;background-color: transparent;" align="center"><table cellpadding="0" cellspacing="0" border="0" style="width:600px;"><tr style="background-color: transparent;"><![endif]-->
	      
	<!--[if (mso)|(IE)]><td align="center" width="600" style="background-color: #000000;width: 600px;padding: 0px;border-top: 0px solid transparent;border-left: 0px solid transparent;border-right: 0px solid transparent;border-bottom: 0px solid transparent;border-radius: 0px;-webkit-border-radius: 0px; -moz-border-radius: 0px;" valign="top"><![endif]-->
	<div class="u-col u-col-100" style="max-width: 320px;min-width: 600px;display: table-cell;vertical-align: top;">
	  <div style="background-color: #000000;height: 100%;width: 100% !important;border-radius: 0px;-webkit-border-radius: 0px; -moz-border-radius: 0px;">
	  <!--[if (!mso)&(!IE)]><!--><div style="box-sizing: border-box; height: 100%; padding: 0px;border-top: 0px solid transparent;border-left: 0px solid transparent;border-right: 0px solid transparent;border-bottom: 0px solid transparent;border-radius: 0px;-webkit-border-radius: 0px; -moz-border-radius: 0px;"><!--<![endif]-->
	  
	<table id="u_content_social_1" style="font-family:'Raleway',sans-serif;" role="presentation" cellpadding="0" cellspacing="0" width="100%" border="0">
	  <tbody>
	    <tr>
	      <td class="v-container-padding-padding" style="overflow-wrap:break-word;word-break:break-word;padding:40px 10px 10px;font-family:'Raleway',sans-serif;" align="left">
	        
	<div align="center">
	  <div style="display: table; max-width:281px;">
	  <!--[if (mso)|(IE)]><table width="281" cellpadding="0" cellspacing="0" border="0"><tr><td style="border-collapse:collapse;" align="center"><table width="100%" cellpadding="0" cellspacing="0" border="0" style="border-collapse:collapse; mso-table-lspace: 0pt;mso-table-rspace: 0pt; width:281px;"><tr><![endif]-->
	  
	    
	    <!--[if (mso)|(IE)]><td width="32" style="width:32px; padding-right: 15px;" valign="top"><![endif]-->
	    <table align="center" border="0" cellspacing="0" cellpadding="0" width="32" height="32" style="width: 32px !important;height: 32px !important;display: inline-block;border-collapse: collapse;table-layout: fixed;border-spacing: 0;mso-table-lspace: 0pt;mso-table-rspace: 0pt;vertical-align: top;margin-right: 15px">
	      <tbody><tr style="vertical-align: top"><td align="center" valign="middle" style="word-break: break-word;border-collapse: collapse !important;vertical-align: top">
	        <a href="https://www.facebook.com/unlayer" title="Facebook" target="_blank">
	          <img src="https://passionsauction.hipl-staging1.com/wp-content/uploads/2024/05/image-4.png" alt="Facebook" title="Facebook" width="32" style="outline: none;text-decoration: none;-ms-interpolation-mode: bicubic;clear: both;display: block !important;border: none;height: auto;float: none;max-width: 32px !important">
	        </a>
	      </td></tr>
	    </tbody></table>
	    <!--[if (mso)|(IE)]></td><![endif]-->
	    
	    <!--[if (mso)|(IE)]><td width="32" style="width:32px; padding-right: 15px;" valign="top"><![endif]-->
	    <table align="center" border="0" cellspacing="0" cellpadding="0" width="32" height="32" style="width: 32px !important;height: 32px !important;display: inline-block;border-collapse: collapse;table-layout: fixed;border-spacing: 0;mso-table-lspace: 0pt;mso-table-rspace: 0pt;vertical-align: top;margin-right: 15px">
	      <tbody><tr style="vertical-align: top"><td align="center" valign="middle" style="word-break: break-word;border-collapse: collapse !important;vertical-align: top">
	        <a href="https://www.linkedin.com/company/unlayer/mycompany/" title="LinkedIn" target="_blank">
	          <img src="https://passionsauction.hipl-staging1.com/wp-content/uploads/2024/05/image-2.png" alt="LinkedIn" title="LinkedIn" width="32" style="outline: none;text-decoration: none;-ms-interpolation-mode: bicubic;clear: both;display: block !important;border: none;height: auto;float: none;max-width: 32px !important">
	        </a>
	      </td></tr>
	    </tbody></table>
	    <!--[if (mso)|(IE)]></td><![endif]-->
	    
	    <!--[if (mso)|(IE)]><td width="32" style="width:32px; padding-right: 15px;" valign="top"><![endif]-->
	    <table align="center" border="0" cellspacing="0" cellpadding="0" width="32" height="32" style="width: 32px !important;height: 32px !important;display: inline-block;border-collapse: collapse;table-layout: fixed;border-spacing: 0;mso-table-lspace: 0pt;mso-table-rspace: 0pt;vertical-align: top;margin-right: 15px">
	      <tbody><tr style="vertical-align: top"><td align="center" valign="middle" style="word-break: break-word;border-collapse: collapse !important;vertical-align: top">
	        <a href="https://www.instagram.com/unlayer_official/" title="Instagram" target="_blank">
	          <img src="https://passionsauction.hipl-staging1.com/wp-content/uploads/2024/05/image-5.png" alt="Instagram" title="Instagram" width="32" style="outline: none;text-decoration: none;-ms-interpolation-mode: bicubic;clear: both;display: block !important;border: none;height: auto;float: none;max-width: 32px !important">
	        </a>
	      </td></tr>
	    </tbody></table>
	    <!--[if (mso)|(IE)]></td><![endif]-->
	    
	    <!--[if (mso)|(IE)]><td width="32" style="width:32px; padding-right: 15px;" valign="top"><![endif]-->
	    <table align="center" border="0" cellspacing="0" cellpadding="0" width="32" height="32" style="width: 32px !important;height: 32px !important;display: inline-block;border-collapse: collapse;table-layout: fixed;border-spacing: 0;mso-table-lspace: 0pt;mso-table-rspace: 0pt;vertical-align: top;margin-right: 15px">
	      <tbody><tr style="vertical-align: top"><td align="center" valign="middle" style="word-break: break-word;border-collapse: collapse !important;vertical-align: top">
	        <a href="https://hu.pinterest.com/unlayer/" title="Pinterest" target="_blank">
	          <img src="https://passionsauction.hipl-staging1.com/wp-content/uploads/2024/05/image-7.png" alt="Pinterest" title="Pinterest" width="32" style="outline: none;text-decoration: none;-ms-interpolation-mode: bicubic;clear: both;display: block !important;border: none;height: auto;float: none;max-width: 32px !important">
	        </a>
	      </td></tr>
	    </tbody></table>
	    <!--[if (mso)|(IE)]></td><![endif]-->
	    
	    <!--[if (mso)|(IE)]><td width="32" style="width:32px; padding-right: 15px;" valign="top"><![endif]-->
	    <table align="center" border="0" cellspacing="0" cellpadding="0" width="32" height="32" style="width: 32px !important;height: 32px !important;display: inline-block;border-collapse: collapse;table-layout: fixed;border-spacing: 0;mso-table-lspace: 0pt;mso-table-rspace: 0pt;vertical-align: top;margin-right: 15px">
	      <tbody><tr style="vertical-align: top"><td align="center" valign="middle" style="word-break: break-word;border-collapse: collapse !important;vertical-align: top">
	        <a href="https://www.youtube.com/@unlayer574" title="YouTube" target="_blank">
	          <img src="https://passionsauction.hipl-staging1.com/wp-content/uploads/2024/05/image-3.png" alt="YouTube" title="YouTube" width="32" style="outline: none;text-decoration: none;-ms-interpolation-mode: bicubic;clear: both;display: block !important;border: none;height: auto;float: none;max-width: 32px !important">
	        </a>
	      </td></tr>
	    </tbody></table>
	    <!--[if (mso)|(IE)]></td><![endif]-->
	    
	    <!--[if (mso)|(IE)]><td width="32" style="width:32px; padding-right: 0px;" valign="top"><![endif]-->
	    <table align="center" border="0" cellspacing="0" cellpadding="0" width="32" height="32" style="width: 32px !important;height: 32px !important;display: inline-block;border-collapse: collapse;table-layout: fixed;border-spacing: 0;mso-table-lspace: 0pt;mso-table-rspace: 0pt;vertical-align: top;margin-right: 0px">
	      <tbody><tr style="vertical-align: top"><td align="center" valign="middle" style="word-break: break-word;border-collapse: collapse !important;vertical-align: top">
	        <a href="https://twitter.com/unlayerapp" title="X" target="_blank">
	          <img src="https://passionsauction.hipl-staging1.com/wp-content/uploads/2024/05/image-6.png" alt="X" title="X" width="32" style="outline: none;text-decoration: none;-ms-interpolation-mode: bicubic;clear: both;display: block !important;border: none;height: auto;float: none;max-width: 32px !important">
	        </a>
	      </td></tr>
	    </tbody></table>
	    <!--[if (mso)|(IE)]></td><![endif]-->
	    
	    
	    <!--[if (mso)|(IE)]></tr></table></td></tr></table><![endif]-->
	  </div>
	</div>

	      </td>
	    </tr>
	  </tbody>
	</table>

	<table id="u_content_text_5" style="font-family:'Raleway',sans-serif;" role="presentation" cellpadding="0" cellspacing="0" width="100%" border="0">
	  <tbody>
	    <tr>
	      <td class="v-container-padding-padding" style="overflow-wrap:break-word;word-break:break-word;padding:10px 10px 30px;font-family:'Raleway',sans-serif;" align="left">
	        
	  <div style="font-size: 14px; color: #ffffff; line-height: 140%; text-align: center; word-wrap: break-word;">
	    <p style="font-size: 14px; line-height: 140%;">email@website.com </p>
	<p style="font-size: 14px; line-height: 140%;">+12 458 4658</p>
	  </div>

	      </td>
	    </tr>
	  </tbody>
	</table>

	  <!--[if (!mso)&(!IE)]><!--></div><!--<![endif]-->
	  </div>
	</div>
	<!--[if (mso)|(IE)]></td><![endif]-->
	      <!--[if (mso)|(IE)]></tr></table></td></tr></table><![endif]-->
	    </div>
	  </div>
	  </div>
	  


	  
	  
	<div class="u-row-container" style="padding: 0px;background-color: transparent">
	  <div class="u-row" style="margin: 0 auto;min-width: 320px;max-width: 600px;overflow-wrap: break-word;word-wrap: break-word;word-break: break-word;background-color: transparent;">
	    <div style="border-collapse: collapse;display: table;width: 100%;height: 100%;background-color: transparent;">
	      <!--[if (mso)|(IE)]><table width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td style="padding: 0px;background-color: transparent;" align="center"><table cellpadding="0" cellspacing="0" border="0" style="width:600px;"><tr style="background-color: transparent;"><![endif]-->
	      
	<!--[if (mso)|(IE)]><td align="center" width="600" style="background-color: #ffffff;width: 600px;padding: 0px;border-top: 0px solid transparent;border-left: 0px solid transparent;border-right: 0px solid transparent;border-bottom: 0px solid transparent;border-radius: 0px;-webkit-border-radius: 0px; -moz-border-radius: 0px;" valign="top"><![endif]-->
	<div class="u-col u-col-100" style="max-width: 320px;min-width: 600px;display: table-cell;vertical-align: top;">
	  <div style="background-color: #ffffff;height: 100%;width: 100% !important;border-radius: 0px;-webkit-border-radius: 0px; -moz-border-radius: 0px;">
	  <!--[if (!mso)&(!IE)]><!--><div style="box-sizing: border-box; height: 100%; padding: 0px;border-top: 0px solid transparent;border-left: 0px solid transparent;border-right: 0px solid transparent;border-bottom: 0px solid transparent;border-radius: 0px;-webkit-border-radius: 0px; -moz-border-radius: 0px;"><!--<![endif]-->
	  
	<table id="u_content_text_4" style="font-family:'Raleway',sans-serif;" role="presentation" cellpadding="0" cellspacing="0" width="100%" border="0">
	  <tbody>
	    <tr>
	      <td class="v-container-padding-padding" style="overflow-wrap:break-word;word-break:break-word;padding:30px 60px;font-family:'Raleway',sans-serif;" align="left">
	        
	  <div style="font-size: 14px; line-height: 140%; text-align: center; word-wrap: break-word;">
	    <p style="font-size: 14px; line-height: 140%;"><span style="text-decoration: underline; font-size: 14px; line-height: 19.6px;">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore. </span></p>
	  </div>

	      </td>
	    </tr>
	  </tbody>
	</table>

	  <!--[if (!mso)&(!IE)]><!--></div><!--<![endif]-->
	  </div>
	</div>
	<!--[if (mso)|(IE)]></td><![endif]-->
	      <!--[if (mso)|(IE)]></tr></table></td></tr></table><![endif]-->
	    </div>
	  </div>
	  </div>
	  


	    <!--[if (mso)|(IE)]></td></tr></table><![endif]-->
	    </td>
	  </tr>
	  </tbody>
	  </table>
	  <!--[if mso]></div><![endif]-->
	  <!--[if IE]></div><![endif]-->
	</body>

</html>

    <?php

    $message = ob_get_clean();
    $headers = array('Content-Type: text/html; charset=UTF-8');

    if (wp_mail($to, $subject, $message, $headers)) {
        echo 'Email sent successfully.';
    } else {
        echo 'Failed to send email.';
    }

    wp_die();
}
add_action('wp_ajax_send_email', 'handle_send_email');
add_action('wp_ajax_nopriv_send_email', 'handle_send_email');

add_action('wp_ajax_save_block_image', 'save_block_image');
add_action('wp_ajax_nopriv_save_block_image', 'save_block_image');
function save_block_image() {

    if(isset($_POST['imageData'])) {
        $imageData = $_POST['imageData'];
        $image = imagecreatefromstring(base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $imageData)));
        $upload_dir = wp_upload_dir();
        $filename = $upload_dir['path'] . '/' . uniqid() . '.png';
        imagepng($image, $filename);
        $attachment = array(
            'guid'           => $upload_dir['url'] . '/' . basename($filename),
            'post_mime_type' => 'image/png',
            'post_title'     => preg_replace( '/\.[^.]+$/', '', basename( $filename ) ),
            'post_content'   => '',
            'post_status'    => 'inherit'
        );
        $attach_id = wp_insert_attachment( $attachment, $filename );
        require_once(ABSPATH . 'wp-admin/includes/image.php');
        $attach_data = wp_generate_attachment_metadata( $attach_id, $filename );
        wp_update_attachment_metadata( $attach_id, $attach_data );
        if($attach_id) {
             $image_url = wp_get_attachment_url($attach_id);
            echo json_encode(array('attach_id' => $attach_id, 'image_url' => $image_url));
        } else {
            echo 'Failed to save the image.';
        }
    } else {
        echo 'No image data received.';
    }
    wp_die();
}
