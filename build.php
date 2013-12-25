<?php
if ($argc != 2) {
	echo "必须指定版本号";
	exit;
}

$version=$argv[1];

$dirs = array(".");

$ignores = array(
  'README.md',
  'build.php',
  'install.php',
  'install_template.txt',
);

$files = '';

build($dirs, $files);

$template = file_get_contents("install.txt");

$template = str_replace('/*MINICMS_VERSION*/', $version, $template);
$template = str_replace('/*MINICMS_FILES*/', $files, $template);

file_put_contents("install.php", $template);

function build($dirs, &$files) {
  global $ignores;

	foreach ($dirs as $dir) {
		if (!is_dir($dir)) {
			echo "目录\"$dir\"不存在";
			exit;
		}

		if ($dh = opendir($dir)) {
			$sub_dirs = array();
			while (($item = readdir($dh)) !== false) {
				if ($item[0] == '.')
					continue;

        if ($dir == '.')
          $file = $item;
        else
				  $file = $dir."/".$item;

        if (in_array($file, $ignores))
          continue;

				if (is_dir($file)) {
					$sub_dirs[] = $file;
				} else {
					$files .= "install('$file', '";
					$files .= base64_encode(gzcompress(file_get_contents($file)));
					$files .= "');\n";
				}
			}
			closedir($dh);
			build($sub_dirs, $files);
		} else {
			echo "目录\"$dir\"无法访问";
			exit;
		}
	}
}

