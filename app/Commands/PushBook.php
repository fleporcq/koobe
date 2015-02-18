<?php namespace App\Commands;

use App\Models\Book;
use App\Services\BookCreator;
use App\Services\BookParser;
use App\Services\ContainerFileNotFoundException;
use App\Services\CoverCreator;
use App\Services\EpubFileNotFoundException;
use App\Services\Notifier;
use App\Services\NotValidEpubException;
use App\Services\RootFileNotFoundException;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Queue\ShouldBeQueued;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\File;
use InvalidArgumentException;
use Exception;

class PushBook extends Command implements SelfHandling, ShouldBeQueued
{

    use InteractsWithQueue, SerializesModels;

    const EXTENSION = "epub";

    protected $file;
    protected $user;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct($file, $user = null)
    {
        if ($file == null) {
            throw new InvalidArgumentException();
        }
        $this->file = $file;
        $this->user = $user;
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
            try {
                $parser = new BookParser($this->file);
                $meta = $parser->parse();
            } catch (\Exception $e) {
                File::delete($this->file);
                throw $e;
            }
        } catch (EpubFileNotFoundException $e) {
            if ($this->user != null) {
                Notifier::error($this->user, "EpubFileNotFoundException");
            }
        } catch (NotValidEpubException $e) {
            if ($this->user != null) {
                Notifier::error($this->user, "NotValidEpubException");
            }
        } catch (ContainerFileNotFoundException $e) {
            if ($this->user != null) {
                Notifier::error($this->user, "ContainerFileNotFoundException");
            }
        } catch (RootFileNotFoundException $e) {
            if ($this->user != null) {
                Notifier::error($this->user, "RootFileNotFoundException");
            }
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
                Notifier::error($this->user->id, "BookAlreadyStored");
            }
        }

        $this->delete();
    }

}
