#!/bin/sh
/usr/bin/perl go.pl > config.xml.new
mv config.xml config.xml.bak
mv config.xml.new config.xml

cordova build android
cat config.xml | grep ACS2
