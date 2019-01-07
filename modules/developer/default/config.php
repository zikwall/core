<?php echo "<?php\n"; ?>
return [
	'id' => '<?php echo $generator->moduleID; ?>',
	'class' => 'app\modules\<?php echo $generator->moduleID; ?>\<?php echo $generator->getModuleClassName(); ?>',
	'namespace' => 'app\modules\<?php echo $generator->moduleID; ?>',
	'events' => [
		// toDo: generate events callbacks
	],
];
?>

