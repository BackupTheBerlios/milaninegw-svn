# this scripts helps make the documentation in html ps and pds
# the sed scripts is there to counter a bug in docbook export of Lyx
set -x
db2html --nochunks sitemgr.sgml | sed 's/<\/LISTITEM><\/LISTITEM>/<\/LISTITEM>/g
s/&lcub;/<b>/g
s/&rcub;/<\/b>/g
s/&dollar;/$/g
s/&lsqb;/{/g
s/&rsqb;/}/g' > sitemgr.html
tidy -m sitemgr.html
rm -rf sitemgr
exit
db2dvi sitemgr.sgml
dvips -o sitemgr.ps sitemgr.dvi
ps2pdf sitemgr.ps
