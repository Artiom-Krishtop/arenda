<?php

if (PHP_SAPI !== 'cli')
{
	die('Must be run from command line');
}

/**
 * ������ �������������
 *
 *    $icomoonImport = new IconFontImport(
 *        'https://i.icomoon.io/public/ba472d2098/test/style.css',
 *        Application::getDocumentRoot().'/test/iconfont/');
 *    $icomoonImport->updateFont();
 */
$icomoonImport = new IconFontImport($argv[1] ?: 'https://i.icomoon.io/public/ba472d2098/arealty3/style.css');
$icomoonImport->updateFont();

class IconFontImport
{
	public $arFonts = array();
	private $link;
	private $path;
	private $styleFileName = 'icomoon.css';

	function __construct($link, $path = __DIR__ . '/')
	{
		if (!$link)
		{
			echo 'Empty $link';
			exit();
		}
		$this->link = $link;
		$this->path = $path;
	}

	// @todo ��������� � css hash � �������� ��������� ��������� ������
	function updateFont($checkHash = true)
	{
		if (!$css_code = file_get_contents($this->link))
		{
			echo 'Empty file ' . $this->link;

			return;
		}

		//��� ������ �� ����� �������
		preg_match_all("/url\('([^']+)/", $css_code, $matchFontLink);

		$css_clear_code = $css_code;
		foreach ($matchFontLink[1] as $link)
		{
			$font = array();
			$font["LINK"] = $link;

			//������� ����� ������ � ������������
			preg_match("/\/([^\/]+)\?[^?]+$/", $link, $matchFileName);
			$font["FILE_NAME_WITH_EXT"] = $matchFileName[1];

			//������� ���������� ���� ������� ��������������
			$css_clear_code = str_replace($font["LINK"], 'fonts/' . $font["FILE_NAME_WITH_EXT"], $css_clear_code);
			$this->arFonts[] = $font;
		}

		//�������� �� ����������� ������
		if ($checkHash &&
			file_exists($this->path . $this->styleFileName) &&
			md5(file_get_contents($this->path . $this->styleFileName)) == md5($css_clear_code))
		{
			echo 'Already updated';

			return;
		}

		//��������� ���� ������
		file_put_contents($this->path . $this->styleFileName, $css_clear_code);

		//��������� ������
		foreach ($this->arFonts as &$font)
		{
			if (!$font["CONTENT"] = file_get_contents($font["LINK"]))
			{
				echo 'ERROR: access denied or file is empty: ' . $font["LINK"];
			}
			file_put_contents($this->path . 'fonts/' . $font["FILE_NAME_WITH_EXT"], $font["CONTENT"]);
		}

		echo "SUCCESS: updated";
	}
}