# Instruções do projeto PNA (para IA e desenvolvedores)

Leia este arquivo antes de alterar qualquer coisa. Ele define as convenções do projeto.

## Contexto

- Site do projeto de extensão Pets no Agreste (CAA – UFPE, Caruaru): adoção e apadrinhamento de cães e gatos.
- Público no Brasil; idioma de interface e comentários: **português (pt-BR)**.
- Quem mantém o site depois da entrega não é quem o desenvolveu: priorize código simples, documentado e com o mínimo de dependências.
- Design de referência no Figma (arquivo "Sem título", fileKey `CW6a3pZ3ygqzaZE3qOwbS9`).

## Arquitetura

- WordPress tradicional com **block theme** (FSE). Sem page builders (Elementor, Divi etc.). Sem headless.
- `themes/pna-theme/`: somente visual (templates, parts, patterns, estilos).
- `plugins/pna-core/`: regras de negócio (CPTs `pet` e solicitações, taxonomias, papéis, e-mails). O visual nunca depende de lógica escrita no tema.
- Ambiente local: `wp-env` (Docker). Use `npm run wp -- <comando>` para WP-CLI.
- Ganchos entre tema e plugin: o tema expõe filtros (`pna_notificacoes_nao_lidas`, `pna_breadcrumb_itens`) que o plugin preenche. O tema funciona sem o plugin.
- O desenvolvedor usa Ubuntu: instruções de terminal devem funcionar em bash no Linux.

## Escopo

- **Apenas desktop**, canvas de 1280px ([decisão 0002](docs/decisoes/0002-escopo-apenas-desktop.md)). Não crie breakpoints, menus mobile nem versões adaptadas para telas pequenas.

## Design system

- Todos os tokens (cores, fontes, tamanhos, espaçamentos, raios, bordas) vivem em `themes/pna-theme/theme.json`. Referência completa em `docs/design-tokens.md`.
- **Nunca** use cores, fontes ou medidas fixas em CSS ou em blocos. Use as variáveis geradas pelo theme.json (`var(--wp--preset--color--primary-text)`, `var(--wp--preset--spacing--40)`, `var(--wp--custom--radius--medium)` etc.).
- Uso das cores ([decisão 0001](docs/decisoes/0001-contraste-das-cores.md)):
  - `primary`: bordas, ícones e decoração. Nunca para texto.
  - `primary-strong`: fundo de botões, cabeçalho e rodapé, com texto claro de 24px ou mais.
  - `primary-text`: títulos, links e qualquer texto verde sobre fundo claro.
  - `body-text`: texto corrido e rótulos.
  - `danger`: mensagens de erro (não existe no Figma; ver `docs/design-tokens.md`).
- Todo texto precisa de contraste mínimo WCAG AA (4,5:1 texto normal; 3:1 texto grande). Confira combinações novas antes de usar.
- Todo campo de formulário tem rótulo visível acima; o placeholder é só um exemplo.
- CSS extra só em `assets/css/`: `base.css` (estrutura da página) e um arquivo por componente em `assets/css/componentes/`, carregado automaticamente. Apenas para o que o theme.json não cobre.
- Antes de criar um componente, consulte `docs/componentes.md`: ele lista os estilos de bloco, os padrões e a estrutura HTML dos componentes gerados pelo plugin. Componente novo = atualizar esse documento e a página "Guia de estilo" (`patterns/guia-de-estilo.php`).
- Classes CSS em português, no padrão BEM: `pna-bloco__elemento--variacao` (ex.: `pna-campo__entrada`, `pna-notificacao--nao-lida`).
- Fonte Poppins hospedada no tema (`assets/fonts/poppins/`); não carregar do Google Fonts (LGPD e desempenho).
- Ícones em SVG em `assets/icons/`, impressos com `pna_icone( 'nome' )`. Registre origem e licença em `assets/icons/README.md`.
- Blocos próprios do tema ficam em `blocks/<nome>/`, são dinâmicos (`render.php`) e não têm etapa de build: o `editor.js` usa as bibliotecas globais do WordPress (`window.wp`).
- Cabeçalho e rodapé: a parte de template já gera `<header>`/`<footer>`; o grupo interno usa `div`.
- Não use `text` nem `background` como nome de cor: colidem com classes internas do WordPress.

## Decisões

- Decisões importantes ficam em `docs/decisoes/`, numeradas. Antes de mudar algo coberto por uma decisão, leia o arquivo; ao mudar, registre uma nova decisão em vez de apagar a antiga.

## Plugin pna-core

- Funções com prefixo `pna_core_`, constantes `PNA_CORE_*`, metadados com prefixo `_pna_`. (O tema usa o prefixo `pna_`.)
- Modelo de dados, fluxos e papéis em `docs/modelo-de-dados.md`. Atualize-o a cada mudança de dados.
- Status de solicitação muda **somente** por `pna_core_alterar_status_solicitacao()`. Nunca altere `_pna_status` de solicitação diretamente.
- Situação do pet muda por `pna_core_definir_status_pet()`.
- Ao mudar papéis ou permissões, aumente `PNA_CORE_VERSAO_PAPEIS`.
- O plugin não depende de funções do tema. Se precisar de uma, verifique com `function_exists()`.
- Slugs das características (`macho`, `femea`, `gato`…) são contrato com o tema: não os renomeie.
- Blocos do plugin ficam em `plugins/pna-core/blocks/<nome>/` (namespace `pna-core/`) e são registrados automaticamente. Formulários enviam para a própria página com `pna_acao` + nonce `pna_{acao}` e são tratados em `pna_core_tratar_envios()`.
- Campos de formulário: use `pna_core_campo()`, `pna_core_aceite()` e `pna_core_resumo_erros()` (`includes/campos.php`), que já geram a marcação acessível do contrato.
- Nomes de parâmetros de URL e de campos de formulário **não podem** coincidir com variáveis públicas do WordPress: o nome do tipo de conteúdo (`pet`), as variáveis das características (`especie`, `sexo`, `porte`, `idade`) e as nativas (`name`, `page`, `p`, `s`, `author`, `order`, `year`, `error`…). O WordPress lê essas variáveis tanto da URL quanto do POST e passa a buscar outro conteúdo, resultando em "página não encontrada". Use prefixo: `pet_id`, `pna_pet`.
- Dados pessoais novos precisam entrar no exportador e no apagador de `includes/privacidade.php` (LGPD).

## Código

- Padrão de código WordPress (WPCS): tabs, `snake_case` em PHP.
- Prefixo `pna_` em funções, hooks, options e meta keys. Text domain: `pna`.
- Toda saída escapada (`esc_html`, `esc_attr`, `esc_url`, `wp_kses_post`); toda entrada sanitizada; formulários com nonce e verificação de capability.
- Textos visíveis sempre traduzíveis: `__( 'Texto', 'pna' )`.
- Não adicionar plugins de terceiros sem registrar a decisão em `docs/`.

## Versão do tema

- A versão fica no cabeçalho de `themes/pna-theme/style.css`. Aumente a cada entrega que vá para a produção (padrões e templates ficam em cache até a versão mudar).
- No ambiente local, `WP_DEVELOPMENT_MODE` = `theme` desliga esse cache; não remova essa linha do `.wp-env.json`.
- A versão do plugin fica em `plugins/pna-core/pna-core.php` (cabeçalho e constante `PNA_CORE_VERSAO`): aumente as duas juntas.
- Configurações que um site novo precisa (páginas, opções) entram em `pna_core_configuracao_inicial()` (`includes/configuracao.php`), nunca só no `scripts/conteudo-inicial.php`, que é exclusivo do ambiente local.
- Publicação: `docs/publicacao.md`. Não há ambiente de testes na hospedagem (decisão 0007): toda mudança é validada no ambiente local antes de ser publicada, e o site fica fechado pelo modo pré-lançamento até o lançamento.

## Git

- `git switch`/`git pull` podem apagar e recriar pastas; depois deles, oriente reiniciar o ambiente (`npm stop` e `npm start`).
- Branch por tarefa (`feat/`, `fix/`, `docs/`, `chore/`), Pull Request para a `main`.
- Nunca versionar: `wp-config.php`, `.env`, dumps de banco, uploads, `node_modules`.
