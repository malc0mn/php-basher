#!/bin/bash

set -e -v

cd /opt/approot
mkdir -p build-new
rm -f previous
if [ -d current -o -f current -o -L current ]; then mv -f current previous ; fi
ln -s build-new current
