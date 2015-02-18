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
use Exception;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Queue\ShouldBeQueued;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Lang;
use InvalidArgumentException;

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
                if ($this->user != null && $this->user->id != null) {
                    throw $e;
                }
            }
        } catch (EpubFileNotFoundException $e) {
            Notifier::error($this->user, Lang::get('notifications.epubFileNotFound', ['file' => $this->file]));
        } catch (NotValidEpubException $e) {
            Notifier::error($this->user, Lang::get('notifications.notValidEpub', ['file' => $this->file]));
        } catch (ContainerFileNotFoundException $e) {
            Notifier::error($this->user, Lang::get('notifications.containerFileNotFound', ['file' => $this->file]));
        } catch (RootFileNotFoundException $e) {
            Notifier::error($this->user, Lang::get('notifications.rootFileNotFound', ['file' => $this->file]));
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
                Notifier::error($this->user, Lang::get('notifications.bookAlreadyStored', ['file' => $this->file]));
                File::delete($this->file);
            }
        }

        $this->delete();
    }

}
