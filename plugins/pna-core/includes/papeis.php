<?php
/**
 * Papéis e permissões do PNA.
 *
 * | Papel          | Quem                   | Pode                                                        |
 * |----------------|------------------------|-------------------------------------------------------------|
 * | Membro         | Público cadastrado     | Apenas a área logada (sem acesso ao painel)                 |
 * | Avaliador PNA  | Voluntários            | Ver solicitações e marcá-las como "Em análise"              |
 * | Gestor PNA     | Coordenação            | Pets, características e todas as decisões das solicitações |
 * | Editor         | Comunicação (WordPress)| Conteúdo do site e cadastro de pets                         |
 * | Administrador  | Responsáveis técnicos  | Tudo                                                        |
 *
 * Ao mudar qualquer coisa aqui, aumente PNA_CORE_VERSAO_PAPEIS em pna-core.php.
 *
 * @package PNA_Core
 */

defined( 'ABSPATH' ) || exit;

/**
 * Permissões sobre pets (tipo de conteúdo "pet").
 *
 * @return string[]
 */
function pna_core_caps_pets() {
	return array(
		'edit_pna_pets',
		'edit_others_pna_pets',
		'edit_published_pna_pets',
		'edit_private_pna_pets',
		'publish_pna_pets',
		'read_private_pna_pets',
		'delete_pna_pets',
		'delete_others_pna_pets',
		'delete_published_pna_pets',
		'delete_private_pna_pets',
	);
}

/**
 * Permissões para ver e editar solicitações (sem decidir).
 *
 * @return string[]
 */
function pna_core_caps_solicitacoes_avaliar() {
	return array(
		'edit_pna_solicitacoes',
		'edit_others_pna_solicitacoes',
		'edit_private_pna_solicitacoes',
		'edit_published_pna_solicitacoes',
		'read_private_pna_solicitacoes',
	);
}

/**
 * Permissões extras de quem decide (aprovar, recusar, concluir) e exclui.
 *
 * @return string[]
 */
function pna_core_caps_solicitacoes_decidir() {
	return array(
		'pna_decidir_solicitacoes',
		'delete_pna_solicitacoes',
		'delete_others_pna_solicitacoes',
		'delete_private_pna_solicitacoes',
		'delete_published_pna_solicitacoes',
	);
}

/**
 * Cria os papéis do PNA e distribui as permissões.
 * Pode ser executada várias vezes sem efeito colateral.
 */
function pna_core_sincronizar_papeis() {
	$papeis = array(
		'pna_membro'    => array(
			'nome' => __( 'Membro', 'pna' ),
			'caps' => array( 'read' ),
		),
		'pna_avaliador' => array(
			'nome' => __( 'Avaliador PNA', 'pna' ),
			'caps' => array_merge( array( 'read' ), pna_core_caps_solicitacoes_avaliar() ),
		),
		'pna_gestor'    => array(
			'nome' => __( 'Gestor PNA', 'pna' ),
			'caps' => array_merge(
				array( 'read', 'upload_files', 'pna_gerenciar_caracteristicas' ),
				pna_core_caps_pets(),
				pna_core_caps_solicitacoes_avaliar(),
				pna_core_caps_solicitacoes_decidir()
			),
		),
	);

	foreach ( $papeis as $slug => $papel ) {
		if ( ! get_role( $slug ) ) {
			add_role( $slug, $papel['nome'], array() );
		}
		$objeto = get_role( $slug );
		foreach ( $papel['caps'] as $cap ) {
			$objeto->add_cap( $cap );
		}
	}

	// Papéis nativos do WordPress.
	$admin = get_role( 'administrator' );
	if ( $admin ) {
		$todas = array_merge(
			array( 'pna_gerenciar_caracteristicas' ),
			pna_core_caps_pets(),
			pna_core_caps_solicitacoes_avaliar(),
			pna_core_caps_solicitacoes_decidir()
		);
		foreach ( $todas as $cap ) {
			$admin->add_cap( $cap );
		}
	}

	$editor = get_role( 'editor' );
	if ( $editor ) {
		foreach ( pna_core_caps_pets() as $cap ) {
			$editor->add_cap( $cap );
		}
	}

	update_option( 'pna_core_versao_papeis', PNA_CORE_VERSAO_PAPEIS );
}

/**
 * Reaplica as permissões quando PNA_CORE_VERSAO_PAPEIS muda
 * (a ativação do plugin não roda de novo numa atualização de código).
 */
function pna_core_verificar_versao_papeis() {
	if ( (int) get_option( 'pna_core_versao_papeis' ) !== PNA_CORE_VERSAO_PAPEIS ) {
		pna_core_sincronizar_papeis();
	}
}
add_action( 'admin_init', 'pna_core_verificar_versao_papeis' );

/**
 * Membros não acessam o painel: são levados à página inicial.
 */
function pna_core_bloquear_painel_membros() {
	// admin-ajax.php e admin-post.php são usados pelos formulários do site.
	$pagina = isset( $GLOBALS['pagenow'] ) ? $GLOBALS['pagenow'] : '';
	if ( wp_doing_ajax() || 'admin-post.php' === $pagina || ! is_user_logged_in() ) {
		return;
	}
	$usuario = wp_get_current_user();
	if ( array( 'pna_membro' ) === array_values( (array) $usuario->roles ) ) {
		wp_safe_redirect( home_url( '/' ) );
		exit;
	}
}
add_action( 'admin_init', 'pna_core_bloquear_painel_membros' );

/**
 * Esconde a barra de administração para Membros.
 *
 * @param bool $mostrar Valor atual.
 * @return bool
 */
function pna_core_barra_admin_membros( $mostrar ) {
	$usuario = wp_get_current_user();
	if ( $usuario->exists() && array( 'pna_membro' ) === array_values( (array) $usuario->roles ) ) {
		return false;
	}
	return $mostrar;
}
add_filter( 'show_admin_bar', 'pna_core_barra_admin_membros' );
