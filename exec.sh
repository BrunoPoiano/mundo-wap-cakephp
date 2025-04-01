#!/usr/bin/env bash

function exec {
  FILE=./shell/"$1".sh

  if test -f "$FILE"; then
    if [ -z "${*:2}" ]; then
        source "$FILE" ""
    else
        source "$FILE" ${*:2}
    fi
  elif [ "$1" = "help" ]; then
    echo -e 'List of available commands:\n'
    echo -e '\033[34mCommand \t |\t Description \t\t\t\t\t\t |\t Options\033[0m'

    declare -a Messages=(
      "build-env \t |\t Copy \".env.*.example\" files to \".env.*\" files \t\t |\t [-rm] \t Remove current \".env.*\" files"
      "db \t\t |\t Execute command inside \"db\" service container \t\t |"
      "install \t |\t Setup project \t\t\t\t\t\t |\t [-rm] \t Remove vendor folder before setup"
      "logs \t\t |\t Watch logs \t\t\t\t\t\t |"
      "start \t\t |\t Start project \t\t\t\t\t\t |\t [-l] \t Watch logs"
      "stop \t\t |\t Stop project \t\t\t\t\t\t |"
      "\n\033[33mAny other command (like composer) will be redirected to run within the \"app\" docker service\033[0m"
    )
    for msg in "${Messages[@]}"; do
      printf '\033[34m%.s─\033[0m' $(seq 1 $(tput cols))
      echo -e "$msg"
    done
  else
    echo -e "Running \"$1\" inside \"app\" docker service\n"
    docker compose run --rm --user $UID:$UID app "$@"
  fi
}

exec "$@" || echo -e '\nIf this is not the expected command, use "./exec.sh help" to list available commands'
