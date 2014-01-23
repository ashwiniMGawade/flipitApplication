<?php
// scp /home/kortingscode.nl/kortingscode.nl/varnish/restart.php kortingsco@141.138.196.192:/home/kortingsco/public_html/varnish
// r2J761M3
// $connection = ssh2_connect('31.3.97.202', 22);
// ssh2_auth_password($connection, 'root', '4jbXW7w7mpjQ');
// $stream = ssh2_exec($connection, "service varnish restart");
// stream_get_contents($stream);
if (!function_exists("ssh2_connect")) die("function ssh2_connect doesn't exist");
// log in at server1.example.com on port 22
if(!($con = ssh2_connect("31.3.97.202", 22))){
    echo "fail: unable to establish connection\n";
} else {
    // try to authenticate with username root, password secretpassword
    if(!ssh2_auth_password($con, "root", "4jbXW7w7mpjQ")) {
        echo "fail: unable to authenticate\n";
    } else {
        // allright, we're in!
        echo "okay: logged in...\n";

        // execute a command
        if (!($stream = ssh2_exec($con, "service varnish restart" ))) {
            echo "fail: unable to execute command\n";
        } else {
            // collect returning data from command
            stream_set_blocking($stream, true);
            $data = "";
            while ($buf = fread($stream,4096)) {
                $data .= $buf;
            }
            fclose($stream);
        }
    }
}
?>