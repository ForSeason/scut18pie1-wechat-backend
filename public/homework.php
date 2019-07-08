<?php
	//$content=file_get_contents('weixin/contents/homework.txt');
?>
<html>
<head><title>作业栏</title></head>
<body>
<form method="post" action="actionHomework.php">
<?php 
	$content=file_get_contents(__DIR__.'/assets/homework.txt');
	echo '<textarea cols=30 rows=50 name="content">'.$content.'</textarea>';
?>
<br/>
<input type="submit" value="提交修改">
</form>
</body>
</html>
