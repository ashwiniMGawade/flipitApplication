#!/usr/bin/env bash

cp qualitycontrol/pre-commit qualitycontrol/commit-msg ../.git/modules/flipit_application/hooks
chmod +x ../.git/modules/flipit_application/hooks/pre-commit ../.git/modules/flipit_application/hooks/commit-msg