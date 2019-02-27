#!/bin/bash

s=$PWD
basename="${s##*/}"

echo "ACS QAmC setup script";
echo "Please make sure the acs folder belongs to you"
echo "Hint: run"
echo "sudo chown -R <username>:<username> <htdocs_path>/acs"
echo ""
echo "and run this script from the acs folder !"

if [ ! "$basename" = "acs" ] ; then
  echo "Aborting because not in right directory: $PWD";
  exit -1;
fi

mkdir -p REST/logs REST/tmp
chmod -R 777 REST/logs REST/tmp

gcc monarch_9419/print_lbl.c -o monarch_9419/print_lbl