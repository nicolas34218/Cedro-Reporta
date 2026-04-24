# 🚨 Cedro Reporta

Sistema de denúncias desenvolvido com Laravel 11 e MySQL.

---

## 📋 Pré-requisitos

- XAMPP (Apache, MySQL, PHP 8.0+)
- Git
- Composer
- Node.js

---

## 🚀 Setup Rápido

### 1. Clonar/Atualizar Repositório
```bash
git clone https://github.com/seu-usuario/cedro-reporta.git
cd cedro-reporta
# ou
git pull origin main
```

### 2. Instalar Dependências
```bash
composer install
npm install
```

### 3. Gerar Chave da Aplicação
```bash
php artisan key:generate
```

### 4. Criar Banco de Dados
Abra `http://localhost/phpmyadmin/` e crie um banco chamado `cedro_reporta`

### 5. Rodar Migrations e Seeds
```bash
php artisan migrate
php artisan db:seed
```

### 6. Iniciar Servidor
- Abra **XAMPP Control Panel**
- Clique em **Start** para Apache e MySQL

### 7. Acessar Aplicação
```
http://localhost/Vs_Code/cedro-reporta/public/
```

---

## 🔑 Credenciais de Teste

| Tipo | Email | Senha |
|------|-------|-------|
| Admin | admin@cedroreporta.com | senha123 |
| Secretário | secretario@cedroreporta.com | senha123 |

---

