<?php namespace App\Commands;

use App\Models\Author;
use App\Models\Book;
use App\Models\Language;
use App\Models\Theme;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Queue\ShouldBeQueued;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\File;
use Intervention\Image\Facades\Image;
use ZipArchive;

class ParseBook extends Command implements SelfHandling, ShouldBeQueued
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
        if (File::exists($this->file) && File::extension($this->file) === self::EXTENSION) {
            $epub = new ZipArchive();
            $epub->open($this->file);
            $opf = $this->extractOpf($this->file, $epub);

            if ($opf) {
                $slug = $this->createBook($opf);
                if (!empty($slug)) {
                    $this->createCover($opf, $epub, $slug);
                    $path = dirname($this->file);
                    File::move($this->file, ($path == "." ? "" : $path . DIRECTORY_SEPARATOR) . $slug . "." . self::EXTENSION);
                }
            } else {
                //Todo File::delete($file);
            }
            $this->delete();
        } else {
            Log::error($this->file . ' not found. ');
        }
    }

    protected function extractOpf($file, $epub)
    {
        $opf = null;
        try {
            $containerFile = $epub->getFromName(self::CONTAINER_FILE_PATH);
        } catch (Exception $e) {
            Log::error($file . " - " . self::CONTAINER_FILE_PATH . ' not found. ' . $e);
            return null;
        }

        $container = simplexml_load_string($containerFile);
        $rootFilePath = $container->rootfiles->rootfile['full-path'];

        try {
            $rootFile = $epub->getFromName($rootFilePath);
        } catch (Exception $e) {
            Log::error($file . " - " . self::CONTAINER_FILE_PATH . ' not found. ' . $e);
            return null;
        }

        $md5 = md5($rootFile);

        if (Book::whereMd5($md5)->count() == 0) {
            $opf = (object)array(
                'package' => simplexml_load_string($rootFile),
                'md5' => $md5,
                'path' => dirname($rootFilePath)
            );
        } else {
            //Todo log + notification 'Book already in DB'
        }

        return $opf;
    }

    protected function createBook($opf)
    {
        $dc = $opf->package->metadata->children('dc', true);

        $book = new Book();
        $book->enabled = true;
        $book->md5 = $opf->md5;
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

    protected function createCover($opf, $epub, $slug)
    {
        $coverMetadata = $this->getCoverMetadata($opf->package);
        if ($coverMetadata != null) {
            //Todo tester existence fichier + créer différentes tailles d'images
            $coverSource = $epub->getFromName(($opf->path == "." ? "" : $opf->path . DIRECTORY_SEPARATOR) . $coverMetadata->href);
            $cover = Image::make($coverSource)->encode('jpg', 75);
            $cover->save(storage_path(Book::COVERS_DIRECTORY) . DIRECTORY_SEPARATOR . $slug . '.jpg');
        }
    }

    protected function getCoverMetadata($package)
    {
        $coverMetadata = null;
        $package->registerXPathNamespace('opf', self::OPF_XMLNS);
        $metas = $package->xpath('//opf:metadata//opf:meta[@name="cover"]');

        $items = null;

        if (!empty($metas)) {
            $coverId = $metas[0]->attributes()["content"];
            $items = $package->xpath('//opf:manifest//opf:item[@id="' . $coverId . '"]');
        }

        //fallback
        if (empty($items)) {
            $items = $package->xpath("//opf:manifest//opf:item[contains(@href,'cover') and contains(@media-type,'image')]");
        }

        if (!empty($items)) {
            $item = $items[0];
            $coverMetadata = (object)array(
                'href' => $item->attributes()["href"],
                'type' => $item->attributes()["media-type"]
            );
        }
        return $coverMetadata;
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
