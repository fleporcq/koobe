<?php namespace App\Commands;

use App\Models\Author;
use App\Models\Book;
use App\Models\Language;
use App\Models\Theme;
use App\Services\BookParser;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Queue\ShouldBeQueued;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\File;
use Intervention\Image\Facades\Image;
use ZipArchive;

class PushBook extends Command implements SelfHandling, ShouldBeQueued
{

    use InteractsWithQueue, SerializesModels;

    const EXTENSION = "epub";

    const CONTAINER_FILE_PATH = 'META-INF/container.xml';

    const OPF_XMLNS = 'http://www.idpf.org/2007/opf';

    protected $file;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct($file)
    {
        $this->file = $file;
    }

    /**
     * Execute the command.
     *
     * @return void
     */
    public function handle()
    {
        try {
            $meta =  BookParser::getInstance()->parse($this->file);
        } catch (\Exception $e) {
            //todo traiter les erreur par type
            //créer les exceptions
        }
        if ($meta) {
            $slug = $this->createBook($meta);
            if (!empty($slug)) {
                $epub = new ZipArchive();
                $epub->open($this->file);
                $this->createCover($meta, $epub, $slug);
                $path = dirname($this->file);
                File::move($this->file, ($path == "." ? "" : $path . DIRECTORY_SEPARATOR) . $slug . "." . self::EXTENSION);
            }
        }

        $this->delete();


    }

    protected function createBook($meta)
    {
        $dc = $meta->package->metadata->children('dc', true);

        $book = new Book();
        $book->enabled = true;
        $book->md5 = $meta->md5;
        $book->title = $this->sanitize($dc->title, true);
        $book->description = $this->sanitize($dc->description);
        $year = substr($dc->date, 0, 4);
        $book->year = is_numeric($year) && strlen($year) == 4 ? $year : null;


        $lang = $this->sanitize($dc->language);
        if (!empty($lang)) {
            $book->language()->associate(Language::firstOrCreate(array('lang' => $lang)));
        }

        $book->save();

        foreach ($dc->creator as $author) {
            $author = $this->sanitize($author, true);
            if (!empty($author)) {
                $book->authors()->attach(Author::firstOrCreate(array('name' => $author))->id);
            }
        }

        $type = $this->sanitize($dc->type);
        if (!empty($type)) {
            $book->themes()->attach(Theme::firstOrCreate(array('name' => $type))->id);
        }

        foreach ($dc->subject as $theme) {
            $theme = $this->sanitize($theme, true);
            if (!empty($theme)) {
                $book->themes()->attach(Theme::firstOrCreate(array('name' => $theme))->id);
            }
        }

        return $book->slug;
    }

    protected function createCover($meta, $epub, $slug)
    {
        if ($meta->cover != null) {
            //Todo tester existence fichier + créer différentes tailles d'images
            $coverSource = $epub->getFromName(($meta->path == "." ? "" : $meta->path . DIRECTORY_SEPARATOR) . $meta->cover->href);
            $cover = Image::make($coverSource)->encode('jpg', 75);
            $cover->save(storage_path(Book::COVERS_DIRECTORY) . DIRECTORY_SEPARATOR . $slug . '.jpg');
        }
    }

    protected function sanitize($string, $capitalize = false)
    {
        $string = trim(strip_tags($string));
        if ($capitalize) {
            $string = ucfirst(strtolower($string));
        }
        return $string;
    }
}
