<?php
/**
 * Campos de formulário e estado de validação, compartilhados pelos
 * formulários do site (cadastro, login, perfil, adoção, apadrinhamento).
 *
 * A marcação segue o contrato de docs/componentes.md (classes pna-campo,
 * pna-opcao...), que o tema estiliza.
 *
 * @package PNA_Core
 */

defined( 'ABSPATH' ) || exit;

/**
 * Estado dos formulários na requisição atual: erros e valores enviados.
 *
 * @param string     $form  Identificador do formulário.
 * @param array|null $novo  Estado novo (para gravar) ou null (para ler).
 * @return array{erros:array,valores:array}
 */
function pna_core_estado_form( $form, $novo = null ) {
	static $estados = array();
	if ( null !== $novo ) {
		$estados[ $form ] = $novo;
	}
	return $estados[ $form ] ?? array(
		'erros'   => array(),
		'valores' => array(),
	);
}

/**
 * Valor de um campo: o que a pessoa enviou (se houve erro) ou o padrão.
 *
 * @param string $form   Formulário.
 * @param string $campo  Campo.
 * @param string $padrao Valor padrão.
 * @return string
 */
function pna_core_valor( $form, $campo, $padrao = '' ) {
	$estado = pna_core_estado_form( $form );
	return isset( $estado['valores'][ $campo ] ) ? (string) $estado['valores'][ $campo ] : (string) $padrao;
}

/**
 * Imprime um campo com rótulo, ajuda e erro.
 *
 * @param string $form  Formulário.
 * @param string $campo Nome do campo.
 * @param string $rotulo Rótulo visível.
 * @param array  $opcoes {
 *     @type string $tipo        text, email, tel, password, date, number ou textarea.
 *     @type string $valor       Valor padrão.
 *     @type string $placeholder Exemplo.
 *     @type string $ajuda       Texto de ajuda.
 *     @type bool   $somente_leitura Campo travado.
 *     @type bool   $largo       Ocupa as duas colunas.
 *     @type string $autocomplete Valor de autocomplete.
 *     @type int    $max         Máximo de caracteres.
 *     @type bool   $obrigatorio Campo obrigatório (padrão: sim).
 * }
 */
function pna_core_campo( $form, $campo, $rotulo, $opcoes = array() ) {
	$o      = wp_parse_args(
		$opcoes,
		array(
			'tipo'            => 'text',
			'valor'           => '',
			'placeholder'     => '',
			'ajuda'           => '',
			'somente_leitura' => false,
			'largo'           => false,
			'autocomplete'    => '',
			'max'             => 0,
			'obrigatorio'     => true,
		)
	);
	$estado = pna_core_estado_form( $form );
	$erro   = $estado['erros'][ $campo ] ?? '';
	$id     = 'pna-' . $form . '-' . $campo;
	$valor  = 'password' === $o['tipo'] ? '' : pna_core_valor( $form, $campo, $o['valor'] );
	$descr  = array();
	if ( $o['ajuda'] ) {
		$descr[] = $id . '-ajuda';
	}
	if ( $erro ) {
		$descr[] = $id . '-erro';
	}
	$classes = 'pna-campo' . ( $o['largo'] ? ' pna-campo--largo' : '' ) . ( $erro ? ' pna-campo--erro' : '' );
	$atributos = sprintf(
		'class="pna-campo__entrada" id="%1$s" name="%2$s"%3$s%4$s%5$s%6$s%7$s%8$s',
		esc_attr( $id ),
		esc_attr( $campo ),
		$o['placeholder'] ? ' placeholder="' . esc_attr( $o['placeholder'] ) . '"' : '',
		$o['somente_leitura'] ? ' readonly' : '',
		$o['autocomplete'] ? ' autocomplete="' . esc_attr( $o['autocomplete'] ) . '"' : '',
		$o['max'] ? ' maxlength="' . (int) $o['max'] . '"' : '',
		( $descr ? ' aria-describedby="' . esc_attr( implode( ' ', $descr ) ) . '"' : '' ) . ( $erro ? ' aria-invalid="true"' : '' ),
		$o['obrigatorio'] ? ' required' : ''
	);
	?>
	<div class="<?php echo esc_attr( $classes ); ?>">
		<label class="pna-campo__rotulo" for="<?php echo esc_attr( $id ); ?>"><?php echo esc_html( $rotulo ); ?></label>
		<?php if ( 'textarea' === $o['tipo'] ) : ?>
			<textarea <?php echo $atributos; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>><?php echo esc_textarea( $valor ); ?></textarea>
			<?php if ( $o['max'] ) : ?>
				<p class="pna-campo__contador"><?php echo esc_html( sprintf( /* translators: %d: limite de caracteres. */ __( 'Até %d caracteres', 'pna' ), $o['max'] ) ); ?></p>
			<?php endif; ?>
		<?php else : ?>
			<input type="<?php echo esc_attr( $o['tipo'] ); ?>" value="<?php echo esc_attr( $valor ); ?>" <?php echo $atributos; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
		<?php endif; ?>
		<?php if ( $o['ajuda'] ) : ?>
			<p class="pna-campo__ajuda" id="<?php echo esc_attr( $id ); ?>-ajuda"><?php echo esc_html( $o['ajuda'] ); ?></p>
		<?php endif; ?>
		<?php if ( $erro ) : ?>
			<p class="pna-campo__erro" id="<?php echo esc_attr( $id ); ?>-erro"><?php echo esc_html( $erro ); ?></p>
		<?php endif; ?>
	</div>
	<?php
}

/**
 * Checkbox de aceite (termo, privacidade).
 *
 * @param string $form   Formulário.
 * @param string $campo  Campo.
 * @param string $html   Texto do aceite (pode conter link).
 */
function pna_core_aceite( $form, $campo, $html ) {
	$estado = pna_core_estado_form( $form );
	$erro   = $estado['erros'][ $campo ] ?? '';
	$id     = 'pna-' . $form . '-' . $campo;
	?>
	<div class="pna-campo<?php echo $erro ? ' pna-campo--erro' : ''; ?>">
		<label class="pna-opcao" for="<?php echo esc_attr( $id ); ?>">
			<input class="pna-opcao__marcador" type="checkbox" id="<?php echo esc_attr( $id ); ?>" name="<?php echo esc_attr( $campo ); ?>" value="1" required <?php checked( '1', pna_core_valor( $form, $campo ) ); ?><?php echo $erro ? ' aria-invalid="true" aria-describedby="' . esc_attr( $id ) . '-erro"' : ''; ?>>
			<span><?php echo wp_kses( $html, array( 'a' => array( 'href' => array(), 'target' => array() ), 'strong' => array() ) ); ?></span>
		</label>
		<?php if ( $erro ) : ?>
			<p class="pna-campo__erro" id="<?php echo esc_attr( $id ); ?>-erro"><?php echo esc_html( $erro ); ?></p>
		<?php endif; ?>
	</div>
	<?php
}

/**
 * Resumo de erros no topo do formulário (acessível: anunciado ao carregar).
 *
 * @param string $form Formulário.
 */
function pna_core_resumo_erros( $form ) {
	$estado = pna_core_estado_form( $form );
	if ( empty( $estado['erros'] ) ) {
		return;
	}
	?>
	<div class="pna-aviso pna-aviso--erro" role="alert">
		<p><strong><?php esc_html_e( 'Confira os campos abaixo:', 'pna' ); ?></strong></p>
		<ul>
			<?php foreach ( $estado['erros'] as $erro ) : ?>
				<li><?php echo esc_html( $erro ); ?></li>
			<?php endforeach; ?>
		</ul>
	</div>
	<?php
}

/**
 * Campo invisível contra robôs (pessoas não o veem nem o preenchem).
 */
function pna_core_campo_armadilha() {
	echo '<div class="pna-so-leitor" aria-hidden="true"><label>Não preencha<input type="text" name="pna_site" value="" tabindex="-1" autocomplete="off"></label></div>';
}

/**
 * O envio parece de robô (campo armadilha preenchido)?
 *
 * @return bool
 */
function pna_core_envio_de_robo() {
	return ! empty( $_POST['pna_site'] ); // phpcs:ignore WordPress.Security.NonceVerification.Missing
}

/**
 * Lê um campo de texto enviado por POST, já limpo.
 *
 * @param string $campo Nome.
 * @param bool   $multilinha Texto com várias linhas.
 * @return string
 */
function pna_core_post( $campo, $multilinha = false ) {
	if ( ! isset( $_POST[ $campo ] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
		return '';
	}
	$valor = wp_unslash( $_POST[ $campo ] ); // phpcs:ignore WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
	return $multilinha ? sanitize_textarea_field( $valor ) : sanitize_text_field( $valor );
}

/**
 * Aviso de sucesso vindo da URL (?pna_aviso=...).
 *
 * @param array $mensagens chave => texto.
 */
function pna_core_aviso_da_url( $mensagens ) {
	$chave = isset( $_GET['pna_aviso'] ) ? sanitize_key( $_GET['pna_aviso'] ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	if ( $chave && isset( $mensagens[ $chave ] ) ) {
		printf( '<div class="pna-aviso pna-aviso--sucesso" role="status"><p>%s</p></div>', esc_html( $mensagens[ $chave ] ) );
	}
}

/**
 * Apenas os dígitos de um texto (CEP, telefone).
 *
 * @param string $texto Texto.
 * @return string
 */
function pna_core_digitos( $texto ) {
	return preg_replace( '/\D+/', '', (string) $texto );
}
