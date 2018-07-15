#!/bin/bash
if [ ! -f vendor/bin/chromedriver ]; then
    echo "Downloading Chromedriver"
    wget -nv https://chromedriver.storage.googleapis.com/2.38/chromedriver_mac64.zip
    echo "Extracting .zip and removing"
    tar -zxvf chromedriver_mac64.zip
    rm chromedriver_mac64.zip
    echo "Moving chromedriver to vendor/bin"
    mv chromedriver vendor/bin/
fi
vendor/bin/chromedriver --url-base=/wd/hub
exit