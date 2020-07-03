<?php
/*
AirVuz Embedder
by: izzy26
Copyright 2020 jiri.stanglica@gmail.com
IntoFPV Rocks!
*/
if(!defined('IN_MYBB'))
    die('This file cannot be accessed directly.');

$plugins->add_hook("parse_message", "airvuz_embedder_process");


function airvuz_embedder_info()
{
    $codename = str_replace('.php', '', basename(__FILE__));
    return [
        "name"          => "AirVuz Embedder",
        "description"   => "Converts AirVuz links to embedded video.",
        "website"       => "https://github.com/jiristanglica",
        "author"		=> "izzy26",
        "authorsite"	=> "https://github.com/jiristanglica",
        "version"		=> "1.0",
        "codename"      => $codename,
        "guid" 			=> "dd85ea1c4c28a886643aabd723af83cf",
        "compatibility"	=> "18*"
    ];
}

function airvuz_embedder_install()
{
    global $db, $mybb;

    $setting_group = [
        'name' => 'arvuz_embedder',
        'title' => 'AirVuz Embedder',
        'description' => 'AirVuz Embedder Settings',
        'disporder' => 5,
        'isdefault' => 0
    ];

    $gid = $db->insert_query("settinggroups", $setting_group);

    $setting_array = [
        'airvuz_video_width' => [
            'title' => 'Video Width',
            'description' => 'Emter the embedded video width in pixels:',
            'optionscode' => 'numeric',
            'value' => 640,
            'disporder' => 1
        ],
        'airvuz_video_height' => [
            'title' => 'Video Height',
            'description' => 'Emter the embedded video height in pixels:',
            'optionscode' => 'numeric',
            'value' => 385,
            'disporder' => 1
        ],
    ];
    
    foreach($setting_array as $name => $setting)
    {
        $setting['name'] = $name;
        $setting['gid'] = $gid;
    
        $db->insert_query('settings', $setting);
    }

    rebuild_settings();
}
function airvuz_embedder_uninstall()
{
    global $db;

    $db->delete_query('settings', "name IN ('airvuz_video_width', 'airvuz_video_height')");
    $db->delete_query('settinggroups', "name = 'arvuz_embedder'");

    rebuild_settings();
}
function airvuz_embedder_is_installed()
{
	global $mybb;
    return isset($mybb->settings['airvuz_video_width']);
}
function airvuz_embedder_activate()
{
}
function airvuz_embedder_deactivate()
{
}

function airvuz_embedder_process(&$message)
{
    global $mybb;

    if (strlen($message) < 7) {
        return $message;
    }

    $videoWidth = !empty($mybb->settings['airvuz_video_width']) ? $mybb->settings['airvuz_video_width'] : 640;
    $videoHeight = !empty($mybb->settings['airvuz_video_height']) ? $mybb->settings['airvuz_video_height'] : 385;

    $message = preg_replace('#<a href="(https?:\/\/(?:www\.|(?!www)))airvuz.com\/video\/(.+)\?id=([a-z0-9]+)(.+)"[^>]*>([^<]+)</a>#i', '<iframe width="'.$videoWidth.'" height="'.$videoHeight.'" src="https://www.airvuz.com/videoPlayerEmbed/$3?autoplay=false" frameborder="0"></iframe>', $message);

    return $message;
}
?>