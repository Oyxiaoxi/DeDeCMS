# DeDeCMS
According to the individual needs a modified version

# Official
> 1. 官方网站  http://www.dedecms.com
> 2. 帮助手册	http://help.dedecms.com/index_old.htm

# Modify Source Code

> 1.  修改栏目首字母

----
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
