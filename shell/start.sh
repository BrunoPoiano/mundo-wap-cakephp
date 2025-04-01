#!/usr/bin/env bash

./exec.sh stop

docker compose up -d --remove-orphans

if [[ " $@ " =~ " -l " ]]; then
  ./exec.sh logs
fi
