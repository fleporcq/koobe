<?php

use App\Commands\PushBook;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades;

class EpubSeeder extends Seeder
{

    public function run()
    {
        self::clean();
        $seeds = Config::get('koobe.paths.epubsSeeds');
        $epubs = Config::get('koobe.paths.epubs');
        File::copyDirectory($seeds, $epubs);
        foreach (File::files($epubs) as $epub) {
            //todo ajouter user ?
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
        $epubs = Config::get('koobe.paths.epubs');
        foreach (File::files($epubs) as $epub) {
            File::delete($epub);
        }
        File::cleanDirectory(Config::get('koobe.paths.covers'));
    }
}