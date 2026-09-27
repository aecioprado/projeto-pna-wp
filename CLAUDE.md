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

## Design system

- Todos os tokens (cores, fontes, tamanhos, espaçamentos, raios) vivem em `themes/pna-theme/theme.json`.
- **Nunca** use cores, fontes ou tamanhos fixos em CSS ou em blocos. Use as variáveis do theme.json (`var(--wp--preset--color--primary)`, `var(--wp--preset--spacing--40)` etc.).
- CSS extra só em `assets/css/components.css`, e apenas para o que o theme.json não cobre.
- Fonte Poppins hospedada no tema (`assets/fonts/`); não carregar do Google Fonts (LGPD e desempenho).
- Ícones em SVG em `assets/icons/`.
- Todo texto precisa de contraste mínimo WCAG AA (4,5:1 texto normal; 3:1 texto grande).
- Mobile first: o Figma só tem desktop (1280px); todo componente deve funcionar a partir de 360px.

## Código

- Padrão de código WordPress (WPCS): tabs, `snake_case` em PHP.
- Prefixo `pna_` em funções, hooks, options e meta keys. Text domain: `pna`.
- Toda saída escapada (`esc_html`, `esc_attr`, `esc_url`, `wp_kses_post`); toda entrada sanitizada; formulários com nonce e verificação de capability.
- Textos visíveis sempre traduzíveis: `__( 'Texto', 'pna' )`.
- Não adicionar plugins de terceiros sem registrar a decisão em `docs/`.

## Git

- Branch por tarefa (`feat/`, `fix/`, `docs/`, `chore/`), Pull Request para a `main`.
- Nunca versionar: `wp-config.php`, `.env`, dumps de banco, uploads, `node_modules`.
