<?php
/**
 * Formulários de adoção e de apadrinhamento (/adotar/?pet_id=ID e
 * /apadrinhar/?pet_id=ID). Campos conforme o Figma e a página Dúvidas.
 *
 * @package PNA_Core
 */

defined( 'ABSPATH' ) || exit;

/**
 * Valores mensais sugeridos no apadrinhamento (Figma).
 *
 * @return array
 */
function pna_core_valores_apadrinhamento() {
	return array(
		'25'  => 'R$ 25',
		'50'  => 'R$ 50',
		'100' => 'R$ 100',
	);
}

/**
 * Formas de pagamento do apadrinhamento.
 *
 * @return array
 */
function pna_core_formas_pagamento() {
	return array(
		'pix'    => __( 'Pix', 'pna' ),
		'cartao' => __( 'Cartão de crédito ou débito', 'pna' ),
	);
}

/**
 * Motivo pelo qual o usuário não pode enviar este formulário para este pet.
 *
 * @param string $tipo    'adocao' ou 'apadrinhamento'.
 * @param int    $pet_id  ID do pet.
 * @param int    $user_id ID do usuário.
 * @return string Mensagem (vazia = pode enviar).
 */
function pna_core_impedimento_formulario( $tipo, $pet_id, $user_id ) {
	if ( ! $pet_id ) {
		return __( 'Escolha um pet na página de pets para adoção.', 'pna' );
	}
	$status = pna_core_obter_status_pet( $pet_id );
	if ( 'adotado' === $status ) {
		return __( 'Este pet já foi adotado.', 'pna' );
	}
	if ( 'adocao' === $tipo && 'em_processo' === $status ) {
		return __( 'Este pet já está em processo de adoção. Você ainda pode apadrinhá-lo.', 'pna' );
	}

	$ativas = get_posts(
		array(
			'post_type'      => 'pna_solicitacao',
			'post_status'    => 'any',
			'author'         => $user_id,
			'posts_per_page' => 1,
			'fields'         => 'ids',
			'meta_query'     => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
				array(
					'key'   => '_pna_pet_id',
					'value' => $pet_id,
				),
				array(
					'key'   => '_pna_tipo',
					'value' => $tipo,
				),
				array(
					'key'     => '_pna_status',
					'value'   => array( 'enviada', 'em_analise', 'aprovada' ),
					'compare' => 'IN',
				),
			),
		)
	);
	if ( $ativas ) {
		return 'adocao' === $tipo
			? __( 'Você já tem um pedido de adoção deste pet em andamento. Acompanhe em Meu perfil.', 'pna' )
			: __( 'Você já apadrinha este pet (ou tem um pedido em andamento). Acompanhe em Meu perfil.', 'pna' );
	}
	return '';
}

/**
 * Valida e envia um formulário de adoção ou apadrinhamento.
 *
 * @param string $tipo 'adocao' ou 'apadrinhamento'.
 */
function pna_core_tratar_formulario_solicitacao( $tipo ) {
	if ( ! is_user_logged_in() || pna_core_envio_de_robo() ) {
		return;
	}
	$user_id = get_current_user_id();
	$usuario = wp_get_current_user();
	$pet_id  = isset( $_POST['pna_pet'] ) ? absint( $_POST['pna_pet'] ) : 0; // phpcs:ignore WordPress.Security.NonceVerification.Missing
	$pet_id  = 'pet' === get_post_type( $pet_id ) ? $pet_id : 0;

	$impedimento = pna_core_impedimento_formulario( $tipo, $pet_id, $user_id );
	if ( $impedimento ) {
		pna_core_estado_form( $tipo, array( 'erros' => array( 'geral' => $impedimento ), 'valores' => array() ) );
		return;
	}

	$v = array(
		'nome'        => pna_core_post( 'nome' ),
		'telefone'    => pna_core_post( 'telefone' ),
		'cep'         => pna_core_post( 'cep' ),
		'compromisso' => pna_core_post( 'compromisso' ),
		'privacidade' => pna_core_post( 'privacidade' ),
	);
	$erros = array();

	if ( mb_strlen( $v['nome'] ) < 3 ) {
		$erros['nome'] = __( 'Informe seu nome completo.', 'pna' );
	}
	if ( strlen( pna_core_digitos( $v['telefone'] ) ) < 10 ) {
		$erros['telefone'] = __( 'Informe o telefone com DDD.', 'pna' );
	}
	if ( 8 !== strlen( pna_core_digitos( $v['cep'] ) ) ) {
		$erros['cep'] = __( 'Informe um CEP com 8 números.', 'pna' );
	}

	if ( 'adocao' === $tipo ) {
		$v['nascimento'] = pna_core_post( 'nascimento' );
		$v['ocupacao']   = pna_core_post( 'ocupacao' );
		$v['animais']    = pna_core_post( 'animais' );
		$v['motivo']     = pna_core_post( 'motivo', true );

		if ( ! pna_core_data_valida( $v['nascimento'] ) ) {
			$erros['nascimento'] = __( 'Informe uma data de nascimento válida.', 'pna' );
		} elseif ( pna_core_idade( $v['nascimento'] ) < 18 ) {
			$erros['nascimento'] = __( 'A adoção é permitida apenas para maiores de 18 anos.', 'pna' );
		}
		if ( mb_strlen( $v['ocupacao'] ) < 2 ) {
			$erros['ocupacao'] = __( 'Informe sua ocupação.', 'pna' );
		}
		if ( '' === $v['animais'] ) {
			$erros['animais'] = __( 'Conte quantos animais você já tem (pode ser "nenhum").', 'pna' );
		}
		if ( mb_strlen( $v['motivo'] ) < 20 ) {
			$erros['motivo'] = __( 'Conte um pouco mais sobre por que decidiu adotar (pelo menos 20 caracteres).', 'pna' );
		} elseif ( mb_strlen( $v['motivo'] ) > 1000 ) {
			$erros['motivo'] = __( 'O texto pode ter até 1000 caracteres.', 'pna' );
		}
	} else {
		$v['valor']       = pna_core_post( 'valor' );
		$v['outro_valor'] = pna_core_post( 'outro_valor' );
		$v['pagamento']   = pna_core_post( 'pagamento' );

		if ( 'outro' === $v['valor'] ) {
			$numero = (float) str_replace( ',', '.', pna_core_digitos_e_virgula( $v['outro_valor'] ) );
			if ( $numero < 10 ) {
				$erros['outro_valor'] = __( 'Informe um valor mensal de pelo menos R$ 10.', 'pna' );
			}
		} elseif ( ! array_key_exists( $v['valor'], pna_core_valores_apadrinhamento() ) ) {
			$erros['valor'] = __( 'Escolha um valor mensal.', 'pna' );
		}
		if ( ! array_key_exists( $v['pagamento'], pna_core_formas_pagamento() ) ) {
			$erros['pagamento'] = __( 'Escolha a forma de pagamento.', 'pna' );
		}
	}

	if ( '1' !== $v['compromisso'] ) {
		$erros['compromisso'] = __( 'É preciso confirmar o compromisso.', 'pna' );
	}
	if ( '1' !== $v['privacidade'] ) {
		$erros['privacidade'] = __( 'É preciso concordar com a Política de Privacidade.', 'pna' );
	}

	if ( $erros ) {
		pna_core_estado_form( $tipo, array( 'erros' => $erros, 'valores' => $v ) );
		return;
	}

	// Respostas guardadas na solicitação (nomes em docs/modelo-de-dados.md).
	$dados = array(
		'nome'     => $v['nome'],
		'email'    => $usuario->user_email,
		'telefone' => $v['telefone'],
		'cep'      => pna_core_digitos( $v['cep'] ),
	);
	if ( 'adocao' === $tipo ) {
		$dados['nascimento'] = mysql2date( 'd/m/Y', $v['nascimento'] );
		$dados['ocupacao']   = $v['ocupacao'];
		$dados['animais']    = $v['animais'];
		$dados['motivo']     = $v['motivo'];
	} else {
		$dados['valor_mensal']    = 'outro' === $v['valor'] ? 'R$ ' . pna_core_digitos_e_virgula( $v['outro_valor'] ) : pna_core_valores_apadrinhamento()[ $v['valor'] ];
		$dados['forma_pagamento'] = pna_core_formas_pagamento()[ $v['pagamento'] ];
	}

	$id = pna_core_criar_solicitacao(
		array(
			'tipo'    => $tipo,
			'pet_id'  => $pet_id,
			'user_id' => $user_id,
			'dados'   => $dados,
		)
	);

	if ( is_wp_error( $id ) ) {
		pna_core_estado_form( $tipo, array( 'erros' => array( 'geral' => $id->get_error_message() ), 'valores' => $v ) );
		return;
	}

	// Guarda os dados no perfil para preencher os próximos formulários.
	update_user_meta( $user_id, 'pna_telefone', $v['telefone'] );
	update_user_meta( $user_id, 'pna_cep', pna_core_digitos( $v['cep'] ) );
	if ( 'adocao' === $tipo ) {
		update_user_meta( $user_id, 'pna_nascimento', $v['nascimento'] );
		update_user_meta( $user_id, 'pna_ocupacao', $v['ocupacao'] );
	}

	wp_safe_redirect( add_query_arg( 'pna_aviso', 'enviada-' . $tipo, pna_core_url_pagina( 'meu-perfil' ) ) . '#minhas-solicitacoes' );
	exit;
}

/**
 * Mantém só dígitos e vírgula (valores em reais digitados pela pessoa).
 *
 * @param string $texto Texto.
 * @return string
 */
function pna_core_digitos_e_virgula( $texto ) {
	return preg_replace( '/[^\d,]+/', '', (string) $texto );
}
