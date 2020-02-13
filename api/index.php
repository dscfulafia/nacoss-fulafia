<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: PUT, GET, POST");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");

require 'WP_REST.php';
$WP_REST = new Tasiukwaplong\WP_REST\WP_REST("nacoss-ful", 'nf', 'root', '', 'localhost');
// $WP_REST = new Tasiukwaplong\WP_REST\WP_REST("id12093794_wp_5b9cea5354483637e55e9a0f75e59df0", "wp_", "id12093794_wp_5b9cea5354483637e55e9a0f75e59df0", "7277Pass", "localhost");

$CALL_METHOD =  "asJSON";//asArray
$id = (isset($_GET['id'])) ? htmlspecialchars($_GET['id']) : '' ;
$page = (isset($_GET['page'])) ? htmlspecialchars($_GET['page']) : '' ;
$call = (isset($_GET['call'])) ? htmlspecialchars($_GET['call']) : '' ;


switch ($call) {
	case 'getallposts':
		die($WP_REST->getAllPosts($CALL_METHOD));
		break;
	case 'getpost':
		die($WP_REST->getPost($id, $CALL_METHOD));
		break;	
	case 'getpage':
		die($WP_REST->getPage($page, $CALL_METHOD));
		break;	
	case 'getallfiles':
		die($WP_REST->getAllFiles($CALL_METHOD));
		break;	
	default:
		die(json_encode(["data"=> ["errorExist"=>true, "body"=>"Incorrect API call"]], true));
		break;
}

