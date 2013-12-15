<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>ProBAM Custom ROM</title>

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.0.3/css/bootstrap.min.css">

</head>
<body>

<?php

include('./GooAPI.php');

$api = new GooAPI;
$devices = $api->GET_DEVICES();
?>


<div class="container">


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


    <div style="width: 20%; float: left">
    <ul class="side-nav">
        <?php
           ## Get all Devices (Folders)
	   $api->GET_DEVICES_LIST();
        ?>
    </ul>
    </div>

    <table style="width: 78%; float: right"  class="row">
        <thead>
        <tr>
            <th>File</th>
            <th>Size</th>
            <th>Upload date</th>
            <th>Downloads</th>
        </tr>
        </thead>
        <tbody>

        <?

        if(!isset($_GET['device'])){

            foreach( $devices as $device ){

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
                    echo '<td><a href="http://freefr.dl.sourceforge.net/project/probam/'.$device->filename.'" target="_blank">SourceForge</a></td>';
                    echo '</tr>';
                }

            }

        }else{

 	    $i = 0;
            foreach($api->GET_DEVICE_ROMS($_GET['device']) as $device){
                $device = (object) $device;

		$device_name = end( explode("/",substr($device->folder,0,-1)) );

		if(strpos($device->filename, 'kernel') !== FALSE){
			$android_version = 'Android Kernel';

		}elseif(strpos($device->filename, 'gapps') !== FALSE){
			$android_version = 'Android Gapps';

		}elseif(strpos($device->filename, '.apk') !== FALSE){
			$android_version = 'Android App';

		}elseif(strpos($device->filename, $device_name) !== FALSE) {
			$android_version =  'Android ROM';

		}else $android_version = false;

		$new = ($i == 0) ? '<strong style="color:#E10707">New !</strong>' : false;

                $filesize = number_format($device->filesize / 1048576, 2) . ' MB';

                $upload_date = date("d/M g:i a",$device->modified);

                $dl = 'http://goo.im'.$device->path;
                echo '<tr>';
                echo '<td><small>'.$android_version.'</small> '.$new.'<br><a href="'.$dl.'" target="_blank">'.$device->filename.'</a><br><small>MD5: '.$device->md5.'</small></td>';
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
</div>
