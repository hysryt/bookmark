<?php

spl_autoload_register(function(string $class) {
	$targetNamespaces = array(
		'Hysryt\\Bookmark\\' => '.',
		'Psr\\' => '../vendor/Psr',
	);

	foreach ($targetNamespaces as $namespace => $dir) {
		if (!(strpos($class, $namespace) === 0)) {
			continue;
		}

		// 念のためパスの区切りをDIRECTORY_SEPARATORに変換
		$dir = str_replace('/', DIRECTORY_SEPARATOR, $dir);
	
		$includePath = __DIR__ . DIRECTORY_SEPARATOR . $dir;
		$classPath = substr($class, strlen($namespace));
		$filePath = str_replace('\\', DIRECTORY_SEPARATOR, $classPath) . '.php';
		$fileFullPath = $includePath . DIRECTORY_SEPARATOR . $filePath;
		
		if (is_file($fileFullPath) && is_readable($fileFullPath)) {
			require_once($fileFullPath);
			return;
		}
	}
});