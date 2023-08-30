<?php

$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => 'https://backend.estrateg.com/smart/API/token.php',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'POST',
  CURLOPT_POSTFIELDS => array('email' => 'jailtonyanesromero@gmail.com','last_name' => 'Jailton','name' => 'Coltabaco','token' => '586897','password' => '1234','street' => 'calle30','house_number' => '34','phone' => '3346090','zip_code' => '8001','municipality_id' => '1','website' => 'www.coco.com','company_phone' => '1234567','contact_person_name' => 'Jose Pedroza','contact_person_phone' => '3608080','contact_person_email' => 'jpedroza@gmail.com','merchant_registry_info' => 'Caraballo','registry_court_id' => '1','registry_date' => '2023-01-14','merchant_registry_type' => 'callocallo','merchant_registry_number' => '43','company_type_id' => '1','birthdate' => '1998-08-09','country_id' => '1','driver_license_number' => '435677','expedition_driver_license_date' => '2024-01-12','registry_class_id' => '1','cat_flesh_id' => '1','tax_number' => '34234','farm_registry_id' => '1','iva_tax' => '1','work_other_companies' => '1','company_name' => 'Marinillos','start_date' => '1990-01-08','end_date' => '1995-01-08','front_card_side' => 'sefaew','back_card_side' => 'sdfsdgfdgfdfgfdsgdf','driver_id' => '1','judicial_record' => 'erfjddjssj','avaliability_id' => '1'),
  CURLOPT_HTTPHEADER => array(
    'Accept: application/json'
  ),
));

$response = curl_exec($curl);

curl_close($curl);
echo $response;
