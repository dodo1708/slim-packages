#!/bin/bash

. .env

export CONTEXT=${OVERRIDE:-}
export COMPOSE_PROJECT_NAME=slim-packages

function startFunction {
  key="$1"
  case ${key} in
     start)
        startFunction pull && \
        startFunction build  && \
        startFunction up
        return
        ;;
     login)
        docker-compose --project-directory . ${OVERRIDE} exec -u1000 web bash
        return
        ;;
     up)
        docker-compose --project-directory . ${OVERRIDE} up -d --remove-orphans
        return
        ;;
     down)
        docker-compose --project-directory . ${OVERRIDE} down --remove-orphans
        return
        ;;
     *)
        docker-compose --project-directory . ${OVERRIDE} "${@:1}"
        return
        ;;
  esac
  shift
}

startFunction "${@:1}"
        exit $?
