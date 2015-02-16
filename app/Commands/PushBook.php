<?php namespace App\Commands;

use App\Models\Book;
use App\Services\BookCreator;
use App\Services\BookParser;
use App\Services\ContainerFileNotFoundException;
use App\Services\CoverCreator;
use App\Services\EpubFileNotFoundException;
use App\Services\NotValidEpubException;
use App\Services\RootFileNotFoundException;
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
        $meta = null;

        try {
            $parser = new BookParser($this->file);
            $meta = $parser->parse();
        } catch (EpubFileNotFoundException $e) {
            //todo notify
        } catch (NotValidEpubException $e) {
            //todo notify
        } catch (ContainerFileNotFoundException $e) {
            //todo notify
        } catch (RootFileNotFoundException $e) {
            //todo notify
        }

        if ($meta) {
            if (Book::whereMd5($meta->md5)->count() == 0) {
                $bookCreator = new BookCreator($meta);
                $slug = $bookCreator->create();
                if (!empty($slug)) {
                    $coverCreator = new CoverCreator($meta->cover, $slug);
                    $coverCreator->create();
                    $path = dirname($this->file);
                    File::move($this->file, ($path == "." ? "" : $path . DIRECTORY_SEPARATOR) . $slug . "." . self::EXTENSION);
                }
            } else {
                //todo notifier book deja dans la base
            }
        }

        $this->delete();
    }

}
