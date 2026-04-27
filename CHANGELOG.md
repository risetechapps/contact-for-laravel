# Changelog

Todas as alterações notáveis neste projeto serão documentadas neste arquivo.
O formato é baseado em [Keep a Changelog](https://keepachangelog.com/en/1.0.0/), e este projeto segue o [Versionamento Semântico](https://semver.org/lang/pt-BR/) (SemVer).

## [1.2.0] - 2026-04-27
### Added
- **Sistema de Audit Log (Histórico de Mudanças)**:
  - Tabela `contact_histories` para rastrear todas as alterações
  - Model `ContactHistory` com métodos úteis (`getOldValues()`, `getNewValues()`, `fieldChanged()`)
  - Trait `HasAuditing` com eventos automáticos (created, updated, deleted, restored)
  - Registra: quem alterou, quando, o quê mudou, IP, user-agent e URL
  - Métodos no Contact: `histories()`, `latestHistory()`, `historiesForAction()`, `lastUpdatedBy()`, `createdBy()`
  - Resource `ContactHistoryResource` para API
- Configuração de campos auditáveis via propriedade `$auditable`
- Configuração de guard via propriedade `$auditGuard`

## [1.1.0] - 2026-04-27
### Added
- **Sync Inteligente**: O listener agora atualiza contatos existentes em vez de deletar e recriar todos
- **Contato Principal**: Adicionado campo `is_primary` para marcar um contato como principal
- **Ordenação**: Adicionado campo `sort_order` para ordenação personalizada dos contatos
- **Métodos Úteis no Trait**:
  - `getPrimaryContact()` - Retorna o contato principal
  - `getContactsByType($department)` - Retorna contatos por departamento/tipo
  - `hasEmail($email)` - Verifica se existe contato com o email
  - `hasContact($field, $value)` - Verifica se existe contato com valor em campo específico
- Scopes no model: `primary()` e `ordered()`
- Método `restoreIfTrashed()` no model Contact
- Transaction no listener para garantir integridade dos dados

### Fixed
- Corrigido bug em `ContactResource` onde `cellphone` retornava valor duplicado de `telephone`
- Adicionado índices para os novos campos `is_primary` e `sort_order`

## [1.0.2] - 2026-03-15
### Added
- Corrigido relacionamento de model
- Atualizado packages.
- 
## [1.0.1] - 2026-03-14
### Added
- Corrigido relacionamento de model
- Atualizado packages.

## [1.0.0] - 2026-01-24
### Added
- Lançamento inicial (Primeira versão estável).
