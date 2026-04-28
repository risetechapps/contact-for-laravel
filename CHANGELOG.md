# Changelog

Todas as alterações notáveis neste projeto serão documentadas neste arquivo.
O formato é baseado em [Keep a Changelog](https://keepachangelog.com/en/1.0.0/), e este projeto segue o [Versionamento Semântico](https://semver.org/lang/pt-BR/) (SemVer).

## [1.1.0] - 2026-04-28
### Added
- Dependências atualizadas
- **Sistema de Audit Log (Histórico de Mudanças)**:
  - Tabela `contact_histories` para rastrear todas as alterações
  - Model `ContactHistory` com métodos úteis (`getOldValues()`, `getNewValues()`, `fieldChanged()`)
  - Trait `HasAuditing` com eventos automáticos (created, updated, deleted, restored)
  - Registra: quem alterou, quando, o quê mudou, IP, user-agent e URL
  - Métodos no Contact: `histories()`, `latestHistory()`, `historiesForAction()`, `lastUpdatedBy()`, `createdBy()`
  - Resource `ContactHistoryResource` para API
- Configuração de campos auditáveis via propriedade `$auditable`
- Configuração de guard via propriedade `$auditGuard`
- **Gerenciamento Automático do Contato Principal**:
- Ao criar um contato, se não existir nenhum primário, ele automaticamente assume essa função
- Ao marcar um contato como primário, o anterior é automaticamente desmarcado (garante apenas 1 primário)
- Ao deletar o contato primário, outro contato do mesmo model é promovido automaticamente (respeitando `sort_order` e `created_at`)

## [1.0.0] - 2026-01-24
### Added
- Lançamento inicial (Primeira versão estável).
