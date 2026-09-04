<?php

namespace Database\Seeders;

use App\Models\Post;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $posts = [
            [
                'title' => 'Cate camere de supraveghere iti trebuie pentru o casa?',
                'excerpt' => 'Ghid practic pentru a estima numarul de camere necesare in functie de suprafata si punctele vulnerabile ale locuintei.',
                'body' => "Numarul de camere depinde de dimensiunea proprietatii, numarul de intrari si punctele oarbe. In general, o casa medie necesita intre 4 si 6 camere: intrarea principala, curtea, garajul si zonele laterale.\n\nPentru apartamente, de multe ori sunt suficiente 1-2 camere la intrare. Recomandam intotdeauna o vizita tehnica gratuita inainte de a stabili configuratia finala.",
                'meta_description' => 'Aflati cate camere de supraveghere sunt necesare pentru casa sau apartamentul dumneavoastra si ce factori influenteaza aceasta decizie.',
            ],
            [
                'title' => 'Diferenta dintre camerele analogice si IP',
                'excerpt' => 'Explicam avantajele si dezavantajele fiecarei tehnologii pentru a alege solutia potrivita bugetului tau.',
                'body' => "Camerele analogice (HD-CVI/TVI/AHD) sunt mai accesibile si usor de instalat pe cablu coaxial existent. Camerele IP ofera rezolutie mai mare, functii inteligente (detectie faciala, linie de trecere) si transmisie prin cablu de retea (UTP/PoE).\n\nPentru instalatii noi recomandam IP, iar pentru extinderea unui sistem existent analogic ramane o optiune buna din punct de vedere al costului.",
                'meta_description' => 'Comparatie intre camerele de supraveghere analogice si IP: cost, calitate imagine si usurinta instalarii.',
            ],
            [
                'title' => 'Cat costa un sistem complet de supraveghere video?',
                'excerpt' => 'Prezentam pachetele entry, medium si premium impreuna cu factorii care influenteaza pretul final.',
                'body' => "Pretul unui sistem CCTV depinde de numarul de camere, rezolutie, capacitatea de stocare si complexitatea instalarii (lungime cablu, inaltime montaj).\n\nPachetul Entry porneste de la configuratii cu 4 camere si NVR de baza, Medium adauga rezolutie mai mare si mai multe canale, iar Premium include camere 4K, analitica avansata si acces remote pe termen lung. Foloseste configuratorul nostru pentru o estimare rapida.",
                'meta_description' => 'Afla care este costul mediu al unui sistem de supraveghere video si ce influenteaza pretul final.',
            ],
        ];

        foreach ($posts as $index => $post) {
            Post::updateOrCreate(
                ['slug' => Str::slug($post['title'])],
                [
                    'title' => $post['title'],
                    'excerpt' => $post['excerpt'],
                    'body' => $post['body'],
                    'meta_title' => $post['title'],
                    'meta_description' => $post['meta_description'],
                    'published_at' => now()->subDays(10 - $index * 3),
                ]
            );
        }
    }
}
