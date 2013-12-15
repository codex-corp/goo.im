<?php
/**
 * @author Hany alsamman (<hany.alsamman@gmail.com>)
 * @copyright Copyright © 2013 CODEXC.COM
 * @version 1.0
 * @license MIT
 */

class GooAPI {

    var $DEV_ID = 'probam'; ## change this

    var $check_per = 24; ##clean cache every 24 hour

    function GET_CONTENT($url){
	    $hash = md5($url);
	    $cacheFile = dirname(__FILE__)."/cache/$hash";	    

	    ## clean cache every 1 day
	    if ( file_exists($cacheFile) and (filemtime($cacheFile) > (time() - 60 * 60 * $this->check_per )) ) {
		$data = file_get_contents($cacheFile);
	    } else {
		$data = file_get_contents($url);
		file_put_contents($cacheFile, $data);
	    }
	    return json_decode($data, true);
    }

    function GET_DEVICES(){

        $jsrc = "http://goo.im/json2&path=/devs/$this->DEV_ID";
        $jset = $this->GET_CONTENT($jsrc);

        return $jset['list'];

    }


    /**
     * Download : http://goo.im/devs/probam/n7100/probam_kk_1.1_n7100.zip
     * stdClass Object (
     * [id] => 294330
     * [filename] => probam_v4.4.9b_endeavoru.zip
     * [path] => /devs/probam/endeavoru/probam_v4.4.9b_endeavoru.zip
     * [folder] => /devs/probam/endeavoru/
     * [md5] => 310633b060aad43b63d50b19eeba234c
     * [type] => File
     * [description] => Changelog was not provided for this ROM
     * [is_flashable] => 1
     * [modified] => 1381769002
     * [downloads] => 117
     * [status] => 1
     * [additional_info] =>
     * [short_url] =>
     * [developer_id] => 1211
     * [ro_developerid] => cyanogenmod
     * [ro_board] => endeavoru
     * [ro_rom] => cyanogenmod
     * [ro_version] => 0
     * [gapps_package] => 0
     * [incremental_file] => 0
     * [filesize] => 347984859 )
     */
    function GET_DEVICE_ROMS($device){

        $jsrc = "http://goo.im/json2&path=/devs/$this->DEV_ID/$device";
        $jset = $this->GET_CONTENT($jsrc);

        return $jset['list'];

    }

    function GET_DEVICES_LIST(){

	foreach( $this->GET_DEVICES() as $device ){

            $device_name = end( explode("/",$device['folder']) );

            //if($device_name == 'n7100')
            echo '<li><a title="#" href="?device='.$device_name.'">'.$device_name.'</a></li>';
        }
    }


}
