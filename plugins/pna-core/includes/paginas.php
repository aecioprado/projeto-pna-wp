<?php
/**
 * Páginas da área logada: criação, acesso e endereços de login/cadastro.
 *
 * | Página       | Endereço      | Quem acessa     | Bloco                          |
 * |--------------|---------------|-----------------|--------------------------------|
 * | Entrar       | /entrar/      | Visitantes      | pna-core/entrar                |
 * | Cadastro     | /cadastro/    | Visitantes      | pna-core/cadastro              |
 * | Meu perfil   | /meu-perfil/  | Usuários logados| pna-core/perfil                |
 * | Adotar       | /adotar/      | Usuários logados| pna-core/formulario (adocao)   |
 * | Apadrinhar   | /apadrinhar/  | Usuários logados| pna-core/formulario (apadr.)   |
 *
 * @package PNA_Core
 */

defined( 'ABSPATH' ) || exit;

/**
 * Páginas obrigatórias do plugin.
 *
 * @return array slug => [título, conteúdo, exige_login]
 */
function pna_core_paginas() {
	return array(
		'entrar'     => array( __( 'Entrar', 'pna' ), '<!-- wp:pna-core/entrar /-->', false ),
		'cadastro'   => array( __( 'Cadastro', 'pna' ), '<!-- wp:pna-core/cadastro /-->', false ),
		'meu-perfil' => array( __( 'Meu perfil', 'pna' ), '<!-- wp:pna-core/perfil /-->', true ),
		'adotar'     => array( __( 'Adotar', 'pna' ), '<!-- wp:pna-core/formulario {"tipo":"adocao"} /-->', true ),
		'apadrinhar' => array( __( 'Apadrinhar', 'pna' ), '<!-- wp:pna-core/formulario {"tipo":"apadrinhamento"} /-->', true ),
	);
}

/**
 * Cria as páginas que faltarem (e preenche as que existirem vazias).
 * Executada na ativação do plugin e pelo "npm run conteudo".
 */
function pna_core_criar_paginas() {
	foreach ( pna_core_paginas() as $slug => $pagina ) {
		$existente = get_page_by_path( $slug );
		if ( $existente ) {
			if ( '' === trim( $existente->post_content ) ) {
				wp_update_post(
					array(
						'ID'           => $existente->ID,
						'post_content' => $pagina[1],
					)
				);
			}
			update_post_meta( $existente->ID, '_wp_page_template', 'pagina-conta' );
			continue;
		}
		$id = wp_insert_post(
			array(
				'post_type'    => 'page',
				'post_status'  => 'publish',
				'post_name'    => $slug,
				'post_title'   => $pagina[0],
				'post_content' => $pagina[1],
			)
		);
		if ( $id && ! is_wp_error( $id ) ) {
			update_post_meta( $id, '_wp_page_template', 'pagina-conta' );
		}
	}
}

/**
 * Cria as páginas quando o plugin é atualizado sem ser reativado
 * (a ativação não roda de novo numa atualização de código).
 */
function pna_core_verificar_paginas() {
	if ( (int) get_option( 'pna_core_versao_paginas' ) !== 1 ) {
		pna_core_criar_paginas();
		update_option( 'pna_core_versao_paginas', 1 );
	}
}
add_action( 'admin_init', 'pna_core_verificar_paginas' );

/**
 * Endereço de uma página do plugin.
 *
 * @param string $slug Slug (entrar, cadastro, meu-perfil, adotar, apadrinhar).
 * @return string
 */
function pna_core_url_pagina( $slug ) {
	return home_url( '/' . $slug . '/' );
}

/**
 * Controle de acesso: páginas que exigem login levam ao login; Entrar e
 * Cadastro levam o usuário já logado ao perfil.
 */
function pna_core_controlar_acesso() {
	if ( ! is_page() ) {
		return;
	}
	$slug    = get_post_field( 'post_name', get_queried_object_id() );
	$paginas = pna_core_paginas();
	if ( ! isset( $paginas[ $slug ] ) ) {
		return;
	}

	if ( $paginas[ $slug ][2] && ! is_user_logged_in() ) {
		$atual = isset( $_SERVER['REQUEST_URI'] ) ? esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '/';
		wp_safe_redirect( wp_login_url( home_url( $atual ) ) );
		exit;
	}

	if ( in_array( $slug, array( 'entrar', 'cadastro' ), true ) && is_user_logged_in() ) {
		wp_safe_redirect( pna_core_url_pagina( 'meu-perfil' ) );
		exit;
	}
}
add_action( 'template_redirect', 'pna_core_controlar_acesso', 5 );

/**
 * wp_login_url() passa a apontar para a página "Entrar".
 * (O wp-login.php continua funcionando para quem acessar diretamente.)
 *
 * @param string $url      URL padrão.
 * @param string $redirect Para onde voltar depois.
 * @return string
 */
function pna_core_url_login( $url, $redirect ) {
	if ( ! get_page_by_path( 'entrar' ) ) {
		return $url;
	}
	$nova = pna_core_url_pagina( 'entrar' );
	return $redirect ? add_query_arg( 'redirect_to', rawurlencode( $redirect ), $nova ) : $nova;
}
add_filter( 'login_url', 'pna_core_url_login', 10, 2 );

/**
 * wp_registration_url() passa a apontar para a página "Cadastro".
 *
 * @param string $url URL padrão.
 * @return string
 */
function pna_core_url_cadastro( $url ) {
	return get_page_by_path( 'cadastro' ) ? pna_core_url_pagina( 'cadastro' ) : $url;
}
add_filter( 'register_url', 'pna_core_url_cadastro' );

/**
 * Pet indicado na URL dos formulários (?pet_id=ID).
 *
 * Atenção: o parâmetro NÃO pode se chamar "pet". Esse é o nome do tipo de
 * conteúdo, e o WordPress trataria ?pet=17 como "abrir o pet de endereço 17",
 * resultando em página não encontrada.
 *
 * @return int ID do pet ou 0.
 */
function pna_core_pet_da_url() {
	$id = isset( $_GET['pet_id'] ) ? absint( $_GET['pet_id'] ) : 0; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	return ( $id && 'pet' === get_post_type( $id ) && 'publish' === get_post_status( $id ) ) ? $id : 0;
}

/**
 * Breadcrumb dos formulários: Página inicial › Pets para adoção › Rhaenyra › Adotar.
 * Usa o filtro do tema (pna_breadcrumb_itens).
 *
 * @param array $itens Itens atuais.
 * @return array
 */
function pna_core_breadcrumb_formularios( $itens ) {
	if ( ! is_page( array( 'adotar', 'apadrinhar' ) ) ) {
		return $itens;
	}
	$pet = pna_core_pet_da_url();
	if ( ! $pet ) {
		return $itens;
	}
	$ultimo = array_pop( $itens );
	$itens[] = array(
		'rotulo' => get_post_type_object( 'pet' )->labels->name,
		'url'    => get_post_type_archive_link( 'pet' ),
	);
	$itens[] = array(
		'rotulo' => get_the_title( $pet ),
		'url'    => get_permalink( $pet ),
	);
	$itens[] = $ultimo;
	return $itens;
}
add_filter( 'pna_breadcrumb_itens', 'pna_core_breadcrumb_formularios' );

/**
 * Registra os blocos do plugin (pasta blocks/) e a prévia no editor.
 */
function pna_core_registrar_blocos() {
	wp_register_script(
		'pna-core-editor-ssr',
		plugins_url( 'assets/editor-ssr.js', PNA_CORE_ARQUIVO ),
		array( 'wp-blocks', 'wp-element', 'wp-block-editor', 'wp-server-side-render' ),
		PNA_CORE_VERSAO,
		true
	);
	foreach ( glob( PNA_CORE_DIR . 'blocks/*/block.json' ) as $arquivo ) {
		register_block_type( dirname( $arquivo ) );
	}
}
add_action( 'init', 'pna_core_registrar_blocos' );
