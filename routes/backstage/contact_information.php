<?php

$api->get('contact_information', 'ContactInformationController@index')->name('backstage.contact_information.index'); 
$api->post('contact_information', 'ContactInformationController@store')->name('backstage.contact_information.store'); 
$api->patch('contact_information/{information}', 'ContactInformationController@update')->name('backstage.contact_information.update'); 


