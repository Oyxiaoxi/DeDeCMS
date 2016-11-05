<?php
function litimgurls($imgid=0)
{
    global $lit_imglist,$dsql;
    //获取附加表
    $row = $dsql->GetOne("SELECT c.addtable FROM #@__archives AS a LEFT JOIN #@__channeltype AS c 
                                                            ON a.channel=c.id where a.id='$imgid'");
    $addtable = trim($row['addtable']);
    
    //获取图片附加表imgurls字段内容进行处理
    $row = $dsql->GetOne("Select imgurls From `$addtable` where aid='$imgid'");
    
    //调用inc_channel_unit.php中ChannelUnit类
    $ChannelUnit = new ChannelUnit(2,$imgid);
    
    //调用ChannelUnit类中GetlitImgLinks方法处理缩略图
    $lit_imglist = $ChannelUnit->GetlitImgLinks($row['imgurls']);
    
    //返回结果
    return $lit_imglist;
}

function get_firstbigimg($arcid){
   //获取图片附加表imgurls字段内容进行处理
   $dsql = new DedeSql(false);
   $row = $dsql->GetOne("Select imgurls From #@__addonimages where aid='$arcid'");
    preg_match_all("|{dede:img ddimg='(.*)' text=(.*)|Uis",$row['imgurls'],$imgurls); //获取所有图片地址
    $get_firestimg = $imgurls[1][0]; // 
    return $get_firestimg;
　}