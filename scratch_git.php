<?php
echo "=== GIT STATUS ===\n";
echo shell_exec("git status") . "\n";
echo "=== GIT REMOTES ===\n";
echo shell_exec("git remote -v") . "\n";
