<?php
/**
 * Plugin Name:       PNA Core
 * Plugin URI:        https://github.com/aecioprado/projeto-pna-wp
 * Description:       Regras de negócio do Pets no Agreste: pets, solicitações de adoção e apadrinhamento, e papéis da equipe.
 * Version:           0.3.0
 * Requires at least: 6.6
 * Requires PHP:      8.1
 * Author:            Projeto PNA
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       pna
 *
 * @package PNA_Core
 */

defined( 'ABSPATH' ) || exit;

define( 'PNA_CORE_VERSAO', '0.3.0' );
define( 'PNA_CORE_ARQUIVO', __FILE__ );
define( 'PNA_CORE_DIR', plugin_dir_path( __FILE__ ) );

/*
 * Versão dos papéis e permissões. Ao alterar qualquer papel ou
 * permissão em includes/papeis.php, aumente este número: o plugin
 * reaplica as permissões automaticamente no próximo acesso ao painel.
 */
define( 'PNA_CORE_VERSAO_PAPEIS', 1 );

require_once PNA_CORE_DIR . 'includes/papeis.php';
require_once PNA_CORE_DIR . 'includes/pets.php';
require_once PNA_CORE_DIR . 'includes/solicitacoes.php';
require_once PNA_CORE_DIR . 'includes/campos.php';
require_once PNA_CORE_DIR . 'includes/paginas.php';
require_once PNA_CORE_DIR . 'includes/conta.php';
require_once PNA_CORE_DIR . 'includes/formularios.php';
require_once PNA_CORE_DIR . 'includes/privacidade.php';

if ( is_admin() ) {
	require_once PNA_CORE_DIR . 'includes/admin-comum.php';
	require_once PNA_CORE_DIR . 'includes/pets-admin.php';
	require_once PNA_CORE_DIR . 'includes/solicitacoes-admin.php';
}

/**
 * Ativação: registra tipos de conteúdo, papéis e características
 * padrão, e atualiza os endereços amigáveis.
 */
function pna_core_ativar() {
	pna_core_registrar_pets();
	pna_core_registrar_solicitacoes();
	pna_core_sincronizar_papeis();
	pna_core_criar_caracteristicas_padrao();
	pna_core_criar_paginas();

	// Novos cadastros entram como Membro (se ainda estiver no padrão do WordPress).
	if ( 'subscriber' === get_option( 'default_role' ) ) {
		update_option( 'default_role', 'pna_membro' );
	}

	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'pna_core_ativar' );

/**
 * Desativação: só limpa os endereços amigáveis. Dados e papéis
 * permanecem (ver uninstall.php).
 */
function pna_core_desativar() {
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'pna_core_desativar' );
