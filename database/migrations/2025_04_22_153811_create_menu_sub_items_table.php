<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('menu_sub_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('menu_item_id')->constrained('menu_items')->onDelete('cascade');
            $table->string('subitem_id')->unique(); // Identificador único (ej: "lista-usuarios")
            $table->string('label'); // Etiqueta visible (ej: "Lista de Usuarios")
            $table->string('href'); // Ruta (ej: "/usuarios")
            $table->enum('operation', ['create', 'read', 'update', 'delete']);
            $table->integer('order')->default(0); // Orden de visualización
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menu_sub_items');
    }
};
