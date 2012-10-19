CUR_YEAR=`date +"%Y"`

# set variables
MJ_VERSION="1.2.1-dev"
MJ_DATE=`date +"%B %Y"`
MJ_URL="http://www.mobilejoomla.com"
MJ_DESC="Mobile Joomla!"
MJ_AUTHOR="Mobile Joomla!"
MJ_EMAIL="hello@mobilejoomla.com"
MJ_COPYRIGHT="(C) 2008-$CUR_YEAR Mobile Joomla!"
MJ_LICENSE="GNU/GPL v2 - http://www.gnu.org/licenses/gpl-2.0.html"
MJ_SHORTHEADER="Mobile Joomla! $MJ_VERSION \| mobilejoomla.com/license.html"

# do replacements
for F in `find . -name "*.xml" -o -name "*.xm_" -o -name "*.php" -o -name "*.js" -o -name "*.css" -type f`
do
	# test for ### in file (trick to speed up processing)
	if grep "###" $F >/dev/null 2>&1 ;
	then
		echo "Processing $F ..."
		# assume that gnu sed is installed, so use -i option
		sed -i -e "s|###VERSION###|$MJ_VERSION|g" -e "s|###DATE###|$MJ_DATE|g" -e "s|###URL###|$MJ_URL|g" -e "s|###DESC###|$MJ_DESC|g" -e "s|###AUTHOR###|$MJ_AUTHOR|g" -e "s|###EMAIL###|$MJ_EMAIL|g" -e "s|###COPYRIGHT###|$MJ_COPYRIGHT|g" -e "s|###LICENSE###|$MJ_LICENSE|g" -e "s|###SHORTHEADER###|$MJ_SHORTHEADER|g" $F
	fi
done
