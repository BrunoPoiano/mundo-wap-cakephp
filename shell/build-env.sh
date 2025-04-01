#!/usr/bin/env bash

if [[ " $@ " =~ " -rm " ]]; then
  rm -rf ./.env.app ./.env.db
fi

if [ ! -s "./.env.app" ]; then
    cp ./.env.app.example ./.env.app
fi

if [ ! -s "./.env.db" ]; then
    cp ./.env.db.example ./.env.db
fi
