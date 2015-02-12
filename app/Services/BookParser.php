<?php
/**
 * Created by IntelliJ IDEA.
 * User: fleporcq
 * Date: 12/02/15
 * Time: 14:11
 */

namespace App\Services;

use ZipArchive;

class BookParser {



    const CONTAINER_FILE_PATH = 'META-INF/container.xml';

    const OPF_XMLNS = 'http://www.idpf.org/2007/opf';

    private static $_instance = null;

    protected $file;

    private function __construct()
    {
    }

    public static function getInstance() {
        if(is_null(self::$_instance)) {
            self::$_instance = new BookParser();
        }
        return self::$_instance;
    }

    public function parse($file){
        $this->file = $file;
        $meta = null;
        if (file_exists($this->file)) {
            //todo vérifier ext + mime type sinon throw
            $epub = new ZipArchive();
            $epub->open($this->file);
            $meta =  $this->extractRootFile($epub);
        } else {
           //todo throw file not found
        }
        return $meta;
    }

    protected function extractRootFile($epub)
    {

        try {
            $containerFile = $epub->getFromName(self::CONTAINER_FILE_PATH);
        } catch (Exception $e) {
            //todo throw container file not found
            return null;
        }

        $container = simplexml_load_string($containerFile);
        $rootFilePath = $container->rootfiles->rootfile['full-path'];

        try {
            $rootFile = $epub->getFromName($rootFilePath);
        } catch (Exception $e) {
            //todo throw root file not found
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
}