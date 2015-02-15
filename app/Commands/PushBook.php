<?php namespace App\Commands;

use App\Services\BookCreator;
use App\Services\BookParser;
use App\Services\CoverCreator;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Queue\ShouldBeQueued;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\File;

class PushBook extends Command implements SelfHandling, ShouldBeQueued
{

    use InteractsWithQueue, SerializesModels;

    const EXTENSION = "epub";

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
            $parser = new BookParser($this->file);
            $meta = $parser->parse();
        } catch (\Exception $e) {
            //todo traiter les erreur par type
            //créer les exceptions
        }

        if ($meta) {
            $bookCreator = new BookCreator($meta);
            $slug = $bookCreator->create();
            if (!empty($slug)) {
                $coverCreator = new CoverCreator($meta->cover, $slug);
                $coverCreator->create();
                $path = dirname($this->file);
                File::move($this->file, ($path == "." ? "" : $path . DIRECTORY_SEPARATOR) . $slug . "." . self::EXTENSION);
            }
        }

        $this->delete();
    }

}
