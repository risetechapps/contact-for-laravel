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

