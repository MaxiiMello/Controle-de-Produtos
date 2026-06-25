// ============================================
// 📁 sortable.js - Ordenação client-side
// ============================================

document.addEventListener('DOMContentLoaded', function() {
    // Encontrar todas as tabelas com sort-links
    document.querySelectorAll('table').forEach(function(table) {
        const sortLinks = table.querySelectorAll('th a.sort-link');
        
        if (sortLinks.length === 0) return;
        
        // Pegar todos os dados da tabela
        const tbody = table.querySelector('tbody');
        if (!tbody) return;
        
        const rows = Array.from(tbody.querySelectorAll('tr'));
        let currentSort = { column: null, direction: 'ASC' };
        
        // Função para ordenar linhas
        function sortRows(columnIndex, direction) {
            const sortedRows = rows.sort((a, b) => {
                const aText = a.cells[columnIndex].textContent.trim();
                const bText = b.cells[columnIndex].textContent.trim();
                
                // Tentar comparar como números
                const aNum = parseFloat(aText.replace(/[^\d,-]/g, '').replace(',', '.'));
                const bNum = parseFloat(bText.replace(/[^\d,-]/g, '').replace(',', '.'));
                
                if (!isNaN(aNum) && !isNaN(bNum)) {
                    return direction === 'ASC' ? aNum - bNum : bNum - aNum;
                }
                
                // Comparar como texto
                return direction === 'ASC' 
                    ? aText.localeCompare(bText, 'pt-BR') 
                    : bText.localeCompare(aText, 'pt-BR');
            });
            
            // Reordenar DOM
            sortedRows.forEach(row => tbody.appendChild(row));
        }
        
        // Adicionar evento de clique em cada link de ordenação
        sortLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                
                // Extrair coluna e direção da URL
                const url = new URL(this.href);
                const column = url.searchParams.get('ordenar');
                const newDirection = url.searchParams.get('ordem');
                
                // Determinar direção
                let direction = 'ASC';
                if (currentSort.column === column && currentSort.direction === 'ASC') {
                    direction = 'DESC';
                }
                
                // Atualizar ícones
                table.querySelectorAll('th a.sort-link').forEach(link => {
                    link.innerHTML = link.innerHTML.replace(/ [↑↓]$/, '');
                });
                
                // Adicionar ícone ao link clicado
                const icon = direction === 'ASC' ? ' ↑' : ' ↓';
                this.innerHTML = this.innerHTML.replace(/ [↑↓]$/, '') + icon;
                
                // Ordenar
                const columnIndex = Array.from(link.parentElement.parentElement.children).indexOf(link.parentElement);
                sortRows(columnIndex, direction);
                
                // Atualizar estado
                currentSort = { column: column, direction: direction };
            });
        });
    });
});