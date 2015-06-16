#!/bin/bash

curl -X "PURGE" -H "Content-Type:application/x-javascript" http://www.kortingscode.nl >/dev/null 2>&1
curl -X "PURGE" -H "Content-Type:application/javascript" http://www.kortingscode.nl >/dev/null 2>&1

curl -X "PURGE" -H "Content-Type:application/x-javascript" http://www.flipit.com >/dev/null 2>&1
curl -X "PURGE" -H "Content-Type:application/javascript" http://www.flipit.com >/dev/null 2>&1