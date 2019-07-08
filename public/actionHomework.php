<?php
	$content=$_POST['content'];
	if (isset($content) and $content<>''){
   		file_put_contents(__DIR__.'/assets/homework.txt',$content);
   		echo '修改成功';
	}
?>
