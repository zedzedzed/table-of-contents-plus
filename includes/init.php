<?php

require_once __DIR__ . '/globals.php';
require_once __DIR__ . '/class-toc-plus.php';
require_once __DIR__ . '/class-toc-widget.php';
require_once __DIR__ . '/functions.php';

// do the magic
global $toc_plus;
$toc_plus = new TOC_Plus();
