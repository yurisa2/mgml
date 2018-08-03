<?php

$git_reset = shell_exec("git reset --hard");
var_dump($git_reset);

$git_stash = shell_exec("git stash");
var_dump($git_stash);

$git_pull = shell_exec("git pull");
var_dump($git_pull);


 ?>
