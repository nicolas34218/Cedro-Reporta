/**
 * Gerenciador de Filtros do Dashboard da Secretária
 * 
 * Este módulo é responsável por:
 * - Capturar seleções de filtros
 * - Atualizar display de filtros ativos
 * - Preparar dados para envio ao backend
 * - Mostrar/ocultar feedback visual
 */

class FilterManager {
    constructor() {
        this.form = document.getElementById('filtersForm');
        this.priorityFilter = document.getElementById('priorityFilter');
        this.statusFilter = document.getElementById('statusFilter');
        this.activeFiltersDisplay = document.getElementById('activeFiltersDisplay');
        this.activeFiltersTags = document.getElementById('activeFiltersTags');
        this.resetButton = document.querySelector('.filters-reset-btn');
        
        this.init();
    }

    /**
     * Inicializa event listeners
     */
    init() {
        // Quando o formulário é submetido
        this.form.addEventListener('submit', (e) => this.handleFilterApply(e));
        
        // Quando filtros mudam (opcional - feedback instantâneo)
        this.priorityFilter.addEventListener('change', () => this.updateActiveFiltersDisplay());
        this.statusFilter.addEventListener('change', () => this.updateActiveFiltersDisplay());
        
        // Botão de reset
        this.resetButton.addEventListener('click', () => this.resetAllFilters());
    }

    /**
     * Aplica filtros (será conectado ao backend depois)
     * Por enquanto, apenas coleta os dados
     */
    handleFilterApply(e) {
        e.preventDefault();
        
        // Coleta os valores dos filtros
        const filters = {
            priority: this.priorityFilter.value,
            status: this.statusFilter.value,
            timestamp: new Date().toISOString()
        };
        
        console.log('Filtros selecionados:', filters);
        
        // Atualiza display de filtros ativos
        this.updateActiveFiltersDisplay();
        
        // FUTURA INTEGRAÇÃO COM BACKEND:
        // this.sendFiltersToBackend(filters);
    }

    /**
     * Atualiza o display de filtros ativos
     */
    updateActiveFiltersDisplay() {
        const activeFilters = [];
        
        if (this.priorityFilter.value) {
            const priorityLabel = this.priorityFilter.options[this.priorityFilter.selectedIndex].text;
            activeFilters.push({
                name: 'Prioridade',
                value: priorityLabel,
                field: 'priority'
            });
        }
        
        if (this.statusFilter.value) {
            const statusLabel = this.statusFilter.options[this.statusFilter.selectedIndex].text;
            activeFilters.push({
                name: 'Status',
                value: statusLabel,
                field: 'status'
            });
        }
        
        // Se há filtros ativos, mostra display
        if (activeFilters.length > 0) {
            this.activeFiltersDisplay.style.display = 'flex';
            this.renderActiveTags(activeFilters);
        } else {
            this.activeFiltersDisplay.style.display = 'none';
        }
    }

    /**
     * Renderiza as tags de filtros ativos
     */
    renderActiveTags(filters) {
        this.activeFiltersTags.innerHTML = '';
        
        filters.forEach(filter => {
            const tag = document.createElement('span');
            tag.className = 'active-filter-tag';
            tag.innerHTML = `
                ${filter.name}: <strong>${filter.value}</strong>
                <button class="remove-tag" type="button">×</button>
            `;
            
            // Remove filtro ao clicar no ×
            tag.querySelector('.remove-tag').addEventListener('click', () => {
                if (filter.field === 'priority') this.priorityFilter.value = '';
                if (filter.field === 'status') this.statusFilter.value = '';
                this.updateActiveFiltersDisplay();
            });
            
            this.activeFiltersTags.appendChild(tag);
        });
    }

    /**
     * Reseta todos os filtros
     */
    resetAllFilters() {
        this.priorityFilter.value = '';
        this.statusFilter.value = '';
        this.activeFiltersDisplay.style.display = 'none';
        this.activeFiltersTags.innerHTML = '';
        console.log('Filtros resetados');
    }

    /**
     * FUTURA INTEGRAÇÃO COM BACKEND
     * Será chamada quando o backend estiver pronto
     */
    sendFiltersToBackend(filters) {
        // Será implementado na próxima etapa
        // fetch('/api/secretary/reports/filter', {
        //     method: 'POST',
        //     headers: {
        //         'Content-Type': 'application/json',
        //         'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        //     },
        //     body: JSON.stringify(filters)
        // })
    }
}

// Inicializa quando o DOM está pronto
document.addEventListener('DOMContentLoaded', () => {
    new FilterManager();
});