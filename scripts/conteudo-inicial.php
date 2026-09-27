<?php
/**
 * Cria o conteúdo mínimo para o ambiente local funcionar:
 * páginas do menu, página inicial e de postagens, cadastro aberto,
 * postagens de exemplo e a página privada "Guia de estilo".
 *
 * Use apenas no ambiente local: as postagens de exemplo não devem ir para a produção.
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

// Página privada "Guia de estilo" (só administradores veem).
if ( ! get_page_by_path( 'guia-de-estilo' ) ) {
	wp_insert_post(
		array(
			'post_type'    => 'page',
			'post_status'  => 'private',
			'post_name'    => 'guia-de-estilo',
			'post_title'   => 'Guia de estilo',
			'post_content' => '<!-- wp:pattern {"slug":"pna/guia-de-estilo"} /-->',
		)
	);
	WP_CLI::log( 'Criada: Guia de estilo (privada)' );
} else {
	WP_CLI::log( 'Já existe: Guia de estilo' );
}

// Postagens de exemplo (textos do Figma), para ver os cards de postagem.
$pna_postagens = array(
	'quais-alimentos-dar-aos-pets'     => array( 'Quais alimentos dar aos pets?', 'Assim como ocorre com as pessoas, a nutrição é essencial para manter a saúde física e mental de cães e gatos. A alimentação correta impacta positivamente a qualidade de vida.' ),
	'cuidados-com-filhotes-de-cachorro' => array( 'Cuidados com filhotes de cachorro', 'Este post apresenta algumas formas de adaptar o pequeno à sua nova casa, com dicas de cuidado para o tutor quanto à alimentação, higiene e gasto de energia.' ),
	'gravidez-felina'                   => array( 'Gravidez felina', 'A gestação de gato é um assunto que merece muita atenção! Nesse período, a futura mamãe fica mais vulnerável e, por isso, precisamos tomar alguns cuidados.' ),
	'vacinas-obrigatorias'              => array( 'Vacinas obrigatórias para cães e gatos', 'A vacinação em pets protege contra diversas doenças graves e contagiosas, prevenindo riscos que podem comprometer a vida dos animais e até mesmo afetar pessoas.' ),
);

foreach ( $pna_postagens as $pna_slug => $pna_post ) {
	if ( get_page_by_path( $pna_slug, OBJECT, 'post' ) ) {
		WP_CLI::log( "Já existe: {$pna_post[0]}" );
		continue;
	}
	wp_insert_post(
		array(
			'post_type'    => 'post',
			'post_status'  => 'publish',
			'post_name'    => $pna_slug,
			'post_title'   => $pna_post[0],
			'post_excerpt' => $pna_post[1],
			'post_content' => '<!-- wp:paragraph --><p>' . esc_html( $pna_post[1] ) . '</p><!-- /wp:paragraph -->',
		)
	);
	WP_CLI::log( "Criada postagem de exemplo: {$pna_post[0]}" );
}

// Cadastro aberto ao público. O papel "Membro" virá do plugin pna-core;
// até lá, novos usuários entram como Assinante.
update_option( 'users_can_register', 1 );
update_option( 'default_role', 'subscriber' );

WP_CLI::success( 'Conteúdo inicial pronto. O link "Pets" do menu funcionará quando o plugin pna-core existir.' );
