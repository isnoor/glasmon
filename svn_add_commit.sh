#!/bin/bash
#format param ./svn_add_commit.sh <directory> <username> <password>

varDir="/opt/mhnglas/mhnglas/glasmon5_2"
cd  $varDir
if [ "$#" -eq 3 ]
then
	varUser="$1"
	varPass="$2"

	svn cleanup --non-interactive --username $varUser --password $varPass $varDir
	svn update --non-interactive  --username $varUser --password $varPass $varDir
	svn add --force  $varDir
	svn commit -m "commit from ubuntu 16.01"  --username $varUser --password $varPass --no-unlock $varDir --non-interactive
else
	svn cleanup --non-interactive  $varDir
        svn update --non-interactive $varDir
        svn add --force  $varDir
        svn commit -m "commit from ubuntu 16.01"   --no-unlock $varDir --non-interactive

fi
