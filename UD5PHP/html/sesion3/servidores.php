<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Servidores</title>
</head>
<body>
    <?php
        $servers= ["WebServer", "DBServer", "FileServer", "MailServer", "ProxyServer"];
        $servers[]="BackupServer";

        echo "<ul>";
        foreach ($servers as $server) {
            
            echo "<li>".$server."</li>";
        }
        echo "</ul>";

        echo "Total de servidores: ".count($servers);

    ?>
</body>
</html>