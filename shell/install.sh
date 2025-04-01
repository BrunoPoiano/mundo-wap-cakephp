#!/usr/bin/env bash

./exec.sh stop

if [[ " $@ " =~ " -rm " ]]; then
  sudo rm -rf ./app/vendor/
fi

docker compose build --force-rm

./exec.sh composer install

if [[ " $@ " =~ " -start " ]]; then
  ./exec.sh start
else
  ./exec.sh stop
fi
