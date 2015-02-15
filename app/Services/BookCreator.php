<?php namespace App\Services;


use App\Helpers\String;
use App\Models\Author;
use App\Models\Book;
use App\Models\Language;
use App\Models\Theme;

class BookCreator
{

    private $meta;

    public function __construct($meta)
    {
        $this->meta = $meta;
    }

    public function create()
    {
        $book = new Book();
        $book->enabled = true;
        $book->md5 = $this->meta->md5;
        $book->title = String::sanitize($this->meta->title, true);
        $book->description = String::sanitize($this->meta->description);
        $year = substr($this->meta->date, 0, 4);
        $book->year = is_numeric($year) && strlen($year) == 4 ? $year : null;


        $lang = String::sanitize($this->meta->language);
        if (!empty($lang)) {
            $book->language()->associate(Language::firstOrCreate(array('lang' => $lang)));
        }

        $book->save();

        foreach ($this->meta->authors as $author) {
            $author = String::sanitize($author, true);
            if (!empty($author)) {
                $book->authors()->attach(Author::firstOrCreate(array('name' => $author))->id);
            }
        }

        foreach ($this->meta->themes as $theme) {
            $theme = String::sanitize($theme, true);
            if (!empty($theme)) {
                $book->themes()->attach(Theme::firstOrCreate(array('name' => $theme))->id);
            }
        }

        return $book->slug;
    }
}