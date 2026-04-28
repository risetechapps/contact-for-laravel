# Laravel Contact

## 📌 Sobre o Projeto
O **Laravel Contact** é um package para Laravel que te possibilita criar multiplos contatos no mesmo registro.

---

## 🚀 Instalação

### 1️⃣ Requisitos
Antes de instalar, certifique-se de que seu projeto atenda aos seguintes requisitos:
- PHP >= 8.3
- Laravel >= 12
- Composer instalado

### 2️⃣ Instalação do Package
Execute o seguinte comando no terminal:
```bash
composer require risetechapps/contact-for-laravel
```

### 3️⃣ Configure seu Model
```php
  use RiseTechApps\Contact\Traits\HasContacts\HasContacts;
  
  class Client extends Model
  {
    use HasFactory, HasContacts;
  }
```

### 4️⃣ Rodar Migrations
```bash
php artisan migrate
```

---

## 📖 Uso

> **Nota:** O trait `HasContacts` pode ser usado em qualquer model, com ou sem `SoftDeletes`.

### Campos do Contato
- `name` - Nome do contato
- `telephone` - Telefone fixo
- `cellphone` - Celular
- `email` - Email
- `department` - Departamento/Tipo (ex: comercial, suporte)
- `is_primary` - Define se é o contato principal (boolean)
- `sort_order` - Ordenação personalizada (inteiro)

### Métodos Disponíveis no Trait

```php
use RiseTechApps\Contact\Traits\HasContacts\HasContacts;

class Client extends Model
{
    use HasFactory, HasContacts;
}

// Obter contato principal
$primaryContact = $client->getPrimaryContact();

// Obter contatos por departamento
$comercialContacts = $client->getContactsByType('comercial');

// Verificar se existe email
$hasEmail = $client->hasEmail('joao@exemplo.com');

// Verificar contato em campo específico
$hasPhone = $client->hasContact('telephone', '11999999999');
```

### Relacionamento
```php
// Todos os contatos ordenados por sort_order
$client->contacts;

// Apenas o contato principal
$client->contacts()->primary()->first();

// Ordenados
$client->contacts()->ordered()->get();
```

### Contato Principal (Automático)

O sistema gerencia automaticamente o contato principal:

- **Ao criar um contato**: Se não existir nenhum contato primário para o model, ele automaticamente será marcado como primário
- **Ao definir como primário**: Se já existir um contato primário, o anterior perde automaticamente essa marcação
- **Ao deletar o primário**: Outro contato do mesmo model será promovido a primário automaticamente (respeitando `sort_order` e `created_at`)

```php
// Criar primeiro contato - automaticamente vira primário
$client->contacts()->create(['name' => 'João', 'email' => 'joao@exemplo.com']);

// Criar segundo como primário - o primeiro perde a marcação
$client->contacts()->create([
    'name' => 'Maria',
    'email' => 'maria@exemplo.com',
    'is_primary' => true  // João deixa de ser primário
]);

// Deletar o primário - outro contato assume automaticamente
$client->getPrimaryContact()->delete();
$novoPrimario = $client->fresh()->getPrimaryContact(); // Retorna outro contato
```

### Histórico de Mudanças (Audit Log)

Todos os contatos possuem rastreamento automático de alterações:

```php
// Obter histórico de um contato
$history = $contact->histories;

// Últimas 10 alterações
$latest = $contact->latestHistory(10);

// Filtrar por tipo de ação
$updates = $contact->historiesForAction('updated');

// Quem criou o contato
$creator = $contact->createdBy();

// Quem fez a última alteração
$lastEditor = $contact->lastUpdatedBy();

// Verificar se um campo específico mudou
foreach ($history as $record) {
    if ($record->fieldChanged('email')) {
        $oldEmail = $record->getOldValue('email');
        $newEmail = $record->getNewValue('email');
    }
}
```

### Configurar Campos Auditáveis (Opcional)

Por padrão, todos os campos `fillable` são auditados. Para personalizar:

```php
class Contact extends Model
{
    protected $auditable = ['name', 'email', 'telephone']; // apenas estes
    protected $auditGuard = 'web'; // guard alternativo
}
```

---

## 🛠 Contribuição
Sinta-se à vontade para contribuir! Basta seguir estes passos:
1. Faça um fork do repositório
2. Crie uma branch (`feature/nova-funcionalidade`)
3. Faça um commit das suas alterações
4. Envie um Pull Request

---

## 📜 Licença
Este projeto é distribuído sob a licença MIT. Veja o arquivo [LICENSE](LICENSE) para mais detalhes.

---

💡 **Desenvolvido por [Rise Tech](https://risetech.com.br)**

