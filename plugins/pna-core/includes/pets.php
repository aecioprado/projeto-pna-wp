<?php
/**
 * Pets: tipo de conteúdo, características (taxonomias) e dados (metadados).
 *
 * Características usadas nos filtros da galeria viram taxonomias, o que
 * permite filtrar pela URL: /pets/?especie=gato&porte=pequeno
 *
 * Referência completa: docs/modelo-de-dados.md
 *
 * @package PNA_Core
 */

defined( 'ABSPATH' ) || exit;

/**
 * Características do pet (taxonomias de escolha única) e seus valores padrão.
 *
 * Chave = nome da taxonomia. 'variavel' = nome usado na URL dos filtros.
 *
 * @return array
 */
function pna_core_caracteristicas() {
	return array(
		'pna_especie' => array(
			'variavel' => 'especie',
			'rotulo'   => __( 'Espécie', 'pna' ),
			'plural'   => __( 'Espécies', 'pna' ),
			'termos'   => array(
				'cachorro' => __( 'Cachorro', 'pna' ),
				'gato'     => __( 'Gato', 'pna' ),
			),
		),
		'pna_sexo'    => array(
			'variavel' => 'sexo',
			'rotulo'   => __( 'Sexo', 'pna' ),
			'plural'   => __( 'Sexos', 'pna' ),
			'termos'   => array(
				'macho' => __( 'Macho', 'pna' ),
				'femea' => __( 'Fêmea', 'pna' ),
			),
		),
		'pna_porte'   => array(
			'variavel' => 'porte',
			'rotulo'   => __( 'Porte', 'pna' ),
			'plural'   => __( 'Portes', 'pna' ),
			'termos'   => array(
				'pequeno' => __( 'Pequeno', 'pna' ),
				'medio'   => __( 'Médio', 'pna' ),
				'grande'  => __( 'Grande', 'pna' ),
			),
		),
		'pna_idade'   => array(
			'variavel' => 'idade',
			'rotulo'   => __( 'Idade', 'pna' ),
			'plural'   => __( 'Faixas de idade', 'pna' ),
			'termos'   => array(
				'filhote'      => __( 'Filhote', 'pna' ),
				'jovem-adulto' => __( 'Jovem adulto', 'pna' ),
				'adulto'       => __( 'Adulto', 'pna' ),
				'desconhecida' => __( 'Desconhecida', 'pna' ),
			),
		),
	);
}

/**
 * Situações possíveis de um pet.
 *
 * @return array
 */
function pna_core_status_pet() {
	return array(
		'disponivel'  => __( 'Disponível', 'pna' ),
		'em_processo' => __( 'Em processo de adoção', 'pna' ),
		'adotado'     => __( 'Adotado', 'pna' ),
	);
}

/**
 * Opções de saúde (castração e vacinas).
 *
 * @return array
 */
function pna_core_opcoes_saude() {
	return array(
		'sem_info' => __( 'Sem informação', 'pna' ),
		'sim'      => __( 'Sim', 'pna' ),
		'nao'      => __( 'Não', 'pna' ),
	);
}

/**
 * Traços de personalidade (nível de 0 a 3, como os ícones do Figma).
 *
 * @return array
 */
function pna_core_tracos() {
	return array(
		'sociavel'   => __( 'Sociável', 'pna' ),
		'brincalhao' => __( 'Brincalhão', 'pna' ),
		'carinhoso'  => __( 'Carinhoso', 'pna' ),
	);
}

/**
 * Registra o tipo de conteúdo "pet", as características e os metadados.
 */
function pna_core_registrar_pets() {
	register_post_type(
		'pet',
		array(
			'labels'          => array(
				'name'               => __( 'Pets para adoção', 'pna' ),
				'singular_name'      => __( 'Pet', 'pna' ),
				'menu_name'          => __( 'Pets', 'pna' ),
				'all_items'          => __( 'Todos os pets', 'pna' ),
				'add_new'            => __( 'Adicionar pet', 'pna' ),
				'add_new_item'       => __( 'Adicionar pet', 'pna' ),
				'edit_item'          => __( 'Editar pet', 'pna' ),
				'new_item'           => __( 'Novo pet', 'pna' ),
				'view_item'          => __( 'Ver pet', 'pna' ),
				'search_items'       => __( 'Buscar pets', 'pna' ),
				'not_found'          => __( 'Nenhum pet encontrado.', 'pna' ),
				'not_found_in_trash' => __( 'Nenhum pet na lixeira.', 'pna' ),
				'featured_image'     => __( 'Foto principal', 'pna' ),
				'set_featured_image' => __( 'Definir foto principal', 'pna' ),
			),
			'public'          => true,
			'has_archive'     => 'pets',
			'rewrite'         => array(
				'slug'       => 'pets',
				'with_front' => false,
			),
			'menu_icon'       => 'dashicons-pets',
			'menu_position'   => 5,
			'supports'        => array( 'title', 'editor', 'thumbnail', 'revisions' ),
			'show_in_rest'    => true,
			'capability_type' => array( 'pna_pet', 'pna_pets' ),
			'map_meta_cap'    => true,
		)
	);

	foreach ( pna_core_caracteristicas() as $taxonomia => $dados ) {
		register_taxonomy(
			$taxonomia,
			'pet',
			array(
				'labels'            => array(
					'name'          => $dados['plural'],
					'singular_name' => $dados['rotulo'],
					'menu_name'     => $dados['plural'],
				),
				'public'            => true,
				'publicly_queryable' => true,
				'hierarchical'      => false,
				'rewrite'           => false,
				'query_var'         => $dados['variavel'],
				'show_ui'           => true,
				'show_in_rest'      => true,
				'show_admin_column' => false,
				'meta_box_cb'       => false, // A escolha é feita no quadro "Dados do pet".
				'capabilities'      => array(
					'manage_terms' => 'pna_gerenciar_caracteristicas',
					'edit_terms'   => 'pna_gerenciar_caracteristicas',
					'delete_terms' => 'pna_gerenciar_caracteristicas',
					'assign_terms' => 'edit_pna_pets',
				),
			)
		);
	}

	$metas = array(
		'_pna_status'   => 'disponivel',
		'_pna_castrado' => 'sem_info',
		'_pna_vacinado' => 'sem_info',
	);
	foreach ( array_keys( pna_core_tracos() ) as $traco ) {
		$metas[ '_pna_' . $traco ] = '0';
	}

	foreach ( $metas as $chave => $padrao ) {
		register_post_meta(
			'pet',
			$chave,
			array(
				'type'              => 'string',
				'single'            => true,
				'default'           => $padrao,
				'show_in_rest'      => true,
				'sanitize_callback' => 'sanitize_key',
				'auth_callback'     => static function ( $permitido, $meta_key, $post_id ) {
					return current_user_can( 'edit_post', $post_id );
				},
			)
		);
	}
}
add_action( 'init', 'pna_core_registrar_pets' );

/**
 * Cria os valores padrão das características (se ainda não existirem).
 */
function pna_core_criar_caracteristicas_padrao() {
	foreach ( pna_core_caracteristicas() as $taxonomia => $dados ) {
		foreach ( $dados['termos'] as $slug => $nome ) {
			if ( ! term_exists( $slug, $taxonomia ) ) {
				wp_insert_term( $nome, $taxonomia, array( 'slug' => $slug ) );
			}
		}
	}
}

/**
 * Cadastro de pets com formulário próprio, sem o editor de blocos
 * (ver docs/decisoes/0003-formulario-proprio-para-pets.md).
 *
 * @param bool   $usar      Usar o editor de blocos.
 * @param string $post_type Tipo de conteúdo.
 * @return bool
 */
function pna_core_pets_sem_editor_de_blocos( $usar, $post_type ) {
	return 'pet' === $post_type ? false : $usar;
}
add_filter( 'use_block_editor_for_post_type', 'pna_core_pets_sem_editor_de_blocos', 10, 2 );

/**
 * Situação de um pet.
 *
 * @param int $pet_id ID do pet.
 * @return string
 */
function pna_core_obter_status_pet( $pet_id ) {
	$status = get_post_meta( $pet_id, '_pna_status', true );
	return array_key_exists( $status, pna_core_status_pet() ) ? $status : 'disponivel';
}

/**
 * Altera a situação de um pet.
 *
 * @param int    $pet_id ID do pet.
 * @param string $status Nova situação (chave de pna_core_status_pet()).
 */
function pna_core_definir_status_pet( $pet_id, $status ) {
	if ( ! array_key_exists( $status, pna_core_status_pet() ) ) {
		return;
	}
	$anterior = pna_core_obter_status_pet( $pet_id );
	update_post_meta( $pet_id, '_pna_status', $status );

	if ( $anterior !== $status ) {
		/**
		 * Disparado quando a situação de um pet muda.
		 *
		 * @param int    $pet_id   ID do pet.
		 * @param string $status   Nova situação.
		 * @param string $anterior Situação anterior.
		 */
		do_action( 'pna_core_pet_status_alterado', $pet_id, $status, $anterior );
	}
}

/**
 * Valor (slug) de uma característica do pet, ex.: 'gato', 'femea'.
 *
 * @param int    $pet_id    ID do pet.
 * @param string $taxonomia Nome da taxonomia (ex.: 'pna_sexo').
 * @return string Slug ou string vazia.
 */
function pna_core_caracteristica_do_pet( $pet_id, $taxonomia ) {
	$termos = get_the_terms( $pet_id, $taxonomia );
	return ( $termos && ! is_wp_error( $termos ) ) ? $termos[0]->slug : '';
}

/**
 * A galeria (/pets/) mostra só pets disponíveis ou em processo.
 * Pets adotados continuam acessíveis pelo endereço direto.
 *
 * @param WP_Query $query Consulta.
 */
function pna_core_galeria_sem_adotados( $query ) {
	if ( is_admin() || ! $query->is_main_query() || ! $query->is_post_type_archive( 'pet' ) ) {
		return;
	}
	$meta_query   = (array) $query->get( 'meta_query' );
	$meta_query[] = array(
		'relation' => 'OR',
		array(
			'key'     => '_pna_status',
			'value'   => 'adotado',
			'compare' => '!=',
		),
		array(
			'key'     => '_pna_status',
			'compare' => 'NOT EXISTS',
		),
	);
	$query->set( 'meta_query', $meta_query );
}
add_action( 'pre_get_posts', 'pna_core_galeria_sem_adotados' );
