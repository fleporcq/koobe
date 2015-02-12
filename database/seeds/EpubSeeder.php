<?php

use Illuminate\Database\Seeder;
use App\Models\Book;
use App\Commands\PushBook;

class EpubSeeder extends Seeder
{

    public function run()
    {
        self::clean();
        $seeds = base_path("database" . DIRECTORY_SEPARATOR . "seeds" . DIRECTORY_SEPARATOR . Book::SEEDS_DIRECTORY);
        $epubs = storage_path(Book::EPUBS_DIRECTORY);
        File::copyDirectory($seeds, $epubs);
        foreach (File::files($epubs) as $epub) {
            Queue::push(new PushBook($epub));

        }
    }

    private function clean()
    {

        DB::table('author_book')->delete();
        DB::table('authors')->delete();
        DB::table('book_theme')->delete();
        DB::table('themes')->delete();
        DB::table('books')->delete();
        DB::table('languages')->delete();
        $epubs = storage_path(Book::EPUBS_DIRECTORY);
        foreach (File::files($epubs) as $epub) {
            File::delete($epub);
        }
        File::cleanDirectory(storage_path(Book::COVERS_DIRECTORY));
    }
}