<?php
/**
 * Creator: Tasiu kwaplong
 * Email: tasiukwaplong@gmail.com
 */

namespace Tasiukwaplong\WP_REST;

use Exception;
use Mysqli;

class WP_REST{
	public $resp = ["data"=>[]];
	public $tblPrefix;
    public $DB;
	public $currentDomain  = "";


	function __construct($dbName, $tblPrefix, $dbUser, $dbPsw, $dbHost){
    	$this->tblPrefix = $tblPrefix;
   
    	$this->connectSQLDB($dbHost, $dbUser, $dbPsw, $dbName);	
    }

    function connectSQLDB($dbHost, $dbUser, $dbPsw, $dbName){
    	$this->DB = new mysqli($dbHost, $dbUser, $dbPsw, $dbName);

		if ($this->DB->error) {
			$this->setError("SQl connection not possible. #".$this->DB->errno, "asArray");
		}
    }

    function setError($msg, $callMethod){
        $this->resp = ["data"=> ["errorExist"=>true, "error"=>$msg]];
        return $this->msgBody($callMethod);
    }

    function setMsg($msg, $callMethod = "asArray"){
        $this->resp = ["data"=> ["errorExist"=>false, "body"=>$msg]];
        return $this->msgBody($callMethod);
    }

    function msgBody($callMethod){
    	//default is asArray, set JSOn as asJSON
    	return ($callMethod == "asArray") ? $this->resp : json_encode($this->resp, true);
    }

    function getAllPosts($callMethod = "asArray"){
    	$tbl = $this->tblPrefix."posts";
        $queryGetPost = $this->DB->query("SELECT ID, post_title, post_content FROM $tbl WHERE post_status = 'publish' AND post_type = 'post' ORDER BY ID DESC");
    	$allPosts = [];
    	
    	if($queryGetPost){
            for ($i=0; $i < $queryGetPost->num_rows ; $i++) { 
                    //get all posts in a single array
                    $post = $queryGetPost->fetch_assoc();
                    $post["imageLink"] = $this->getFeaturedImage($post['ID']);
                    $allPosts[$i] = $post;
                }
            }else{
                return $this->setError("No posts exist yet", $callMethod);
            }
    	
    	$this->DB->close();
    	// $this->resp =  $allPosts;
    	return $this->setMsg($allPosts, $callMethod);
    }

    function getPost($id, $callMethod = "asArray"){
    	//get a particular post
    	if (!isset($id) || empty($id) ) {
    		# no post id supplied or empty post id
    		return $this->setError("There was a problem loading post content. Please confirm if it still exists", $callMethod);
    	}else{
    		$id = htmlspecialchars($id);
    	}
    	$tbl = $this->tblPrefix."posts";

    	$queryGetPost = $this->DB->query("SELECT ID, post_title, post_content FROM $tbl WHERE post_status = 'publish' AND post_type = 'post' AND ID = $id");
    	// $this->DB->close();    	
    	$post = $queryGetPost->fetch_assoc();
        $post["imageLink"] = $this->getFeaturedImage($post['ID']);
        $this->DB->close();     

        return ($queryGetPost->num_rows == 1) ? $this->setMsg($post, $callMethod) : $this->setError("Sorry, this post does not exist", $callMethod);
    }



    function getAllPages($callMethod = "asArray"){
    	$tbl = $this->tblPrefix."posts";
    	$queryGetPages = $this->DB->query("SELECT ID, post_name AS post_title, post_content FROM $tbl WHERE post_status = 'publish' AND post_type = 'page' ORDER BY ID DESC");
    	$allages = [];
    	if($queryGetPages){
        	for ($i=0; $i < $queryGetPages->num_rows ; $i++) { 
        		//get all posts in a single array
        		$page = $queryGetPages->fetch_assoc();
        		$allages[$i] = $page;
        	}
        }else{
            return $this->setError("No page created yet", $callMethod);
        }
    	
    	$this->DB->close();
    	// $this->resp =  $allages;
    	return $this->setMsg($allages, $callMethod);
    }

    function getPage($id_or_name, $callMethod = "asArray"){
    	//get a particular post
    	if (!isset($id_or_name) || empty($id_or_name) ) {
    		# no post id supplied or empty post id
    		return $this->setError("There was a problem loading $id_or_name. Please confirm if it still exists", $callMethod);
    	}else{
    		$id_or_name = htmlspecialchars($id_or_name);
    	}
    	$tbl = $this->tblPrefix."posts";

    	$queryGetPost = $this->DB->query("SELECT ID, post_name AS post_title, post_content FROM $tbl WHERE post_status = 'publish' AND post_type = 'page' AND (ID = '$id_or_name' OR post_name = '$id_or_name')");
    	$this->DB->close();    	
    	return ($queryGetPost && $queryGetPost->num_rows == 1) ? $this->setMsg($queryGetPost->fetch_assoc(), $callMethod) : $this->setError("Sorry, this page does not exist", $callMethod);
    }



   function getFeaturedImage($id){
    	//get a particular post
    	if (!isset($id) || empty($id) ) {
    		# no post id supplied or empty post id
    		return false;
    	}else{
    		$id = htmlspecialchars($id);
    	}

        $this->getCurrentDomain();

    	$postmeta = $this->tblPrefix."postmeta";

    	$queryGetFeaturedImage = $this->DB->query("SELECT meta_value AS imageLink FROM $postmeta WHERE meta_key = '_wp_attached_file' AND  post_id in (SELECT meta_value FROM $postmeta WHERE meta_key = '_thumbnail_id' AND post_id = $id)");

    	return ($queryGetFeaturedImage && $queryGetFeaturedImage->num_rows == 1) ? $this->currentDomain.'/wp-content/uploads/'.$queryGetFeaturedImage->fetch_assoc()['imageLink'] : null ;//wp-content/uploads
    }


  function getAllFiles($callMethod = "asArray"){
        //get a particular post
        $log = $this->tblPrefix."wfu_log";
        $userdata = $this->tblPrefix."wfu_userdata";
        $this->getCurrentDomain();

        //query to get certain properties of the file uploaded
        $queryGetFiles = $this->DB->query("SELECT DISTINCT 
                $log.idlog, $log.filepath, $log.fileSize, 
                (SELECT $userdata.propvalue FROM $userdata WHERE $userdata.property = 'level' AND $userdata.uploadid = $log.uploadid ) AS level, 
                (SELECT $userdata.propvalue FROM $userdata WHERE $userdata.property = 'Coursecode' AND $userdata.uploadid = $log.uploadid ) AS code, 
                (SELECT $userdata.propvalue FROM $userdata WHERE $userdata.property = 'Any info? (optional)' AND $userdata.uploadid = $log.uploadid ) AS info
                FROM $log, $userdata WHERE $log.action = 'upload' AND $userdata.uploadid = $log.uploadid");//This is an overkill..Pls a lesser version of this query
        $allFiles = [];

        // print_r($queryGetFiles);
        if($queryGetFiles ){
            $arrayIndex = 0;//init for allFiles index
            for ($j=0; $j < $queryGetFiles->num_rows ; $j++) { 
                //get all files in a single array
                $file = $queryGetFiles->fetch_assoc();
                $file['filepath'] = $this->currentDomain.$file['filepath'];
                $file['fileName'] = $this->getFileName($file['filepath']);
                if ($this->fileExists($file['idlog'])) {
                    //check if file is not deleted and save to aray for biew
                    $allFiles[$arrayIndex] = $file;
                    $arrayIndex++;
                }
            }
        }else{
            return $this->setError("No file uploaded yet", $callMethod);
        }

        $this->DB->close();
        return $this->setMsg($allFiles, $callMethod);
    }

    function getCurrentDomain(){
        //get current domain: void
        $tbl = $this->tblPrefix."options";
        $queryGetCurrentDomain = $this->DB->query("SELECT option_value FROM $tbl WHERE option_name = 'siteurl'");
        $this->currentDomain = ($queryGetCurrentDomain) ? $queryGetCurrentDomain->fetch_assoc()['option_value'] : '';
        return;
    }

    function getFileName($filePath){
        $filePath =  explode('/', $filePath);
        $lastIndex = count($filePath)-1;
        return $filePath[$lastIndex];
    }

    function fileExists($fileID){
        //check if the file still exist or deleted
        $log = $this->tblPrefix."wfu_log";
        
        //query to check if file is deleted
        $queryGetFiles = $this->DB->query("SELECT idlog
                FROM $log WHERE action = 'delete' AND linkedTo = '$fileID'");
        return ($queryGetFiles && ($queryGetFiles->num_rows >= 1)) ? FALSE : TRUE;
    }
}