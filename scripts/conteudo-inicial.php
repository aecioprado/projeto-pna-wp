<?php
/**
 * Cria o conteúdo mínimo para o ambiente local funcionar:
 * páginas do menu, página inicial e de postagens, e cadastro aberto.
 *
 * Uso: npm run conteudo
 * Pode ser executado várias vezes: o que já existe não é duplicado.
 *
 * @package PNA
 */

if ( ! defined( 'WP_CLI' ) ) {
	exit;
}

$pna_paginas = array(
	'inicio'              => 'Página inicial',
	'postagens'           => 'Postagens',
	'duvidas'             => 'Dúvidas',
	'doacoes'             => 'Doações',
	'meu-perfil'          => 'Meu perfil',
	'minhas-notificacoes' => 'Minhas notificações',
);

$pna_ids = array();

foreach ( $pna_paginas as $pna_slug => $pna_titulo ) {
	$pna_existente = get_page_by_path( $pna_slug );

	if ( $pna_existente ) {
		$pna_ids[ $pna_slug ] = $pna_existente->ID;
		WP_CLI::log( "Já existe: {$pna_titulo}" );
		continue;
	}

	$pna_id = wp_insert_post(
		array(
			'post_type'   => 'page',
			'post_status' => 'publish',
			'post_name'   => $pna_slug,
			'post_title'  => $pna_titulo,
		),
		true
	);

	if ( is_wp_error( $pna_id ) ) {
		WP_CLI::warning( "Não foi possível criar {$pna_titulo}: " . $pna_id->get_error_message() );
		continue;
	}

	$pna_ids[ $pna_slug ] = $pna_id;
	WP_CLI::log( "Criada: {$pna_titulo}" );
}

if ( isset( $pna_ids['inicio'], $pna_ids['postagens'] ) ) {
	update_option( 'show_on_front', 'page' );
	update_option( 'page_on_front', $pna_ids['inicio'] );
	update_option( 'page_for_posts', $pna_ids['postagens'] );
}

// Cadastro aberto ao público. O papel "Membro" virá do plugin pna-core;
// até lá, novos usuários entram como Assinante.
update_option( 'users_can_register', 1 );
update_option( 'default_role', 'subscriber' );

WP_CLI::success( 'Conteúdo inicial pronto. O link "Pets" do menu funcionará quando o plugin pna-core existir.' );
