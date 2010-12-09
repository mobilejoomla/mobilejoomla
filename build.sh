CUR_YEAR=`date +"%Y"`

# set variables
MJ_VERSION="0.9.10-dev"
MJ_DATE=`date +"%B %Y"`
MJ_URL="http://www.mobilejoomla.com"
MJ_DESC="Mobile Joomla!"
MJ_AUTHOR="Mobile Joomla!"
MJ_EMAIL="hello@mobilejoomla.com"
MJ_COPYRIGTH="(C) 2008-$CUR_YEAR MobileJoomla!"
MJ_LICENSE="http://www.gnu.org/licenses/gpl-2.0.htm GNU/GPL"

# do replacements
for F in `find . -name "*.xml" -o -name "*.xm_" -o -name "*.php" -type f`
do
	# test for ### in file (trick to speed up processing)
	if grep "###" $F >/dev/null 2>&1 ;
	then
		echo "Processing $F ..."
		# assume that gnu sed is installed, so use -i option
		sed -i "s|###VERSION###|$MJ_VERSION|g" $F
		sed -i "s|###DATE###|$MJ_DATE|g" $F
		sed -i "s|###URL###|$MJ_URL|g" $F
		sed -i "s|###DESC###|$MJ_DESC|g" $F
		sed -i "s|###AUTHOR###|$MJ_AUTHOR|g" $F
		sed -i "s|###EMAIL###|$MJ_EMAIL|g" $F
		sed -i "s|###COPYRIGHT###|$MJ_COPYRIGTH|g" $F
		sed -i "s|###LICENSE###|$MJ_LICENSE|g" $F
	fi
done
