<?php
/* <one line to give the program's name and a brief idea of what it does.>
* Copyright (C) <year> <name of author>
*
* This program is free software: you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation, either version 3 of the License, or
* (at your option) any later version.
*
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
* GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License
* along with this program. If not, see <http://www.gnu.org/licenses/>.
*/

/**
* \file admin/mymodule.php
* \ingroup mymodule
* \brief This file is an example module setup page
* Put some comments here
*/
// Dolibarr environment
$res = @include("../../main.inc.php"); // From htdocs directory
if (! $res) {
    $res = @include("../../../main.inc.php"); // From "custom" directory
}


// Libraries
require_once DOL_DOCUMENT_ROOT . "/core/lib/admin.lib.php";
//require_once '../lib/mymodule.lib.php';
//require_once "../class/myclass.class.php";
// Translations
$langs->load("smisync@smisync");

// Access control
if (! $user->admin) {
    accessforbidden();
}

// Parameters
$action = GETPOST('action', 'alpha');

/*
* Actions
*/



/*
 * ID BDD SMI
  */
// on ouvre le fichier
$file = fopen('bddsmi.ini', 'r+');

if(isset($_REQUEST['urlbdd']) && isset($_REQUEST['portbdd']) && isset($_REQUEST['nombdd']) && isset($_REQUEST['idbdd']) && isset($_REQUEST['mdpbdd'])
 && !empty($_REQUEST['urlbdd']) && !empty($_REQUEST['portbdd']) && !empty($_REQUEST['nombdd']) && !empty($_REQUEST['idbdd']) && !empty($_REQUEST['mdpbdd']))
{
    //met a jour les variables
    $url = $_REQUEST['urlbdd'];
    $port = $_REQUEST['portbdd'];
    $nom = $_REQUEST['nombdd'];
    $id = $_REQUEST['idbdd'];
    $mdp = $_REQUEST['mdpbdd'];
    
    // remet le curseur en debut de fichier
    fseek($file, 0);
    
    // on réécris dans le fichier
    fputs($file, $url);
    fputs($file, "\n");
    fputs($file, $port);
    fputs($file, "\n");
    fputs($file, $nom);
    fputs($file, "\n");
    fputs($file, $id);
    fputs($file, "\n");
    fputs($file, $mdp);
    fputs($file, "\n");
}

// remet le curseur en debut de fichier
fseek($file, 0);

// lecture des variables, sans le retour a la ligne
$url = substr(fgets($file), 0, -1);
$port = substr(fgets($file), 0, -1);
$nom = substr(fgets($file), 0, -1);
$id = substr(fgets($file), 0, -1);
$mdp = substr(fgets($file), 0, -1);

// on ferme le fichier
fclose($file);

/*
 * Champ a afficher dans le suivi d'intervention
  */
  try {
    //identifiants pour la bdd de dolibarr
    $idDoli = array(
        "URL"			=> "localhost", 
        "port"			=> "8888", 
        "nomBDD"		=> "dolibarr", 
        "identifiant"	=> "root", 
        "mdp"			=> "root" 
    );
    
    //identifiants pour la bdd de smi
    $idSmi = array(
        "URL"			    => $url, 
        "port"			    => $port, 
        "nomBDD"		=> $nom, 
        "identifiant"	=> $id, 
        "mdp"			    => $mdp 
    );
    
    $pdo_options[PDO::ATTR_ERRMODE] = PDO::ERRMODE_EXCEPTION;
    // connections aux bdd
    $bddSmi = new PDO('mysql:host='. $idSmi['URL'] .';dbname='. $idSmi['nomBDD'].';port='. $idSmi['port'], $idSmi['identifiant'], $idSmi['mdp'], $pdo_options); 
    $bddDoli = new PDO('mysql:host='. $idDoli['URL'] .';dbname='. $idDoli['nomBDD'].';port='. $idDoli['port'], $idDoli['identifiant'], $idDoli['mdp'], $pdo_options); 



    if(isset($_REQUEST['addLabel']) && isset($_REQUEST['addColumn']) && isset($_REQUEST['addTable']) && isset($_REQUEST['addDisp'])
     && !empty($_REQUEST['addLabel']) && !empty($_REQUEST['addColumn']) && !empty($_REQUEST['addTable']) && is_numeric($_REQUEST['addDisp']))
    {
        $addLabel = htmlspecialchars(addslashes($_REQUEST['addLabel']));
        $addColumn = htmlspecialchars(addslashes($_REQUEST['addColumn']));
        $addTable = htmlspecialchars(addslashes($_REQUEST['addTable']));
        $addDisp = htmlspecialchars(addslashes($_REQUEST['addDisp']));
        //INSERT
        $bddDoli->query("INSERT INTO llx_cfgdetail (cfgdetail_column, cfgdetail_label, cfgdetail_table, cfgdetail_display) VALUES ('$addColumn', '$addLabel', '$addTable', '$addDisp')");
    }
    else if(isset($_REQUEST['modLabel']) && isset($_REQUEST['modColumn']) && isset($_REQUEST['modTable']) && isset($_REQUEST['modDisp']) && isset($_REQUEST['modId'])
     && !empty($_REQUEST['modLabel']) && !empty($_REQUEST['modColumn']) && !empty($_REQUEST['modTable']) && is_numeric($_REQUEST['modDisp']) && !empty($_REQUEST['modId']))
    {
        $modLabel = htmlspecialchars(addslashes($_REQUEST['modLabel']));
        $modColumn = htmlspecialchars(addslashes($_REQUEST['modColumn']));
        $modTable = htmlspecialchars(addslashes($_REQUEST['modTable']));
        $modDisp = htmlspecialchars(addslashes($_REQUEST['modDisp']));
        $modId = htmlspecialchars(addslashes($_REQUEST['modId']));
        //UPDATE
        $bddDoli->query("UPDATE llx_cfgdetail SET cfgdetail_column = '$modColumn', cfgdetail_label = '$modLabel', cfgdetail_table = '$modTable', cfgdetail_display = '$modDisp' WHERE cfgdetail_rowid = $modId");
    }
    else if(isset($_REQUEST['delId']) && !empty($_REQUEST['delId']))
    {
        $delId = htmlspecialchars(addslashes($_REQUEST['delId']));
        //DELETE
        $bddDoli->query("DELETE FROM llx_cfgdetail WHERE cfgdetail_rowid = $delId");
    }
    
    $detailsCols = $bddDoli->query("SELECT cfgdetail_rowid, cfgdetail_column, cfgdetail_label, cfgdetail_table, cfgdetail_display FROM llx_cfgdetail ORDER BY cfgdetail_table");
    
    $tabCol2disp = '';
    while($detailsCol = $detailsCols->fetch(PDO::FETCH_BOTH))
    {
        $tabCol2disp .= '<tr>';
        $tabCol2disp .= '<form method="post" action="">';
        $tabCol2disp .= '<td><input type="text" name="modLabel" value="'. $detailsCol['cfgdetail_label'] .'" /></td>';
        $tabCol2disp .= '<td><input type="text" name="modColumn" value="'. $detailsCol['cfgdetail_column'] .'" /></td>';
        $tabCol2disp .= '<td><input type="text" name="modTable" value="'. $detailsCol['cfgdetail_table'] .'" /></td>';
        $tabCol2disp .= '<td><input type="text" name="modDisp"  maxlength="1" size="3" value="'. $detailsCol['cfgdetail_display'] .'" /></td>';
        $tabCol2disp .= '<td><input type="hidden" name="modId" value="'. $detailsCol['cfgdetail_rowid'] .'" /><input class="button" type="submit" value="Modifier" /></td>';
        $tabCol2disp .= '</form>';
        $tabCol2disp .= '<td><form method="post" action=""><input type="hidden" name="delId" value="'. $detailsCol['cfgdetail_rowid'] .'" /><input title="Supprimer cette ligne" class="button" type="submit" value="X" /></form></td>';
        $tabCol2disp .= '</tr>';
    }

}
catch (Exception $e)
{
    die('Erreur : ' . $e->getMessage());
}






/*
* View
*/
$page_name = "SmiSyncSetup";
llxHeader('', $langs->trans($page_name));

// Subheader
$linkback = '<a href="' . DOL_URL_ROOT . '/admin/modules.php">Retour à la liste des modules</a>';
print_fiche_titre($langs->trans($page_name), $linkback);




?>

<br />
<div class="titre">
    Lancer le Script pour réparer les deux bases de données
    <a class="button" href="<?php print DOL_URL_ROOT; ?>/smisync/scripts/SmiSync_repair.php" style="text-decoration: none;">Lancer</a>
</div>

<br />
<br />

<div class="titre">
    Identifiants de la base de donnée SMI
</div>
<form method="post" action="">
    <table class="noborder">
        <tr class="liste_titre">
            <th>Champ</th>
            <th>Valeur</th>
        </tr>
        <tr class="impair">
            <td><label for="urlbdd">URL</label></td>
            <td><input id="urlbdd" name="urlbdd" type="text" value="<?php print $url; ?>" /></td>
        </tr>
        <tr class="pair">
            <td><label for="portbdd">port</label></td>
            <td><input id="portbdd" name="portbdd" type="text" value="<?php print $port; ?>" /></td>
        </tr>
        <tr class="impair">
            <td><label for="nombdd">nomBDD</label></td>
            <td><input id="nombdd" name="nombdd" type="text" value="<?php print $nom; ?>" /></td>
        </tr>
        <tr class="pair">
            <td><label for="idbdd">identifiant</label></td>
            <td><input id="idbdd" name="idbdd" type="text" value="<?php print $id; ?>" /></td>
        </tr>
        <tr class="impair">
            <td><label for="mdpbdd">mot de passe</label></td>
            <td><input id="mdpbdd" name="mdpbdd" type="text" value="<?php print $mdp; ?>" /></td>
        </tr>
        <tr class="pair">
            <td colspan="2" align="center"><input class="button" type="submit" value="Modifier" /></td>
        </tr>
    </table>
</form>

<br />
<br />

<div class="titre">
    Suivi d''intervention
</div>
<table class="noborder">
    <tr class="liste_titre">
        <th>Libéllé</th>
        <th>Nom de colonne</th>
        <th>Nom de la table</th>
        <th>Type d''affichage <img border="0" style="cursor: help" title="0 - non affiché, 1 - admin seulement, 2 - admin et utilisateur" alt="help" src="/doli/htdocs/theme/eldy/img/info.png" /></th>
        <th></th>
        <th>Supprimer</th>
    </tr>
    <?php print $tabCol2disp; ?>
    <tr>
        <form method="post" action="">
            <td><input type="text" name="addLabel" /></td>
            <td><input type="text" name="addColumn" /></td>
            <td><input type="text" name="addTable" /></td>
            <td><input type="text" name="addDisp" maxlength="1" size="3" value="1" /></td>
            <td><input class="button" type="submit" value="Ajouter" /></td>
        </form>
        <td></td>
    </tr>
    
</table>



<?php

llxFooter();

$db->close();