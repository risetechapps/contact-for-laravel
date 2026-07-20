<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Remove índices ociosos da tabela `contacts`.
 *
 * Mesmo caso do `addresses`: o planner do PostgreSQL avalia TODOS os índices ao
 * planejar cada query. Os 6 índices de coluna única (department/email/telephone/
 * cellphone/is_primary/sort_order) estão sem uso (idx_scan = 0) e só inflam o
 * tempo de planejamento — ninguém filtra contatos por essas colunas no caminho
 * quente (as buscas são por contact_type + contact_id, servidas pelo morphs).
 *
 * Mantidos:
 *  - `contacts_pkey` (PK — integridade);
 *  - `contacts_contact_type_contact_id_index` (uuidMorphs) — é o índice CERTO das
 *    queries quentes; aparece com idx_scan=0 só porque a tabela ainda é pequena
 *    (o planner faz seq scan), mas passa a ser usado quando ela crescer.
 *
 * DROP CONCURRENTLY (fora de transação) para não travar escritas.
 */
return new class extends Migration {
    public $withinTransaction = false;

    private array $indexes = [
        'contacts_department_index',
        'contacts_email_index',
        'contacts_telephone_index',
        'contacts_cellphone_index',
        'contacts_is_primary_index',
        'contacts_sort_order_index',
    ];

    public function up(): void
    {
        if (DB::getDriverName() !== 'pgsql') {
            return;
        }

        foreach ($this->indexes as $index) {
            DB::statement("DROP INDEX CONCURRENTLY IF EXISTS \"{$index}\"");
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() !== 'pgsql') {
            return;
        }

        $recreate = [
            'contacts_department_index' => 'department',
            'contacts_email_index'      => 'email',
            'contacts_telephone_index'  => 'telephone',
            'contacts_cellphone_index'  => 'cellphone',
            'contacts_is_primary_index' => 'is_primary',
            'contacts_sort_order_index' => 'sort_order',
        ];

        foreach ($recreate as $name => $column) {
            DB::statement("CREATE INDEX CONCURRENTLY IF NOT EXISTS \"{$name}\" ON contacts ({$column})");
        }
    }
};
