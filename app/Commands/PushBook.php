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
    protected $filename;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct($file, $user = null, $filename = null)
    {
        if ($file == null) {
            throw new InvalidArgumentException();
        }
        $this->file = $file;
        $this->user = $user;
        $this->filename = !empty($filename) ? $filename : basename($this->file);
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
                if ($this->user != null && $this->user->id != null && !empty($this->filename)) {
                    throw $e;
                }
            }
        } catch (EpubFileNotFoundException $e) {
            Notifier::error($this->user, Lang::get('notifications.epubFileNotFound', ['filename' => $this->filename]));
        } catch (NotAValidEpubException $e) {
            Notifier::error($this->user, Lang::get('notifications.notAValidEpub', ['filename' => $this->filename]));
        } catch (ContainerFileNotFoundException $e) {
            Notifier::error($this->user, Lang::get('notifications.containerFileNotFound', ['filename' => $this->filename]));
        } catch (RootFileNotFoundException $e) {
            Notifier::error($this->user, Lang::get('notifications.rootFileNotFound', ['filename' => $this->filename]));
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
                Notifier::error($this->user, Lang::get('notifications.bookAlreadyStored', ['filename' => $this->filename]));
                File::delete($this->file);
            }
        }

        $this->delete();
    }

}
