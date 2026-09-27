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

## Código

- Padrão de código WordPress (WPCS): tabs, `snake_case` em PHP.
- Prefixo `pna_` em funções, hooks, options e meta keys. Text domain: `pna`.
- Toda saída escapada (`esc_html`, `esc_attr`, `esc_url`, `wp_kses_post`); toda entrada sanitizada; formulários com nonce e verificação de capability.
- Textos visíveis sempre traduzíveis: `__( 'Texto', 'pna' )`.
- Não adicionar plugins de terceiros sem registrar a decisão em `docs/`.

## Versão do tema

- A versão fica no cabeçalho de `themes/pna-theme/style.css`. Aumente a cada entrega que vá para a produção (padrões e templates ficam em cache até a versão mudar).
- No ambiente local, `WP_DEVELOPMENT_MODE` = `theme` desliga esse cache; não remova essa linha do `.wp-env.json`.

## Git

- `git switch`/`git pull` podem apagar e recriar pastas; depois deles, oriente reiniciar o ambiente (`npm stop` e `npm start`).
- Branch por tarefa (`feat/`, `fix/`, `docs/`, `chore/`), Pull Request para a `main`.
- Nunca versionar: `wp-config.php`, `.env`, dumps de banco, uploads, `node_modules`.
