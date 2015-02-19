<?php namespace App\Http\Controllers;

use App\Commands\PushBook;
use App\Models\Book;
use App\Models\Download;
use File;
use Flow;
use Illuminate\Encryption\Encrypter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Intervention\Image\Facades\Image;
use Queue;
use Response;

class BookController extends KoobeController
{

    public function cover($slug)
    {
        self::notFoundIfNull($slug);

        $coversPath = Config::get('koobe.paths.covers');

        $coverFilePath = $coversPath . DIRECTORY_SEPARATOR . $slug . ".jpg";

        $noCoverFileName = $coversPath . DIRECTORY_SEPARATOR . Config::get('koobe.covers.noCover');

        if (File::exists($coverFilePath)) {
            $cover = Image::make($coverFilePath);
        } else if (File::exists($noCoverFileName)) {
            $cover = Image::make($noCoverFileName);
        } else {
            abort(404);
        }

        return $cover->response();
    }

    public function download($slug)
    {
        self::notFoundIfNull($slug);
        $epubsPath = Config::get('koobe.paths.epubs');
        $epubFilePath = $epubsPath . DIRECTORY_SEPARATOR . $slug . ".epub";

        $book = Book::bySlug($slug);

        self::notFoundIfNull($book);

        if (File::exists($epubFilePath)) {
            Download::create([
                "book_id" => $book->id,
                "user_id" => $this->connectedUser->id
            ]);
            $response = response()->download($epubFilePath);
        } else {
            abort(404);
        }

        return $response;
    }

    public function get(Request $request)
    {
        $terms = trim($request->input("terms"));
        $themeId = $request->input("theme");
        $books = Book::with('authors', 'themes');
        if (!empty($terms)) {
            $books = $books->search($terms);
        }
        if (!empty($themeId)) {
            if (empty($terms)) {
                $books = $books->leftJoin('book_theme', 'book_theme.book_id', '=', 'books.id');
            }
            $books->where("book_theme.theme_id", "=", $themeId);
        }
        $books = $books->whereEnabled(true)->paginate(15);
        return Response::json($books);
    }

    public function upload(Encrypter $encrypter)
    {
        return view('book/upload', ['csrfToken' => $encrypter->encrypt(csrf_token())]);
    }

    public function flow(Request $request)
    {
        $flowRequest = new Flow\Request();

        $chunksPath = Config::get('koobe.paths.chunks');

        $config = new Flow\Config([
            'tempDir' => $chunksPath
        ]);

        $file = new Flow\File($config, $flowRequest);

        $response = Response::make('', 200);

        if ($request->isMethod('get')) {
            if (!$file->checkChunk()) {
                return Response::make('', 204);
            }
        } else {
            if ($file->validateChunk()) {
                $file->saveChunk();
            } else {
                // error, invalid chunk upload request, retry
                return Response::make('', 400);
            }
        }

        $epubsPath = Config::get('koobe.paths.epubs');
        $destination = $epubsPath . DIRECTORY_SEPARATOR . $file->getIdentifier() . '.epub';

        if ($file->validateFile() && $file->save($destination)) {
            Queue::push(new PushBook($destination, $this->connectedUser));
            $response = Response::make('pass some success message to flow.js', 200);
        }

        return $response;
    }
}
