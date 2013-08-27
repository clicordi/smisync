 <?php
 // on ouvre le fichier
                $file = fopen('/var/www/doli/htdocs/smisync/admin/bddsmi.ini', 'r+');

                // remet le curseur en debut de fichier
                fseek($file, 0);

                // lecture des variables
                echo '<pre>';
                echo $url = fgets($file);
                echo $port = fgets($file);
                echo $nom = fgets($file);
                echo $id = fgets($file);
                echo $mdp = fgets($file);
    echo '</pre>';
                echo '<pre>';
                echo $url = substr($url, 0, -1);
                echo $port = substr($port, 0, -1);
                echo $nom = substr($nom, 0, -1);
                echo $id = substr($id, 0, -1);
                echo $mdp =substr($mdp, 0, -1);
    echo '</pre>';

    fclose($file);
?>