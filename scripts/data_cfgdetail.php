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


try {
    $dbSmiInfo = db_smi::getInstance($db);
    $dbSmi = $dbSmiInfo->getSmi();
    
    // Remplissage de la table de configuration des items à afficher sur la page de détail d'une intervention
    $db->query("insert into llx_cfgdetail (cfgdetail_label, cfgdetail_column, cfgdetail_table, cfgdetail_display) values ('Code d\'intervention', 'int_code', '".$dbSmiInfo->getTpref()."_int', 2)");
    $db->query("insert into llx_cfgdetail (cfgdetail_label, cfgdetail_column, cfgdetail_table, cfgdetail_display) values ('Date de fin', 'int_datefin', '".$dbSmiInfo->getTpref()."_int', 2)");
    $db->query("insert into llx_cfgdetail (cfgdetail_label, cfgdetail_column, cfgdetail_table, cfgdetail_display) values ('Matériel', 'int_mat', '".$dbSmiInfo->getTpref()."_int', 2)");
    $db->query("insert into llx_cfgdetail (cfgdetail_label, cfgdetail_column, cfgdetail_table, cfgdetail_display) values ('Problème', 'int_pbm', '".$dbSmiInfo->getTpref()."_int', 2)");
    $db->query("insert into llx_cfgdetail (cfgdetail_label, cfgdetail_column, cfgdetail_table, cfgdetail_display) values ('Code client', 'int_codecli', '".$dbSmiInfo->getTpref()."_int', 2)");
    $db->query("insert into llx_cfgdetail (cfgdetail_label, cfgdetail_column, cfgdetail_table, cfgdetail_display) values ('Nom client', 'cli_prenom', '".$dbSmiInfo->getTpref()."_cli', 2)");
    $db->query("insert into llx_cfgdetail (cfgdetail_label, cfgdetail_column, cfgdetail_table, cfgdetail_display) values ('Statut', 'int_codestatut', '".$dbSmiInfo->getTpref()."_int', 2)");

}
catch (Exception $e)
{
    die('Erreur : ' . $e->getMessage());
}

header('Location: ../admin/SmiSync_setuppage.php');

/*
* View
*/
$page_name = "SmiSync_data_configdetail";
llxHeader('', $langs->trans($page_name));

// Subheader
$linkback = '<a href="' . DOL_URL_ROOT . '/smisync/admin/SmiSync_setuppage.php">Retour à la page de configuration de SmiSync</a>';
print_fiche_titre($langs->trans($page_name), $linkback);



?>

<?php

llxFooter();

$db->close();