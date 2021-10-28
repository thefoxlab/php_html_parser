<?php 

require 'vendor/autoload.php';
use KubAT\PhpSimple\HtmlDomParser;

$file=  './tmp_out.php' ;
$DOM  = HtmlDomParser::file_get_html( $file );


$ARR = array( );
function WalkUL( $ul, &$ar )
{
    foreach( $ul->children as $li )
    {
        if ( $li->tag != "li" )
        {
            continue;
        }
       
       
        $desc = $li->find( "label span", 0 );
        $txt = $li->find( "label", 0 )->plaintext;
        $val = $li->find( "label input", 0 )->value;
        $txt = $val." >> ".$txt;
        
        if(trim($desc))
        {
            $txt = str_replace($desc->plaintext, " >> ".$desc->plaintext, $txt);
            //$txt .= " - Description";
        }
        $txt = preg_replace('/\s+/', ' ', $txt);
        
        $arar = array( );
        
        $t = explode(">>", $txt);
        
        $k = trim($t['0']);
        $name = trim($t['1']);
        $desc = isset($t['2']) ? trim($t['2']) : "";
        
        
        $arar['name'] = $name;
        $arar['desc'] = $desc;
        $arar['full_pa'] = $txt;
        
        foreach( $li->children as $ulul )
        {
            if ( $ulul->tag != "ul" )
            {
                continue;
            }
            WalkUL( $ulul, $arar['list'] );
        }
        
        
        $ar[$k] = $arar;
    }
}
WalkUL( $DOM->find( "ul", 0 ), $ARR );

function assign_val($ARR,$parent=0){
    foreach ($ARR as $key=>$val){
        
        //echo "<br/> PARENT = ".$parent." ID = ".$key." NAME = ".$val['name']." DESC = ".$val['desc']." <br/> <br/>";
        
        $t_v = $parent ? $parent : "NULL";
        
        echo "<br/>";
        echo "INSERT INTO `learning_framework` (`learning_framework_id`,`name`, `description`, `parent_id`, `status`, `created_time`, `updated_time`, `created_by_admin_id`, `updated_by_admin_id`) VALUES ('".$key."', '".$val['name']."', '".$val['desc']."', $t_v, '1', now(), NULL, '1', NULL);";
        
        if(isset($val['list']))
        {
            
            echo "<br/>";
           assign_val($val['list'],$key);
        }
    }
}
assign_val($ARR);
//echo "<pre>";print_r($ARR);exit;
?>