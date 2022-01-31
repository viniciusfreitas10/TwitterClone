<?php
namespace App\Controllers;

//os recursos do miniframework
use MF\Controller\Action;
use MF\Model\Container;

class AppController extends Action {

	public function timeline(){

			$this->validaAutenticacao();
			// RECUPERAR OS TWEETS
			$tweet = Container::getModel('tweet');
			
			$tweet->__set('id_usuario', $_SESSION['id']);

			
			$total_registro = 10;
			$pagina = isset($_GET['pagina']) ? $_GET['pagina'] :1;
			$deslocamento = ($pagina - 1) * $total_registro;

			$tweets = $tweet->getPorPagina($total_registro, $deslocamento);
			$total_tweet = $tweet->getTotalRegistro();
			
			$this->view->total_de_paginas = ceil($total_tweet['total'] / $total_registro);
			$this->view->pagina_ativa = $pagina;


			$usuario = Container::getModel('usuario');
			$usuario->__set('id', $_SESSION['id']);
			$this->view->info_usuario = $usuario->getInfoUsuario();
			$this->view->total_tweet = $usuario->getTotalTweets();
			$this->view->total_seguindo = $usuario->getTotalUsuariosSeguindo();
			$this->view->total_seguidores = $usuario->getTotalUsuariosSeguidores();

			$this->view->tweets = $tweets;



			$this->render('timeline');
	}
	public function tweet(){
			$this->validaAutenticacao();
			
			$tweet = Container::getModel('tweet');
			$tweet->__set('tweet', $_POST['tweet']);
			$tweet->__set('id_usuario', $_SESSION['id']);

			$tweet->salvar();
			header('location: /timeline');
	}
	public function validaAutenticacao(){
		session_start();
		if(!isset($_SESSION['id']) || $_SESSION['id'] == '' ||!isset($_SESSION['nome']) || $_SESSION['nome'] == ''  ){
			header('location: /?login=erro');
		}
	}
	public function quemSeguir(){
		$this->validaAutenticacao();
		$pesquisarPor = isset($_GET['pesquisarPor']) ? $_GET['pesquisarPor'] : '';
		$usuarios = array();
		
		if($pesquisarPor != ''){
			$usuario = Container::getModel('usuario');
			$usuario->__set('nome', $pesquisarPor);
			$usuario->__set('id', $_SESSION['id']);
			$usuarios = $usuario->getAll();
		}
		$usuario = Container::getModel('usuario');
			$usuario->__set('id', $_SESSION['id']);
			$this->view->info_usuario = $usuario->getInfoUsuario();
			$this->view->total_tweet = $usuario->getTotalTweets();
			$this->view->total_seguindo = $usuario->getTotalUsuariosSeguindo();
			$this->view->total_seguidores = $usuario->getTotalUsuariosSeguidores();
		$this->view->usuarios = $usuarios;
		$this->render('quemSeguir');
	}
	public function acao(){
		$this->validaAutenticacao();
		$acao = isset($_GET['acao']) ? $_GET['acao'] : '';
		$id_usuario = isset($_GET['id_usuario']) ? $_GET['id_usuario'] : '';
		
		$usuario = Container::getModel('usuario');
		$usuario->__set('id', $_SESSION['id']);

		if($acao == 'seguir'){
			$usuario->seguirUsuario($id_usuario);
		}else if($acao == 'deixar_de_seguir'){
			$usuario->deixarSeguirUsuario($id_usuario);
		}
		header('location: /quem_seguir');
	}
	public function remover(){
		$this->validaAutenticacao();

		$tweet = Container::getModel('tweet');
		$tweet->__set('id_usuario', $_SESSION['id']);
		$tweet->__set('id', $_GET['id']);
		$tweet->remover();

		header('location: /timeline');
	}
}
?>