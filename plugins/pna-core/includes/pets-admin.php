<?php
/**
 * Pets no painel: quadro "Dados do pet", colunas e filtro da lista.
 *
 * @package PNA_Core
 */

defined( 'ABSPATH' ) || exit;

/**
 * Registra o quadro "Dados do pet" na tela de edição.
 */
function pna_core_quadro_dados_pet() {
	add_meta_box( 'pna_dados_pet', __( 'Dados do pet', 'pna' ), 'pna_core_render_quadro_dados_pet', 'pet', 'normal', 'high' );
}
add_action( 'add_meta_boxes_pet', 'pna_core_quadro_dados_pet' );

/**
 * Campo de seleção simples.
 *
 * @param string $nome     Atributo name/id.
 * @param string $rotulo   Rótulo.
 * @param array  $opcoes   valor => texto.
 * @param string $atual    Valor selecionado.
 * @param string $vazio    Texto da opção vazia (vazio = sem opção vazia).
 */
function pna_core_campo_select( $nome, $rotulo, $opcoes, $atual, $vazio = '' ) {
	?>
	<p>
		<label for="<?php echo esc_attr( $nome ); ?>"><?php echo esc_html( $rotulo ); ?></label>
		<select name="<?php echo esc_attr( $nome ); ?>" id="<?php echo esc_attr( $nome ); ?>">
			<?php if ( $vazio ) : ?>
				<option value=""><?php echo esc_html( $vazio ); ?></option>
			<?php endif; ?>
			<?php foreach ( $opcoes as $valor => $texto ) : ?>
				<option value="<?php echo esc_attr( $valor ); ?>" <?php selected( $atual, (string) $valor ); ?>><?php echo esc_html( $texto ); ?></option>
			<?php endforeach; ?>
		</select>
	</p>
	<?php
}

/**
 * Conteúdo do quadro "Dados do pet".
 *
 * @param WP_Post $post Pet.
 */
function pna_core_render_quadro_dados_pet( $post ) {
	wp_nonce_field( 'pna_salvar_pet', 'pna_pet_nonce' );
	?>
	<div class="pna-quadro-campos">
		<?php
		pna_core_campo_select( 'pna_status', __( 'Situação', 'pna' ), pna_core_status_pet(), pna_core_obter_status_pet( $post->ID ) );

		foreach ( pna_core_caracteristicas() as $taxonomia => $dados ) {
			$opcoes = array();
			$termos = get_terms(
				array(
					'taxonomy'   => $taxonomia,
					'hide_empty' => false,
				)
			);
			if ( ! is_wp_error( $termos ) ) {
				foreach ( $termos as $termo ) {
					$opcoes[ $termo->slug ] = $termo->name;
				}
			}
			pna_core_campo_select( 'pna_' . $dados['variavel'], $dados['rotulo'], $opcoes, pna_core_caracteristica_do_pet( $post->ID, $taxonomia ), __( '— Escolha —', 'pna' ) );
		}

		pna_core_campo_select( 'pna_castrado', __( 'Castrado(a)', 'pna' ), pna_core_opcoes_saude(), (string) get_post_meta( $post->ID, '_pna_castrado', true ) );
		pna_core_campo_select( 'pna_vacinado', __( 'Vacinas em dia', 'pna' ), pna_core_opcoes_saude(), (string) get_post_meta( $post->ID, '_pna_vacinado', true ) );

		$niveis = array(
			'0' => __( 'Não informado', 'pna' ),
			'1' => __( '1 – Pouco', 'pna' ),
			'2' => __( '2 – Médio', 'pna' ),
			'3' => __( '3 – Muito', 'pna' ),
		);
		foreach ( pna_core_tracos() as $traco => $rotulo ) {
			pna_core_campo_select( 'pna_' . $traco, $rotulo, $niveis, (string) get_post_meta( $post->ID, '_pna_' . $traco, true ) );
		}
		?>
	</div>
	<p class="description"><?php esc_html_e( 'Use o campo de texto acima para a descrição do pet e o quadro "Foto principal" para a foto que aparece na galeria.', 'pna' ); ?></p>
	<?php
}

/**
 * Salva o quadro "Dados do pet".
 *
 * @param int $post_id ID do pet.
 */
function pna_core_salvar_dados_pet( $post_id ) {
	if ( ! isset( $_POST['pna_pet_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['pna_pet_nonce'] ), 'pna_salvar_pet' ) ) {
		return;
	}
	if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || wp_is_post_revision( $post_id ) || ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	if ( isset( $_POST['pna_status'] ) ) {
		pna_core_definir_status_pet( $post_id, sanitize_key( $_POST['pna_status'] ) );
	}

	foreach ( pna_core_caracteristicas() as $taxonomia => $dados ) {
		$campo = 'pna_' . $dados['variavel'];
		if ( ! isset( $_POST[ $campo ] ) ) {
			continue;
		}
		$slug = sanitize_key( $_POST[ $campo ] );
		wp_set_object_terms( $post_id, $slug ? array( $slug ) : array(), $taxonomia );
	}

	foreach ( array( 'castrado', 'vacinado' ) as $campo ) {
		if ( isset( $_POST[ 'pna_' . $campo ] ) ) {
			$valor = sanitize_key( $_POST[ 'pna_' . $campo ] );
			if ( array_key_exists( $valor, pna_core_opcoes_saude() ) ) {
				update_post_meta( $post_id, '_pna_' . $campo, $valor );
			}
		}
	}

	foreach ( array_keys( pna_core_tracos() ) as $traco ) {
		if ( isset( $_POST[ 'pna_' . $traco ] ) ) {
			$nivel = (string) min( 3, max( 0, absint( $_POST[ 'pna_' . $traco ] ) ) );
			update_post_meta( $post_id, '_pna_' . $traco, $nivel );
		}
	}
}
add_action( 'save_post_pet', 'pna_core_salvar_dados_pet' );

/**
 * Colunas da lista de pets.
 *
 * @param array $colunas Colunas atuais.
 * @return array
 */
function pna_core_colunas_pets( $colunas ) {
	$novas = array();
	foreach ( $colunas as $chave => $rotulo ) {
		if ( 'title' === $chave ) {
			$novas['pna_foto'] = __( 'Foto', 'pna' );
		}
		$novas[ $chave ] = $rotulo;
		if ( 'title' === $chave ) {
			$novas['pna_especie']   = __( 'Espécie', 'pna' );
			$novas['pna_sexo']      = __( 'Sexo', 'pna' );
			$novas['pna_porte']     = __( 'Porte', 'pna' );
			$novas['pna_situacao']  = __( 'Situação', 'pna' );
			$novas['pna_padrinhos'] = __( 'Padrinhos', 'pna' );
		}
	}
	return $novas;
}
add_filter( 'manage_pet_posts_columns', 'pna_core_colunas_pets' );

/**
 * Conteúdo das colunas da lista de pets.
 *
 * @param string $coluna  Coluna.
 * @param int    $post_id ID do pet.
 */
function pna_core_conteudo_colunas_pets( $coluna, $post_id ) {
	switch ( $coluna ) {
		case 'pna_foto':
			echo get_the_post_thumbnail( $post_id, 'thumbnail' ) ?: '—'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			break;
		case 'pna_especie':
		case 'pna_sexo':
		case 'pna_porte':
			$termos = get_the_terms( $post_id, $coluna );
			echo esc_html( ( $termos && ! is_wp_error( $termos ) ) ? $termos[0]->name : '—' );
			break;
		case 'pna_situacao':
			$status = pna_core_obter_status_pet( $post_id );
			echo pna_core_etiqueta_status( $status, pna_core_status_pet()[ $status ] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			break;
		case 'pna_padrinhos':
			echo esc_html( (string) pna_core_contar_padrinhos( $post_id ) );
			break;
	}
}
add_action( 'manage_pet_posts_custom_column', 'pna_core_conteudo_colunas_pets', 10, 2 );

/**
 * Filtro por situação na lista de pets.
 *
 * @param string $post_type Tipo de conteúdo da lista.
 */
function pna_core_filtro_lista_pets( $post_type ) {
	if ( 'pet' !== $post_type ) {
		return;
	}
	$atual = isset( $_GET['pna_situacao'] ) ? sanitize_key( $_GET['pna_situacao'] ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	?>
	<label class="screen-reader-text" for="pna_situacao"><?php esc_html_e( 'Filtrar por situação', 'pna' ); ?></label>
	<select name="pna_situacao" id="pna_situacao">
		<option value=""><?php esc_html_e( 'Todas as situações', 'pna' ); ?></option>
		<?php foreach ( pna_core_status_pet() as $valor => $texto ) : ?>
			<option value="<?php echo esc_attr( $valor ); ?>" <?php selected( $atual, $valor ); ?>><?php echo esc_html( $texto ); ?></option>
		<?php endforeach; ?>
	</select>
	<?php
}
add_action( 'restrict_manage_posts', 'pna_core_filtro_lista_pets' );

/**
 * Aplica o filtro por situação.
 *
 * @param WP_Query $query Consulta.
 */
function pna_core_aplicar_filtro_pets( $query ) {
	global $pagenow;
	if ( 'edit.php' !== $pagenow || ! $query->is_main_query() || 'pet' !== $query->get( 'post_type' ) || empty( $_GET['pna_situacao'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		return;
	}
	$query->set(
		'meta_query',
		array(
			array(
				'key'   => '_pna_status',
				'value' => sanitize_key( $_GET['pna_situacao'] ), // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			),
		)
	);
}
add_action( 'pre_get_posts', 'pna_core_aplicar_filtro_pets' );
