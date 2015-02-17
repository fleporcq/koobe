<?php

use App\Models\NotificationType;
use Illuminate\Database\Migrations\Migration;

class Initial extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function ($table) {
            $table->increments('id');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('remember_token')->nullable();
            $table->timestamps();
        });

        Schema::create('languages', function ($table) {
            $table->increments('id');
            $table->char('lang', 8)->unique();
            $table->timestamps();
        });

        Schema::create('books', function ($table) {
            $table->engine = 'MyISAM';
            $table->increments('id');
            $table->char('md5', 32);
            $table->string('title');
            $table->integer('language_id')->nullable()->unsigned();
            $table->string('slug');
            $table->smallInteger('year')->nullable()->unsigned();
            $table->longText('description')->nullable();
            $table->boolean('enabled')->default(false);
            $table->decimal('average_rate', 2, 1)->nullable()->unsigned();
            $table->integer('checker_id')->nullable()->unsigned();
            $table->dateTime('checked_at')->nullable();
            $table->timestamps();
        });

        Schema::create('authors', function ($table) {
            $table->engine = 'MyISAM';
            $table->increments('id');
            $table->string('name')->unique();
            $table->string('slug')->unique();
            $table->timestamps();
        });

        Schema::create('author_book', function ($table) {
            $table->integer('author_id')->unsigned();
            $table->integer('book_id')->unsigned();
        });

        Schema::create('themes', function ($table) {
            $table->engine = 'MyISAM';
            $table->increments('id');
            $table->string('name')->unique();
            $table->string('slug')->unique();
            $table->timestamps();
        });

        Schema::create('book_theme', function ($table) {
            $table->integer('book_id')->unsigned();
            $table->integer('theme_id')->unsigned();
        });

        Schema::create('rates', function ($table) {
            $table->integer('book_id')->unsigned();
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');
            $table->tinyInteger('rate');
            $table->timestamps();
        });

        Schema::create('downloads', function ($table) {
            $table->integer('book_id')->unsigned();
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');
            $table->dateTime('downloaded_at');
        });

        Schema::create('notifications', function ($table) {
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');
            $table->dateTime('pushed_at');
            $table->dateTime('readed_at');
            $table->string('message');
            $table->enum('type', NotificationType::getKeys());
        });

        DB::statement('ALTER TABLE authors ADD FULLTEXT author_search(name)');
        DB::statement('ALTER TABLE themes ADD FULLTEXT theme_search(name)');
        DB::statement('ALTER TABLE books ADD FULLTEXT title_search(title)');
        DB::statement('ALTER TABLE books ADD FULLTEXT description_search(description)');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('author_book');

        Schema::dropIfExists('authors');

        Schema::dropIfExists('book_theme');

        Schema::dropIfExists('themes');

        Schema::dropIfExists('rates');

        Schema::dropIfExists('downloads');

        Schema::dropIfExists('books');

        Schema::dropIfExists('languages');

        Schema::dropIfExists('notifications');

        Schema::dropIfExists('users');

    }

}
