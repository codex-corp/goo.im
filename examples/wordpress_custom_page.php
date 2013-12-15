<?php
// Template Name: ProBAM ROMS

/**
 * @author Hany alsamman (<hany.alsamman@gmail.com>)
 * @copyright Copyright © 2013 CODEXC.COM
 * @version 1.0
 * @license MIT
 */

get_header();

the_content();

class GooAPI {

    var $DEV_ID = 'probam';


    function getContent($url){
	    $hash = md5($url);
	    $cacheFile = "/var/www/vhosts/probam.net/httpdocs/wp-content/cache/$hash";
	    $check_per = 24; ##every 24 hour

	    ## clean cache every $check_per value
	    if ( file_exists($cacheFile) and (filemtime($cacheFile) > (time() - 60 * 60 * $check_per )) ) {
		$data = file_get_contents($cacheFile);
	    } else {
		$data = file_get_contents($url);
		file_put_contents($cacheFile, $data);
	    }
	    return json_decode($data, true);
    }

    function GET_DEVICES(){

        $jsrc = "http://goo.im/json2&path=/devs/$this->DEV_ID";
        $jset = $this->getContent($jsrc);

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

        $jsrc = "http://goo.im/json2&path=/devs/probam/$device";
        $jset = $this->getContent($jsrc);

        return $jset['list'];

    }

}

$api = new GooAPI;

?>

<div class="title">
    <?php
    if(!isset($_GET['device'])){
        echo '<h2>ProBAM All ROMS available</h2>';
    }else{
        $device_name = end( explode("/",$_GET['device']) );
        echo '<h2>ProBAM '.$device_name.' ROMS</h2>';
    }
    ?>


    <div class="title-sep-container">
        <div class="title-sep"></div>
    </div>
</div>


<div class="table-1">
    <div style="width: 20%; float: left">
    <ul class="side-nav">

        <?php

        foreach( $api->GET_DEVICES() as $device ){

            $device_link = $device['folder'];

            $device_name = end( explode("/",$device['folder']) );

            //if($device_name == 'n7100')
            echo '<li><a title="'.$device_name.'" href="?device='.$device_name.'">'.$device_name.'</a></li>';

        }
        ?>

    </ul>
    </div>

    <table style="width: 78%; float: right">
        <thead>
        <tr>
            <th>File</th>
            <th>Size</th>
            <th>Upload date</th>
            <th>Downloads</th>
            <th>Mirror</th>
        </tr>
        </thead>
        <tbody>

        <?

        if(!isset($_GET['device'])){

            foreach( $api->GET_DEVICES() as $device ){

                $device_name = end( explode("/",$device['folder']) );

                foreach($api->GET_DEVICE_ROMS($device_name) as $device){
                    $device = (object) $device;

                    $filesize = number_format($device->filesize / 1048576, 2) . ' MB';

                    $upload_date = date("d/M g:i a",$device->modified);

                    $dl = 'http://goo.im'.$device->path;
                    echo '<tr>';
                    echo '<td><a href="'.$dl.'" target="_blank">'.$device->filename.'</a><br><small>MD5: '.$device->md5.'</small></td>';
                    echo '<td>'.$filesize.'</td>';
                    echo '<td>'.$upload_date.'</td>';
                    echo '<td>'.$device->downloads.'</td>';      
                    echo '</tr>';
                }

            }

        }else{

	    $i = 0;
            foreach($api->GET_DEVICE_ROMS($_GET['device']) as $device){
                $device = (object) $device;

		$new = ($i == 0) ? '<strong style="color:#E10707">New !</strong>' : false;

                $filesize = number_format($device->filesize / 1048576, 2) . ' MB';

                $upload_date = date("d/M g:i a",$device->modified);

                $dl = 'http://goo.im'.$device->path;
                echo '<tr>';
                echo '<td>'.$new.'<br><a href="'.$dl.'" target="_blank">'.$device->filename.'</a><br><small>MD5: '.$device->md5.'</small></td>';
                echo '<td>'.$filesize.'</td>';
                echo '<td>'.$upload_date.'</td>';
                echo '<td>'.$device->downloads.'</td>';
                echo '</tr>';

		$i++;
            }
        }

        ?>


        </tbody>
    </table>
    <div style="margin-top:40px;" class="demo-sep sep-none"></div>
</div>

<?php get_footer(); ?>
