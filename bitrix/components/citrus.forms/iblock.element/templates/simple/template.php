<?php

/** @var \Citrus\Forms\IblockElementComponent $component Текущий вызванный компонент */

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

$this->setFrameMode(true);

\Citrus\Forms\includeFormTemplate($component, 'simple');
