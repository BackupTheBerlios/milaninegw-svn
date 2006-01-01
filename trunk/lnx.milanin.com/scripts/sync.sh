#!/bin/sh
rsync -vcr --exclude=CVS --exclude=header.inc.php --exclude='*~' --exclude=.svn --exclude='includes.php' --exclude='egw_bridge.php' $1 $2
