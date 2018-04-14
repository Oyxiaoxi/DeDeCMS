# DeDeCMS
According to the individual needs a modified version

# Official
> 1. 官方网站  http://www.dedecms.com
> 2. 帮助手册	http://help.dedecms.com/index_old.htm

# Modify Source Code

> 修改栏目首字母

____


<p> 找到 /dede/catalog.add.php </p>

<pre>
	85: $toptypedir = GetPinyin(stripslashes($toptypename));
	$toptypedir = GetPinyin(stripslashes($toptypename),1);
	108: $typedir = $toptypedir.'/'.GetPinyin(stripslashes($v));
	$typedir = $toptypedir.'/'.GetPinyin(stripslashes($v),1);
	134: $toptypedir = GetPinyin(stripslashes($toptypename));
	$toptypedir = GetPinyin(stripslashes($toptypename),1);
	187: $typedir = GetPinyin(stripslashes($typename));
	$typedir = GetPinyin(stripslashes($typename),1); 
</pre>


> 修改发布文章时 keywords 字数限制

____

找到 /dede/article_add.php

<pre> sql: ALTER TABLE `dede_archives` MODIFY COLUMN `keywords`  char(255)
107：$keywords = cn_substrR($keywords,60);
$keywords = cn_substrR($keywords,255);
</pre>

> 采集完文章以后自动生成描述

____
找到 /dede/archives_do.php

<pre>
783： $allindexs = preg_replace("/#p#|#e#/",'',$sp->GetFinallyIndex());
后增加：
if($description=='' && $cfg_auot_description>0){
    $description = cn_substr(html2text($body),$cfg_auot_description);
    $description = trim(preg_replace('/#p#|#e#/','',$description));
    $description = addslashes($description);
}
</pre>

> 修改文章，不改变发布时间的方法

____

找到 /dede/templets/article_edit.htm
<pre>
464： $nowtime = GetDateTimeMk(time());
$nowtime = GetDateTimeMk($arcRow["pubdate"]);
</pre>


# Modify the function example
>1. 批量填加关键词 ( /dede/archives_do.php，/dede/js/list.js)

>2. 频道首页调用图集的第一张图片 ( /include/extend.func.php , /include/taglib/arclist.lib.php ) 调用方法： [field:first_imgurl/] 

>3. 列表页调用文章内容第一张图片(非缩略图)方法 （ /include/common.func.php ）调用方法：[field:litpic function='firstimg(@me)'/] 

>4. 自定义表单发邮件功能 （ /plus/diy.php.bak ）

>5. 添加批量增加tag标签功能 ( /dede/templates/content_list.htm ，/dede/js/list.js，/dede/archives_do.php )

>6. DEDE 生成手机静态 (utf-8/uploads/DEDE生成手机静态(utf-8).xml)，bug {dede:field name='position'/}
会导致生成的 文章页连接时 出现不该有的字符
修改 include/typelink.class.php 里的 GetPositionLink()，GetOneTypeUrl() 方法，删除掉 defined('DEDEMOB') 判断...

>7. 相关技巧代码:

<pre>
// 判断当前状态的代码
{dede:field name=typeid runphp="yes"}(@me=="")? @me=" class='current' ":@me="";{/dede:field}>
class='current' 被选中时的状态
// 二级域名下面包削导航
{dede:field name='position'  runphp='yes'}
$b = array("<a href='http://www.xxxx.com/'>网站首页</a> >","m/","手机站");
$c = array("","","网站首页");
@me=str_replace($b,$c,@me); 
{/dede:field}
</pre>

>8. linux dedecms 执行权限设置:

```php
$filename=scandir("./"); //遍历当前目录
$configure1 = "          location ~* ^/(";
$configure2 = ")/.*\.(php|php5)$";
$configure3 = "{<br>		
               deny all;<br>
          }";
for($i=0;$i<sizeof($filename);$i++) {
	if(is_dir($filename[$i]))  //判断是否为文件夹
	{  
		if($i+1==sizeof($filename)){
			$configure .= $filename[$i]; 
		}else{
			$configure .= $filename[$i]."|";
		}
	}
}
$configure = str_replace(".|..|","",$configure);
$configure = str_replace("include|","",$configure);
$configure = str_replace("plus|","",$configure);
echo $configure1.$configure.$configure2.$configure3;
```

>9. 文章内容里的图片替换连接( 生成手机静态模型导入后，会出现文章图片连接错误！ ):
<pre>
{dede:field.body runphp='yes'}
@me=str_replace('/uploads/','http://www.xxxxx.com/uploads/',@me); 
{/dede:field.body}
{dede:field.typedir runphp='yes'}
@me=str_replace('{cmspath}','http://m.xxxxxx.com',@me);
{/dede:field.typedir}
</pre>

>10. 手机对应跳转链接 :
+ 1. 如果有指定栏目，封面连接，就跳转 指定连接。
+ 2. 如果文章没有指定连接，栏目也没有指定连接就跳转 首页。
+ 3. 如果有指定栏目，封面连接，但是没有指定文章连接，就跳转到 栏目 连接。

+ 系统->系统基本参数->站点设置，添加新变量 cfg_mobilehost。
+ 核心->内容模型管理->普通文章，添加新字段 mobileurl。
+ #@__arctype 表，在siteurl 之后填加 mobileurl，varchar(100)
+ catalog_edit.php 填加 mobileurl字段。catalog_edit.htm 增加填写URL地址的input
+ catalog_add.php 填加 mobileurl字段。catalog_add.htm

```php
# common.func.php 填加共用方法
function getArcUrl($data){
    global $cfg_mobilehost,$dsql;   
    // 栏目连接 
    $type = $dsql->GetOne("SELECT * FROM `#@__arctype` WHERE `mobileurl` = '$data'");
    if ($type){
        $typeUrl = $type['mobileurl'];
        $typeUrl = $typeUrl?$typeUrl:$cfg_mobilehost;
        return $typeUrl;
    } 
    
    // 文章连接
    $article = $dsql->GetOne("SELECT * FROM `#@__addonarticle` WHERE `aid` = '$data'");
    if ($article['mobileurl']) {
        $arcUrl = $article['mobileurl'];
    } else {
        $result = $dsql->GetOne("SELECT * FROM `#@__archives` WHERE `id` = '$data'");
        $typeid = $result['typeid'];
        $result = $dsql->GetOne("SELECT * FROM `#@__arctype` WHERE `id` = {$typeid}");
        $arcUrl = $result['mobileurl'];
        $arcUrl = $arcUrl?$arcUrl:$cfg_mobilehost;
    }
    return $arcUrl;
}
```

+ 模板调用

```html
主页:<script type="text/javascript">uaredirect("{dede:global.cfg_mobilehost/}"); </script> 
封面,列表：<script type="text/javascript">uaredirect("{dede:field.mobileurl function='getArcUrl(@me)'/}"); </script>
文章：<script type="text/javascript">uaredirect("{dede:field.id function='getArcUrl(@me)'/}"); </script>
```

#### 因为时间关系，还有很多修改功能没有展现出来，请有兴趣的自己研读代码吧！