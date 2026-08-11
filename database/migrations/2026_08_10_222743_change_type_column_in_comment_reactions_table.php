<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('comment_reactions')->where('type', 'like')->update(['type' => 'support']);
        DB::statement('ALTER TABLE comment_reactions DROP CONSTRAINT IF EXISTS comment_reactions_type_check');
        // Add new constraint
        DB::statement("ALTER TABLE comment_reactions ADD CONSTRAINT comment_reactions_type_check CHECK (type::text = ANY (ARRAY['support'::character varying, 'sad'::character varying, 'angry'::character varying, 'worried'::character varying, 'useful'::character varying]::text[]))");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('ALTER TABLE comment_reactions DROP CONSTRAINT IF EXISTS comment_reactions_type_check');
        DB::statement("ALTER TABLE comment_reactions ADD CONSTRAINT comment_reactions_type_check CHECK (type::text = ANY (ARRAY['like'::character varying, 'support'::character varying, 'angry'::character varying, 'useful'::character varying]::text[]))");
    }
};
