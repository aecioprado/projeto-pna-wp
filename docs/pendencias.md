# Pendências do projeto

Itens conhecidos que dependem de informação externa ou de fases futuras. Ao resolver um item, remova-o daqui (o histórico fica no Git).

| Item | Onde | Depende de |
|---|---|---|
| Número do WhatsApp do PNA (hoje o link é `#`) | `themes/pna-theme/parts/footer.html` | Coordenação do PNA |
| Enviar o logo do PNA **e remover o link provisório "Página inicial" do menu** (item com a classe `pna-menu__inicio-provisorio`) | Aparência → Editor → Padrões → Cabeçalho e `themes/pna-theme/parts/header.html` | Arquivo do logo em boa resolução (PNG transparente ou SVG) |
| Trocar ícones provisórios pelos do Figma | `themes/pna-theme/assets/icons/` | Exportação do Figma (limite do plano) |
| Formulário de apadrinhamento no Figma | Arquivo do Figma | Limite do plano Figma |
| Avisar a pessoa designer sobre os tons ajustados | Figma | Contato com o design ([decisão 0001](decisoes/0001-contraste-das-cores.md)) |
| Confirmar se o site será institucional da UFPE (eMAG) | — | Coordenação do PNA ([decisão 0001](decisoes/0001-contraste-das-cores.md)) |
| Galeria de fotos extras do pet (Figma: 3 miniaturas) | Plugin `pna-core` | Fase das páginas públicas |
| Atribuir cada solicitação a um avaliador específico | Plugin `pna-core` | Validar com a coordenação se é necessário |
| Etapa de "entrevista" no fluxo de adoção | Plugin `pna-core` | Validar com a coordenação |
| Imagem de destaque da página inicial (Figma: gato sobre fundo ilustrado) | Páginas → Página inicial → Imagem destacada | Arquivo da imagem |
| Seção "ONGs" da página inicial (logos das ONGs parceiras) | `templates/front-page.html` | Logos e autorização das ONGs |
| Chave Pix para doações em dinheiro | Página Doações (`#pix`) | Coordenação do PNA |
| Revisar os textos de Dúvidas e Doações (o texto de maus-tratos não veio do Figma) | Páginas Dúvidas e Doações | Coordenação do PNA |
| Notificações: página "Minhas notificações", contador do sino e e-mails | Plugin `pna-core` | Fase 9, na hospedagem ([decisão 0005](decisoes/0005-notificacoes-por-ultimo.md)) |
| "Esqueci minha senha" depende de e-mail | Página Entrar | Fase 9, na hospedagem |
| Revisar e publicar a Política de Privacidade (rascunho criado pela configuração inicial) | Páginas → Política de Privacidade | Dados de contato, hospedagem, prazo de retenção e aprovação da coordenação |
| Confirmar a retirada do CPF e do login social | Cadastro e Login | Coordenação do PNA ([decisão 0006](decisoes/0006-cadastro-sem-cpf-e-sem-login-social.md)) |
| Foto de perfil do Membro (hoje mostra a inicial do nome) | Meu perfil | Definir se é necessária |
| Publicar na HostGator seguindo `docs/publicacao.md` (site fechado ao público) | Hospedagem | Acesso ao cPanel e ao painel do WordPress |
