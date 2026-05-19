# 📋 Sistema de Atribuição Automática de Denúncias aos Setores

## 📌 Visão Geral

Este documento descreve a implementação do **sistema de atribuição automática de denúncias** aos setores responsáveis baseado na categoria selecionada pelo cidadão, com notificações internas para os responsáveis do setor.

---

## 🎯 Critérios de Aceitação Atendidos

✅ O sistema realiza a atribuição automática das denúncias aos setores responsáveis com base na categoria selecionada  
✅ O sistema permite associar **1 ou mais secretárias** a cada categoria  
✅ O sistema envia notificações internas para os responsáveis do setor quando uma denúncia é atribuída  
✅ O campo "Setor Responsável" é preenchido automaticamente no formulário de denúncia  
✅ O campo fica igual ao screenshot da interface fornecida  

---

## 🔧 Alterações Realizadas

### **1. Migração: Relação Muitos-para-Muitos (M-M)**

**Arquivo:** `database/migrations/2026_05_19_create_category_secretary_table.php`

Cria a tabela `category_secretary` que permite associar múltiplas secretárias a cada categoria:

```php
Schema::create('category_secretary', function (Blueprint $table) {
    $table->id();
    $table->foreignId('category_id')->references('id')->on('categories')->onDelete('cascade');
    $table->foreignId('secretary_id')->references('id')->on('secretaries')->onDelete('cascade');
    $table->timestamps();
    $table->unique(['category_id', 'secretary_id']); // Previne duplicatas
});
```

---

### **2. Modelos: Relacionamentos M-M**

#### **2.1 - Category Model** (`app/Models/Category.php`)

Adiciona relacionamento com secretárias:

```php
public function secretaries(): BelongsToMany
{
    return $this->belongsToMany(
        Secretary::class,
        'category_secretary',
        'category_id',
        'secretary_id'
    );
}
```

#### **2.2 - Secretary Model** (`app/Models/Secretary.php`)

Adiciona relacionamento com categorias:

```php
public function categories(): BelongsToMany
{
    return $this->belongsToMany(
        Category::class,
        'category_secretary',
        'secretary_id',
        'category_id'
    );
}
```

---

### **3. Formulário: Campo Setor Responsável**

**Arquivo:** `resources/views/citizen/reports/create.blade.php`

Adicionado novo campo que carrega automaticamente:

```blade
<div class="form-field">
    <label for="secretary">SETOR RESPONSÁVEL</label>
    <select id="secretary" name="secretary_id">
        <option value="">Carregando...</option>
    </select>
    <small style="display: block; margin-top: 8px; color: #666;">
        <i style="color: #2f6b3f;">💡</i> Preenchido automaticamente com base na categoria
    </small>
    <x-error-message field="secretary_id" />
</div>
```

---

### **4. JavaScript: Carregamento Dinâmico**

**Arquivo:** `resources/views/citizen/reports/create.blade.php` - `@push('scripts')`

Script que busca os setores responsáveis ao selecionar uma categoria:

```javascript
document.getElementById('category').addEventListener('change', async function() {
    const categoryId = this.value;
    const secretarySelect = document.getElementById('secretary');
    
    if (!categoryId) {
        secretarySelect.innerHTML = '<option value="">Selecione uma categoria primeiro</option>';
        return;
    }

    try {
        secretarySelect.innerHTML = '<option value="">Carregando...</option>';
        
        const response = await fetch(`/api/categories/${categoryId}/secretaries`, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            }
        });

        if (!response.ok) throw new Error('Erro ao carregar setores');

        const secretaries = await response.json();
        
        if (secretaries.length === 0) {
            secretarySelect.innerHTML = '<option value="">Nenhum setor responsável configurado</option>';
            return;
        }

        secretarySelect.innerHTML = '<option value="">Selecione (opcional)</option>';
        secretaries.forEach(secretary => {
            const option = document.createElement('option');
            option.value = secretary.id;
            option.textContent = secretary.name;
            secretarySelect.appendChild(option);
        });
    } catch (error) {
        console.error('Erro:', error);
        secretarySelect.innerHTML = '<option value="">Erro ao carregar setores</option>';
    }
});
```

---

### **5. Rota API: Buscar Secretários**

**Arquivo:** `routes/web.php`

Nova rota que retorna secretárias ativas de uma categoria:

```php
Route::prefix('api')->middleware(['auth:citizen'])->group(function () {
    Route::get('/categories/{categoryName}/secretaries', function ($categoryName) {
        try {
            $category = \App\Models\Category::where('name', $categoryName)->first();

            if (!$category) {
                return response()->json([], 404);
            }

            $secretaries = $category->secretaries()
                ->where('is_active', true)
                ->select('id', 'name')
                ->get();

            return response()->json($secretaries);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Erro ao buscar secretárias', [
                'category' => $categoryName,
                'error' => $e->getMessage(),
            ]);
            return response()->json(['error' => 'Erro ao carregar setores'], 500);
        }
    });
});
```

---

### **6. Notification: Notificar Secretárias**

**Arquivo:** `app/Notifications/NewReportAssigned.php`

Notificação armazenada no banco de dados quando uma denúncia é atribuída:

```php
public function via($notifiable)
{
    return ['database']; // Armazena no banco de dados
}

public function toDatabase($notifiable)
{
    return [
        'type' => 'new_report_assigned',
        'report_id' => $this->report->id,
        'report_title' => $this->report->title,
        'report_category' => $this->report->category,
        'citizen_name' => $this->report->citizen->name ?? 'Cidadão',
        'message' => "Nova denúncia atribuída: {$this->report->title}",
        'url' => route('secretary.classify-reports'),
        'created_at' => now(),
    ];
}
```

---

### **7. Controller: Atribuição Múltipla**

**Arquivo:** `app/Http/Controllers/ReportController.php` - método `store()`

Lógica de atribuição e notificação de secretárias:

```php
// Busca a categoria para obter as secretárias responsáveis
$category = \App\Models\Category::where('name', $validated['category'])->first();

if ($category) {
    // Obtém todas as secretárias responsáveis pela categoria
    $secretaries = $category->secretaries()
        ->where('is_active', true)
        ->get();

    if ($secretaries->isNotEmpty()) {
        // Atribui a primeira secretária como responsável principal
        $primarySecretary = $secretaries->first();
        $report->update([
            'secretary_id' => $primarySecretary->id,
        ]);

        // Notifica TODAS as secretárias responsáveis pela categoria
        foreach ($secretaries as $secretary) {
            $secretary->notify(new \App\Notifications\NewReportAssigned($report));
        }

        Log::info('Denúncia atribuída automaticamente', [
            'report_id' => $report->id,
            'primary_secretary_id' => $primarySecretary->id,
            'total_secretaries_notified' => $secretaries->count(),
            'category' => $validated['category'],
        ]);
    }
}
```

---

### **8. Seeder: Associar Secretárias às Categorias**

**Arquivo:** `database/seeders/CategorySecretarySeeder.php`

Seed que associa automaticamente secretárias às suas categorias:

```php
public function run(): void
{
    $secretaries = Secretary::where('is_active', true)->get();

    foreach ($secretaries as $secretary) {
        if (!empty($secretary->category)) {
            $category = Category::where('name', $secretary->category)->first();
            
            if ($category) {
                $category->secretaries()->syncWithoutDetaching([$secretary->id]);
                echo "✓ Secretária '{$secretary->name}' associada à categoria '{$category->name}'\n";
            }
        }
    }
}
```

---

## 📦 Comandos para Executar

Execute estes comandos **em sequência** no terminal:

```bash
# 1. Executar as migrações (cria a tabela category_secretary)
php artisan migrate

# 2. Executar o seeder (associa secretárias às categorias)
php artisan db:seed --class=CategorySecretarySeeder

# 3. Limpar o cache de configuração
php artisan config:cache

# 4. Iniciar o servidor Laravel
php artisan serve
```

---

## 🧪 Como Testar

### **Teste 1: Formulário de Denúncia**

1. Acesse `/cidadao/denuncias/nova` como cidadão
2. Selecione uma categoria no dropdown "CATEGORIA"
3. Observe o dropdown "SETOR RESPONSÁVEL" carregar automaticamente
4. Crie uma denúncia

### **Teste 2: Atribuição Automática**

1. Após criar a denúncia, verifique no banco de dados:
   - A denúncia deve ter `secretary_id` preenchido
   - Deve haver registros em `notifications` para as secretárias

### **Teste 3: Notificações**

1. Faça login como secretária
2. Vá para `/secretary/classify-reports`
3. Você deve ver a denúncia que foi criada
4. No banco em `notifications`, deve haver registros para a secretária

---

## 📊 Fluxo de Dados

```
Cidadão cria denúncia
    ↓
Seleciona categoria
    ↓
JavaScript busca secretárias via API (/api/categories/{category}/secretaries)
    ↓
Formulário mostra secretárias disponíveis
    ↓
Denúncia é criada
    ↓
ReportController busca todas as secretárias da categoria
    ↓
Denúncia atribuída à primeira secretária (secretary_id)
    ↓
TODAS as secretárias da categoria recebem notificação
    ↓
Secretárias veem a denúncia em /secretary/classify-reports
```

---

## 🔗 Tabelas do Banco de Dados Envolvidas

| Tabela | Descrição |
|--------|-----------|
| `categories` | Categorias de denúncias |
| `secretaries` | Secretárias/setores |
| `category_secretary` | **[NOVA]** Relação M-M entre categorias e secretárias |
| `reports` | Denúncias (com campo `secretary_id` para atribuição) |
| `notifications` | Notificações internas para secretárias |

---

## ✨ Features Implementadas

- ✅ Seletor de categoria com carregamento dinâmico de setores
- ✅ Múltiplas secretárias por categoria
- ✅ Atribuição automática de denúncia à secretária responsável
- ✅ Notificações para TODAS as secretárias da categoria
- ✅ Armazenamento de notificações em banco de dados
- ✅ Logs detalhados de atribuições
- ✅ Validação e tratamento de erros

---

## 🐛 Resolução de Problemas

**P: O dropdown "SETOR RESPONSÁVEL" não carrega?**  
R: Verifique se:
- A rota API está registrada em `routes/web.php`
- As secretárias estão associadas às categorias (rode o seeder)
- O cidadão está autenticado
- Abra o console (F12) e veja os erros

**P: A denúncia não está sendo atribuída?**  
R: Verifique:
- Se existem secretárias ativas para a categoria
- Se a categoria existe no banco de dados
- Os logs em `storage/logs/laravel.log`

**P: As notificações não aparecem?**  
R: Verifique:
- Se o campo `notifications` foi criado no banco (rode migrações do Laravel)
- Se as secretárias recebem as notificações em `notifications` table
- Implemente uma view para mostrar notificações

---

## 📝 Notas Importantes

1. **Atribuição**: A primeira secretária da lista é atribuída como principal (`secretary_id`), mas **todas** recebem notificação
2. **Notificações**: Estão armazenadas no banco em `notifications`, implemente uma view para exibi-las
3. **Seeder**: Associa secretárias às categorias baseado no campo `category` que possuem
4. **API**: Requer autenticação de cidadão (`auth:citizen`)
5. **Performance**: Use lazy loading para grandes volumes de denúncias

---

## 🔐 Segurança

- ✅ CSRF token obrigatório
- ✅ Autenticação obrigatória na rota API
- ✅ Apenas secretárias ativas podem receber denúncias
- ✅ Logs de todas as atribuições
- ✅ Validação de entrada
- ✅ Tratamento de exceções

---

## 📖 Arquivos Modificados/Criados

| Arquivo | Tipo | Descrição |
|---------|------|-----------|
| `database/migrations/2026_05_19_create_category_secretary_table.php` | Novo | Migração M-M |
| `app/Models/Category.php` | Modificado | Relacionamento `secretaries()` |
| `app/Models/Secretary.php` | Modificado | Relacionamento `categories()` |
| `resources/views/citizen/reports/create.blade.php` | Modificado | Campo "Setor Responsável" + JavaScript |
| `routes/web.php` | Modificado | Rota API `/api/categories/{categoryName}/secretaries` |
| `app/Notifications/NewReportAssigned.php` | Novo | Notification class |
| `app/Http/Controllers/ReportController.php` | Modificado | Lógica de atribuição no método `store()` |
| `database/seeders/CategorySecretarySeeder.php` | Novo | Seeder de associações |

---

**Implementação Concluída! ✅**
