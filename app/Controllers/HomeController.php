<?php
require_once '../app/Models/Candidato.php';

class HomeController extends Controller {

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Proteção total: Redireciona obrigatoriamente para a tela de login se não houver sessão ativa
        if (!isset($_SESSION['usuario_admin'])) {
            header('Location: /login');
            exit();
        }
    }

    public function index() {
        $candidatoModel = new Candidato();
        
        $area_atuacao = isset($_GET['area_atuacao']) ? trim($_GET['area_atuacao']) : '';
        $localizacao = isset($_GET['localizacao']) ? trim($_GET['localizacao']) : '';
        
        $pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
        $limite = isset($_GET['limite']) ? (int)$_GET['limite'] : 10;

        if ($pagina < 1) $pagina = 1;
        if (!in_array($limite, [10, 25, 100])) $limite = 10;

        $offset = ($pagina - 1) * $limite;

        $totalGeral = $candidatoModel->contarTotal('', ''); 
        $totalFiltrado = $candidatoModel->contarTotal($area_atuacao, $localizacao); 
        $candidatos = $candidatoModel->obterDadosPaginados($area_atuacao, $localizacao, $limite, $offset);

        $listaAreas = $candidatoModel->obterAreasAtuacao();
        $listaLocalizacoes = $candidatoModel->obterLocalizacoes();

        $totalPaginas = ceil($totalFiltrado / $limite);

        $data = [
            'total_geral' => $totalGeral,
            'total_filtrado' => $totalFiltrado,
            'candidatos' => $candidatos,
            'lista_areas' => $listaAreas,
            'lista_localizacoes' => $listaLocalizacoes,
            'paginacao' => [
                'pagina_atual' => $pagina,
                'limite' => $limite,
                'total_paginas' => $totalPaginas,
                'area_atuacao' => $area_atuacao,
                'localizacao' => $localizacao
            ]
        ];
        
        $this->view('home', $data);
    }
}