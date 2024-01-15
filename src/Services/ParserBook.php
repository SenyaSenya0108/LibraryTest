<?php

namespace App\Services;

use App\DataFixtures\BookCategoryFixtures;
use App\Entity\Book;
use App\Entity\BookAuthor;
use App\Entity\BookCategory;
use App\Entity\MediaObject;
use DateTime;
use DirectoryIterator;
use Doctrine\ORM\EntityManagerInterface;

class ParserBook
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    public function load(string $source): void
    {
        $filesInfo = $this->getFilesPath($source);

        foreach ($filesInfo as $file) {
            $json = file_get_contents($file);
            $array = json_decode($json, true);
            foreach ($array as $value) {
                $foundBook = $this->entityManager->getRepository(Book::class)->findOneBy(['title' => $value['title']]);

                if (!$foundBook) {
                    $book = new Book();
                    $book->setTitle($value['title']);

                    if (isset($value['isbn'])) {
                        $book->setIsbn($value['isbn']);
                    }

                    if (isset($value['thumbnailUrl'])) {
                        preg_match('/[^?]*\K[^\\/]+(?=\.[^.]+$)|\K[^\\/]+$/', $value['thumbnailUrl'], $matches);
                        $filename = md5($value['thumbnailUrl']);
                        $path = "/app/images/{$filename}";

                        if (strpos($path, "\0") !== false || strpos($value['thumbnailUrl'], "\0") !== false) {
                            die('Путь к файлу или URL содержит нулевые байты');
                        }

                        $image = file_get_contents($value['thumbnailUrl']);
                        file_put_contents($path, $image);
                        $book->setImageName($path);
                    }

                    if ($value['pageCount']) {
                        $book->setPageCount($value['pageCount']);
                    }

                    if (isset($value['publishedDate']['$date'])) {
                        $date = new DateTime($value['publishedDate']['$date']);
                        $book->setPublishedDate($date);
                    }

                    if (isset($value['shortDescription'])) {
                        $book->setShortDescription($value['shortDescription']);
                    }

                    if (isset($value['longDescription'])) {
                        $book->setLongDescription($value['longDescription']);
                    }

                    if ($value['authors']) {
                        foreach ($value['authors'] as $author) {
                            $bookAuthor = $this->entityManager->getRepository(BookAuthor::class)->findOneBy(['name' => $author]);
                            if (!$bookAuthor) {
                                $bookAuthor = new BookAuthor();
                                $bookAuthor->setName($author);
                                $this->entityManager->persist($bookAuthor);
                            }
                            $book->addAuthor($bookAuthor);
                        }
                    }

                    if (isset($value['categories'])) {
                        foreach ($value['categories'] as $category) {
                            $bookCategory = $this->entityManager->getRepository(BookCategory::class)->findOneBy(['name' => $category]);
                            if (!$bookCategory) {
                                $bookCategory = new BookCategory();
                                $bookCategory->setName($category);
                                $this->entityManager->persist($bookCategory);
                            }
                            $book->addCategory($bookCategory);
                        }
                    } else {
                        $bookCategory = $this->entityManager->getRepository(BookCategory::class)->findOneBy(['name' => BookCategoryFixtures::NEW]);
                        $book->addCategory($bookCategory);
                    }

                    $this->entityManager->persist($book);
                    $this->entityManager->flush();
                }
            }
        }
    }

    private function getFilesPath(string $path): array
    {
        $dir = new DirectoryIterator($path);
        foreach ($dir as $file) {
            if ($file->isFile() && $file->getExtension() == 'json') {
                $filesInfo[] = $file->getPathname();
            }
        }
        natsort($filesInfo);

        return $filesInfo;
    }
}
