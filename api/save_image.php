<?php

header('Access-Control-Allow-Origin: *');

try{
if ($_SERVER["REQUEST_METHOD"]=="POST"){
    $url="";
    if (isset($_FILES["media_file"]) && $_FILES["media_file"]["size"]>0){
        $filename=$_FILES["media_file"]["name"];
        $fullfilepath="media/".time();
        $vals=explode(".",$filename);
        $is_png=false;
        $file_ext="jpg";
        if (count($vals)>1){
            $vl=$vals[count($vals)-1];
            if (strtolower($vl)=="png"){
                $fullfilepath.=".png";
                $file_ext="png";
                $is_png=true;
            }else if (strtolower($vl)=="mp4"){
                $fullfilepath.=".mp4";
                $file_ext="mp4";
            }else{
                $fullfilepath.=".jpg";
                //$is_png=true;
            }
        }else{
            //add jpg
            $fullfilepath.=".jpg";
        }
        move_uploaded_file($_FILES["media_file"]["tmp_name"],$fullfilepath);

        if ($file_ext=="jpg" || $file_ext=="png"){
            include "imageprocess.php";
            $destination="media/".time()."_compressed.".$file_ext;
            $destination=pw_compress_image($fullfilepath,$destination,640,-1,100);
            if ($destination!=$fullfilepath){
                unlink($fullfilepath);
            }
            $fullfilepath=$destination;
        }
        
        /*$destination_rot="media/".time()."_compressed_rot.".$file_ext;
        $destination_rot=pw_fix_orientation($destination,$destination_rot,100);
        if ($destination_rot!=$destination){
            unlink($destination);
        }
        $fullfilepath=$destination_rot;*/

        $emUrl = "http".(!empty($_SERVER['HTTPS'])?"s":"").
        "://".$_SERVER['SERVER_NAME'].($_SERVER['SERVER_PORT']=='80'?"":(":".$_SERVER['SERVER_PORT'])).$_SERVER['REQUEST_URI'];
        $codeUrl=dirname($emUrl)."/".$fullfilepath;
        $url=$codeUrl;
    }else{
        $result=array("status"=>400,"msg"=>"Media file not present in request");
        echo json_encode($result);
        exit();
    }
    $result=array("status"=>200,"url"=>$url);
    echo json_encode($result);
    exit();
}
}catch(Exception $ex){
    $result=array("status"=>400,"msg"=>"Exception: ".$ex->getMessage());
    echo json_encode($result);
    exit();
}
?>

<body>
    <form method="POST" enctype="multipart/form-data">
        <input type="file" name="media_file" />
        <input type="submit" value="Submit" />
    </form>
</body>