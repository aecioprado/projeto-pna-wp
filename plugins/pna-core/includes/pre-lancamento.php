<?php
/**
 * Modo pré-lançamento (ver docs/decisoes/0007-sem-ambiente-de-testes.md).
 *
 * Enquanto ligado, visitantes não logados veem apenas uma página
 * "Em breve". A equipe entra pela página /entrar/ e vê o site completo
 * para testar. É ligado automaticamente quando o plugin é ativado pela
 * primeira vez e desligado em Ferramentas → PNA: configuração inicial.
 *
 * @package PNA_Core
 */

defined( 'ABSPATH' ) || exit;

/**
 * O modo pré-lançamento está ligado?
 *
 * @return bool
 */
function pna_core_pre_lancamento_ativo() {
	return '1' === (string) get_option( 'pna_core_pre_lancamento', '0' );
}

/**
 * Mostra a página "Em breve" a visitantes não logados.
 */
function pna_core_pagina_em_breve() {
	if ( ! pna_core_pre_lancamento_ativo() || is_user_logged_in() ) {
		return;
	}
	// A página Entrar continua acessível para a equipe.
	if ( is_page( 'entrar' ) ) {
		return;
	}

	status_header( 503 );
	header( 'Retry-After: 86400' );
	nocache_headers();
	$nome = get_bloginfo( 'name' );
	?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="robots" content="noindex, nofollow">
	<title><?php echo esc_html( sprintf( /* translators: %s: nome do site. */ __( '%s – Em breve', 'pna' ), $nome ) ); ?></title>
	<style>
		body { margin: 0; min-height: 100vh; display: flex; align-items: center; justify-content: center; background: #FFFFF1; color: #666; font-family: Poppins, system-ui, sans-serif; text-align: center; }
		main { max-width: 560px; padding: 40px; border: 4px solid #35B08A; border-radius: 20px; }
		h1 { margin: 0 0 12px; color: #1F7A5E; font-size: 40px; }
		p { font-size: 18px; line-height: 1.5; }
		a { color: #1F7A5E; font-weight: 700; }
	</style>
</head>
<body>
	<main>
		<h1><?php esc_html_e( 'Em breve', 'pna' ); ?></h1>
		<p><?php echo esc_html( sprintf( /* translators: %s: nome do site. */ __( 'O site do %s está sendo preparado com muito carinho. Volte em breve!', 'pna' ), $nome ) ); ?></p>
		<p><a href="<?php echo esc_url( wp_login_url() ); ?>"><?php esc_html_e( 'Entrar (equipe do PNA)', 'pna' ); ?></a></p>
	</main>
</body>
</html>
	<?php
	exit;
}
add_action( 'template_redirect', 'pna_core_pagina_em_breve', 1 );

/**
 * Aviso na barra de administração, para a equipe lembrar que o site
 * está fechado ao público.
 *
 * @param WP_Admin_Bar $barra Barra de administração.
 */
function pna_core_aviso_barra_pre_lancamento( $barra ) {
	if ( ! pna_core_pre_lancamento_ativo() || ! current_user_can( 'edit_posts' ) ) {
		return;
	}
	$barra->add_node(
		array(
			'id'    => 'pna-pre-lancamento',
			'title' => '🔒 ' . __( 'Site fechado ao público', 'pna' ),
			'href'  => current_user_can( 'manage_options' ) ? admin_url( 'tools.php?page=pna-configuracao' ) : false,
		)
	);
}
add_action( 'admin_bar_menu', 'pna_core_aviso_barra_pre_lancamento', 100 );
