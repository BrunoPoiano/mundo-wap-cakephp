#!/usr/bin/env bash

docker compose run --rm --user $UID:$UID db "$@"
