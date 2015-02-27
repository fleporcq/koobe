<?php namespace App\Services;

use ZipArchive;
use Exception;

class BookParser
{

    const CONTAINER_FILE_PATH = 'META-INF/container.xml';

    const OPF_XMLNS = 'http://www.idpf.org/2007/opf';

    protected $file;

    public function __construct($file)
    {
        $this->file = $file;
    }

    public function parse()
    {
        $bookMeta = null;
        if (file_exists($this->file)) {
            $mimeType = finfo_file(finfo_open(FILEINFO_MIME_TYPE), $this->file);
            if ($mimeType == "application/epub+zip" || $mimeType == "application/zip") {
                $epub = new ZipArchive();
                $epub->open($this->file);
                $bookMeta = $this->getBookMeta($epub);
            } else {
                throw new NotAValidEpubException();
            }
        } else {
            throw new EpubFileNotFoundException();
        }
        return $bookMeta;
    }

    protected function getBookMeta($epub)
    {
        $bookMeta = new BookMeta();

        $rootFileMeta = $this->extractRootFileMeta($epub);

        if ($rootFileMeta != null) {
            $dc = $rootFileMeta->package->metadata->children('dc', true);
            if ($dc != null && @count($dc)) {

                $bookMeta->md5 = $rootFileMeta->md5;
                $bookMeta->title = $dc->title;
                $bookMeta->description = $dc->description;
                $bookMeta->date = $dc->date;
                $bookMeta->language = $dc->language;

                foreach ($dc->creator as $author) {
                    $bookMeta->authors[] = $author;
                }

                if (!empty($dc->type)) {
                    $bookMeta->themes[] = $dc->type;
                }

                foreach ($dc->subject as $subject) {
                    $bookMeta->themes[] = $subject;
                }

            }
            if ($rootFileMeta->cover != null) {
                $bookMeta->cover = $epub->getFromName(($rootFileMeta->path == "." ? "" : $rootFileMeta->path . DIRECTORY_SEPARATOR) . $rootFileMeta->cover->href);
            }
        }
        return $bookMeta;
    }


    protected function extractRootFileMeta($epub)
    {

        try {
            $containerFile = $epub->getFromName(self::CONTAINER_FILE_PATH);
        } catch (Exception $e) {
            throw new ContainerFileNotFoundException();
            return null;
        }

        $container = simplexml_load_string($containerFile);
        $rootFilePath = $container->rootfiles->rootfile['full-path'];

        try {
            $rootFile = $epub->getFromName($rootFilePath);
        } catch (Exception $e) {
            throw new RootFileNotFoundException();
            return null;
        }

        $md5 = md5($rootFile);
        $package = simplexml_load_string($rootFile);
        $path = dirname($rootFilePath);
        $cover = $this->getCoverMetadata($package);

        return (object)array(
            'path' => $path,
            'md5' => $md5,
            'package' => $package,
            'cover' => $cover
        );
    }

    protected function getCoverMetadata($package)
    {
        $coverMetadata = null;
        $package->registerXPathNamespace('opf', self::OPF_XMLNS);

        $coverMeta = $package->xpath('//opf:metadata//opf:meta[@name="cover"]');

        $items = null;

        if (!empty($coverMeta)) {
            $coverId = $coverMeta[0]->attributes()["content"];
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
}

class ContainerFileNotFoundException extends \Exception
{

}

class EpubFileNotFoundException extends \Exception
{

}

class NotAValidEpubException extends \Exception
{

}

class RootFileNotFoundException extends \Exception
{

}

class BookMeta
{
    public $md5 = null;
    public $title = null;
    public $description = null;
    public $date = null;
    public $language = null;
    public $authors = [];
    public $themes = [];
    public $cover = null;
}
