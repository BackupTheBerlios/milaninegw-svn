#!/bin/sh
rsync -cr --exclude=CVS --exclude=header.inc.php --exclude='*~' --exclude=.svn $1 $2
