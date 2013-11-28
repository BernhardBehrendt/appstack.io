<?php
class ConsoleController extends Zend_Controller_Action
{

	private $sConsoleOut;
	private $aBrushes;
	private $aThemes;
	public function init()
	{
		$this->aBrushes = array('JAVASCRIPT'	=>'as3',
								'PHP'			=>'php',
								'CSS'			=>'css',
								'HTML'			=>'xml',
								'XML'			=>'xml');

		$this->aThemes = array(	'DEFAULT'		=>	'Default',
								'DJANGO'		=>	'Django',
								'ECLIPSE'		=>	'Eclipse',
								'EMACS'			=>	'Emacs',
								'FADETOGREY'	=>	'FadeToGrey',
								'MDULTRA'		=>	'MDUltra',
								'MIDNIGHT'		=>	'Midnight',
								'RDARK'			=>	'RDark');

		/* Initialize action controller here */

		$this->sConsoleOut = str_replace(array('[LBR]', '[TAB]'), array("\n", '    '), $_GET['code']);//file_get_contents(APPLICATION_PATH.'/javascript/main.js');
		//print_R($_GET['code']);
	}
	public function indexAction(){

		$this->view->out	=	$this->sConsoleOut;
		$this->view->brush	=	$this->aBrushes[$_GET['brush']];
		$this->view->theme	=	$this->aThemes[$_GET['theme']];
	}
}
?>