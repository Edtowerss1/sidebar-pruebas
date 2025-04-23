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
        Schema::create('menu_items', function (Blueprint $table) {
            $table->id();
            $table->string('item_id')->unique(); // Identificador único (ej: "usuarios")
            $table->string('label'); // Etiqueta visible (ej: "Usuarios")
            $table->string('icon')->nullable(); // Nombre del icono (ej: "users")
            $table->string('href'); // Ruta (ej: "/usuarios")
            $table->boolean('enabled')->default(true); // Si está habilitado
            $table->integer('order')->default(0); // Orden de visualización
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menu_items');
    }
};
