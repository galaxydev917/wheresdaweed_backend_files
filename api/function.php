<?php

$hoursDifference=(7*60*60); //2 hours for unix timestamp

function get_hours_from_my_timezone($tzone){
    $multiplier=1;
    //format GMT+5:00 or GMT-5:00
    $vals=explode("+",$tzone);
    if (count($vals)<=1){
        $vals=explode("-",$tzone);
        if (count($vals)<=1){
            return 0;
        }
        $multiplier=-1;
    }

    $offset=$vals[1];
    $vals=explode(":",$offset);
    $offset=intval($vals[0]);
    return ($offset*$multiplier);
}


function get_gmt_from_my_timezone($format,$tzone){
    $tzone="GMT+5:00";
    //$tzone="GMT-5:00";
    $currTime=time();
    $hrs=get_hours_from_my_timezone($tzone);
    $currTime+=($hrs*60*60);
    //return gmdate($format,$currTime);
    return gmdate($format);
}

function get_users_to_follow($user_id=0,$limit=10){
    $params=array();
    $query="SELECT u.* FROM user u WHERE u.id<>?";
    $params[]=$user_id;
    $query.=" AND (SELECT COUNT(f.id) FROM follower f WHERE f.follower_id=? AND f.user_id=u.id)=0";
    $params[]=$user_id;
    $query.=" LIMIT 0,".$limit;
    $users=R::getAll($query,$params);
    return $users;
}

function get_checkin_date_str($diff_timestamp,$checkin_dt_str){
    if ($diff_timestamp<1){
        $checkin_dt_str="JUST NOW";
    }else if ($diff_timestamp<60){
        $checkin_dt_str=$diff_timestamp." SECONDS AGO";
    }else{
        $diff_timestamp=ceil($diff_timestamp/60);
        if ($diff_timestamp<60){
            $checkin_dt_str=$diff_timestamp." MINUTES AGO";
        }else{
            $diff_timestamp=ceil($diff_timestamp/60); //hours
            if ($diff_timestamp<=24){
                //$checkin_dt_str="Today at ".$checkins[$a]["checkin_dt"];
                $checkin_dt_str=$diff_timestamp." HOURS AGO";
            }else if ($diff_timestamp<=48){
                //$checkin_dt_str="Yesterday at ".$checkins[$a]["checkin_dt"];
                $checkin_dt_str="1 DAY AGO";
            }else{
                $diff_timestamp=ceil($diff_timestamp/24);
                $checkin_dt_str=$diff_timestamp." DAYS AGO";
            }
        }
    }

    return $checkin_dt_str;
}

function get_single_checkin($post_id,$user_id){
    $params=array();
    $query="SELECT c.*,u.image_url AS profile_image_url,u.name AS profile_name,u.username AS profile_username,UNIX_TIMESTAMP() AS ux_timestamp,UNIX_TIMESTAMP(c.checkin_dt) AS dt_timestamp,UNIX_TIMESTAMP()-UNIX_TIMESTAMP(c.checkin_dt) AS diff_timestamp FROM checkin c,user u WHERE c.user_id=u.id";
    $query.=" AND c.id=?";
    $params[]=$post_id;
    
    $checkins=R::getAll($query,$params);

    for ($a=0;$a<count($checkins);$a++){
        $chk=$checkins[$a];
        $checkins[$a]["liked"]=R::getRow("SELECT post_id FROM liked WHERE post_id=? AND user_id=?",array($chk['id'],$user_id));
        $checkins[$a]["like_count"]=R::getCol("SELECT COUNT(*) AS like_count FROM liked WHERE post_id=?",array($chk['id']));

        $diff_timestamp=$checkins[$a]["diff_timestamp"];
        $checkin_dt_str=$checkins[$a]["checkin_dt"];
        $checkins[$a]["checkin_datestr"]=get_checkin_date_str($diff_timestamp,$checkin_dt_str);
    }

    return $checkins;
}

function get_search_checkins($user_id,$fish_type="",$city="",$radius="",$gps_lat="",$gps_lng="",$limit=100){
    $params=array();
    $query="SELECT c.*,u.image_url AS profile_image_url,u.name AS profile_name,u.username AS profile_username,UNIX_TIMESTAMP() AS ux_timestamp,UNIX_TIMESTAMP(c.checkin_dt) AS dt_timestamp,UNIX_TIMESTAMP()-UNIX_TIMESTAMP(c.checkin_dt) AS diff_timestamp FROM checkin c,user u WHERE c.user_id=u.id";
    if ($fish_type!=""){
        $query.=" AND LOWER(c.fish_type)=?";
        $params[]=strtolower($fish_type);
    }
    if ($city!=""){
        $query.=" AND LOWER(c.city)=?";
        $params[]=strtolower($city);
    }

    if ($city=="" && $fish_type=="" && $radius!="" && $gps_lat!="" && $gps_lat!=0){
        //search on $gps_lat and $gps_lng
        $query.=" AND (( 3959 * acos( cos( radians(".$gps_lat.") ) * cos( radians( c.checkin_latitude ) ) 
                * cos( radians(c.checkin_longitude) - radians(".$gps_lng.")) + sin(radians(".$gps_lat.")) 
                * sin( radians(c.checkin_latitude)))))<=".$radius;

        $query.=" AND (c.checkin_latitude IS NOT NULL AND c.checkin_latitude>0)";
    }

    $query.=" ORDER BY c.checkin_dt DESC";
    if ($limit!=-1){
        $query.=" LIMIT 0,".$limit;
    }

    $checkins=R::getAll($query,$params);

    for ($a=0;$a<count($checkins);$a++){
        $chk=$checkins[$a];
        $checkins[$a]["liked"]=R::getRow("SELECT post_id FROM liked WHERE post_id=? AND user_id=?",array($chk['id'],$user_id));
        $checkins[$a]["like_count"]=R::getCol("SELECT COUNT(*) AS like_count FROM liked WHERE post_id=?",array($chk['id']));

        $diff_timestamp=$checkins[$a]["diff_timestamp"];
        $checkin_dt_str=$checkins[$a]["checkin_dt"];
        $checkins[$a]["checkin_datestr"]=get_checkin_date_str($diff_timestamp,$checkin_dt_str);
    }

    return $checkins;
}

function get_post_text_parsed($post_text){
    $post_text=str_replace("<","&lt;",$post_text);
    $post_text=str_replace(">","&gt;",$post_text);
    return $post_text;
}

function get_feed_ads($user_id){
    global $hoursDifference;
    $companies=R::getAll("SELECT id,name FROM user WHERE id<>? AND account_type='business'",array($user_id));
    $companyAds=array();
    //echo count($companies)."<br />";
    for ($a=0;$a<count($companies);$a++){
        $cmp=$companies[$a];
        $query="SELECT c.*,u.profile_pic_url,u.name AS profile_name,NOW() AS curr_dt,UNIX_TIMESTAMP() AS ux_timestamp,(UNIX_TIMESTAMP(c.post_dt)-".$hoursDifference.") AS dt_timestamp,UNIX_TIMESTAMP()-(UNIX_TIMESTAMP(c.post_dt)-".$hoursDifference.") AS diff_timestamp FROM post c,user u WHERE c.user_id=u.id";
        $query.=" AND c.user_id=?";
        $query.=" AND c.is_sponsored=1";
        $checkins=R::getAll($query,array($cmp["id"]));

        //echo count($checkins)."<br />";
        //goto return_ads;
        for ($b=0;$b<count($checkins);$b++){
            $chk=$checkins[$b];
            $checkins[$b]["liked"]=R::getRow("SELECT * FROM liked WHERE post_id=? AND user_id=?",array($chk['id'],$user_id));
            $checkins[$b]["like_count"]=R::getCol("SELECT COUNT(*) AS like_count FROM liked WHERE post_id=?",array($chk['id']));
            $checkins[$b]["reactions_type"]=R::getAll("SELECT emoji_type FROM liked WHERE post_id=? GROUP BY emoji_type",array($chk['id']));
    
            $diff_timestamp=$checkins[$b]["diff_timestamp"];
            $checkin_dt_str=$checkins[$b]["post_dt"];
            $checkins[$b]["checkin_datestr"]=get_checkin_date_str($diff_timestamp,$checkin_dt_str);
            $checkins[$b]["post_text"]=get_post_text_parsed($checkins[$b]["post_text"]);
        }
        
        $selIndex=0;
        if (count($checkins)>0){
            $companyAds[]=array("company"=>$cmp,"ad"=>$checkins[$selIndex]);
        }
    }

    return_ads:
    return $companyAds;
}

function get_tag_feed($user_id,$tag="",$limit=100){
    global $hoursDifference;
    $params=array();
    $query="SELECT c.*,u.profile_pic_url,u.name AS profile_name,NOW() AS curr_dt,UNIX_TIMESTAMP() AS ux_timestamp,(UNIX_TIMESTAMP(c.post_dt)-".$hoursDifference.") AS dt_timestamp,UNIX_TIMESTAMP()-(UNIX_TIMESTAMP(c.post_dt)-".$hoursDifference.") AS diff_timestamp FROM post c,user u,hashtag ht,posthashtag pht WHERE c.user_id=u.id";
    $query.=" AND ht.tag_value=?";
    $params[]=$tag;
    $query.=" AND ht.id=pht.hashtag_id";
    $query.=" AND pht.post_id=c.id";
    /*if ($user_id!=0){
        $query.=" AND (SELECT COUNT(f.id) FROM follower f WHERE f.user_id=c.user_id AND f.follower_id=?)>0";
        $params[]=$user_id;
    }*/
    $query.=" AND c.is_sponsored=0";
    $query.=" ORDER BY c.post_dt DESC";
    if ($limit!=-1){
        $query.=" LIMIT 0,".$limit;
    }

    //echo $query."<br />";

    $checkins=R::getAll($query,$params);

    for ($a=0;$a<count($checkins);$a++){
        $chk=$checkins[$a];
        $checkins[$a]["liked"]=R::getRow("SELECT * FROM liked WHERE post_id=? AND user_id=?",array($chk['id'],$user_id));
        $checkins[$a]["like_count"]=R::getCol("SELECT COUNT(*) AS like_count FROM liked WHERE post_id=?",array($chk['id']));
        $checkins[$a]["reactions_type"]=R::getAll("SELECT emoji_type FROM liked WHERE post_id=? GROUP BY emoji_type",array($chk['id']));

        $diff_timestamp=$checkins[$a]["diff_timestamp"];
        $checkin_dt_str=$checkins[$a]["post_dt"];
        $checkins[$a]["checkin_datestr"]=get_checkin_date_str($diff_timestamp,$checkin_dt_str);
        $checkins[$a]["post_text"]=get_post_text_parsed($checkins[$a]["post_text"]);
    }

    return $checkins;
}

function get_home_feed($user_id,$limit=100){
    global $hoursDifference;
    $params=array();
    $query="SELECT c.*,u.profile_pic_url,u.name AS profile_name,NOW() AS curr_dt,UNIX_TIMESTAMP() AS ux_timestamp,(UNIX_TIMESTAMP(c.post_dt)-".$hoursDifference.") AS dt_timestamp,UNIX_TIMESTAMP()-(UNIX_TIMESTAMP(c.post_dt)-".$hoursDifference.") AS diff_timestamp FROM post c,user u WHERE c.user_id=u.id";
    if ($user_id!=0){
        $query.=" AND (SELECT COUNT(f.id) FROM follower f WHERE f.user_id=c.user_id AND f.follower_id=?)>0";
        $params[]=$user_id;
    }
    $query.=" AND c.is_sponsored=0";
    $query.=" ORDER BY c.post_dt DESC";
    if ($limit!=-1){
        $query.=" LIMIT 0,".$limit;
    }

    $checkins=R::getAll($query,$params);

    for ($a=0;$a<count($checkins);$a++){
        $chk=$checkins[$a];
        $checkins[$a]["liked"]=R::getRow("SELECT * FROM liked WHERE post_id=? AND user_id=?",array($chk['id'],$user_id));
        $checkins[$a]["like_count"]=R::getCol("SELECT COUNT(*) AS like_count FROM liked WHERE post_id=?",array($chk['id']));
        $checkins[$a]["reactions_type"]=R::getAll("SELECT emoji_type FROM liked WHERE post_id=? GROUP BY emoji_type",array($chk['id']));

        $diff_timestamp=$checkins[$a]["diff_timestamp"];
        $checkin_dt_str=$checkins[$a]["post_dt"];
        $checkins[$a]["checkin_datestr"]=get_checkin_date_str($diff_timestamp,$checkin_dt_str);
        $checkins[$a]["post_text"]=get_post_text_parsed($checkins[$a]["post_text"]);
    }

    return $checkins;
}

function get_user_feed($user_id,$from_user_id,$limit=100){
    global $hoursDifference;
    $params=array();
    $query="SELECT c.*,u.profile_pic_url,u.name AS profile_name,UNIX_TIMESTAMP() AS ux_timestamp,(UNIX_TIMESTAMP(c.post_dt)-".$hoursDifference.") AS dt_timestamp,UNIX_TIMESTAMP()-(UNIX_TIMESTAMP(c.post_dt)-".$hoursDifference.") AS diff_timestamp FROM post c,user u WHERE c.user_id=u.id";
    $query.=" AND c.user_id=?";
    $params[]=$user_id;
    $query.=" ORDER BY is_sponsored DESC,c.post_dt DESC";
    if ($limit!=-1){
        $query.=" LIMIT 0,".$limit;
    }

    //echo $query."<Br />";

    $checkins=R::getAll($query,$params);

    for ($a=0;$a<count($checkins);$a++){
        $chk=$checkins[$a];
        $checkins[$a]["liked"]=R::getRow("SELECT * FROM liked WHERE post_id=? AND user_id=?",array($chk['id'],$from_user_id));
        $checkins[$a]["like_count"]=R::getCol("SELECT COUNT(*) AS like_count FROM liked WHERE post_id=?",array($chk['id']));
        $checkins[$a]["reactions_type"]=R::getAll("SELECT emoji_type FROM liked WHERE post_id=? GROUP BY emoji_type",array($chk['id']));

        $diff_timestamp=$checkins[$a]["diff_timestamp"];
        $checkin_dt_str=$checkins[$a]["post_dt"];
        $checkins[$a]["checkin_datestr"]=get_checkin_date_str($diff_timestamp,$checkin_dt_str);
        $checkins[$a]["post_text"]=get_post_text_parsed($checkins[$a]["post_text"]);
    }

    return $checkins;
}

function get_liked_checkins($user_id=0,$limit=-1){
    $params=array();
    $query="SELECT c.*,u.image_url AS profile_image_url,u.name AS profile_name,u.username AS profile_username,UNIX_TIMESTAMP() AS ux_timestamp,UNIX_TIMESTAMP(c.checkin_dt) AS dt_timestamp,UNIX_TIMESTAMP()-UNIX_TIMESTAMP(c.checkin_dt) AS diff_timestamp";
    $query.=" FROM checkin c,user u,liked l WHERE c.id=l.post_id AND c.user_id=u.id AND l.user_id=?";
    $params[]=$user_id;
    
    $query.=" ORDER BY c.checkin_dt DESC";
    if ($limit!=-1){
        $query.=" LIMIT 0,".$limit;
    }

    //echo $query;

    $checkins=R::getAll($query,$params);

    for ($a=0;$a<count($checkins);$a++){
        $chk=$checkins[$a];
        $checkins[$a]["liked"]=R::getRow("SELECT post_id FROM liked WHERE post_id=? AND user_id=?",array($chk['id'],$user_id));
        $checkins[$a]["like_count"]=R::getCol("SELECT COUNT(*) AS like_count FROM liked WHERE post_id=?",array($chk['id']));

        $diff_timestamp=$checkins[$a]["diff_timestamp"];
        $checkin_dt_str=$checkins[$a]["checkin_dt"];
        $checkins[$a]["checkin_datestr"]=get_checkin_date_str($diff_timestamp,$checkin_dt_str);
    }

    return $checkins;
}

function save_activity($user_id,$actor_id,$type,$post_id,$object_id){
    $activity=R::dispense('activity');
    $activity->user_id=$user_id;
    $activity->actor_id=$actor_id;
    $activity->activity_dt=date('Y-m-d H:i:s');
    $activity->type=$type;
    $activity->post_id=$post_id;
    $activity->object_id=$object_id;

    R::store($activity);
}

function get_activity($user_id,$act_type="other"){
    $params=array();
    $query="SELECT act.activity_dt,act.actor_id,act.user_id,act.post_id,act.type";
    $query.=",UNIX_TIMESTAMP() AS ux_timestamp,UNIX_TIMESTAMP(act.activity_dt) AS dt_timestamp,UNIX_TIMESTAMP()-UNIX_TIMESTAMP(act.activity_dt) AS diff_timestamp";
    $query.=",auser.name AS actor_name,auser.image_url AS actor_profile_image";
    $query.=",u.name AS user_name,u.image_url AS user_profile_image";
    $query.=",p.image_url AS post_image";
    $query.=" FROM activity act";
    $query.=" LEFT JOIN checkin p ON act.post_id=p.id";
    $query.=",user auser,user u";
    $query.=" WHERE act.user_id=? AND auser.id=act.actor_id AND u.id=act.user_id";

    //echo $query;

    $params[]=$user_id;

    $checkins=R::getAll($query,$params);

    for ($a=0;$a<count($checkins);$a++){
        $chk=$checkins[$a];
        
        $diff_timestamp=$checkins[$a]["diff_timestamp"];
        $checkin_dt_str=$checkins[$a]["activity_dt"];
        $checkins[$a]["checkin_datestr"]=get_checkin_date_str($diff_timestamp,$checkin_dt_str);
    }

    return $checkins;
}

?>