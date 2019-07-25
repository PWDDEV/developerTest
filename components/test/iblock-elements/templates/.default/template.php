<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die(); ?>
<? foreach ($arResult as $item): ?>
	Название элемената: <?=$item['NAME']?>
	<? //здесь выводим другие нужные нам свойства ?>
	<?
	// Вывод пользовательских свойств
	if ($item['PROPERTIES']) {
		echo '<br>Пользовательские свойства<br>';
		foreach ($item['PROPERTIES'] as $prop) {
			echo $prop . '<br>';
		}
	}
	?>
	<hr>

<? endforeach; ?>
