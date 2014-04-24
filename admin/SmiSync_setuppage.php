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
require_once "../class/db_smi.class.php";
//require_once DOL_DOCUMENT_ROOT."/smisync/class/db_smi.class.php";

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
  try {
    
    //connection bdd smi
    $dbSmi = db_smi::getInstance($db);

    if(isset($_REQUEST['urlbdd']) && isset($_REQUEST['portbdd']) && isset($_REQUEST['nombdd']) && isset($_REQUEST['idbdd']) && isset($_REQUEST['mdpbdd'])
     && !empty($_REQUEST['urlbdd']) && !empty($_REQUEST['nombdd']) && !empty($_REQUEST['idbdd']) && !empty($_REQUEST['mdpbdd']))
    {
        //met a jour les variables    
        $dbSmi->setVar($_REQUEST['urlbdd'], $_REQUEST['portbdd'], $_REQUEST['nombdd'], $_REQUEST['idbdd'], $_REQUEST['mdpbdd']);

        $dbSmi->write();
    }

    $dbSmi->read();
    $dbSmi->connect();

    /*
     * Champ a afficher dans le detail d'une intervention
     */

    if(isset($_REQUEST['addLabel']) && isset($_REQUEST['addColumn']) && isset($_REQUEST['addTable']) && isset($_REQUEST['addDisp'])
     && !empty($_REQUEST['addLabel']) && !empty($_REQUEST['addColumn']) && !empty($_REQUEST['addTable']) && is_numeric($_REQUEST['addDisp']))
    {
        $addLabel = htmlspecialchars(addslashes($_REQUEST['addLabel']));
        $addColumn = htmlspecialchars(addslashes($_REQUEST['addColumn']));
        $addTable = htmlspecialchars(addslashes($_REQUEST['addTable']));
        $addDisp = htmlspecialchars(addslashes($_REQUEST['addDisp']));
        //INSERT
        $db->query("INSERT INTO llx_cfgdetail (cfgdetail_column, cfgdetail_label, cfgdetail_table, cfgdetail_display) VALUES ('$addColumn', '$addLabel', '$addTable', '$addDisp')");
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
        $db->query("UPDATE llx_cfgdetail SET cfgdetail_column = '$modColumn', cfgdetail_label = '$modLabel', cfgdetail_table = '$modTable', cfgdetail_display = '$modDisp' WHERE cfgdetail_rowid = $modId");
    }
    else if(isset($_REQUEST['delId']) && !empty($_REQUEST['delId']))
    {
        $delId = htmlspecialchars(addslashes($_REQUEST['delId']));
        //DELETE
        $db->query("DELETE FROM llx_cfgdetail WHERE cfgdetail_rowid = $delId");
    }
    else if(isset($_REQUEST['delAll']) && $_REQUEST['delAll'] == 'delete all')
    {
        $db->query("DELETE FROM llx_cfgdetail");
    }
    
    $detailsCols = $db->query("SELECT cfgdetail_rowid, cfgdetail_column, cfgdetail_label, cfgdetail_table, cfgdetail_display FROM llx_cfgdetail ORDER BY cfgdetail_table");
    
    $tabCol2disp = '';
    $iParite = 0;
    while($detailsCol = $db->fetch_object($detailsCols))
    {
        $iParite++;
        if($iParite%2)
            $tabCol2disp .= '<tr class="impair">';
        else
            $tabCol2disp .= '<tr class="pair">';
        $tabCol2disp .= '<form method="post" action="">';
        $tabCol2disp .= '<td><input type="text" name="modLabel" value="'. $detailsCol->cfgdetail_label .'" /></td>';
        $tabCol2disp .= '<td><input type="text" name="modColumn" value="'. $detailsCol->cfgdetail_column .'" /></td>';
        $tabCol2disp .= '<td><input type="text" name="modTable" value="'. $detailsCol->cfgdetail_table .'" /></td>';
        $tabCol2disp .= '<td><input type="text" name="modDisp"  maxlength="1" size="3" value="'. $detailsCol->cfgdetail_display .'" /></td>';
        $tabCol2disp .= '<td><input type="hidden" name="modId" value="'. $detailsCol->cfgdetail_rowid .'" /><input class="button" type="submit" value="Modifier" /></td>';
        $tabCol2disp .= '</form>';
        $tabCol2disp .= '<td><form method="post" action=""><input type="hidden" name="delId" value="'. $detailsCol->cfgdetail_rowid .'" /><input title="Supprimer cette ligne" class="button" type="submit" value="X" /></form></td>';
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
    Lancer le script de comparaison des bases de données et met à jour les clients de SMI si une différence est trouvée.
    <a class="button" href="../scripts/SmiSync_repair.php" style="text-decoration: none;">Lancer</a>
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
            <td><input id="urlbdd" name="urlbdd" type="text" value="<?php print $dbSmi->getUrl(); ?>" /></td>
        </tr>
        <tr class="pair">
            <td><label for="portbdd">port</label></td>
            <td><input id="portbdd" name="portbdd" type="text" value="<?php print $dbSmi->getPort(); ?>" /></td>
        </tr>
        <tr class="impair">
            <td><label for="nombdd">nomBDD</label></td>
            <td><input id="nombdd" name="nombdd" type="text" value="<?php print $dbSmi->getName(); ?>" /></td>
        </tr>
        <tr class="pair">
            <td><label for="idbdd">identifiant</label></td>
            <td><input id="idbdd" name="idbdd" type="text" value="<?php print $dbSmi->getId(); ?>" /></td>
        </tr>
        <tr class="impair">
            <td><label for="mdpbdd">mot de passe</label></td>
            <td><input id="mdpbdd" name="mdpbdd" type="password" value="<?php print $dbSmi->getPwd(); ?>" /></td>
        </tr>
        <tr class="pair">
            <td colspan="2" align="center"><input class="button" type="submit" value="Modifier" /></td>
        </tr>
    </table>
</form>

<br />
<br />

<div class="titre">
    Lignes à afficher dans le détail d'une intervention
</div>
<table class="noborder">
    <tr class="liste_titre">
        <th>Libéllé</th>
        <th>Nom de colonne</th>
        <th>Nom de la table</th>
        <th>Type d'affichage <img border="0" style="cursor: help" title="0 - non affiché, 1 - admin seulement, 2 - admin et utilisateur" alt="help" src="<?php print DOL_URL_ROOT; ?>/theme/eldy/img/info.png" /></th>
        <th></th>
        <th>Supprimer</th>
    </tr>
    <?php print $tabCol2disp; ?>
    <tr class="<?php if($iParite%2) print 'pair'; else print 'impair';?>">
        <form method="post" action="">
            <td><input type="text" name="addLabel" /></td>
            <td><input type="text" name="addColumn" /></td>
            <td><input type="text" name="addTable" /></td>
            <td><input type="text" name="addDisp" maxlength="1" size="3" value="1" /></td>
            <td><input class="button" type="submit" value="Ajouter" /></td>
        </form>
        <td><form method="post" action=""><input type="hidden" name="delAll" value="delete all" /><input class="button" type="submit" value="Vider le tableau" /></form></td>
    </tr>
    
</table>
<div class="titre">
    Remplir la table de detail avec des valeurs par defaut
    <a class="button" href="../scripts/data_cfgdetail.php" style="text-decoration: none;">Remplir</a>
</div>


<?php

llxFooter();

$db->close();
