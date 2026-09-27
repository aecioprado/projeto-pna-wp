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

## Escopo

- **Apenas desktop**, canvas de 1280px ([decisão 0002](docs/decisoes/0002-escopo-apenas-desktop.md)). Não crie breakpoints, menus mobile nem versões adaptadas para telas pequenas.

## Design system

- Todos os tokens (cores, fontes, tamanhos, espaçamentos, raios, bordas) vivem em `themes/pna-theme/theme.json`. Referência completa em `docs/design-tokens.md`.
- **Nunca** use cores, fontes ou medidas fixas em CSS ou em blocos. Use as variáveis geradas pelo theme.json (`var(--wp--preset--color--primary-text)`, `var(--wp--preset--spacing--40)`, `var(--wp--custom--radius--medium)` etc.).
- Uso das cores ([decisão 0001](docs/decisoes/0001-contraste-das-cores.md)):
  - `primary`: bordas, ícones e decoração. Nunca para texto.
  - `primary-strong`: fundo de botões, cabeçalho e rodapé, com texto claro de 24px ou mais.
  - `primary-text`: títulos, links e qualquer texto verde sobre fundo claro.
  - `text`: texto corrido e rótulos.
- Todo texto precisa de contraste mínimo WCAG AA (4,5:1 texto normal; 3:1 texto grande). Confira combinações novas antes de usar.
- Todo campo de formulário tem rótulo visível acima; o placeholder é só um exemplo.
- CSS extra só em `assets/css/components.css`, e apenas para o que o theme.json não cobre.
- Fonte Poppins hospedada no tema (`assets/fonts/poppins/`); não carregar do Google Fonts (LGPD e desempenho).
- Ícones em SVG em `assets/icons/`.

## Decisões

- Decisões importantes ficam em `docs/decisoes/`, numeradas. Antes de mudar algo coberto por uma decisão, leia o arquivo; ao mudar, registre uma nova decisão em vez de apagar a antiga.

## Código

- Padrão de código WordPress (WPCS): tabs, `snake_case` em PHP.
- Prefixo `pna_` em funções, hooks, options e meta keys. Text domain: `pna`.
- Toda saída escapada (`esc_html`, `esc_attr`, `esc_url`, `wp_kses_post`); toda entrada sanitizada; formulários com nonce e verificação de capability.
- Textos visíveis sempre traduzíveis: `__( 'Texto', 'pna' )`.
- Não adicionar plugins de terceiros sem registrar a decisão em `docs/`.

## Git

- Branch por tarefa (`feat/`, `fix/`, `docs/`, `chore/`), Pull Request para a `main`.
- Nunca versionar: `wp-config.php`, `.env`, dumps de banco, uploads, `node_modules`.
