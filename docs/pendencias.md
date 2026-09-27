# Pendências do projeto

Itens conhecidos que dependem de informação externa ou de fases futuras. Ao resolver um item, remova-o daqui (o histórico fica no Git).

| Item | Onde | Depende de |
|---|---|---|
| Número do WhatsApp do PNA (hoje o link é `#`) | `themes/pna-theme/parts/footer.html` | Coordenação do PNA |
| Enviar o logo do PNA | Aparência → Editor → Padrões → Cabeçalho | Arquivo do logo em boa resolução (PNG transparente ou SVG) |
| Trocar ícones provisórios pelos do Figma | `themes/pna-theme/assets/icons/` | Exportação do Figma (limite do plano) |
| Formulário de apadrinhamento no Figma | Arquivo do Figma | Limite do plano Figma |
| Botões "Cadastro" e "Entrar" usam as telas padrão do WordPress | Bloco `pna/header-acoes` | Telas próprias de cadastro e login (fase da área logada) |
| Avisar a pessoa designer sobre os tons ajustados | Figma | Contato com o design ([decisão 0001](decisoes/0001-contraste-das-cores.md)) |
| Confirmar se o site será institucional da UFPE (eMAG) | — | Coordenação do PNA ([decisão 0001](decisoes/0001-contraste-das-cores.md)) |
| Números do contador de adoções são fixos ("00", "000") | `patterns/contador-adocoes.php` | Plugin `pna-core` (contagem real de adoções) |
| Galeria de fotos extras do pet (Figma: 3 miniaturas) | Plugin `pna-core` | Fase das páginas públicas |
| Exportação e exclusão de dados pessoais das solicitações (LGPD) | Plugin `pna-core` | Fase da área logada |
| Atribuir cada solicitação a um avaliador específico | Plugin `pna-core` | Validar com a coordenação se é necessário |
| Etapa de "entrevista" no fluxo de adoção | Plugin `pna-core` | Validar com a coordenação |
