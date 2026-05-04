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
composer run setup
```

**Este comando automatiza:**
- ✅ Instalação de dependências PHP
- ✅ Configuração do arquivo `.env`
- ✅ Geração da chave da aplicação
- ✅ Execução das migrations
- ✅ Instalação de dependências Node.js
- ✅ Build dos assets

**Nota:** O comando não executa seeds automaticamente. Para popular o banco com dados de teste, execute `php artisan db:seed` após o setup.

### 2. Configurar Banco de Dados (Opcional)

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
6. Execute novamente: `php artisan migrate --seed`

#### Para SQLite (Padrão):
Não é necessária configuração adicional - o banco é criado automaticamente.

---

## 🏃‍♂️ Executando a Aplicação

### Desenvolvimento
```bash
composer run dev
```
**Inicia automaticamente:** Servidor Laravel + Queue Worker + Vite (assets)

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
| Admin | admin@cedroreporta.com | senha123 |
| Secretário | secretario@cedroreporta.com | senha123 |

---

## 📊 API Endpoints

Consulte [API_REFERENCE.md](API_REFERENCE.md) para documentação completa dos endpoints.

---

## 🛠️ Tecnologias Utilizadas

- **Backend**: Laravel 11 (PHP 8.3+)
- **Banco de Dados**: SQLite (desenvolvimento) / MySQL (produção)
- **Frontend**: Vite, Tailwind CSS
- **Testes**: Pest PHP

