#!/bin/sh

cp qualitycontrol/pre-commit ../../.git/modules/flipit_application/hooks
cp qualitycontrol/commit-msg ../../.git/modules/flipit_application/hooks
chmod +x ../../.git/modules/flipit_application/hooks/pre-commit
chmod +x ../../.git/modules/flipit_application/hooks/commit-msg