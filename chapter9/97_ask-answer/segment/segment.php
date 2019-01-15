<?php
function sinasegment($str)
{
    $seg = new SaeSegment();
    $ret = $seg->segment($str, 1);

    if ($ret === false){
        return;
    }
    $category = "";
    $keyword = "";
    foreach ($ret as $key => $value) {
        if ($value["word_tag"] == 95){
            $category = $value["word"];
        }
        if ($value["word_tag"] == 102){
            $keyword = $value["word"];
        }
    }
    if (!empty($category) && !empty($keyword)){
        return array('category'=>$category, 'keyword'=>$keyword); 
    }else{
        return;
    }
}
?>