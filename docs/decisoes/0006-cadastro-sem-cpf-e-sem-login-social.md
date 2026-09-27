# 0006 – Cadastro sem CPF e sem login social na primeira versão

- **Status:** aceita (a confirmar com a coordenação do PNA)
- **Data:** 27/09/2026

## Contexto

A tela de Cadastro do Figma pede e-mail, nome, CEP, **CPF**, senha e confirmação. A tela de Login mostra botões de entrar com **Google** e **Facebook**.

## Decisão

1. **Sem CPF.** Pela LGPD, só se coleta dado pessoal com finalidade clara. O CPF não é usado em nenhuma etapa do processo: a conferência na entrega do pet é feita presencialmente, com documento. Um dado a menos é um dado a menos para proteger em caso de vazamento.
2. **Sem login social.** Cada provedor exige um aplicativo cadastrado em nome do projeto; o do Facebook passa por revisão da Meta e costuma quebrar com mudanças de política, o que é arriscado para um site que não terá desenvolvedor dedicado.

## Implicações

- O cadastro pede: nome, e-mail, CEP, senha e confirmação, mais o aceite do Termo de Compromisso e da Política de Privacidade.
- O login é feito com e-mail e senha.

## Como mudar no futuro

- **Voltar a pedir CPF:** acrescentar o campo em `plugins/pna-core/blocks/cadastro/render.php` e a validação e gravação em `pna_core_tratar_cadastro()` (`includes/conta.php`), incluí-lo nos exportadores de privacidade (`includes/privacidade.php`) e registrar a finalidade na Política de Privacidade.
- **Login com Google:** usar um plugin consolidado de login social, com o aplicativo do Google Cloud criado na conta do projeto (não na de uma pessoa).
