<?php
/**
 * Página "Entrar" (Figma: LOGIN).
 *
 * @package PNA_Core
 */

defined( 'ABSPATH' ) || exit;

$pna_redirect = isset( $_GET['redirect_to'] ) ? esc_url_raw( wp_unslash( $_GET['redirect_to'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
?>
<div <?php echo get_block_wrapper_attributes( array( 'class' => 'pna-conta' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<h1 class="pna-conta__titulo"><?php esc_html_e( 'Login', 'pna' ); ?></h1>

	<?php pna_core_resumo_erros( 'entrar' ); ?>

	<form class="pna-form pna-conta__form" method="post" action="">
		<?php wp_nonce_field( 'pna_entrar', 'pna_nonce' ); ?>
		<input type="hidden" name="pna_acao" value="entrar">
		<input type="hidden" name="redirect_to" value="<?php echo esc_attr( $pna_redirect ); ?>">
		<?php pna_core_campo_armadilha(); ?>

		<?php
		pna_core_campo( 'entrar', 'usuario', __( 'E-mail', 'pna' ), array( 'tipo' => 'text', 'autocomplete' => 'username' ) );
		pna_core_campo( 'entrar', 'senha', __( 'Senha', 'pna' ), array( 'tipo' => 'password', 'autocomplete' => 'current-password' ) );
		?>

		<div class="pna-form__acoes pna-conta__acoes">
			<button type="submit" class="wp-element-button"><?php esc_html_e( 'Entrar', 'pna' ); ?></button>
		</div>

		<p class="pna-conta__links">
			<a href="<?php echo esc_url( wp_lostpassword_url() ); ?>"><?php esc_html_e( 'Esqueci minha senha', 'pna' ); ?></a>
			<?php if ( get_option( 'users_can_register' ) ) : ?>
				· <a href="<?php echo esc_url( wp_registration_url() ); ?>"><?php esc_html_e( 'Ainda não tenho cadastro', 'pna' ); ?></a>
			<?php endif; ?>
		</p>
	</form>
</div>
