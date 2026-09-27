# Publicação na hospedagem

Roteiro para publicar o site num **ambiente de testes** (Fase 8) e, depois, na **produção** (Fase 10). O banco de dados local **nunca** é copiado para a hospedagem: ele só tem dados de exemplo.

## O que vai para a hospedagem

Só o código do projeto, em dois pacotes gerados por:

```bash
npm run pacote
```

- `dist/pna-theme-<versão>.zip`: o tema.
- `dist/pna-core-<versão>.zip`: o plugin.

A pasta `scripts/` (conteúdo de exemplo, fotos de pets fictícios) **não** vai para a hospedagem.

## Pré-requisitos da hospedagem

| Item | Exigência |
|---|---|
| WordPress | 6.6 ou mais recente (o ambiente local usa a versão mais recente) |
| PHP | 8.1 ou mais recente; de preferência a mesma versão do `phpVersion` do `.wp-env.json` |
| HTTPS | Certificado ativo (obrigatório: há login e dados pessoais) |
| Ambiente de testes | Subdomínio separado (ex.: `testes.dominio.org.br`), com banco de dados próprio |
| Backups | Automáticos, diários, com restauração testada |
| E-mail | Envio autenticado (SMTP), necessário na Fase 9 (notificações) |

## Ambiente de testes: primeira publicação

1. **Instale um WordPress limpo** no subdomínio de testes, pelo painel da hospedagem.
2. **Acessos:** crie os administradores conforme a matriz de acessos do projeto (usuários nominais, sem `admin`, 2FA ativo). Remova qualquer usuário criado automaticamente pela hospedagem que não seja necessário.
3. **Idioma e busca:** em Configurações → Geral, escolha "Português do Brasil". Em Configurações → Leitura, marque **"Evitar que mecanismos de busca indexem este site"** (só no ambiente de testes).
4. **Segurança:** peça à hospedagem (ou edite o `wp-config.php`) para incluir `define( 'DISALLOW_FILE_EDIT', true );`.
5. **Tema:** Aparência → Temas → Adicionar novo → Enviar tema → escolha `pna-theme-<versão>.zip` → Instalar → Ativar.
6. **Plugin:** Plugins → Adicionar novo → Enviar plugin → escolha `pna-core-<versão>.zip` → Instalar → Ativar.
7. **Configuração inicial:** Ferramentas → **PNA: configuração inicial** → Executar. Confira a tabela "Situação atual".
8. **Conteúdo real:** cadastre alguns pets com fotos reais (menu Pets), envie o logo (Aparência → Editor → Padrões → Cabeçalho) e a imagem destacada da Página inicial.
9. **Política de Privacidade:** revise o rascunho (Páginas → Política de Privacidade), complete os trechos entre [colchetes] e publique.
10. **Valide** com o roteiro das fases 6 e 7, agora na hospedagem.

## Atualizações (novas versões)

1. Aumente a versão do tema (`style.css`) e/ou do plugin (`pna-core.php`) no mesmo PR da mudança.
2. Depois do merge na `main`, rode `npm run pacote`.
3. No painel da hospedagem, envie o novo `.zip` pelo mesmo caminho da instalação. O WordPress pergunta se deseja **substituir** a versão atual: confirme.
4. Se a atualização incluiu páginas novas, rode Ferramentas → PNA: configuração inicial de novo (é seguro repetir).

Faça as atualizações **primeiro no ambiente de testes**; só depois de validadas, repita na produção.

## Produção (Fase 10)

Mesmo roteiro do ambiente de testes, com três diferenças:

- **Não** marque "Evitar que mecanismos de busca indexem este site".
- Não crie usuários de teste.
- Ao final, remova os acessos do desenvolvedor (ver matriz de acessos).
