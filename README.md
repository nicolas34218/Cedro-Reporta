# 🚨 Cedro Reporta

Sistema de denúncias desenvolvido com Laravel 11.

---

## 📋 Pré-requisitos

- PHP 8.3+
- Composer
- Node.js
- **Banco de Dados**: SQLite (padrão) ou MySQL

---

## 🚀 Setup Automático

### 1. Clonar e Configurar
```bash
git clone https://github.com/seu-usuario/cedro-reporta.git
cd cedro-reporta

### 2. Configurar Storage (Upload de Imagens)

**Essencial para a funcionalidade de upload de denúncias!**

# Setup do storage (cria pastas e valida permissões)
php artisan storage:setup

# Diagnosticar problemas de upload (se houver)
php artisan upload:diagnose
```

### 3. Configurar Banco de Dados (Opcional)

#### Para MySQL com XAMPP:
1. Abra o **XAMPP Control Panel**
2. Clique em **Start** para Apache e MySQL
3. Acesse `http://localhost/phpmyadmin/`
4. Crie um banco chamado `cedro_reporta`
5. Edite o arquivo `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=cedro_reporta
DB_USERNAME=root
DB_PASSWORD=
```
6. Execute novamente:
 `
php artisan migrate:fresh --seed
php artisan serve

#### Para SQLite (Padrão):
Não é necessária configuração adicional - o banco é criado automaticamente.

### Produção
```bash
npm run build
php artisan serve
```

---

## 🧪 Testes

```bash
# Executar todos os testes
php artisan test

# Executar testes específicos
php artisan test tests/Feature/ReportDetailsTest.php
php artisan test tests/Feature/ReportFilterTest.php
```

---

## 🔑 Credenciais de Teste

| Tipo | Email | Senha |
|------|-------|-------|
| Admin | admin@cedroreporta.com | admin123 |
| Secretário | secretario@cedroreporta.com | senha123 |
| Cidadão | teste@gmail.com | teste123 |



---

## 🛠️ Tecnologias Utilizadas

- **Backend**: Laravel 11 (PHP 8.3+)
- **Banco de Dados**: SQLite (desenvolvimento) / MySQL (produção)
- **Frontend**: Vite, Tailwind CSS
- **Testes**: Pest PHP
